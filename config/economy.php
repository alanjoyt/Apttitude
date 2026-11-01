<?php
/**
 * Apttitudee Economy & Progression Configuration
 * Centralized registry for all XP rewards, Coin earnings, Tier thresholds, Store prices, and Anti-Farming rules.
 */

return [
    // --------------------------------------------------------------------------
    // 1. SIGNUP & ONBOARDING REWARDS
    // --------------------------------------------------------------------------
    'signup_bonus_coins' => 25,          // One-time welcome grant (was 100)
    'diagnostic_completion_xp' => 100,    // One-time onboarding diagnostic test XP (was 250)
    'diagnostic_completion_coins' => 15,  // One-time onboarding diagnostic test coins (was 50)

    // --------------------------------------------------------------------------
    // 2. REFERRAL PROGRAM
    // --------------------------------------------------------------------------
    'referral_bonus_coins' => 15,        // Coins per successful referral (was 25)
    'referral_min_questions_required' => 20, // Referred user must answer 20 questions before bonus pays out
    'referral_max_per_month' => 10,      // Anti-farming monthly cap per user

    // --------------------------------------------------------------------------
    // 3. PRACTICE QUESTION REWARDS (Base, Speed, and Calibration)
    // --------------------------------------------------------------------------
    'xp' => [
        'easy' => [
            'base' => 5,
            'speed' => 2,
            'sure' => 2,
            'max' => 9,
        ],
        'medium' => [
            'base' => 12,
            'speed' => 4,
            'sure' => 3,
            'max' => 19,
        ],
        'hard' => [
            'base' => 25,
            'speed' => 8,
            'sure' => 5,
            'max' => 38,
        ],
        'overconfidence_penalty' => -5,   // Penalty for tagged 'sure' and answered incorrectly (was -3)
        'retry_wrong_xp_factor' => 0.50, // Retrying previously wrong question earns 50% XP
    ],

    'coins' => [
        'easy' => 0,                     // Easy questions award 0 coins (XP only)
        'medium' => 1,                   // 1 coin if solved correctly in <= 75% estimated time
        'hard' => 2,                     // 2 coins if solved correctly in <= 75% estimated time
    ],

    // --------------------------------------------------------------------------
    // 4. SPEED FLOORS & ANTI-EXPLOIT THRESHOLDS
    // --------------------------------------------------------------------------
    // Minimum solve time required to earn rewards: max(min_seconds, est_time * min_percentage)
    'speed_floor_min_seconds' => 2,
    'speed_floor_est_percent' => 0.15,

    // --------------------------------------------------------------------------
    // 5. DAILY CAPS & REPEAT DECAY
    // --------------------------------------------------------------------------
    'max_daily_practice_coins' => 30,    // Hard cap on coins from practice + streaks per day
    'daily_goal' => [
        'min_questions' => 10,
        'min_accuracy_percent' => 60,
        'coin_reward' => 3,              // Counted separately from 30-coin practice cap
        // Absolute total possible coins per day = 30 (cap) + 3 (goal) = 33 coins
    ],

    // Decay rules for questions already answered CORRECTLY by user:
    'repeat_decay' => [
        'same_day' => ['xp_factor' => 0.0, 'coins' => 0],   // 0 reward for repeating same question on same day
        'next_day' => ['xp_factor' => 0.25, 'coins' => 0],  // 25% XP, 0 coins if previously correct
        'older'    => ['xp_factor' => 0.0, 'coins' => 0],   // 0 XP, 0 coins after 2+ correct solves
    ],

    // --------------------------------------------------------------------------
    // 6. BOSS QUESTION EVENTS (Server-Validated)
    // --------------------------------------------------------------------------
    'boss' => [
        'xp_multiplier' => 1.5,          // 1.5x XP for hard boss question (was 2x)
        'bonus_coins' => 2,              // +2 bonus coins on top of base
        'max_daily_boss_questions' => 2, // Max 2 boss rewards credited per day
    ],

    // --------------------------------------------------------------------------
    // 7. DAILY STREAK PROGRESSION
    // --------------------------------------------------------------------------
    'streaks' => [
        'tier_1' => ['days' => [1, 3],   'xp' => 5,  'coins' => 0],
        'tier_2' => ['days' => [4, 7],   'xp' => 10, 'coins' => 2],
        'tier_3' => ['days' => [8, 14],  'xp' => 15, 'coins' => 3],
        'tier_4' => ['days' => [15, 9999], 'xp' => 20, 'coins' => 5],
    ],

    // --------------------------------------------------------------------------
    // 8. 1v1 DUELS (PvP & Practice vs Bot)
    // --------------------------------------------------------------------------
    'duels' => [
        'min_questions_to_stake' => 30,  // User must answer 30 practice questions before staking coins
        'pvp_wagers' => [10, 25, 50],
        'pvp_rake_percent' => 5,         // 5% platform coin sink on pot
        'max_daily_duels_same_pair' => 3,// Anti-collusion win trading cap
        'bot' => [
            'coin_stake' => 0,           // Bot duels cost 0 coins
            'coin_winnings' => 0,        // Bot duels award 0 coins
            'xp_win' => 10,              // +10 XP on victory
            'max_daily_wins' => 3,       // Max 3 bot XP rewards per day (max +30 XP)
        ],
    ],

    // --------------------------------------------------------------------------
    // 9. DAILY QUESTS (Restructured for balanced economy)
    // --------------------------------------------------------------------------
    'quests' => [
        'solve_count' => ['xp' => 30, 'coins' => 5],    // Daily Warmup (was 50 XP, 10 Coins)
        'hard_questions' => ['xp' => 60, 'coins' => 10], // Hard Hitter (was 100 XP, 25 Coins)
        'sure_correct' => ['xp' => 75, 'coins' => 10],  // Master of Certainty (was 120 XP, 30 Coins)
    ],

    // --------------------------------------------------------------------------
    // 10. MOCK TESTS & TIME ATTACK
    // --------------------------------------------------------------------------
    'mock_test' => [
        'base_xp' => 50,                 // Base completion XP (was 100)
        'accuracy_xp_multiplier' => 1.5, // + (accuracy% * 1.5) (was 2.0)
        'coins_awarded' => 0,            // Mock tests do not farm coins directly
    ],

    // --------------------------------------------------------------------------
    // 11. LEAGUE TIER THRESHOLDS (Total XP Required)
    // --------------------------------------------------------------------------
    'tiers' => [
        'Bronze'   => 0,
        'Silver'   => 2500,              // ~140 questions at 18 XP avg (was 1,000)
        'Gold'     => 7000,              // ~390 questions (was 3,500)
        'Platinum' => 14000,             // ~780 questions (was 8,000)
        'Diamond'  => 25000,             // ~1,390 questions (was 15,000)
    ],

    // --------------------------------------------------------------------------
    // 12. STORE & CURRENCY SINKS
    // --------------------------------------------------------------------------
    'store' => [
        'hint_cost' => 5,                // Eliminate 1 wrong option lifeline (5 Speed Coins)
        'streak_freeze_cost' => 200,     // Emergency streak protection (was 150, now 200)
        'formula_cheat_sheet_cost' => 120, // Quick formula reference (was 75)
        'booster_24h_cost' => 150,       // 2X Speed Coins booster for 24h (was 100)
        'mock_pack_cost' => 250,         // Company-curated mock pack (was 150)
        'cosmetic_badge_cost' => 400,    // Leaderboard flair & cyberpunk border (was 200)
    ]
];
