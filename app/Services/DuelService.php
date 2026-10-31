<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Services/GamificationEngine.php';
require_once __DIR__ . '/WalletService.php';

class DuelService {

    /**
     * Create a new 1v1 Quiz Duel challenge (PvP)
     * Enforces minimum 30 questions requirement to stake coins
     */
    public static function createDuel(int $userId, int $wagerCoins = 10): array {
        $db = Database::getConnection();
        $cfg = GamificationEngine::getConfig();

        // 0. Server-authoritative wager tier validation
        $allowedWagers = $cfg['duels']['pvp_wagers'] ?? [10, 25, 50];
        if (!in_array($wagerCoins, $allowedWagers, true)) {
            return [
                'status' => 'error',
                'message' => 'Invalid wager amount. Allowed stakes are: ' . implode(', ', $allowedWagers) . ' Speed Coins.'
            ];
        }

        // 1. Anti-Exploit: Require at least 30 practice questions before staking coins in PvP
        $minQuestions = (int)($cfg['duels']['min_questions_to_stake'] ?? 30);
        $attStmt = $db->prepare("SELECT COUNT(*) FROM attempts WHERE user_id = ?");
        $attStmt->execute([$userId]);
        $totalAttempted = (int)$attStmt->fetchColumn();

        if ($wagerCoins > 0 && $totalAttempted < $minQuestions) {
            return [
                'status' => 'error',
                'message' => "You must answer at least {$minQuestions} practice questions before staking Speed Coins in PvP duels. (Current: {$totalAttempted}/{$minQuestions})"
            ];
        }

        // 2. Check wallet balance
        $balance = WalletService::getUserBalance($userId);
        if ($balance < $wagerCoins) {
            return ['status' => 'error', 'message' => "Insufficient Speed Coins! You need at least {$wagerCoins} coins."];
        }

        // 3. Spend wager
        if ($wagerCoins > 0) {
            WalletService::spendCoins($userId, $wagerCoins, "1v1 Quiz Duel Wager Stake");
        }

        // 4. Create duel record
        $stmt = $db->prepare("INSERT INTO duels (challenger_id, wager_coins, status, is_bot) VALUES (?, ?, 'waiting', 0)");
        $stmt->execute([$userId, $wagerCoins]);
        $duelId = (int)$db->lastInsertId();

        // 5. Select 5 questions
        self::populateDuelQuestions($duelId, $userId);

        return [
            'status' => 'success',
            'message' => '1v1 Duel created! Waiting for an opponent to accept the challenge.',
            'duel_id' => $duelId
        ];
    }

