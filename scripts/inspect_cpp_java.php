<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$rows = $db->query("
    SELECT id, company_name, language, question_text, code_snippet, correct_option, option_a, option_b, option_c, option_d, explanation
    FROM questions
    WHERE is_published = 1 AND language IN ('cpp', 'java')
    ORDER BY language, id
")->fetchAll(PDO::FETCH_ASSOC);

echo "Total published C++ and Java questions: " . count($rows) . "\n\n";

foreach ($rows as $r) {
    echo "========================================================\n";
    echo "ID: {$r['id']} | Comp: {$r['company_name']} | Lang: {$r['language']} | Answer: {$r['correct_option']}\n";
    echo "Question: {$r['question_text']}\n";
    echo "Code:\n{$r['code_snippet']}\n";
    echo "A: {$r['option_a']} | B: {$r['option_b']} | C: {$r['option_c']} | D: {$r['option_d']}\n";
    echo "Expl: {$r['explanation']}\n";
}
