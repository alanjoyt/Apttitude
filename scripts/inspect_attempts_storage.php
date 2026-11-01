<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=== Columns in 'attempts' ===\n";
$cols = $db->query("SHOW COLUMNS FROM attempts")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    echo "{$c['Field']} ({$c['Type']})\n";
}

echo "\n=== Sample 5 rows in 'attempts' ===\n";
$rows = $db->query("SELECT * FROM attempts ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);

echo "\n=== Saved / Bookmarked Tables ===\n";
$tables = $db->query("SHOW TABLES LIKE '%bookmark%' OR SHOW TABLES LIKE '%save%' OR SHOW TABLES LIKE '%mistake%'")->fetchAll();
print_r($tables);
