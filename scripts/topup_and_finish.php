<?php
/**
 * Apttitudee Final Expansion Stage: UST, IBM, and Company Top-Ups
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/verify_and_ingest.php';
require_once __DIR__ . '/generators/helper.php';
require_once __DIR__ . '/generators/gen_ust.php';
require_once __DIR__ . '/generators/gen_ibm.php';

$db = Database::getConnection();

echo "=================================================================\n";
echo "   APTTITUDEE FINAL EXPANSION STAGE: UST, IBM & TOP-UPS          \n";
echo "=================================================================\n\n";

// Function to run DB backup
function runBackup() {
    echo "  [DB BACKUP] Running pre-import database backup...\n";
    $backupScript = __DIR__ . '/backup_db.php';
    $cmd = escapeshellcmd(PHP_BINARY) . ' ' . escapeshellarg($backupScript);
    $out = [];
    $ret = 0;
    exec($cmd, $out, $ret);
    if ($ret === 0) {
        echo "  [BACKUP OK] Database backup complete.\n";
    } else {
        echo "  [BACKUP NOTICE] Code $ret\n";
    }
}

// 1. Process UST
echo ">>> Processing Company: UST\n";
runBackup();
$ustData = getUstQuestions();
$b1 = $ustData['batch1'];
$b2 = $ustData['batch2'];
echo "  Generated questions: Batch 1 (" . count($b1) . " Qs), Batch 2 (" . count($b2) . " Qs)\n";

$res1 = VerificationEngine::ingestBatch($b1, "BATCH_UST_1");
echo "  Ingested BATCH_UST_1: Total: {$res1['total']} | Imported: {$res1['imported']} | Published: {$res1['published']} | Draft: {$res1['draft']} | Duplicates: {$res1['duplicates_skipped']}\n";

$res2 = VerificationEngine::ingestBatch($b2, "BATCH_UST_2");
echo "  Ingested BATCH_UST_2: Total: {$res2['total']} | Imported: {$res2['imported']} | Published: {$res2['published']} | Draft: {$res2['draft']} | Duplicates: {$res2['duplicates_skipped']}\n";

$ustSample = VerificationEngine::exportReviewSample('UST');
echo "  Exported review sample: " . basename($ustSample) . "\n\n";

// 2. Process IBM
echo ">>> Processing Company: IBM\n";
runBackup();
$ibmData = getIbmQuestions();
$b1 = $ibmData['batch1'];
$b2 = $ibmData['batch2'];
echo "  Generated questions: Batch 1 (" . count($b1) . " Qs), Batch 2 (" . count($b2) . " Qs)\n";

$res1 = VerificationEngine::ingestBatch($b1, "BATCH_IBM_1");
echo "  Ingested BATCH_IBM_1: Total: {$res1['total']} | Imported: {$res1['imported']} | Published: {$res1['published']} | Draft: {$res1['draft']} | Duplicates: {$res1['duplicates_skipped']}\n";

$res2 = VerificationEngine::ingestBatch($b2, "BATCH_IBM_2");
echo "  Ingested BATCH_IBM_2: Total: {$res2['total']} | Imported: {$res2['imported']} | Published: {$res2['published']} | Draft: {$res2['draft']} | Duplicates: {$res2['duplicates_skipped']}\n";

$ibmSample = VerificationEngine::exportReviewSample('IBM');
echo "  Exported review sample: " . basename($ibmSample) . "\n\n";

// 3. Top-up Tech Mahindra (currently 99, need >= 100)
echo ">>> Processing Top-up: Tech Mahindra\n";
$tmExtra = [
    makeQ('Tech Mahindra', 'TECH_DSA', 'medium',
        'Which tree traversal strategy visits the Left subtree, the Root node, and then the Right subtree?',
        'In-order Traversal', 'Pre-order Traversal', 'Post-order Traversal', 'Level-order Traversal', 'A',
        'In-order traversal processes nodes in Left, Root, Right order, yielding sorted sequence in BSTs (Option A).'),
    makeQ('Tech Mahindra', 'QUANT_PERC', 'easy',
        'In a Tech Mahindra telecommunication network, a transmission line transmits 400 MB of data. If traffic grows by 15%, what is the new total transmission volume in MB?',
        '460 MB', '450 MB', '470 MB', '480 MB', 'A',
        'Traffic growth = 15% of 400 = 60 MB. New volume = 400 + 60 = 460 MB (Option A).'),
    makeQ('Tech Mahindra', 'LR_CODING', 'easy',
        'If TELECOM is coded as UFMEDPN by shifting each letter forward by one position, how is SIGNAL coded in that same system?',
        'TJHOBM', 'TJHNBM', 'TKHOBL', 'SIPOBM', 'A',
        'Shift each letter by +1: S->T, I->J, G->H, N->O, A->B, L->M, resulting in TJHOBM (Option A).')
];
$resTM = VerificationEngine::ingestBatch($tmExtra, "BATCH_TECH_MAHINDRA_3");
echo "  Ingested BATCH_TECH_MAHINDRA_3: Total: {$resTM['total']} | Imported: {$resTM['imported']} | Published: {$resTM['published']} | Draft: {$resTM['draft']} | Duplicates: {$resTM['duplicates_skipped']}\n\n";

// 4. Top-up LTI Mindtree (currently 99, need >= 100)
echo ">>> Processing Top-up: LTI Mindtree\n";
$ltiExtra = [
    makeQ('LTI Mindtree', 'TECH_SQL', 'medium',
        'In relational SQL databases, which clause is specifically used with aggregate functions to filter grouped record sets?',
        'HAVING', 'WHERE', 'ORDER BY', 'GROUP FILTER', 'A',
        'The HAVING clause filters rows after aggregation has been applied by GROUP BY (Option A).'),
    makeQ('LTI Mindtree', 'QUANT_WORK', 'easy',
        'A team of 4 software engineers at LTI Mindtree completes a sprint backlog in 10 days. How many days will 8 engineers working at the same pace take to complete the same backlog?',
        '5 days', '4 days', '8 days', '6 days', 'A',
        'Total effort = 4 * 10 = 40 engineer-days. With 8 engineers, days required = 40 / 8 = 5 days (Option A).'),
    makeQ('LTI Mindtree', 'LR_SERIES', 'easy',
        'Find the next term in the arithmetic sequence: 14, 28, 42, 56, ?',
        '70', '68', '72', '74', 'A',
        'The common difference is +14. Next term = 56 + 14 = 70 (Option A).')
];
$resLTI = VerificationEngine::ingestBatch($ltiExtra, "BATCH_LTI_MINDTREE_3");
echo "  Ingested BATCH_LTI_MINDTREE_3: Total: {$resLTI['total']} | Imported: {$resLTI['imported']} | Published: {$resLTI['published']} | Draft: {$resLTI['draft']} | Duplicates: {$resLTI['duplicates_skipped']}\n\n";

echo "Final stage ingestion completed!\n";
