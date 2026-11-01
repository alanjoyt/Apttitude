<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();
$q = $db->query('SELECT id, is_published, question_text, option_a FROM questions WHERE id = 1763')->fetch();
print_r($q);
