<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$r1 = $db->query("SELECT COUNT(*) FROM questions WHERE question_text LIKE '%PYQ%' OR source_tag LIKE '%PYQ%'")->fetchColumn();
echo "Questions with PYQ in text or source_tag: $r1\n";

$r2 = $db->query("SELECT COUNT(*) FROM questions WHERE question_text LIKE '%Authentic%' OR explanation LIKE '%Authentic%'")->fetchColumn();
echo "Questions with 'Authentic': $r2\n";

$r3 = $db->query("SELECT COUNT(*) FROM questions WHERE question_text LIKE '%recruitment rounds%' OR explanation LIKE '%recruitment rounds%'")->fetchColumn();
echo "Questions with 'recruitment rounds': $r3\n";

$r4 = $db->query("SELECT DISTINCT verification_status, COUNT(*) FROM questions GROUP BY verification_status")->fetchAll(PDO::FETCH_KEY_PAIR);
echo "Verification statuses:\n";
print_r($r4);
