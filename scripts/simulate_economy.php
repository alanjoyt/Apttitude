<?php
/**
 * Apttitudee Economy Progression Simulation
 * Simulates progression trajectories for Casual, Average, and Heavy student profiles.
 */

$economy = require __DIR__ . '/../config/economy.php';

$profiles = [
    'Casual Student' => [
        'questions_per_day' => 5,
        'accuracy' => 0.65,
        'speed_qualify_rate' => 0.40, // 40% of correct questions beaten under 75% est time
        'sure_rate' => 0.30,          // 30% tagged 'sure'
        'sure_accuracy' => 0.75,      // 75% of 'sure' tags are correct
        'distribution' => ['easy' => 0.50, 'medium' => 0.40, 'hard' => 0.10],
        'completes_daily_goal' => false, // 5 questions < 10 questions required for daily goal
        'avg_quest_coins' => 2,       // Completes ~1 quest every few days
        'streak_tier' => 'tier_2',    // Maintains ~5-day streaks
    ],
    'Average Student' => [
        'questions_per_day' => 15,
        'accuracy' => 0.75,
        'speed_qualify_rate' => 0.55,
        'sure_rate' => 0.50,
        'sure_accuracy' => 0.85,
        'distribution' => ['easy' => 0.30, 'medium' => 0.50, 'hard' => 0.20],
        'completes_daily_goal' => true, // 15 questions >= 10, accuracy 75% >= 60% -> +3 coins
        'avg_quest_coins' => 10,      // Completes Warmup + 1 other quest
        'streak_tier' => 'tier_3',    // Maintains ~10-day streaks
    ],
    'Heavy Student' => [
        'questions_per_day' => 40,
        'accuracy' => 0.85,
        'speed_qualify_rate' => 0.70,
        'sure_rate' => 0.70,
        'sure_accuracy' => 0.95,
        'distribution' => ['easy' => 0.15, 'medium' => 0.45, 'hard' => 0.40],
        'completes_daily_goal' => true, // 40 questions, accuracy 85% -> +3 coins
        'avg_quest_coins' => 25,      // Completes all 3 daily quests
        'streak_tier' => 'tier_4',    // 15+ day streaks
    ]
];

echo "========================================================================================\n";
echo "                   APTTITUDEE REBALANCED ECONOMY SIMULATION REPORT                      \n";
echo "========================================================================================\n\n";

foreach ($profiles as $name => $p) {
    echo "----------------------------------------------------------------------------------------\n";
    echo "PROFILE: {$name}\n";
    echo "  * Practice: {$p['questions_per_day']} Qs/day | Accuracy: " . ($p['accuracy'] * 100) . "%\n";
    echo "  * Difficulty Mix: " . ($p['distribution']['easy']*100) . "% Easy, " . 
         ($p['distribution']['medium']*100) . "% Med, " . ($p['distribution']['hard']*100) . "% Hard\n\n";

    // Calculate daily XP and coins
    $dailyXP = 0;
    $rawDailyPracticeCoins = 0;

    foreach ($p['distribution'] as $diff => $ratio) {
        $count = $p['questions_per_day'] * $ratio;
        $correct = $count * $p['accuracy'];
        $wrong = $count * (1 - $p['accuracy']);

        // XP Calculation
        $baseXP = $economy['xp'][$diff]['base'];
        $speedXP = $economy['xp'][$diff]['speed'];
        $sureXP = $economy['xp'][$diff]['sure'];

        // Correct XP
        $xpPerCorrect = $baseXP + ($speedXP * $p['speed_qualify_rate']) + ($sureXP * $p['sure_rate'] * $p['sure_accuracy']);
        $dailyXP += $correct * $xpPerCorrect;

        // Overconfidence penalty on wrong answers
        $overconfidentWrong = $wrong * $p['sure_rate'] * (1 - $p['sure_accuracy']);
        $dailyXP += $overconfidentWrong * $economy['xp']['overconfidence_penalty'];

        // Coin calculation
        $coinUnit = $economy['coins'][$diff];
        $rawDailyPracticeCoins += $correct * $p['speed_qualify_rate'] * $coinUnit;
    }

    // Streak bonus
    $streakData = $economy['streaks'][$p['streak_tier']];
    $streakXP = $streakData['xp'];
    $streakCoins = $streakData['coins'];

    // Practice coins + streak coins capped at max_daily_practice_coins (30)
    $cappedPracticeCoins = min($economy['max_daily_practice_coins'], round($rawDailyPracticeCoins + $streakCoins));

    // Daily Goal bonus (separate from 30 practice cap)
    $goalCoins = $p['completes_daily_goal'] ? $economy['daily_goal']['coin_reward'] : 0;

    $totalDailyCoins = $cappedPracticeCoins + $goalCoins;
    $totalDailyXP = round($dailyXP + $streakXP);

    echo "DAILY EARNINGS BREAKDOWN:\n";
    echo "  - Raw Practice Coins:     " . round($rawDailyPracticeCoins, 1) . " coins\n";
    echo "  - Streak Bonus:           +" . $streakCoins . " coins, +" . $streakXP . " XP\n";
    echo "  - Capped Practice Pool:   " . $cappedPracticeCoins . " coins / day (max " . $economy['max_daily_practice_coins'] . ")\n";
    echo "  - Daily Goal Reward:      +" . $goalCoins . " coins / day (10 Qs at >= 60% acc)\n";
    echo "  =======================================================\n";
    echo "  TOTAL EARNINGS:           " . $totalDailyCoins . " Coins / day  |  " . $totalDailyXP . " XP / day\n\n";

    echo "TIER PROGRESSION TIMELINE (Cumulative Days):\n";
    foreach ($economy['tiers'] as $tier => $minXP) {
        if ($minXP === 0) continue;
        $days = ceil($minXP / max(1, $totalDailyXP));
        echo "  - " . str_pad($tier, 10) . " (" . number_format($minXP) . " XP): " . str_pad($days . " days", 10) . 
             " (~" . number_format($days * $p['questions_per_day']) . " questions)\n";
    }
    echo "\n";

    echo "STORE AFFORDABILITY TIMELINE (Days of Saving at {$totalDailyCoins} coins/day):\n";
    foreach ($economy['store'] as $itemKey => $cost) {
        $days = ceil($cost / max(1, $totalDailyCoins));
        $itemLabel = ucwords(str_replace(['_', 'cost'], [' ', ''], $itemKey));
        echo "  - " . str_pad($itemLabel, 26) . " (" . str_pad($cost . " coins", 10) . "): " . $days . " days\n";
    }
    echo "\n";
}
