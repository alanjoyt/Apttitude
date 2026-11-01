<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=== Capgemini coding/decoding questions ===\n";
$stmt = $db->query("
    SELECT id, company_name, question_text, option_a, option_b, option_c, option_d, correct_option, explanation 
    FROM questions 
    WHERE company_name = 'Capgemini' AND (question_text LIKE '%written as%' OR question_text LIKE '%coded as%' OR question_text LIKE '%DARK%' OR explanation LIKE '%DARK%')
");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "=== Infosys coding questions ===\n";
$stmt = $db->query("
    SELECT id, company_name, question_text, option_a, option_b, option_c, option_d, correct_option, explanation 
    FROM questions 
    WHERE company_name = 'Infosys' AND (question_text LIKE '%code%' OR question_text LIKE '%written as%')
");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
