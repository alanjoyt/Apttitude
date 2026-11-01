<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$count = $db->query("SELECT COUNT(*) FROM questions WHERE company_name LIKE '%Innovature%'")->fetchColumn();
echo "Total Innovature questions: $count\n";

$breakdown = $db->query("
    SELECT q.difficulty, q.verification_status, COUNT(*) as cnt 
    FROM questions q 
    WHERE q.company_name LIKE '%Innovature%' 
    GROUP BY q.difficulty, q.verification_status
")->fetchAll(PDO::FETCH_ASSOC);
print_r($breakdown);
