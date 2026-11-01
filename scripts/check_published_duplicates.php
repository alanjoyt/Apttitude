<?php
/**
 * Cross-Company Duplicate and Near-Duplicate Detector for PUBLISHED questions
 */

require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "   PUBLISHED QUESTIONS CROSS-COMPANY DUPLICATE & NEAR-DUP AUDIT   \n";
echo "=================================================================\n\n";

$rows = $db->query("
    SELECT id, company_name, question_text, subtopic_id, is_published, correct_option, option_a, option_b, option_c, option_d, code_snippet
    FROM questions
    WHERE is_published = 1 AND company_name IS NOT NULL AND company_name != ''
")->fetchAll(PDO::FETCH_ASSOC);

echo "Total published questions loaded: " . count($rows) . "\n";

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

$normalizedMap = [];

foreach ($rows as $r) {
    $norm = normalizeStem($r['question_text'], $companyNames);
    $r['norm'] = $norm;
    $normalizedMap[$norm][] = $r;
}

$pairClashes = [];
$clashingIds = [];

foreach ($normalizedMap as $norm => $group) {
    if (count($group) > 1) {
        $compsInGroup = [];
        foreach ($group as $item) {
            $compsInGroup[$item['company_name']][] = $item['id'];
        }
        if (count($compsInGroup) > 1) {
            $cList = array_keys($compsInGroup);
            for ($i = 0; $i < count($cList); $i++) {
                for ($j = $i + 1; $j < count($cList); $j++) {
                    $pair = $cList[$i] < $cList[$j] ? "{$cList[$i]} <-> {$cList[$j]}" : "{$cList[$j]} <-> {$cList[$i]}";
                    if (!isset($pairClashes[$pair])) $pairClashes[$pair] = 0;
                    $pairClashes[$pair]++;
                }
            }
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

echo "\n--- Cross-Company Clashes Across PUBLISHED questions ---\n";
arsort($pairClashes);
if (empty($pairClashes)) {
    echo "[PASS] ZERO cross-company template or number-swapped clashes detected among published questions!\n";
} else {
    foreach ($pairClashes as $pair => $count) {
        printf("  %-40s : %d collisions\n", $pair, $count);
    }
    echo "\nTotal unique published question IDs involved: " . count($clashingIds) . "\n";
    echo "\nSample Clashing Groups:\n";
    $shown = 0;
    foreach ($normalizedMap as $norm => $group) {
        $comps = array_unique(array_column($group, 'company_name'));
        if (count($comps) > 1) {
            $shown++;
            echo "Group {$shown} (companies: " . implode(', ', $comps) . "):\n";
            foreach ($group as $q) {
                echo "  [{$q['id']}] [{$q['company_name']}]: {$q['question_text']}\n";
            }
            if ($shown >= 10) break;
        }
    }
}

// Check 4: Confirm every published question has est_time_seconds (>0), explanation, topic/subtopic, difficulty, and 1 correct option
echo "\n--- Quality and Completeness Audit for All Published Questions ---\n";
$issues = $db->query("
    SELECT 
        SUM(CASE WHEN est_time_seconds IS NULL OR est_time_seconds <= 0 THEN 1 ELSE 0 END) as invalid_time,
        SUM(CASE WHEN explanation IS NULL OR LENGTH(TRIM(explanation)) < 10 THEN 1 ELSE 0 END) as missing_expl,
        SUM(CASE WHEN subtopic_id IS NULL OR subtopic_id = 0 THEN 1 ELSE 0 END) as missing_subtopic,
        SUM(CASE WHEN difficulty NOT IN ('easy', 'medium', 'hard') THEN 1 ELSE 0 END) as invalid_diff,
        SUM(CASE WHEN correct_option NOT IN ('A', 'B', 'C', 'D') THEN 1 ELSE 0 END) as invalid_correct,
        SUM(CASE WHEN verified_by_script != 1 THEN 1 ELSE 0 END) as unverified
    FROM questions
    WHERE is_published = 1
")->fetch(PDO::FETCH_ASSOC);

echo "Invalid / zero est_time_seconds : " . (int)$issues['invalid_time'] . "\n";
echo "Missing / brief explanation     : " . (int)$issues['missing_expl'] . "\n";
echo "Missing subtopic_id             : " . (int)$issues['missing_subtopic'] . "\n";
echo "Invalid difficulty              : " . (int)$issues['invalid_diff'] . "\n";
echo "Invalid correct_option          : " . (int)$issues['invalid_correct'] . "\n";
echo "Unverified published questions  : " . (int)$issues['unverified'] . "\n";

if (array_sum($issues) == 0) {
    echo "\n[PASS] All published questions strictly satisfy every quality & completeness constraint!\n";
} else {
    echo "\n[FAIL] Some published questions failed constraints.\n";
}
