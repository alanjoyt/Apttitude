<?php
require_once __DIR__ . '/../../config/db.php';

class GamificationEngine {

    // Tier definitions & minimum thresholds
    public static array $TIERS = [
        'Bronze'   => ['min_xp' => 0,     'badge' => 'icon-shield', 'color' => '#cd7f32', 'title' => 'Bronze Practicer'],
        'Silver'   => ['min_xp' => 1000,  'badge' => 'icon-swords', 'color' => '#c0c0c0', 'title' => 'Silver Scholar'],
        'Gold'     => ['min_xp' => 3500,  'badge' => 'icon-crown', 'color' => '#ffd700', 'title' => 'Gold Master'],
        'Platinum' => ['min_xp' => 8000,  'badge' => 'icon-medal', 'color' => '#e5e4e2', 'title' => 'Platinum Champion'],
        'Diamond'  => ['min_xp' => 15000, 'badge' => 'icon-flame', 'color' => '#00ffff', 'title' => 'Diamond Legend']
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
     */
    public static function calculateXP(string $difficulty, bool $isCorrect, int $timeTakenSeconds, int $estTimeSeconds, string $confidence = 'sure'): int {
        if (!$isCorrect) {
            // Overconfidence penalty: -3 XP if tagged 'sure' but answered incorrectly
            return ($confidence === 'sure') ? -3 : 0;
        }

        // Base XP per difficulty
        $baseXP = match ($difficulty) {
            'easy' => 10,
            'medium' => 20,
            'hard' => 35,
            default => 15
        };

        // Speed bonus: if solved faster than estimated time
        $speedBonus = 0;
        if ($timeTakenSeconds > 0 && $timeTakenSeconds <= ($estTimeSeconds * 0.75)) {
            $speedBonus = match ($difficulty) {
                'easy' => 5,
                'medium' => 5,
                'hard' => 10,
                default => 5
            };
        }

        // Confidence calibration bonus: +5 XP if tagged 'sure' AND correct
        $confidenceBonus = ($confidence === 'sure') ? 5 : 0;

        return $baseXP + $speedBonus + $confidenceBonus;
    }

    /**
     * Calculate Speed Coins earned for fast + correct answers
     */
    public static function calculateCoins(string $difficulty, bool $isCorrect, int $timeTakenSeconds, int $estTimeSeconds): int {
        if (!$isCorrect) return 0;

        // Speed Coins awarded when beating speed threshold
        if ($timeTakenSeconds > 0 && $timeTakenSeconds <= ($estTimeSeconds * 0.75)) {
            return match ($difficulty) {
                'easy' => 2,
                'medium' => 4,
                'hard' => 6,
                default => 2
            };
        }

        return 0;
    }

    /**
     * Determine user tier based on total XP
     */
    public static function getTierForXP(int $totalXP): string {
        $assignedTier = 'Bronze';
        foreach (self::$TIERS as $tierName => $data) {
            if ($totalXP >= $data['min_xp']) {
                $assignedTier = $tierName;
            }
        }
        return $assignedTier;
    }

    /**
     * Get details for next tier progression (progress %, XP needed)
     */
    public static function getTierProgress(int $totalXP): array {
        $currentTier = self::getTierForXP($totalXP);
        $tierKeys = array_keys(self::$TIERS);
        $currentIndex = array_search($currentTier, $tierKeys);

        if ($currentIndex === count($tierKeys) - 1) {
            // Max tier reached
            return [
                'current_tier' => $currentTier,
                'next_tier' => 'Max Level',
                'progress_percent' => 100,
                'xp_needed' => 0,
                'current_xp' => $totalXP
            ];
        }

        $nextTier = $tierKeys[$currentIndex + 1];
        $currentMin = self::$TIERS[$currentTier]['min_xp'];
        $nextMin = self::$TIERS[$nextTier]['min_xp'];

        $xpInLevel = $totalXP - $currentMin;
        $levelRange = $nextMin - $currentMin;
        $progressPercent = min(100, max(0, round(($xpInLevel / $levelRange) * 100)));

        return [
            'current_tier' => $currentTier,
            'next_tier' => $nextTier,
            'progress_percent' => $progressPercent,
            'xp_needed' => $nextMin - $totalXP,
            'current_xp' => $totalXP
        ];
    }

    /**
     * Update user streak based on activity date
     */
    public static function updateStreak(int $userId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT current_streak, last_active_date, streak_freezes FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) return ['streak' => 0, 'updated' => false];

        $today = date('Y-m-d');
        $lastActive = $user['last_active_date'];
        $streak = (int)$user['current_streak'];
        $freezes = (int)$user['streak_freezes'];

        if ($lastActive === $today) {
            // Already active today
            return ['streak' => $streak, 'updated' => false];
        }

        $yesterday = date('Y-m-d', strtotime('-1 day'));
        if ($lastActive === $yesterday) {
            // Increment streak
            $streak += 1;
        } else if ($lastActive !== null && $lastActive < $yesterday) {
            // Missed a day! Check if freeze is available
            if ($freezes > 0) {
                $freezes -= 1; // Consume streak freeze
            } else {
                $streak = 1; // Reset streak
            }
        } else {
            // First time active
            $streak = 1;
        }

        $updateStmt = $db->prepare("UPDATE users SET current_streak = ?, last_active_date = ?, streak_freezes = ? WHERE id = ?");
        $updateStmt->execute([$streak, $today, $freezes, $userId]);
        User::clearCache($userId);

        return ['streak' => $streak, 'updated' => true];
    }
}
