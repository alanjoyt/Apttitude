<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=== Current Company Question Counts in DB (Published vs Total) ===\n";
echo str_pad("Company", 18) . " | " . str_pad("Published", 10) . " | " . str_pad("Draft", 8) . " | " . str_pad("Total", 8) . "\n";
echo str_repeat("-", 52) . "\n";

$rows = $db->query("
    SELECT company_name, 
           SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as pub_cnt,
           SUM(CASE WHEN is_published = 0 THEN 1 ELSE 0 END) as draft_cnt,
           COUNT(*) as tot_cnt 
    FROM questions 
    WHERE company_name IS NOT NULL AND company_name != '' 
    GROUP BY company_name 
    ORDER BY company_name ASC
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $r) {
    echo str_pad($r['company_name'], 18) . " | " .
         str_pad($r['pub_cnt'], 10, " ", STR_PAD_LEFT) . " | " .
         str_pad($r['draft_cnt'], 8, " ", STR_PAD_LEFT) . " | " .
         str_pad($r['tot_cnt'], 8, " ", STR_PAD_LEFT) . "\n";
}


echo "\n=== Columns in questions table ===\n";
$cols = $db->query("SHOW COLUMNS FROM questions")->fetchAll(PDO::FETCH_COLUMN);
echo implode(", ", $cols) . "\n";
