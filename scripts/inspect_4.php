<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();
$db->query("UPDATE questions SET language = 'c' WHERE id IN (1115, 1121, 1126, 1193)");
echo "Updated 4 questions to language 'c'.\n";
