<?php
/**
 * Unpublish cross-company duplicates and unexecuted snippet questions
 */

require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$comps = [
    'Innovature', 'TCS NQT', 'Infosys', 'Wipro NLTH', 'Accenture',
    'Cognizant', 'Capgemini', 'Deloitte', 'Tech Mahindra', 'Zoho',
    'LTI Mindtree', 'HCL', 'Amazon', 'UST', 'IBM'
];

$companyNames = [
    'innovature', 'tcs nqt', 'tcs', 'infosys', 'wipro nlth', 'wipro',
    'accenture', 'cognizant', 'capgemini', 'deloitte', 'tech mahindra',
    'zoho', 'lti mindtree', 'hcl', 'amazon', 'ust', 'ibm'
];

function normalizeStem(string $text, array $companies): string {
    $t = mb_strtolower($text);
    foreach ($companies as $c) {
        $t = str_replace($c, '<comp>', $t);
    }
    $t = preg_replace('/\b\d+(\.\d+)?\b/', '<num>', $t);
    $t = preg_replace('/[^a-z0-9<>]/', ' ', $t);
    $t = preg_replace('/\s+/', ' ', trim($t));
    return $t;
}

// 1. Unpublish unexecuted C++ and Java snippets
$unpublishLangStmt = $db->prepare("
    UPDATE questions 
    SET is_published = 0, verified_by_script = 0, verification_notes = 'DRAFT: Snippet not executed via native compiler'
    WHERE is_published = 1 AND language IN ('cpp', 'java') AND code_snippet IS NOT NULL AND TRIM(code_snippet) != ''
");
$unpublishLangStmt->execute();
$unpubLangCount = $unpublishLangStmt->rowCount();
echo "Unpublished $unpubLangCount unexecuted C++ / Java code snippet questions.\n";

// 2. Identify template collisions across companies
$rows = $db->query("
    SELECT id, company_name, question_text, subtopic_id
    FROM questions
    WHERE is_published = 1 AND company_name IS NOT NULL AND company_name != ''
    ORDER BY id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$seenTemplates = []; // norm => first company_name
$unpublishDupStmt = $db->prepare("
    UPDATE questions
    SET is_published = 0, verified_by_script = 0, verification_notes = ?
    WHERE id = ?
");

$unpubDupCount = 0;
foreach ($rows as $r) {
    $norm = normalizeStem($r['question_text'], $companyNames);
    if (!isset($seenTemplates[$norm])) {
        // First company keeps this template
        $seenTemplates[$norm] = $r['company_name'];
    } else {
        if ($seenTemplates[$norm] !== $r['company_name']) {
            // Collision from another company -> unpublish this copy!
            $owner = $seenTemplates[$norm];
            $unpublishDupStmt->execute(["UNPUBLISHED: Duplicate template of {$owner} (ID {$r['id']})", $r['id']]);
            $unpubDupCount++;
        }
    }
}
echo "Unpublished $unpubDupCount cross-company duplicate questions.\n\n";

// 3. Count published questions per company now
echo "Published Questions per Company After Deduplication:\n";
$deficitTotal = 0;
$deficits = [];
foreach ($comps as $c) {
    $cnt = $db->prepare("SELECT COUNT(*) FROM questions WHERE is_published = 1 AND company_name = ?");
    $cnt->execute([$c]);
    $pub = (int)$cnt->fetchColumn();
    $deficit = max(0, 100 - $pub);
    $deficits[$c] = $deficit;
    $deficitTotal += $deficit;
    printf("  %-15s : %3d published (Deficit: %2d)\n", $c, $pub, $deficit);
}

echo "\nTotal new original questions needed to bring ALL companies to >= 100 published: $deficitTotal\n";
