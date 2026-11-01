<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$rows = $db->query("
    SELECT id, company_name, question_text, option_a, option_b, option_c, option_d, correct_option, explanation, verification_notes 
    FROM questions 
    WHERE company_name IN ('Innovature', 'TCS NQT', 'Infosys') AND is_published = 0
")->fetchAll(PDO::FETCH_ASSOC);

echo "Total unpublished pilot questions: " . count($rows) . "\n\n";
foreach ($rows as $r) {
    echo "ID: {$r['id']} | {$r['company_name']}\n";
    echo "Q: " . $r['question_text'] . "\n";
    echo "A: {$r['option_a']} | B: {$r['option_b']} | C: {$r['option_c']} | D: {$r['option_d']}\n";
    echo "Correct: {$r['correct_option']} => " . $r['option_' . strtolower($r['correct_option'])] . "\n";
    echo "Expl: {$r['explanation']}\n";
    echo "Note: {$r['verification_notes']}\n";
    echo "-----------------------------------------------------------------\n";
}
