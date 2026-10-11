<?php
require_once __DIR__ . '/config/db.php';

try {
    $db = Database::getConnection();

    $db->exec("
        CREATE TABLE IF NOT EXISTS `system_settings` (
          `setting_key` VARCHAR(50) PRIMARY KEY,
          `setting_value` VARCHAR(255) NOT NULL,
          `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");

    $db->exec("
        INSERT INTO `system_settings` (`setting_key`, `setting_value`)
        VALUES ('developer_mode', '0')
        ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;
    ");

    echo "SUCCESS: system_settings table initialized for Developer Mode!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
