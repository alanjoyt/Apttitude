<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$stmt = $db->query("
    SELECT id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation
    FROM questions
    WHERE is_published = 1 AND (
        explanation REGEXP 'Option [A-D]' OR 
        explanation REGEXP 'answer is [A-D]' OR
        explanation REGEXP '\\([A-D]\\)'
    )
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total matching explanations: " . count($rows) . "\n\n";

for ($i = 0; $i < min(15, count($rows)); $i++) {
    $r = $rows[$i];
    $corr = $r['correct_option'];
    $corrKey = 'option_' . strtolower($corr);
    $corrText = $r[$corrKey] ?? '';
    echo "ID: {$r['id']} | Correct: {$corr} ({$corrText})\n";
    echo "EXP: {$r['explanation']}\n";
    echo "--------------------------------------------------------\n";
}
