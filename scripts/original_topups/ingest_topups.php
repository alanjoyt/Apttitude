<?php
/**
 * Ingestion script for original topups across all 13 companies
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../verify_and_ingest.php';

$db = Database::getConnection();

echo "=================================================================\n";
echo "           INGESTING ORIGINAL VERIFIED COMPANY TOP-UPS           \n";
echo "=================================================================\n\n";

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    die("Data directory not found: $dataDir\n");
}

$files = glob($dataDir . '/*.json');
sort($files);

$comps = [
    'Innovature', 'TCS NQT', 'Infosys', 'Wipro NLTH', 'Accenture',
    'Cognizant', 'Capgemini', 'Deloitte', 'Tech Mahindra', 'Zoho',
    'LTI Mindtree', 'HCL', 'Amazon', 'UST', 'IBM'
];

foreach ($files as $f) {
    $basename = basename($f, '.json');
    $json = file_get_contents($f);
    $questions = json_decode($json, true);
    if (!$questions) {
        echo "Error reading $f\n";
        continue;
    }

    $companyName = $questions[0]['company_name'] ?? 'General';
    $batchCode = strtoupper(str_replace([' ', '-'], '_', $companyName));
    $batchId = "BATCH_{$batchCode}_TOPUP";

    echo ">>> Ingesting {$companyName} (" . count($questions) . " Qs) -> Batch {$batchId}...\n";

    $res = VerificationEngine::ingestBatch($questions, $batchId);
    echo "    Total: {$res['total']} | Imported: {$res['imported']} | Published: {$res['published']} | Draft: {$res['draft']} | Duplicates Skipped: {$res['duplicates_skipped']}\n";

    // Refresh review sample
    $sample = VerificationEngine::exportReviewSample($companyName);
    echo "    Updated review sample: " . basename($sample) . "\n\n";
}

echo "Top-up ingestion complete!\n";
