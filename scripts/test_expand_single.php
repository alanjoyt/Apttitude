<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/verify_and_ingest.php';
require_once __DIR__ . '/generators/gen_tcs.php';

echo "=== Testing Question Expansion for TCS NQT ===\n";

// 1. Run backup
echo "Running database backup...\n";
exec('php ' . escapeshellarg(__DIR__ . '/backup_db.php'), $out, $ret);
echo "Backup finished with code: {$ret}\n";

$data = getTcsNqtQuestions();
echo "Generated Batch 1: " . count($data['batch1']) . " questions.\n";
echo "Generated Batch 2: " . count($data['batch2']) . " questions.\n";

$res1 = VerificationEngine::ingestBatch($data['batch1'], 'BATCH_TCS_NQT_1');
echo "Batch 1 Ingest: Imported: {$res1['imported']}, Published: {$res1['published']}, Draft: {$res1['draft']}, Duplicates: {$res1['duplicates_skipped']}\n";
if (!empty($res1['errors'])) {
    print_r($res1['errors']);
}

$res2 = VerificationEngine::ingestBatch($data['batch2'], 'BATCH_TCS_NQT_2');
echo "Batch 2 Ingest: Imported: {$res2['imported']}, Published: {$res2['published']}, Draft: {$res2['draft']}, Duplicates: {$res2['duplicates_skipped']}\n";
if (!empty($res2['errors'])) {
    print_r($res2['errors']);
}

$csv = VerificationEngine::exportReviewSample('TCS NQT');
echo "Exported Review Sample: {$csv}\n";

$total = Database::getConnection()->query("SELECT count(*) FROM questions WHERE company_name = 'TCS NQT'")->fetchColumn();
$pub = Database::getConnection()->query("SELECT count(*) FROM questions WHERE company_name = 'TCS NQT' AND is_published = 1")->fetchColumn();
echo "Total TCS NQT Questions in DB: {$total} (Published: {$pub})\n";
