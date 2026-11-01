<?php
/**
 * Phase 2 Economy Rollback Script
 * Safely restores original schema and table states if needed.
 */

require_once __DIR__ . '/../config/db.php';

echo "=== Running Phase 2 Economy Rollback ===\n";

try {
    $db = Database::getConnection();

    // 1. Revert daily_quests rewards
    $db->exec("
        UPDATE daily_quests SET xp_reward = 50, coin_reward = 10 WHERE quest_type = 'solve_count';
        UPDATE daily_quests SET xp_reward = 100, coin_reward = 25 WHERE quest_type = 'hard_questions';
        UPDATE daily_quests SET xp_reward = 120, coin_reward = 30 WHERE quest_type = 'sure_correct';
    ");
    echo " [+] Restored 'daily_quests' original reward values.\n";

    // 2. Revert unlockables costs
    $db->exec("UPDATE unlockables SET coin_cost = 150 WHERE item_type = 'company_mock' OR code LIKE '%_MOCK_PACK';");
    $db->exec("UPDATE unlockables SET coin_cost = 75 WHERE code = 'FORMULA_CHEAT_SHEET';");
    $db->exec("UPDATE unlockables SET coin_cost = 100 WHERE code = 'COIN_BOOSTER_24H';");
    $db->exec("UPDATE unlockables SET coin_cost = 200 WHERE code = 'COSMETIC_CYBER_FRAME';");
    $db->exec("UPDATE unlockables SET coin_cost = 150 WHERE code = 'STREAK_FREEZE_REFILL';");
    echo " [+] Restored 'unlockables' original prices.\n";

    // Note: Extra columns (is_bot on duels, is_boss on attempts) are non-destructive and can remain or be dropped.
    echo "\n>>> Phase 2 Economy Rollback Completed Successfully! <<<\n";
    echo "Note: If you need a full snapshot restore, use backups/backup_apttitudee_*.sql.\n";

} catch (Exception $e) {
    echo "\n[ERROR] Rollback failed: " . $e->getMessage() . "\n";
    exit(1);
}
