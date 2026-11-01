<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "              DATABASE COMPLIANCE & INTEGRITY CHECK              \n";
echo "=================================================================\n\n";

// 1. Verification status check
echo "1. Checking verification_status values across all questions:\n";
$stmt = $db->query("SELECT verification_status, COUNT(*) as cnt FROM questions GROUP BY verification_status");
$hasVerifiedPyq = false;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $status = $row['verification_status'] ?? 'NULL';
    echo "   - Status '{$status}': {$row['cnt']} questions\n";
    if ($status === 'verified_pyq') {
        $hasVerifiedPyq = true;
    }
}
if (!$hasVerifiedPyq) {
    echo "   [PASS] Zero questions are tagged as 'verified_pyq'.\n\n";
} else {
    echo "   [ALERT] Found verified_pyq questions. Checking details...\n\n";
}

// 2. Question counts per company
echo "2. Checking question count per company (target >= 100):\n";
$companies = [
    'Innovature', 'TCS NQT', 'Infosys', 'Wipro NLTH', 'Accenture',
    'Cognizant', 'Capgemini', 'Deloitte', 'Tech Mahindra', 'Zoho',
    'LTI Mindtree', 'HCL', 'Amazon', 'UST', 'IBM'
];

$allAbove100 = true;
foreach ($companies as $comp) {
    $qStmt = $db->prepare("SELECT COUNT(*) as total, SUM(is_published) as pub, SUM(CASE WHEN is_published=0 THEN 1 ELSE 0 END) as draft FROM questions WHERE company_name = ?");
    $qStmt->execute([$comp]);
    $counts = $qStmt->fetch(PDO::FETCH_ASSOC);
    $total = (int)$counts['total'];
    $pub = (int)$counts['pub'];
    $draft = (int)$counts['draft'];
    $statusIcon = $total >= 100 ? "[PASS]" : "[FAIL]";
    if ($total < 100) $allAbove100 = false;
    printf("   %-15s : Total = %3d | Published = %3d | Draft = %2d %s\n", $comp, $total, $pub, $draft, $statusIcon);
}

if ($allAbove100) {
    echo "\n   [PASS] ALL 15 companies meet or exceed the target of 100+ questions!\n\n";
} else {
    echo "\n   [FAIL] Some companies do not meet 100 questions.\n\n";
}

// 3. Check published draft separation
$publishedUnverified = $db->query("SELECT COUNT(*) FROM questions WHERE is_published = 1 AND (verified_by_script = 0 OR verification_status != 'practice_style')")->fetchColumn();
echo "3. Draft/Published Gatekeeper Separation:\n";
echo "   - Questions published without script verification: {$publishedUnverified}\n";
if ((int)$publishedUnverified === 0) {
    echo "   [PASS] Every single published question has been verified by the verification script.\n\n";
} else {
    echo "   [FAIL] Unverified questions found in published state.\n\n";
}
