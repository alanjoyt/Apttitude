<?php
require_once __DIR__ . '/../config/db.php';

if ($argc < 2) {
    echo "Usage: php rollback_batch.php <batch_id>\n";
    exit(1);
}

$batchId = trim($argv[1]);
$db = Database::getConnection();

$stmt = $db->prepare("SELECT COUNT(*) FROM questions WHERE import_batch_id = ?");
$stmt->execute([$batchId]);
$count = (int)$stmt->fetchColumn();

if ($count === 0) {
    echo "No questions found matching batch ID: {$batchId}\n";
    exit(0);
}

echo "Found {$count} question(s) with batch ID '{$batchId}'. Rolling back...\n";
$del = $db->prepare("DELETE FROM questions WHERE import_batch_id = ?");
$del->execute([$batchId]);

echo "Successfully rolled back and deleted {$count} question(s) from batch '{$batchId}'.\n";
