<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$rows = $db->query("
    SELECT id, company_name, code_snippet, option_a, correct_option, explanation
    FROM questions
    WHERE is_published = 1 AND language = 'c' AND code_snippet IS NOT NULL
    ORDER BY id
")->fetchAll(PDO::FETCH_ASSOC);

echo "Published C questions count: " . count($rows) . "\n";
foreach ($rows as $r) {
    echo "ID {$r['id']} ({$r['company_name']}) | Ans: {$r['option_a']} | Code: " . str_replace("\n", " ; ", trim($r['code_snippet'])) . "\n";
}
