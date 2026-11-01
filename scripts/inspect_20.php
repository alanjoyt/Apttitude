<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$stmt = $db->query("SELECT id, question_text, correct_option, option_a, option_b, option_c, option_d, explanation FROM questions WHERE is_published = 1");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $q) {
    $exp = $q['explanation'];
    if (preg_match('/\bOption\s+[A-D]\b/i', $exp) || 
        preg_match('/\b(?:answer|choice|option)\s+is\s+[A-D]\b(?!-)/i', $exp)) {
        $corrKey = 'option_' . strtolower($q['correct_option']);
        echo "[ID {$q['id']}] DB Correct Letter: {$q['correct_option']} | Option Text: '{$q[$corrKey]}'\n";
        echo "Q: {$q['question_text']}\n";
        echo "EXP: {$exp}\n";
        echo "--------------------------------------------------------\n";
    }
}
