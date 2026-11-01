<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$stmt = $db->query("
    SELECT id, import_batch_id, verified_by_script, is_published, question_text, created_at
    FROM questions 
    WHERE company_name LIKE '%Innovature%' AND is_published = 1
    ORDER BY id
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total published Innovature questions: " . count($rows) . "\n\n";

$byBatch = [];
foreach ($rows as $r) {
    $batch = $r['import_batch_id'] ?: 'NULL';
    $byBatch[$batch][] = $r;
}

foreach ($byBatch as $batch => $list) {
    echo "Batch: {$batch} | Count: " . count($list) . " | ID Range: {$list[0]['id']} - {$list[count($list)-1]['id']}\n";
    echo "  Sample question: " . substr($list[0]['question_text'], 0, 80) . "...\n";
}
