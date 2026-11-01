<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();
$rows = $db->query("SELECT id, topic_id, code, name FROM subtopics ORDER BY topic_id, id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "ID: {$r['id']} | Topic: {$r['topic_id']} | Code: {$r['code']} | Name: {$r['name']}\n";
}
