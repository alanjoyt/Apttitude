<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();
$rows = $db->query("SELECT id, question_text FROM questions WHERE company_name LIKE '%Innovature%'")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "#{$r['id']}: " . substr($r['question_text'], 0, 80) . "\n";
}
