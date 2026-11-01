<?php
/**
 * Cross-Company Duplicate and Near-Duplicate Detector
 */

require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "       CROSS-COMPANY DUPLICATE & NEAR-DUPLICATE AUDIT            \n";
echo "=================================================================\n\n";

$rows = $db->query("
    SELECT id, company_name, question_text, subtopic_id, is_published, correct_option, option_a, option_b, option_c, option_d
    FROM questions
    WHERE company_name IS NOT NULL AND company_name != ''
")->fetchAll(PDO::FETCH_ASSOC);

echo "Total questions loaded: " . count($rows) . "\n";

$companyNames = [
    'innovature', 'tcs nqt', 'tcs', 'infosys', 'wipro nlth', 'wipro',
    'accenture', 'cognizant', 'capgemini', 'deloitte', 'tech mahindra',
    'zoho', 'lti mindtree', 'hcl', 'amazon', 'ust', 'ibm'
];

function normalizeStem(string $text, array $companies): string {
    $t = mb_strtolower($text);
    // Replace company names with <COMP>
    foreach ($companies as $c) {
        $t = str_replace($c, '<comp>', $t);
    }
    // Replace all numbers (integers and floats) with <NUM>
    $t = preg_replace('/\b\d+(\.\d+)?\b/', '<num>', $t);
    // Remove punctuation and extra whitespace
    $t = preg_replace('/[^a-z0-9<>]/', ' ', $t);
    $t = preg_replace('/\s+/', ' ', trim($t));
    return $t;
}

$byCompany = [];
$normalizedMap = []; // norm_text => [ [id, company, raw] ]
foreach ($rows as $r) {
    $cid = $r['company_name'];
    $norm = normalizeStem($r['question_text'], $companyNames);
    $r['norm'] = $norm;
    $byCompany[$cid][] = $r;
    $normalizedMap[$norm][] = $r;
}

// Find exact normalized clashes across DIFFERENT companies
$pairClashes = [];
$clashingIds = [];

foreach ($normalizedMap as $norm => $group) {
    if (count($group) > 1) {
        // Check if there are different companies
        $compsInGroup = [];
        foreach ($group as $item) {
            $compsInGroup[$item['company_name']][] = $item['id'];
        }
        if (count($compsInGroup) > 1) {
            // Cross-company collision
            $cList = array_keys($compsInGroup);
            for ($i = 0; $i < count($cList); $i++) {
                for ($j = $i + 1; $j < count($cList); $j++) {
                    $pair = $cList[$i] < $cList[$j] ? "{$cList[$i]} <-> {$cList[$j]}" : "{$cList[$j]} <-> {$cList[$i]}";
                    if (!isset($pairClashes[$pair])) $pairClashes[$pair] = 0;
                    $pairClashes[$pair]++;
                }
            }
            // Record ids
            foreach ($group as $item) {
                $clashingIds[$item['id']] = [
                    'company' => $item['company_name'],
                    'text' => $item['question_text'],
                    'norm' => $norm
                ];
            }
        }
    }
}

echo "\n--- Cross-Company Clashes (Normalized Match ignoring numbers/names) ---\n";
arsort($pairClashes);
if (empty($pairClashes)) {
    echo "No cross-company template clashes detected!\n";
} else {
    foreach ($pairClashes as $pair => $count) {
        printf("  %-40s : %d collisions\n", $pair, $count);
    }
    echo "\nTotal unique question IDs involved in cross-company duplication: " . count($clashingIds) . "\n";
}

// Save detailed report to json
file_put_contents(__DIR__ . '/cross_company_duplicates.json', json_encode([
    'pairClashes' => $pairClashes,
    'clashingCount' => count($clashingIds),
    'clashingIds' => $clashingIds
], JSON_PRETTY_PRINT));
echo "Detailed report saved to scripts/cross_company_duplicates.json\n";
