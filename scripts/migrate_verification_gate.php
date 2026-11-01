<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=== Running Migration: Verification Gate & Draft/Published Split ===\n";

$cols = $db->query("SHOW COLUMNS FROM questions")->fetchAll(PDO::FETCH_COLUMN);

if (!in_array('is_published', $cols)) {
    $db->exec("ALTER TABLE questions ADD COLUMN is_published TINYINT(1) NOT NULL DEFAULT 0 AFTER verification_status");
    echo "Added column 'is_published'.\n";
}

if (!in_array('verified_by_script', $cols)) {
    $db->exec("ALTER TABLE questions ADD COLUMN verified_by_script TINYINT(1) NOT NULL DEFAULT 0 AFTER is_published");
    echo "Added column 'verified_by_script'.\n";
}

if (!in_array('import_batch_id', $cols)) {
    $db->exec("ALTER TABLE questions ADD COLUMN import_batch_id VARCHAR(50) NULL AFTER round_pattern");
    echo "Added column 'import_batch_id'.\n";
}

if (!in_array('verification_notes', $cols)) {
    $db->exec("ALTER TABLE questions ADD COLUMN verification_notes TEXT NULL AFTER import_batch_id");
    echo "Added column 'verification_notes'.\n";
}

// Add indexes
try {
    $db->exec("CREATE INDEX idx_q_published_company ON questions(is_published, company_name)");
    echo "Created index idx_q_published_company.\n";
} catch (Exception $e) {
    // Index may already exist
}

try {
    $db->exec("CREATE INDEX idx_q_batch_id ON questions(import_batch_id)");
    echo "Created index idx_q_batch_id.\n";
} catch (Exception $e) {
    // Index may already exist
}

echo "Migration Complete!\n";
