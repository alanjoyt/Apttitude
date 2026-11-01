<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$companies = [
    'Innovature',
    'TCS NQT',
    'Infosys',
    'Wipro NLTH',
    'Accenture',
    'Cognizant',
    'Capgemini',
    'Deloitte',
    'Tech Mahindra',
    'Zoho',
    'LTI Mindtree',
    'HCL',
    'Amazon',
    'UST',
    'IBM'
];

echo "=========================================================================================================\n";
echo "                          APTTITUDEE 15-COMPANY MASTER QUESTION BANK REPORT                             \n";
echo "=========================================================================================================\n\n";

printf(
    "%-16s | %5s | %5s | %5s | %5s | %5s | %5s | %5s | %4s | %4s | %4s | %5s\n",
    "Company", "Total", "Pub", "Draft", "Quant", "Logic", "Verb", "Tech", "Easy", "Med", "Hard", "Code"
);
echo str_repeat("-", 105) . "\n";

$grandTotal = 0;
$grandPub = 0;
$grandDraft = 0;
$grandQuant = 0;
$grandLogic = 0;
$grandVerb = 0;
$grandTech = 0;
$grandEasy = 0;
$grandMed = 0;
$grandHard = 0;
$grandCode = 0;

foreach ($companies as $comp) {
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(is_published) as published,
            SUM(CASE WHEN is_published = 0 THEN 1 ELSE 0 END) as draft,
            SUM(CASE WHEN s.topic_id IN (1, 4) THEN 1 ELSE 0 END) as quant,
            SUM(CASE WHEN s.topic_id = 2 THEN 1 ELSE 0 END) as logic,
            SUM(CASE WHEN s.topic_id = 3 THEN 1 ELSE 0 END) as verbal,
            SUM(CASE WHEN s.topic_id IN (5, 6) THEN 1 ELSE 0 END) as tech,
            SUM(CASE WHEN q.difficulty = 'easy' THEN 1 ELSE 0 END) as easy,
            SUM(CASE WHEN q.difficulty = 'medium' THEN 1 ELSE 0 END) as med,
            SUM(CASE WHEN q.difficulty = 'hard' THEN 1 ELSE 0 END) as hard,
            SUM(CASE WHEN q.code_snippet IS NOT NULL AND q.code_snippet != '' THEN 1 ELSE 0 END) as with_code
        FROM questions q
        JOIN subtopics s ON q.subtopic_id = s.id
        WHERE q.company_name = ?
    ");
    $stmt->execute([$comp]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);

    printf(
        "%-16s | %5d | %5d | %5d | %5d | %5d | %5d | %5d | %4d | %4d | %4d | %5d\n",
        $comp,
        $r['total'],
        $r['published'],
        $r['draft'],
        $r['quant'],
        $r['logic'],
        $r['verbal'],
        $r['tech'],
        $r['easy'],
        $r['med'],
        $r['hard'],
        $r['with_code']
    );

    $grandTotal += $r['total'];
    $grandPub += $r['published'];
    $grandDraft += $r['draft'];
    $grandQuant += $r['quant'];
    $grandLogic += $r['logic'];
    $grandVerb += $r['verbal'];
    $grandTech += $r['tech'];
    $grandEasy += $r['easy'];
    $grandMed += $r['med'];
    $grandHard += $r['hard'];
    $grandCode += $r['with_code'];
}

echo str_repeat("-", 105) . "\n";
printf(
    "%-16s | %5d | %5d | %5d | %5d | %5d | %5d | %5d | %4d | %4d | %4d | %5d\n",
    "GRAND TOTAL",
    $grandTotal,
    $grandPub,
    $grandDraft,
    $grandQuant,
    $grandLogic,
    $grandVerb,
    $grandTech,
    $grandEasy,
    $grandMed,
    $grandHard,
    $grandCode
);

echo "\n=========================================================================================================\n";
echo "                                    SUBTOPIC BREAKDOWN PER COMPANY                                       \n";
echo "=========================================================================================================\n";

$subtopics = $db->query("SELECT id, code, name FROM subtopics ORDER BY topic_id, id")->fetchAll(PDO::FETCH_ASSOC);

foreach ($companies as $comp) {
    echo "\n>>> [{$comp}] Subtopic Distribution:\n";
    $stmt = $db->prepare("
        SELECT s.code, s.name, COUNT(*) as cnt
        FROM questions q
        JOIN subtopics s ON q.subtopic_id = s.id
        WHERE q.company_name = ?
        GROUP BY s.id, s.code, s.name
        ORDER BY cnt DESC, s.code ASC
    ");
    $stmt->execute([$comp]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $chunks = array_chunk($rows, 3);
    foreach ($chunks as $chunk) {
        $line = [];
        foreach ($chunk as $item) {
            $line[] = sprintf("%-15s: %2d", $item['code'], $item['cnt']);
        }
        echo "    " . implode(" | ", $line) . "\n";
    }
}
echo "\n=========================================================================================================\n";
