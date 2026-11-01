<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();
$tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);