    /**
     * Create a Practice vs Bot Duel (0 stake, 0 coin winnings, capped +10 XP max 3x/day)
     */
    public static function createBotDuel(int $userId): array {
        $db = Database::getConnection();

        // Simulated bot score (4 to 8 out of 10)
        $botScore = rand(4, 8);

        $stmt = $db->prepare("
            INSERT INTO duels (challenger_id, opponent_id, wager_coins, status, is_bot, opponent_score)
            VALUES (?, NULL, 0, 'active', 1, ?)
        ");
        $stmt->execute([$userId, $botScore]);
        $duelId = (int)$db->lastInsertId();

        self::populateDuelQuestions($duelId, $userId);

        return [
            'status' => 'success',
            'message' => 'Practice vs Bot Arena initialized! 10 questions ready.',
            'duel_id' => $duelId,
            'is_bot' => true
        ];
    }

    /**
     * Populate 10 balanced questions for a duel
     */
    private static function populateDuelQuestions(int $duelId, int $userId): void {
        $db = Database::getConnection();

        $qStmt = $db->prepare("
            SELECT q.id FROM questions q
            WHERE q.id NOT IN (
                SELECT question_id FROM attempts WHERE user_id = ?
                UNION
                SELECT dq.question_id FROM duel_questions dq
                JOIN duels d ON dq.duel_id = d.id
                WHERE d.challenger_id = ? OR d.opponent_id = ?
            )
            ORDER BY RAND() LIMIT 10
        ");
        $qStmt->execute([$userId, $userId, $userId]);
        $qIds = $qStmt->fetchAll(PDO::FETCH_COLUMN);

        if (count($qIds) < 10) {
            $needed = 10 - count($qIds);
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
    }

    /**
     * Get user active and open duels
     */
    public static function getDuels(?int $userId = null): array {
        if (!$userId) {
            return ['my_duels' => [], 'open_duels' => []];
        }

        $db = Database::getConnection();

        // User's active/completed duels
        $myStmt = $db->prepare("
            SELECT d.*, c.name as challenger_name, 
                   COALESCE(o.name, CASE WHEN d.is_bot = 1 THEN 'Practice Bot (AI)' ELSE 'Open Opponent' END) as opponent_name
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
            WHERE d.status = 'waiting' AND d.challenger_id != ? AND d.is_bot = 0
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
     * Enforces:
     * 1. 30 questions answered threshold
     * 2. Anti-collusion cap: maximum 3 duels per day between the same user pair
     */
    public static function joinDuel(int $userId, int $duelId): array {
        $db = Database::getConnection();
        $cfg = GamificationEngine::getConfig();

        $stmt = $db->prepare("SELECT * FROM duels WHERE id = ? AND status = 'waiting' AND is_bot = 0");
        $stmt->execute([$duelId]);
        $duel = $stmt->fetch();

        if (!$duel) {
            return ['status' => 'error', 'message' => 'Duel is no longer available!'];
        }

        if ($duel['challenger_id'] == $userId) {
            return ['status' => 'error', 'message' => 'You cannot challenge yourself!'];
        }

        $wager = (int)$duel['wager_coins'];

        // 1. Verify 30 questions threshold for joining coin-staked duels
        if ($wager > 0) {
            $minQuestions = (int)($cfg['duels']['min_questions_to_stake'] ?? 30);
            $attStmt = $db->prepare("SELECT COUNT(*) FROM attempts WHERE user_id = ?");
            $attStmt->execute([$userId]);
            $totalAttempted = (int)$attStmt->fetchColumn();

            if ($totalAttempted < $minQuestions) {
                return [
                    'status' => 'error',
                    'message' => "You must complete at least {$minQuestions} practice questions before staking coins in PvP duels. (Current: {$totalAttempted}/{$minQuestions})"
                ];
            }
        }

        // 2. Anti-Collusion Check: Max 3 duels per day between the same pair of users
        $challengerId = (int)$duel['challenger_id'];
        $maxPerPair = (int)($cfg['duels']['max_daily_duels_same_pair'] ?? 3);
        $pairStmt = $db->prepare("
            SELECT COUNT(*) FROM duels
            WHERE ((challenger_id = ? AND opponent_id = ?) OR (challenger_id = ? AND opponent_id = ?))
              AND DATE(created_at) = CURDATE()
        ");
        $pairStmt->execute([$challengerId, $userId, $userId, $challengerId]);
        if ((int)$pairStmt->fetchColumn() >= $maxPerPair) {
            return [
                'status' => 'error',
                'message' => "Daily duel limit reached with this opponent (maximum {$maxPerPair} duels per day to prevent collusion)."
            ];
        }

        // 3. Check wallet & spend wager
        if ($wager > 0) {
            if (WalletService::getUserBalance($userId) < $wager) {
                return ['status' => 'error', 'message' => "Insufficient Speed Coins! You need at least {$wager} coins."];
            }
            WalletService::spendCoins($userId, $wager, "1v1 Quiz Duel Opponent Stake");
        }

        // 4. Mark active
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
            SELECT q.id, q.question_text, q.code_snippet, q.language, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, dq.question_order
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
     * Enforces:
     * 1. 5% house rake on winning PvP pot
     * 2. Practice vs Bot: 0 coin stake/winnings, capped +10 XP max 3x/day
     */
    public static function submitDuelResults(int $userId, int $duelId, int $score): array {
        $db = Database::getConnection();
        $cfg = GamificationEngine::getConfig();

        // Anti-Exploit: Strict score bounds (10 questions max per duel)
        $score = min(10, max(0, $score));

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

        // -------------------------------------------------------------
        // BRANCH A: PRACTICE VS BOT DUEL
        // -------------------------------------------------------------
        if ((int)$duel['is_bot'] === 1) {
            $botScore = (int)$duel['opponent_score'];
            $winnerId = null;
            $msg = '';

            if ($score > $botScore) {
                $winnerId = $userId;
                // Check bot wins cap for today (max 3 per day)
                $botWinCheck = $db->prepare("
                    SELECT COUNT(*) FROM duels
                    WHERE challenger_id = ? AND is_bot = 1 AND winner_id = ? AND DATE(created_at) = CURDATE()
                ");
                $botWinCheck->execute([$userId, $userId]);
                $botWinsToday = (int)$botWinCheck->fetchColumn();

                $maxBotWins = (int)($cfg['duels']['bot']['max_daily_wins'] ?? 3);
                $botWinXP = (int)($cfg['duels']['bot']['xp_win'] ?? 10);

                if ($botWinsToday < $maxBotWins) {
                    User::addXP($userId, $botWinXP);
                    $msg = "Victory vs Bot! You won {$score} - {$botScore}. +{$botWinXP} XP awarded (" . ($botWinsToday + 1) . "/{$maxBotWins} daily bot XP rewards earned).";
                } else {
                    $msg = "Victory vs Bot! You won {$score} - {$botScore}. Daily bot XP reward cap reached ({$maxBotWins}/{$maxBotWins}). Great practice session!";
                }
            } elseif ($score === $botScore) {
                $msg = "Draw vs Bot! Final score: {$score} - {$botScore}. Keep sharpening your speed!";
            } else {
                $msg = "Defeat vs Bot. The Bot scored {$botScore} while you scored {$score}. Review your answers and try again!";
            }

            $db->prepare("
                UPDATE duels
                SET challenger_score = ?, status = 'completed', winner_id = ?
                WHERE id = ? AND status = 'active'
            ")->execute([$score, $winnerId, $duelId]);

            return [
                'status' => 'success',
                'message' => $msg,
                'is_bot' => true,
                'user_score' => $score,
                'bot_score' => $botScore
            ];
        }

        // -------------------------------------------------------------
        // BRANCH B: 1v1 PvP DUEL
        // -------------------------------------------------------------
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

            $wager = (int)$updatedDuel['wager_coins'];
            $totalPot = $wager * 2;

            // Anti-Exploit: Atomic status change ensures pot is awarded EXACTLY ONCE!
            $completeStmt = $db->prepare("UPDATE duels SET status = 'completed', winner_id = ? WHERE id = ? AND status = 'active'");
            $completeStmt->execute([$winnerId, $duelId]);

            if ($completeStmt->rowCount() === 1) {
                if ($winnerId) {
                    // Apply 5% house rake
                    $rakePercent = (float)($cfg['duels']['pvp_rake_percent'] ?? 5);
                    $rakeAmount = (int)round($totalPot * ($rakePercent / 100));
                    $netWinnings = max(1, $totalPot - $rakeAmount);

                    WalletService::addCoins(
                        $winnerId,
                        $netWinnings,
                        'mock_perfect',
                        "1v1 Quiz Duel Victory Pot Reward ({$netWinnings} coins after {$rakePercent}% rake: {$rakeAmount} coins)"
                    );
                    User::addXP($winnerId, 50); // Balanced 50 XP for winning a 10-question PvP duel
                } else {
                    // Draw: Refund stakes without rake
                    if ($wager > 0) {
                        WalletService::addCoins($updatedDuel['challenger_id'], $wager, 'admin_grant', "Duel Draw Refund");
                        WalletService::addCoins($updatedDuel['opponent_id'], $wager, 'admin_grant', "Duel Draw Refund");
                    }
                }
            }
        }

        return ['status' => 'success', 'message' => 'Duel score submitted successfully!'];
    }
}
