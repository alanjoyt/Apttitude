<?php
require_once __DIR__ . '/verify_and_ingest.php';

$db = Database::getConnection();

echo "=================================================================\n";
echo "   PRE-CONDITION VERIFICATION: INNOVATURE, TCS NQT, INFOSYS      \n";
echo "=================================================================\n\n";

$targetCompanies = ['Innovature', 'TCS NQT', 'Infosys'];

foreach ($targetCompanies as $company) {
    echo ">>> Running Verification Gate for {$company}...\n";
    $ids = $db->query("SELECT id FROM questions WHERE company_name = '{$company}'")->fetchAll(PDO::FETCH_COLUMN);
    
    $res = VerificationEngine::processBatchVerification($ids);
    echo "  Total Questions  : {$res['total']}\n";
    echo "  Passed & Published: {$res['passed']}\n";
    echo "  Failed           : {$res['failed']}\n";
    echo "  Needs Review     : {$res['needs_review']}\n";

    if (!empty($res['failures'])) {
        echo "  Failures / Issues Detected:\n";
        foreach (array_slice($res['failures'], 0, 5) as $f) {
            echo "    [#{$f['id']}] {$f['reason']}\n";
        }
    }

    $csvFile = VerificationEngine::exportReviewSample($company);
    echo "  Exported Sample  : " . basename($csvFile) . " (30 questions)\n\n";
}

echo "Pre-condition pilot verification complete!\n";
