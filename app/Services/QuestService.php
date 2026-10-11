<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/WalletService.php';

class QuestService {

    /**
     * Get or initialize today's daily quests for a user
     */
    public static function getUserDailyQuests(int $userId): array {
        $db = Database::getConnection();
        $today = date('Y-m-d');

        // Check if daily quests exist for today, if not seed them
        $stmt = $db->prepare("
            SELECT q.*, uq.progress_count, uq.is_completed, uq.is_claimed
            FROM daily_quests q
            LEFT JOIN user_daily_quests uq ON q.id = uq.quest_id AND uq.user_id = ? AND uq.assigned_date = ?
            ORDER BY q.id ASC
        ");
        $stmt->execute([$userId, $today]);
        $quests = $stmt->fetchAll();

        // If user has missing quest rows for today, populate them
        foreach ($quests as &$q) {
            if ($q['progress_count'] === null) {
                $db->prepare("
                    INSERT IGNORE INTO user_daily_quests (user_id, quest_id, progress_count, is_completed, is_claimed, assigned_date)
                    VALUES (?, ?, 0, 0, 0, ?)
                ")->execute([$userId, $q['id'], $today]);

                $q['progress_count'] = 0;
                $q['is_completed'] = 0;
                $q['is_claimed'] = 0;
            }
        }

        return $quests;
    }

    /**
     * Increment progress for quests matching a quest type
     */
    public static function triggerQuestProgress(int $userId, string $questType, int $count = 1): void {
        $db = Database::getConnection();
        $today = date('Y-m-d');

        // Fetch user's active quests for today matching this type
        $stmt = $db->prepare("
            SELECT q.id, q.target_count, uq.progress_count, uq.is_completed
            FROM daily_quests q
            JOIN user_daily_quests uq ON q.id = uq.quest_id
            WHERE uq.user_id = ? AND uq.assigned_date = ? AND q.quest_type = ? AND uq.is_completed = 0
        ");
        $stmt->execute([$userId, $today, $questType]);
        $activeQuests = $stmt->fetchAll();

        foreach ($activeQuests as $quest) {
            $newProgress = $quest['progress_count'] + $count;
            $isCompleted = ($newProgress >= $quest['target_count']) ? 1 : 0;

            $updateStmt = $db->prepare("
                UPDATE user_daily_quests
                SET progress_count = ?, is_completed = ?
                WHERE user_id = ? AND quest_id = ? AND assigned_date = ?
            ");
            $updateStmt->execute([$newProgress, $isCompleted, $userId, $quest['id'], $today]);
        }
    }

    /**
     * Claim reward for a completed daily quest
     */
    public static function claimQuestReward(int $userId, int $questId): array {
        $db = Database::getConnection();
        $today = date('Y-m-d');

        $stmt = $db->prepare("
            SELECT q.*, uq.is_completed, uq.is_claimed
            FROM daily_quests q
            JOIN user_daily_quests uq ON q.id = uq.quest_id
            WHERE uq.user_id = ? AND uq.quest_id = ? AND uq.assigned_date = ?
        ");
        $stmt->execute([$userId, $questId, $today]);
        $quest = $stmt->fetch();

        if (!$quest) {
            return ['status' => 'error', 'message' => 'Quest not found for today'];
        }

        if (!$quest['is_completed']) {
            return ['status' => 'error', 'message' => 'Quest objectives not completed yet!'];
        }

        if ($quest['is_claimed']) {
            return ['status' => 'error', 'message' => 'Quest reward already claimed!'];
        }

        // Anti-Exploit: Atomic update prevents race-condition double claiming!
        $updateStmt = $db->prepare("
            UPDATE user_daily_quests
            SET is_claimed = 1
            WHERE user_id = ? AND quest_id = ? AND assigned_date = ? AND is_completed = 1 AND is_claimed = 0
        ");
        $updateStmt->execute([$userId, $questId, $today]);

        if ($updateStmt->rowCount() === 0) {
            return ['status' => 'error', 'message' => 'Quest reward already claimed or objectives incomplete!'];
        }

        // Award rewards safely once
        $xpRes = User::addXP($userId, (int)$quest['xp_reward']);
        WalletService::addCoins($userId, (int)$quest['coin_reward'], 'admin_grant', 'Daily Quest Completion: ' . $quest['title']);

        return [
            'status' => 'success',
            'message' => 'Claimed reward: +' . $quest['xp_reward'] . ' XP & +' . $quest['coin_reward'] . ' Speed Coins!',
            'xp_earned' => (int)$quest['xp_reward'],
            'coins_earned' => (int)$quest['coin_reward']
        ];
    }
}
