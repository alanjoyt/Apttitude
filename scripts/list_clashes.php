<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$rows = $db->query("
    SELECT id, company_name, question_text, subtopic_id, correct_option, option_a, option_b, option_c, option_d, explanation
    FROM questions
    WHERE is_published = 1 AND company_name IS NOT NULL AND company_name != ''
")->fetchAll(PDO::FETCH_ASSOC);

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

$normalizedMap = [];
foreach ($rows as $r) {
    $norm = normalizeStem($r['question_text'], $companyNames);
    $r['norm'] = $norm;
    $normalizedMap[$norm][] = $r;
}

$groupIndex = 0;
foreach ($normalizedMap as $norm => $group) {
    $comps = array_unique(array_column($group, 'company_name'));
    if (count($comps) > 1) {
        $groupIndex++;
        echo "=== GROUP $groupIndex: (Companies: " . implode(', ', $comps) . ") ===\n";
        echo "Pattern: $norm\n";
        foreach ($group as $q) {
            echo "  ID: {$q['id']} | {$q['company_name']} | Ans: {$q['correct_option']} ({$q['option_a']}, {$q['option_b']}, {$q['option_c']}, {$q['option_d']})\n";
            echo "  Stem: {$q['question_text']}\n";
            echo "  Expl: {$q['explanation']}\n\n";
        }
    }
}
