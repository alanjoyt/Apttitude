<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "--- Searching all questions for MTHIW ---\n";
$stmt = $db->query("SELECT id, company_name, question_text, option_a, option_b, option_c, option_d, correct_option, explanation FROM questions WHERE question_text LIKE '%MTHIW%' OR explanation LIKE '%MTHIW%' OR option_a LIKE '%MTHIW%' OR option_b LIKE '%MTHIW%' OR option_c LIKE '%MTHIW%' OR option_d LIKE '%MTHIW%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "--- Searching all questions for PWUQV ---\n";
$stmt = $db->query("SELECT id, company_name, question_text, option_a, option_b, option_c, option_d, correct_option, explanation FROM questions WHERE question_text LIKE '%PWUQ%' OR explanation LIKE '%PWUQ%' OR option_a LIKE '%PWUQ%' OR option_b LIKE '%PWUQ%' OR option_c LIKE '%PWUQ%' OR option_d LIKE '%PWUQ%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
