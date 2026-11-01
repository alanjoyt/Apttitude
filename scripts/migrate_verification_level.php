<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "           MIGRATING VERIFICATION LEVEL SCHEMA                   \n";
echo "=================================================================\n\n";

// 1. Add verification_level column
$colCheck = $db->query("SHOW COLUMNS FROM questions LIKE 'verification_level'")->fetch();
if (!$colCheck) {
    $db->exec("ALTER TABLE questions ADD COLUMN verification_level ENUM('executed', 'computed', 'reviewed', 'unchecked') DEFAULT 'unchecked' AFTER verification_status");
    echo "Added column 'verification_level' to questions.\n";
} else {
    echo "Column 'verification_level' already exists.\n";
}

// 2. Add reviewed_by and reviewed_at
$revCheck = $db->query("SHOW COLUMNS FROM questions LIKE 'reviewed_by'")->fetch();
if (!$revCheck) {
    $db->exec("ALTER TABLE questions ADD COLUMN reviewed_by INT DEFAULT NULL AFTER verification_level");
    $db->exec("ALTER TABLE questions ADD COLUMN reviewed_at DATETIME DEFAULT NULL AFTER reviewed_by");
    echo "Added columns 'reviewed_by' and 'reviewed_at' to questions.\n";
} else {
    echo "Columns 'reviewed_by' and 'reviewed_at' already exist.\n";
}

echo "Migration completed successfully.\n";
