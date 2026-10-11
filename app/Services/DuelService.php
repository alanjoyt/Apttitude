<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/WalletService.php';

class DuelService {

    /**
     * Create a new 1v1 Quiz Duel challenge
     */
    public static function createDuel(int $userId, int $wagerCoins = 10): array {
        $db = Database::getConnection();

        // Check wallet balance
        $balance = WalletService::getUserBalance($userId);
        if ($balance < $wagerCoins) {
            return ['status' => 'error', 'message' => "Insufficient Speed Coins! You need at least {$wagerCoins} coins."];
        }

        // Spend wager
        WalletService::spendCoins($userId, $wagerCoins, "1v1 Quiz Duel Wager Stake");

        // Create duel record
        $stmt = $db->prepare("INSERT INTO duels (challenger_id, wager_coins, status) VALUES (?, ?, 'waiting')");
        $stmt->execute([$userId, $wagerCoins]);
        $duelId = (int)$db->lastInsertId();

        // Select 5 UNATTEMPTED, NON-REPETITIVE questions for the user
        $qStmt = $db->prepare("
            SELECT q.id FROM questions q
            WHERE q.id NOT IN (
                SELECT question_id FROM attempts WHERE user_id = ?
                UNION
                SELECT dq.question_id FROM duel_questions dq
                JOIN duels d ON dq.duel_id = d.id
                WHERE d.challenger_id = ? OR d.opponent_id = ?
            )
            ORDER BY RAND() LIMIT 5
        ");
        $qStmt->execute([$userId, $userId, $userId]);
        $qIds = $qStmt->fetchAll(PDO::FETCH_COLUMN);

        // Fallback: If unattempted questions pool is smaller than 5, pick least attempted questions
        if (count($qIds) < 5) {
            $needed = 5 - count($qIds);
            $excludeStr = !empty($qIds) ? "AND id NOT IN (" . implode(',', array_map('intval', $qIds)) . ")" : "";
            $fbStmt = $db->query("SELECT id FROM questions WHERE 1=1 {$excludeStr} ORDER BY total_attempts_count ASC, RAND() LIMIT {$needed}");
            $fbIds = $fbStmt->fetchAll(PDO::FETCH_COLUMN);
            $qIds = array_merge($qIds, $fbIds);
        }

        $order = 1;
        foreach ($qIds as $qid) {
            $db->prepare("INSERT INTO duel_questions (duel_id, question_id, question_order) VALUES (?, ?, ?)")
               ->execute([$duelId, $qid, $order++]);
        }

        return ['status' => 'success', 'message' => '1v1 Duel created! Waiting for an opponent to accept the challenge.', 'duel_id' => $duelId];
    }

    /**
     * Get user active and open duels
     */
    public static function getDuels(int $userId): array {
        $db = Database::getConnection();

        // User's active/completed duels
        $myStmt = $db->prepare("
            SELECT d.*, c.name as challenger_name, o.name as opponent_name
            FROM duels d
            JOIN users c ON d.challenger_id = c.id
            LEFT JOIN users o ON d.opponent_id = o.id
            WHERE d.challenger_id = ? OR d.opponent_id = ?
            ORDER BY d.created_at DESC LIMIT 10
        ");
        $myStmt->execute([$userId, $userId]);
        $myDuels = $myStmt->fetchAll();

        // Open lobby duels to join
        $openStmt = $db->prepare("
            SELECT d.*, c.name as challenger_name, c.tier as challenger_tier
            FROM duels d
            JOIN users c ON d.challenger_id = c.id
            WHERE d.status = 'waiting' AND d.challenger_id != ?
            ORDER BY d.created_at DESC LIMIT 5
        ");
        $openStmt->execute([$userId]);
        $openDuels = $openStmt->fetchAll();

        return [
            'my_duels' => $myDuels,
            'open_duels' => $openDuels
        ];
    }

    /**
     * Join an open duel challenge
     */
    public static function joinDuel(int $userId, int $duelId): array {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM duels WHERE id = ? AND status = 'waiting'");
        $stmt->execute([$duelId]);
        $duel = $stmt->fetch();

        if (!$duel) {
            return ['status' => 'error', 'message' => 'Duel is no longer available!'];
        }

        if ($duel['challenger_id'] == $userId) {
            return ['status' => 'error', 'message' => 'You cannot challenge yourself!'];
        }

        // Check wallet
        $wager = (int)$duel['wager_coins'];
        if (WalletService::getUserBalance($userId) < $wager) {
            return ['status' => 'error', 'message' => "Insufficient Speed Coins! You need at least {$wager} coins."];
        }

        WalletService::spendCoins($userId, $wager, "1v1 Quiz Duel Opponent Stake");

        // Mark active
        $db->prepare("UPDATE duels SET opponent_id = ?, status = 'active' WHERE id = ?")
           ->execute([$userId, $duelId]);

        return ['status' => 'success', 'message' => 'Joined Duel! Loading quiz questions...', 'duel_id' => $duelId];
    }

    /**
     * Get questions for a duel
     */
    public static function getDuelQuestions(int $duelId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT q.id, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, dq.question_order
            FROM duel_questions dq
            JOIN questions q ON dq.question_id = q.id
            WHERE dq.duel_id = ?
            ORDER BY dq.question_order ASC
        ");
        $stmt->execute([$duelId]);
        return $stmt->fetchAll();
    }

    /**
     * Submit duel results
     */
    public static function submitDuelResults(int $userId, int $duelId, int $score): array {
        $db = Database::getConnection();

        // Anti-Exploit: Strict score bounds (5 questions max per duel)
        $score = min(5, max(0, $score));

        $stmt = $db->prepare("SELECT * FROM duels WHERE id = ?");
        $stmt->execute([$duelId]);
        $duel = $stmt->fetch();

        if (!$duel) {
            return ['status' => 'error', 'message' => 'Duel not found!'];
        }

        if ($duel['status'] === 'completed') {
            return ['status' => 'error', 'message' => 'Duel is already completed! Score cannot be re-submitted.'];
        }

        $isChallenger = ($duel['challenger_id'] == $userId);
        $isOpponent = ($duel['opponent_id'] == $userId);

        if (!$isChallenger && !$isOpponent) {
            return ['status' => 'error', 'message' => 'You are not a participant in this duel!'];
        }

        if ($isChallenger) {
            $db->prepare("UPDATE duels SET challenger_score = ? WHERE id = ? AND challenger_id = ?")
               ->execute([$score, $duelId, $userId]);
            $duel['challenger_score'] = $score;
        } else {
            $db->prepare("UPDATE duels SET opponent_score = ? WHERE id = ? AND opponent_id = ?")
               ->execute([$score, $duelId, $userId]);
            $duel['opponent_score'] = $score;
        }

        // Check if both scores are submitted & evaluate winner atomically
        $checkStmt = $db->prepare("SELECT * FROM duels WHERE id = ?");
        $checkStmt->execute([$duelId]);
        $updatedDuel = $checkStmt->fetch();

        if ($updatedDuel['opponent_id'] !== null && $updatedDuel['status'] === 'active') {
            $cScore = (int)$updatedDuel['challenger_score'];
            $oScore = (int)$updatedDuel['opponent_score'];

            $winnerId = null;
            if ($cScore > $oScore) $winnerId = $updatedDuel['challenger_id'];
            else if ($oScore > $cScore) $winnerId = $updatedDuel['opponent_id'];

            $totalPot = (int)$updatedDuel['wager_coins'] * 2;

            // Anti-Exploit: Atomic status change ensures pot is awarded EXACTLY ONCE!
            $completeStmt = $db->prepare("UPDATE duels SET status = 'completed', winner_id = ? WHERE id = ? AND status = 'active'");
            $completeStmt->execute([$winnerId, $duelId]);

            if ($completeStmt->rowCount() === 1) {
                if ($winnerId) {
                    WalletService::addCoins($winnerId, $totalPot, 'mock_perfect', "1v1 Quiz Duel Victory Pot Reward!");
                    User::addXP($winnerId, 150);
                } else {
                    // Draw: Refund stakes
                    WalletService::addCoins($updatedDuel['challenger_id'], $updatedDuel['wager_coins'], 'admin_grant', "Duel Draw Refund");
                    WalletService::addCoins($updatedDuel['opponent_id'], $updatedDuel['wager_coins'], 'admin_grant', "Duel Draw Refund");
                }
            }
        }

        return ['status' => 'success', 'message' => 'Duel score submitted successfully!'];
    }
}
