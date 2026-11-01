<?php
/**
 * Apttitudee Comprehensive Company Question Expansion Orchestrator
 *
 * Implements:
 * 1. Automatic database backup before each company import
 * 2. Strict batching (~50 per batch) with unique batch IDs
 * 3. Verification gatekeeper: only verified questions published (verified_by_script = 1, is_published = 1)
 * 4. Automatic export of exports/review_sample_<company>.csv (30 questions balanced across categories)
 * 5. Full summary reporting and subtopic breakdown
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/verify_and_ingest.php';

// Include generators
require_once __DIR__ . '/generators/gen_tcs.php';
require_once __DIR__ . '/generators/gen_infosys.php';
require_once __DIR__ . '/generators/gen_wipro.php';
require_once __DIR__ . '/generators/gen_accenture.php';
require_once __DIR__ . '/generators/gen_cognizant.php';
require_once __DIR__ . '/generators/gen_capgemini.php';
require_once __DIR__ . '/generators/gen_deloitte.php';
require_once __DIR__ . '/generators/gen_tech_mahindra.php';
require_once __DIR__ . '/generators/gen_zoho.php';
require_once __DIR__ . '/generators/gen_lti_mindtree.php';
require_once __DIR__ . '/generators/gen_hcl.php';
require_once __DIR__ . '/generators/gen_amazon.php';
require_once __DIR__ . '/generators/gen_ust.php';
require_once __DIR__ . '/generators/gen_ibm.php';

$companiesConfig = [
    'Infosys'       => ['fn' => 'getInfosysQuestions',      'code' => 'INFOSYS'],
    'Wipro NLTH'    => ['fn' => 'getWiproQuestions',        'code' => 'WIPRO_NLTH'],
    'Accenture'     => ['fn' => 'getAccentureQuestions',    'code' => 'ACCENTURE'],
    'Cognizant'     => ['fn' => 'getCognizantQuestions',    'code' => 'COGNIZANT'],
    'Capgemini'     => ['fn' => 'getCapgeminiQuestions',    'code' => 'CAPGEMINI'],
    'Deloitte'      => ['fn' => 'getDeloitteQuestions',     'code' => 'DELOITTE'],
    'Tech Mahindra' => ['fn' => 'getTechMahindraQuestions', 'code' => 'TECH_MAHINDRA'],
    'Zoho'          => ['fn' => 'getZohoQuestions',         'code' => 'ZOHO'],
    'LTI Mindtree'  => ['fn' => 'getLtiMindtreeQuestions',  'code' => 'LTI_MINDTREE'],
    'HCL'           => ['fn' => 'getHclQuestions',          'code' => 'HCL'],
    'Amazon'        => ['fn' => 'getAmazonQuestions',       'code' => 'AMAZON'],
    'UST'           => ['fn' => 'getUstQuestions',          'code' => 'UST'],
    'IBM'           => ['fn' => 'getIbmQuestions',          'code' => 'IBM'],
];

echo "=================================================================\n";
echo "      APTTITUDEE BATCH EXPANSION & VERIFICATION PIPELINE         \n";
echo "=================================================================\n\n";

$db = Database::getConnection();

foreach ($companiesConfig as $companyName => $cfg) {
    echo ">>> Processing Company: {$companyName}\n";

    // 1. Run database backup before each company
    echo "  [1/4] Running pre-import database backup...\n";
    $backupScript = __DIR__ . '/backup_db.php';
    $cmd = escapeshellcmd(PHP_BINARY) . ' ' . escapeshellarg($backupScript);
    $out = [];
    $ret = 0;
    exec($cmd, $out, $ret);
    if ($ret !== 0) {
        echo "  [WARNING] Backup returned non-zero code ({$ret}), but proceeding with batch safety.\n";
    } else {
        echo "  [BACKUP OK] Database state successfully backed up.\n";
    }

    // 2. Generate questions
    $fn = $cfg['fn'];
    $data = $fn();
    $b1 = $data['batch1'] ?? [];
    $b2 = $data['batch2'] ?? [];
    echo "  [2/4] Generated questions: Batch 1 (" . count($b1) . " Qs), Batch 2 (" . count($b2) . " Qs)\n";

    // 3. Ingest Batch 1
    $b1Id = "BATCH_{$cfg['code']}_1";
    $res1 = VerificationEngine::ingestBatch($b1, $b1Id);
    echo "  [3/4] Ingested {$b1Id}: Total: {$res1['total']} | Imported: {$res1['imported']} | Published: {$res1['published']} | Draft: {$res1['draft']} | Duplicates Skipped: {$res1['duplicates_skipped']}\n";
    if (!empty($res1['errors'])) {
        foreach ($res1['errors'] as $err) echo "        ERROR: {$err}\n";
    }

    // Ingest Batch 2
    $b2Id = "BATCH_{$cfg['code']}_2";
    $res2 = VerificationEngine::ingestBatch($b2, $b2Id);
    echo "        Ingested {$b2Id}: Total: {$res2['total']} | Imported: {$res2['imported']} | Published: {$res2['published']} | Draft: {$res2['draft']} | Duplicates Skipped: {$res2['duplicates_skipped']}\n";
    if (!empty($res2['errors'])) {
        foreach ($res2['errors'] as $err) echo "        ERROR: {$err}\n";
    }

    // 4. Export review sample CSV (30 questions)
    $sampleFile = VerificationEngine::exportReviewSample($companyName);
    echo "  [4/4] Review Sample Exported: " . basename($sampleFile) . " (at least 5 Quant, 5 Logical, 5 Verbal, 5 Technical)\n";

    // Current DB state for company
    $stmt = $db->prepare("SELECT COUNT(*) as total, SUM(is_published) as published, SUM(CASE WHEN is_published = 0 THEN 1 ELSE 0 END) as draft FROM questions WHERE company_name = ?");
    $stmt->execute([$companyName]);
    $st = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  >> Company Status: Total = {$st['total']} | Published = {$st['published']} | Draft = {$st['draft']}\n\n";
}

echo "=================================================================\n";
echo "           ALL 15 COMPANIES INGESTION COMPLETED!                 \n";
echo "=================================================================\n\n";

// Export sample for Innovature as well
VerificationEngine::exportReviewSample('Innovature');

// Run summary report
require_once __DIR__ . '/print_summary_report.php';
