<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();
$admins = $db->query("SELECT id, name, email, role FROM users WHERE role = 'admin'")->fetchAll(PDO::FETCH_ASSOC);
print_r($admins);
