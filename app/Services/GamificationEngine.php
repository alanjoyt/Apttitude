<?php
require_once __DIR__ . '/../../config/db.php';

class GamificationEngine {

    private static ?array $config = null;

    public static function getConfig(): array {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/../../config/economy.php';
        }
        return self::$config;
    }

    // Tier definitions & minimum thresholds (Silver 2,500, Gold 7,000, Platinum 14,000, Diamond 25,000)
    public static array $TIERS = [
        'Bronze'   => ['min_xp' => 0,     'badge' => 'icon-shield', 'color' => '#cd7f32', 'title' => 'Bronze Practicer'],
        'Silver'   => ['min_xp' => 2500,  'badge' => 'icon-swords', 'color' => '#c0c0c0', 'title' => 'Silver Scholar'],
        'Gold'     => ['min_xp' => 7000,  'badge' => 'icon-crown',  'color' => '#ffd700', 'title' => 'Gold Master'],
        'Platinum' => ['min_xp' => 14000, 'badge' => 'icon-medal',  'color' => '#e5e4e2', 'title' => 'Platinum Champion'],
        'Diamond'  => ['min_xp' => 25000, 'badge' => 'icon-flame',  'color' => '#00ffff', 'title' => 'Diamond Legend']
    ];

    /**
     * Get evolving mascot avatar details based on Tier
     */
    public static function getMascotForTier(string $tier): array {
        return match ($tier) {
            'Bronze' => ['name' => 'Apprentice Golem', 'icon' => 'icon-shield', 'desc' => 'Sturdy beginner crafting core fundamentals.'],
            'Silver' => ['name' => 'Scholar Griffin', 'icon' => 'icon-swords', 'desc' => 'Swift strategist mastering reasoning speed.'],
            'Gold' => ['name' => 'Sage Dragon', 'icon' => 'icon-crown', 'desc' => 'Master of speed & precision calculations.'],
            'Platinum' => ['name' => 'Arch-Mage Phoenix', 'icon' => 'icon-medal', 'desc' => 'Elite solver dominating placement papers.'],
            'Diamond' => ['name' => 'Celestial Legend', 'icon' => 'icon-flame', 'desc' => 'Unstoppable aptitude Titan of the realm.'],
            default => ['name' => 'Novice Adventurer', 'icon' => 'icon-target', 'desc' => 'Beginning the journey.']
        };
    }

    /**
     * Calculate XP earned for a question attempt based on difficulty, time taken, and confidence
     * Enforces speed floor: max(2s, 15% of est_time)
     */
    public static function calculateXP(string $difficulty, bool $isCorrect, int $timeTakenSeconds, int $estTimeSeconds, string $confidence = 'sure'): int {
        $cfg = self::getConfig();
        $diff = strtolower($difficulty);
        if (!in_array($diff, ['easy', 'medium', 'hard'])) {
            $diff = 'medium';
        }

        if (!$isCorrect) {
            // Overconfidence penalty: -5 XP if tagged 'sure' but answered incorrectly
            $penalty = (int)($cfg['xp']['overconfidence_penalty'] ?? -5);
            return ($confidence === 'sure') ? $penalty : 0;
        }

        // Anti-Exploit Speed Floor: max(2s, 15% of est_time)
        $minSec = (int)($cfg['speed_floor_min_seconds'] ?? 2);
        $minPct = (float)($cfg['speed_floor_est_percent'] ?? 0.15);
        $speedFloor = max($minSec, (int)round($estTimeSeconds * $minPct));
        if ($timeTakenSeconds < $speedFloor) {
            // Instant guess or script bot - zero reward
            return 0;
        }

        $xpConfig = $cfg['xp'][$diff] ?? ['base' => 12, 'speed' => 4, 'sure' => 3];
        $baseXP = (int)$xpConfig['base'];

        // Speed bonus: if solved faster than 75% estimated time
        $speedBonus = 0;
        if ($timeTakenSeconds <= ($estTimeSeconds * 0.75)) {
            $speedBonus = (int)$xpConfig['speed'];
        }

        // Confidence calibration bonus: if tagged 'sure' AND correct
        $confidenceBonus = ($confidence === 'sure') ? (int)$xpConfig['sure'] : 0;

        return $baseXP + $speedBonus + $confidenceBonus;
    }

    /**
     * Calculate Speed Coins earned for fast + correct answers
     * Enforces speed floor and <= 75% estimated time requirement
     */
    public static function calculateCoins(string $difficulty, bool $isCorrect, int $timeTakenSeconds, int $estTimeSeconds): int {
        if (!$isCorrect) return 0;

        $cfg = self::getConfig();
        $diff = strtolower($difficulty);
        if (!in_array($diff, ['easy', 'medium', 'hard'])) {
            $diff = 'medium';
        }

        // Anti-Exploit Speed Floor
        $minSec = (int)($cfg['speed_floor_min_seconds'] ?? 2);
        $minPct = (float)($cfg['speed_floor_est_percent'] ?? 0.15);
        $speedFloor = max($minSec, (int)round($estTimeSeconds * $minPct));
        if ($timeTakenSeconds < $speedFloor) {
            return 0;
        }

        // Speed Coins awarded when beating speed threshold (<= 75% of est_time)
        if ($timeTakenSeconds <= ($estTimeSeconds * 0.75)) {
            return (int)($cfg['coins'][$diff] ?? 0);
        }

        return 0;
    }

    /**
     * Determine user tier based on total XP with grandfather protection
     * Never demotes a user below their highest already reached tier
     */
    public static function getTierForXP(int $totalXP, ?string $currentTier = null): string {
        $assignedTier = 'Bronze';
        foreach (self::$TIERS as $tierName => $data) {
            if ($totalXP >= $data['min_xp']) {
                $assignedTier = $tierName;
            }
        }

        // Grandfathering: preserve existing user's tier if it is higher than the new threshold
        if ($currentTier !== null && isset(self::$TIERS[$currentTier])) {
            $tierOrder = array_keys(self::$TIERS);
            $currentIndex = array_search($currentTier, $tierOrder);
            $assignedIndex = array_search($assignedTier, $tierOrder);
            if ($currentIndex !== false && $assignedIndex !== false && $assignedIndex < $currentIndex) {
                return $currentTier;
            }
        }

        return $assignedTier;
    }

    /**
     * Get details for next tier progression (progress %, XP needed)
     */
    public static function getTierProgress(int $totalXP, ?string $currentTier = null): array {
        $activeTier = self::getTierForXP($totalXP, $currentTier);
        $tierKeys = array_keys(self::$TIERS);
        $currentIndex = array_search($activeTier, $tierKeys);

        if ($currentIndex === count($tierKeys) - 1) {
            // Max tier reached
            return [
                'current_tier' => $activeTier,
                'next_tier' => 'Max Level',
                'progress_percent' => 100,
                'xp_needed' => 0,
                'current_xp' => $totalXP
            ];
        }

        $nextTier = $tierKeys[$currentIndex + 1];
        $currentMin = self::$TIERS[$activeTier]['min_xp'];
        $nextMin = self::$TIERS[$nextTier]['min_xp'];

        $xpInLevel = max(0, $totalXP - $currentMin);
        $levelRange = max(1, $nextMin - $currentMin);
        $progressPercent = min(100, max(0, round(($xpInLevel / $levelRange) * 100)));

        return [
            'current_tier' => $activeTier,
            'next_tier' => $nextTier,
            'progress_percent' => $progressPercent,
            'xp_needed' => max(0, $nextMin - $totalXP),
            'current_xp' => $totalXP
        ];
    }

    /**
     * Get coins earned today from practice and streaks (counts toward 30 daily cap)
     */
    public static function getTodayPracticeCoins(int $userId): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT COALESCE(SUM(amount), 0) FROM coin_transactions
            WHERE user_id = ? AND transaction_type IN ('speed_bonus', 'streak_reward')
              AND amount > 0 AND DATE(created_at) = CURDATE()
        ");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Update user streak based on activity date and award streak rewards
     * Streak coins count against the 30-coin daily practice cap
     */
    public static function updateStreak(int $userId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT current_streak, last_active_date, streak_freezes FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) return ['streak' => 0, 'updated' => false, 'streak_xp' => 0, 'streak_coins' => 0];

        $today = date('Y-m-d');
        $lastActive = $user['last_active_date'];
        $streak = (int)$user['current_streak'];
        $freezes = (int)$user['streak_freezes'];

        if ($lastActive === $today) {
            // Already active today
            return ['streak' => $streak, 'updated' => false, 'streak_xp' => 0, 'streak_coins' => 0];
        }

        $yesterday = date('Y-m-d', strtotime('-1 day'));
        if ($lastActive === $yesterday) {
            $streak += 1;
        } else if ($lastActive !== null && $lastActive < $yesterday) {
            if ($freezes > 0) {
                $freezes -= 1; // Consume streak freeze
            } else {
                $streak = 1; // Reset streak
            }
        } else {
            $streak = 1;
        }

        $updateStmt = $db->prepare("UPDATE users SET current_streak = ?, last_active_date = ?, streak_freezes = ? WHERE id = ?");
        $updateStmt->execute([$streak, $today, $freezes, $userId]);
        User::clearCache($userId);

        // Determine streak reward from config
        $cfg = self::getConfig();
        $streakXP = 5;
        $streakCoins = 0;

        if ($streak >= 15) {
            $streakXP = (int)$cfg['streaks']['tier_4']['xp'];
            $streakCoins = (int)$cfg['streaks']['tier_4']['coins'];
        } elseif ($streak >= 8) {
            $streakXP = (int)$cfg['streaks']['tier_3']['xp'];
            $streakCoins = (int)$cfg['streaks']['tier_3']['coins'];
        } elseif ($streak >= 4) {
            $streakXP = (int)$cfg['streaks']['tier_2']['xp'];
            $streakCoins = (int)$cfg['streaks']['tier_2']['coins'];
        } else {
            $streakXP = (int)$cfg['streaks']['tier_1']['xp'];
            $streakCoins = (int)$cfg['streaks']['tier_1']['coins'];
        }

        // Award streak XP
        if ($streakXP > 0) {
            User::addXP($userId, $streakXP);
        }

        // Award streak coins respecting 30 daily practice coin cap
        $coinsAwarded = 0;
        if ($streakCoins > 0) {
            $todayCoins = self::getTodayPracticeCoins($userId);
            $dailyCap = (int)($cfg['max_daily_practice_coins'] ?? 30);
            $available = max(0, $dailyCap - $todayCoins);
            $coinsAwarded = min($streakCoins, $available);

            if ($coinsAwarded > 0) {
                require_once __DIR__ . '/WalletService.php';
                WalletService::addCoins($userId, $coinsAwarded, 'streak_reward', "Day {$streak} Streak Bonus");
            }
        }

        return [
            'streak' => $streak,
            'updated' => true,
            'streak_xp' => $streakXP,
            'streak_coins' => $coinsAwarded
        ];
    }
}
