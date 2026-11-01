<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "--- Capgemini questions with LIGHT or DARK or coding ---\n";
$stmt = $db->query("SELECT id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation FROM questions WHERE company_name = 'Capgemini' AND (question_text LIKE '%coded%' OR question_text LIKE '%code%' OR explanation LIKE '%code%')");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo "ID: {$r['id']} | Q: {$r['question_text']} | A: {$r['option_a']} | Exp: {$r['explanation']}\n";
}

echo "\n--- Infosys questions with code/coded ---\n";
$stmt = $db->query("SELECT id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation FROM questions WHERE company_name = 'Infosys' AND (question_text LIKE '%coded%' OR question_text LIKE '%code%' OR question_text LIKE '%written%')");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo "ID: {$r['id']} | Q: {$r['question_text']} | A: {$r['option_a']} | Exp: {$r['explanation']}\n";
}
