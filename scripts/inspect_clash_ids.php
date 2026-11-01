<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();
$r = $db->query("SELECT id, company_name, question_text, code_snippet, correct_option, option_a FROM questions WHERE id = 1015")->fetch(PDO::FETCH_ASSOC);
print_r($r);

