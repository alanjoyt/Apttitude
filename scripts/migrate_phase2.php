<?php
/**
 * Phase 2 Economy Migration Script
 * Safe, idempotent migration script for Apttitudee Phase 2 Economy Rebalance.
 */

require_once __DIR__ . '/../config/db.php';

echo "=== Running Phase 2 Economy Migration ===\n";

try {
    $db = Database::getConnection();

    // 1. Add is_bot to duels table if not present
    $duelCols = $db->query("SHOW COLUMNS FROM duels LIKE 'is_bot'")->fetchAll();
    if (empty($duelCols)) {
        $db->exec("ALTER TABLE duels ADD COLUMN is_bot TINYINT(1) DEFAULT 0 AFTER opponent_id");
        echo " [+] Added 'is_bot' column to 'duels' table.\n";
    } else {
        echo " [=] 'is_bot' column already exists in 'duels'.\n";
    }

    // 2. Add is_boss to attempts table if not present
    $attemptCols = $db->query("SHOW COLUMNS FROM attempts LIKE 'is_boss'")->fetchAll();
    if (empty($attemptCols)) {
        $db->exec("ALTER TABLE attempts ADD COLUMN is_boss TINYINT(1) DEFAULT 0 AFTER mode");
        echo " [+] Added 'is_boss' column to 'attempts' table.\n";
    } else {
        echo " [=] 'is_boss' column already exists in 'attempts'.\n";
    }

    // 3. Update daily_quests table to match config/economy.php
    $db->exec("
        UPDATE daily_quests SET xp_reward = 30, coin_reward = 5 WHERE quest_type = 'solve_count';
        UPDATE daily_quests SET xp_reward = 60, coin_reward = 10 WHERE quest_type = 'hard_questions';
        UPDATE daily_quests SET xp_reward = 75, coin_reward = 10 WHERE quest_type = 'sure_correct';
    ");
    echo " [+] Updated 'daily_quests' reward values (solve_count: 30XP/5c, hard: 60XP/10c, sure: 75XP/10c).\n";

    // Update Streak Freeze price to 200 (was 150), matched by item code only
    $db->exec("
        UPDATE unlockables 
        SET title = 'Streak Freeze Emergency Shield',
            description = 'Preserves your practice streak if you miss a single day of training.',
            item_type = 'streak_freeze',
            coin_cost = 200,
            icon = 'bi-shield-shaded'
        WHERE code = 'STREAK_FREEZE_REFILL';
    ");

    // Update company mock pack costs to 250
    $db->exec("UPDATE unlockables SET coin_cost = 250 WHERE item_type = 'company_mock' OR code LIKE '%_MOCK_PACK';");

    // Update formula cheat sheet to 120
    $db->exec("UPDATE unlockables SET coin_cost = 120 WHERE code = 'FORMULA_CHEAT_SHEET';");

    // Update 24h booster to 150
    $db->exec("UPDATE unlockables SET coin_cost = 150 WHERE code = 'COIN_BOOSTER_24H';");

    // Update cosmetic badge to 400
    $db->exec("UPDATE unlockables SET coin_cost = 400 WHERE code = 'COSMETIC_CYBER_FRAME';");

    // Check if Accenture mock pack exists, if not add it
    $accCheck = $db->query("SELECT COUNT(*) FROM unlockables WHERE code = 'ACCENTURE_MOCK_PACK'")->fetchColumn();
    if ((int)$accCheck === 0) {
        $db->exec("
            INSERT INTO unlockables (code, title, description, item_type, coin_cost, icon)
            VALUES ('ACCENTURE_MOCK_PACK', 'Accenture & Cognizant Mock Pack', 'Simulated 30-question cognitive and technical placement test for Accenture & Cognizant.', 'company_mock', 250, 'bi-lightning-charge-fill')
        ");
        echo " [+] Added Accenture & Cognizant Mock Pack (250 coins).\n";
    }

    echo " [+] Updated 'unlockables' prices according to config/economy.php.\n";

    echo "\n>>> Phase 2 Economy Migration Completed Successfully! <<<\n";

} catch (Exception $e) {
    echo "\n[ERROR] Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
