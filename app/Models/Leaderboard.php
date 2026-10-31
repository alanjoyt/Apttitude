<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/../Services/WalletService.php';

class Leaderboard {

    /**
     * Fetch leaderboard rankings for a period and optional tier filter
     */
    public static function getRankings(string $period = 'weekly', ?string $tier = null, int $limit = 20): array {
        self::checkWeeklyRollover();
        $db = Database::getConnection();

        $sql = "
            SELECT u.id, u.name, u.tier, u.total_xp, ls.period_xp,
                   RANK() OVER (ORDER BY ls.period_xp DESC, ls.updated_at ASC, u.id ASC) as period_rank
            FROM leaderboard_scores ls
            JOIN users u ON ls.user_id = u.id
            WHERE ls.period = ? AND u.role != 'admin'
        ";
        $params = [$period];

        if ($tier) {
            $sql .= " AND ls.tier = ?";
            $params[] = $tier;
        }

        $sql .= " ORDER BY ls.period_xp DESC, ls.updated_at ASC, u.id ASC LIMIT " . (int)$limit;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();

        // Fallback: If leaderboard_scores is empty or not populated yet, query users directly by total_xp
        if (empty($results)) {
            $fallbackSql = "
                SELECT u.id, u.name, u.tier, u.total_xp, u.total_xp as period_xp,
                       RANK() OVER (ORDER BY u.total_xp DESC, u.created_at ASC, u.id ASC) as period_rank
                FROM users u
                WHERE u.role != 'admin'
            ";
            $fbParams = [];
            if ($tier) {
                $fallbackSql .= " AND u.tier = ?";
                $fbParams[] = $tier;
            }
            $fallbackSql .= " ORDER BY u.total_xp DESC, u.created_at ASC, u.id ASC LIMIT " . (int)$limit;
            $fbStmt = $db->prepare($fallbackSql);
            $fbStmt->execute($fbParams);
            $results = $fbStmt->fetchAll();
        }

        return $results;
    }

    /**
     * Get user rank and relative rank context ("you are 2 spots from Silver")
     */
    public static function getUserRankContext(int $userId, string $period = 'weekly'): array {
        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT ls.user_id, ls.period_xp, ls.tier,
                   (SELECT COUNT(*) + 1 
                    FROM leaderboard_scores ls2 
                    JOIN users u2 ON ls2.user_id = u2.id 
                    WHERE ls2.period = ? AND ls2.period_xp > ls.period_xp AND u2.role != 'admin') as current_rank
            FROM leaderboard_scores ls
            JOIN users u ON ls.user_id = u.id
            WHERE ls.user_id = ? AND ls.period = ? AND u.role != 'admin'
        ");
        $stmt->execute([$period, $userId, $period]);
        $userRank = $stmt->fetch();

        if (!$userRank) {
            $user = User::findById($userId);
            return [
                'rank' => 'Unranked',
                'period_xp' => $user ? (int)$user['total_xp'] : 0,
                'tier' => $user ? $user['tier'] : 'Bronze',
                'player_above' => null,
                'xp_needed' => 50,
                'message' => 'Complete a practice question to get ranked on the leaderboard!'
            ];
        }

        // Get player just above (excluding admins)
        $aboveStmt = $db->prepare("
            SELECT u.name, ls.period_xp, ls.tier 
            FROM leaderboard_scores ls
            JOIN users u ON ls.user_id = u.id
            WHERE ls.period = ? AND ls.period_xp > ? AND u.role != 'admin'
            ORDER BY ls.period_xp ASC LIMIT 1
        ");
        $aboveStmt->execute([$period, $userRank['period_xp']]);
        $playerAbove = $aboveStmt->fetch();

        $xpNeeded = $playerAbove ? ($playerAbove['period_xp'] - $userRank['period_xp'] + 10) : 0;

        return [
            'rank' => (int)$userRank['current_rank'],
            'period_xp' => (int)$userRank['period_xp'],
            'tier' => $userRank['tier'],
            'player_above' => $playerAbove ? $playerAbove['name'] : null,
            'xp_needed' => $xpNeeded,
            'message' => $playerAbove ? "Only {$xpNeeded} XP behind {$playerAbove['name']}!" : "You're at the top of the leaderboard!"
        ];
    }

    /**
     * Idempotent lock-protected weekly rollover on request after week boundary (BUG-011)
     * Automatically executed upon requests without requiring cron jobs.
     * Enforces atomic database locking so concurrent requests can never double-promote.
     */
    public static function checkWeeklyRollover(?string $forceWeek = null): array {
        $db = Database::getConnection();
        $currentWeek = $forceWeek ?: date('o-\WW');

        // Check if current week rollover was already executed
        $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'last_weekly_rollover'");
        $stmt->execute();
        $lastCompleted = $stmt->fetchColumn();
        if ($lastCompleted === $currentWeek) {
            return ['status' => 'skipped', 'message' => "Week {$currentWeek} already completed."];
        }

        // Atomic lock attempt: only one request acquires the lock per week
        $lockKey = 'lock_weekly_' . $currentWeek;
        try {
            $lockStmt = $db->prepare("INSERT INTO system_settings (setting_key, setting_value, updated_at) VALUES (?, 'locked', NOW())");
            $lockStmt->execute([$lockKey]);
        } catch (\PDOException $e) {
            // Duplicate primary key means another process acquired the lock
            return ['status' => 'skipped', 'message' => "Week {$currentWeek} locked by another process."];
        }

        // Execute rollover promotions
        $tierHierarchy = ['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond'];
        $promotionsCount = 0;

        foreach ($tierHierarchy as $idx => $tier) {
            if ($tier === 'Diamond') continue;
            $nextTier = $tierHierarchy[$idx + 1];

            // Select top 3 or top players in this tier with period_xp > 0
            $pStmt = $db->prepare("
                SELECT ls.user_id, ls.period_xp
                FROM leaderboard_scores ls
                JOIN users u ON ls.user_id = u.id
                WHERE ls.period = 'weekly' AND ls.tier = ? AND ls.period_xp > 0 AND u.role != 'admin'
                ORDER BY ls.period_xp DESC
                LIMIT 3
            ");
            $pStmt->execute([$tier]);
            $toPromote = $pStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($toPromote as $p) {
                $uid = (int)$p['user_id'];
                $db->prepare("UPDATE users SET tier = ? WHERE id = ? AND tier = ?")->execute([$nextTier, $uid, $tier]);
                $db->prepare("UPDATE leaderboard_scores SET tier = ? WHERE user_id = ? AND period = 'weekly'")->execute([$nextTier, $uid]);
                WalletService::addCoins($uid, 50, 'admin_grant', "Weekly League Promotion to {$nextTier}!");
                $promotionsCount++;
            }
        }

        // Reset weekly period_xp to 0
        $db->exec("UPDATE leaderboard_scores SET period_xp = 0, rank_position = 0 WHERE period = 'weekly'");

        // Record completion for current week
        $db->prepare("
            INSERT INTO system_settings (setting_key, setting_value, updated_at) 
            VALUES ('last_weekly_rollover', ?, NOW())
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()
        ")->execute([$currentWeek]);

        return [
            'status' => 'success',
            'week' => $currentWeek,
            'promotions' => $promotionsCount
        ];
    }
}