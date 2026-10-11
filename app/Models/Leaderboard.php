<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/User.php';

class Leaderboard {

    /**
     * Fetch leaderboard rankings for a period and optional tier filter
     */
    public static function getRankings(string $period = 'weekly', ?string $tier = null, int $limit = 20): array {
        $db = Database::getConnection();

        $sql = "
            SELECT u.id, u.name, u.tier, u.total_xp, ls.period_xp,
                   RANK() OVER (ORDER BY ls.period_xp DESC) as period_rank
            FROM leaderboard_scores ls
            JOIN users u ON ls.user_id = u.id
            WHERE ls.period = ?
        ";
        $params = [$period];

        if ($tier) {
            $sql .= " AND ls.tier = ?";
            $params[] = $tier;
        }

        $sql .= " ORDER BY ls.period_xp DESC LIMIT " . (int)$limit;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();

        // Fallback: If leaderboard_scores is empty or not populated yet, query users directly by total_xp
        if (empty($results)) {
            $fallbackSql = "
                SELECT u.id, u.name, u.tier, u.total_xp, u.total_xp as period_xp,
                       RANK() OVER (ORDER BY u.total_xp DESC) as period_rank
                FROM users u
                WHERE 1=1
            ";
            $fbParams = [];
            if ($tier) {
                $fallbackSql .= " AND u.tier = ?";
                $fbParams[] = $tier;
            }
            $fallbackSql .= " ORDER BY u.total_xp DESC LIMIT " . (int)$limit;
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
            SELECT user_id, period_xp, tier,
                   (SELECT COUNT(*) + 1 FROM leaderboard_scores WHERE period = ? AND period_xp > ls.period_xp) as current_rank
            FROM leaderboard_scores ls
            WHERE user_id = ? AND period = ?
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

        // Get player just above
        $aboveStmt = $db->prepare("
            SELECT u.name, ls.period_xp, ls.tier 
            FROM leaderboard_scores ls
            JOIN users u ON ls.user_id = u.id
            WHERE ls.period = ? AND ls.period_xp > ? 
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
}
