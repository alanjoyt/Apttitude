<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();
$rows = $db->query("
    SELECT id, language, code_snippet, correct_option, option_a, option_b, option_c, option_d 
    FROM questions 
    WHERE is_published = 1 AND language = 'innovature' AND code_snippet IS NOT NULL
")->fetchAll(PDO::FETCH_ASSOC);
echo "Innovature code questions count: " . count($rows) . "\n";
foreach (array_slice($rows, 0, 5) as $r) {
    echo "ID: {$r['id']}\nSnippet:\n" . substr($r['code_snippet'], 0, 120) . "\n---\n";
}
