<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$rows = $db->query("
    SELECT language, COUNT(*) as cnt 
    FROM questions 
    WHERE is_published = 1 AND language IS NOT NULL AND language != '' 
    GROUP BY language
")->fetchAll(PDO::FETCH_ASSOC);

print_r($rows);
