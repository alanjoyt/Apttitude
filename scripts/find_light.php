<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$stmt = $db->query("SELECT id, company_name, is_published, question_text, option_a, option_b, option_c, option_d, correct_option, explanation FROM questions WHERE question_text LIKE '%LIGHT%' OR explanation LIKE '%LIGHT%'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Questions matching LIGHT: " . count($rows) . "\n";
foreach ($rows as $r) {
    echo "ID: {$r['id']} | Comp: {$r['company_name']} | Published: {$r['is_published']}\n";
    echo "Q: {$r['question_text']}\n";
    echo "A: {$r['option_a']} | B: {$r['option_b']} | C: {$r['option_c']} | D: {$r['option_d']}\n";
    echo "Correct: {$r['correct_option']}\n";
    echo "Exp: {$r['explanation']}\n-----------------------------------\n";
}
