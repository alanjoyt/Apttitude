<?php
/**
 * Ingests final boost questions per company
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../verify_and_ingest.php';

$jsonFile = __DIR__ . '/data/final_boost.json';
if (!file_exists($jsonFile)) {
    die("File not found: $jsonFile\n");
}

$questions = json_decode(file_get_contents($jsonFile), true);
if (!$questions) {
    die("Invalid JSON in $jsonFile\n");
}

// Group by company
$byCompany = [];
foreach ($questions as $q) {
    $c = $q['company_name'];
    $byCompany[$c][] = $q;
}

foreach ($byCompany as $comp => $qs) {
    $batchCode = strtoupper(str_replace([' ', '-'], '_', $comp));
    $batchId = "BATCH_{$batchCode}_FINAL_BOOST";
    echo ">>> Ingesting {$comp} (" . count($qs) . " Qs) -> {$batchId}...\n";
    $res = VerificationEngine::ingestBatch($qs, $batchId);
    echo "    Total: {$res['total']} | Imported: {$res['imported']} | Published: {$res['published']} | Draft: {$res['draft']} | Duplicates: {$res['duplicates_skipped']}\n";
    
    // Refresh review sample
    $sample = VerificationEngine::exportReviewSample($comp);
    echo "    Updated review sample: " . basename($sample) . "\n\n";
}

echo "Final boost ingestion complete!\n";
