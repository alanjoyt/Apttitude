<?php
require_once __DIR__ . '/config/db.php';

try {
    $db = Database::getConnection();

    // Check existing columns in users table
    $columnsStmt = $db->query("DESCRIBE users");
    $columns = array_column($columnsStmt->fetchAll(), 'Field');

    if (!in_array('bio', $columns)) {
        $db->exec("ALTER TABLE `users` ADD COLUMN `bio` VARCHAR(255) DEFAULT 'Apttitudee Scholar'");
    }
    if (!in_array('avatar_icon', $columns)) {
        $db->exec("ALTER TABLE `users` ADD COLUMN `avatar_icon` VARCHAR(50) DEFAULT 'bi-person-circle'");
    }
    if (!in_array('referral_code', $columns)) {
        $db->exec("ALTER TABLE `users` ADD COLUMN `referral_code` VARCHAR(20) DEFAULT NULL");
    }
    if (!in_array('referred_by', $columns)) {
        $db->exec("ALTER TABLE `users` ADD COLUMN `referred_by` INT DEFAULT NULL");
    }
    if (!in_array('device_hash', $columns)) {
        $db->exec("ALTER TABLE `users` ADD COLUMN `device_hash` VARCHAR(255) DEFAULT NULL");
    }

    // Generate referral codes for users missing referral_code
    $usersWithoutRef = $db->query("SELECT id, email FROM users WHERE referral_code IS NULL OR referral_code = ''")->fetchAll();
    foreach ($usersWithoutRef as $u) {
        $code = 'APTT-' . strtoupper(substr(md5($u['id'] . $u['email'] . microtime()), 0, 6));
        $db->prepare("UPDATE users SET referral_code = ? WHERE id = ?")->execute([$code, $u['id']]);
    }

    echo "SUCCESS: Profile, Referral & Device Lock Schema Migration Completed!\n";
} catch (Exception $e) {
    echo "MIGRATION NOTE: " . $e->getMessage() . "\n";
}
