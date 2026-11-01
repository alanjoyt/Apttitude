<?php
/**
 * Analyze duplicates across published questions per company
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

$rows = $db->query("
    SELECT id, company_name, question_text, subtopic_id, is_published, language, code_snippet
    FROM questions
    WHERE is_published = 1 AND company_name IS NOT NULL AND company_name != ''
")->fetchAll(PDO::FETCH_ASSOC);

echo "Total currently published company questions: " . count($rows) . "\n";

$byNorm = [];
foreach ($rows as $r) {
    $norm = normalizeStem($r['question_text'], $companyNames);
    $r['norm'] = $norm;
    $byNorm[$norm][] = $r;
}

// Pairwise collision count
$pairCollisions = [];
$clashGroups = [];

foreach ($byNorm as $norm => $group) {
    $compsInGroup = [];
    foreach ($group as $item) {
        $compsInGroup[$item['company_name']][] = $item['id'];
    }
    if (count($compsInGroup) > 1) {
        $cList = array_keys($compsInGroup);
        for ($i = 0; $i < count($cList); $i++) {
            for ($j = $i + 1; $j < count($cList); $j++) {
                $p = $cList[$i] < $cList[$j] ? "{$cList[$i]} <-> {$cList[$j]}" : "{$cList[$j]} <-> {$cList[$i]}";
                if (!isset($pairCollisions[$p])) $pairCollisions[$p] = 0;
                $pairCollisions[$p]++;
            }
        }
        $clashGroups[] = [
            'norm' => $norm,
            'companies' => $compsInGroup
        ];
    }
}

echo "Total clashing template groups: " . count($clashGroups) . "\n";
arsort($pairCollisions);
echo "\nTop 15 pairwise collisions currently published:\n";
$i = 0;
foreach ($pairCollisions as $pair => $cnt) {
    printf("  %-40s : %d\n", $pair, $cnt);
    if (++$i >= 15) break;
}
