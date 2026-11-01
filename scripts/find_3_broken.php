<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "--- Searching for TZTUFN or PVUQVU ---\n";
$stmt = $db->query("SELECT id, company_name, question_text, option_a, option_b, option_c, option_d, explanation FROM questions WHERE question_text LIKE '%TZTUFN%' OR option_a LIKE '%PVUQVU%' OR option_d LIKE '%PWUQV%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "--- Searching for ECVN or MTHIW or LIGHT ---\n";
$stmt = $db->query("SELECT id, company_name, question_text, option_a, option_b, option_c, option_d, explanation FROM questions WHERE option_a LIKE '%ECVN%' OR question_text LIKE '%MTHIW%' OR explanation LIKE '%ECVN%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
