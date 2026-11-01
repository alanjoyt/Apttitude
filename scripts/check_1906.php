<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();
$q = $db->query('SELECT id, company_name, question_text, option_a, option_b, option_c, option_d, correct_option, explanation FROM questions WHERE id = 1906')->fetch(PDO::FETCH_ASSOC);
print_r($q);
