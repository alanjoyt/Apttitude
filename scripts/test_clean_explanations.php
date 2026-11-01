<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$stmt = $db->query("SELECT id, correct_option, option_a, option_b, option_c, option_d, explanation FROM questions WHERE is_published = 1");
$all = $stmt->fetchAll(PDO::FETCH_ASSOC);

$patterns = [
    '/\s*\(\s*Option\s+[A-D]\s*\)/i' => '',
    '/\s*\[\s*Option\s+[A-D]\s*\]/i' => '',
    '/\s*,\s*which is Option\s+[A-D]\b/i' => '',
    '/\s*which corresponds to Option\s+[A-D]\b/i' => '',
    '/\s*corresponding to Option\s+[A-D]\b/i' => '',
    '/\bOption\s+[A-D]\s+is\s+correct\b\.?/i' => '',
    '/\bOption\s+[A-D]\s+is\s+the\s+correct\s+answer\b\.?/i' => '',
    '/\bOption\s+[A-D]\s+is\s+the\s+right\s+answer\b\.?/i' => '',
    '/\bOption\s+[A-D]\b/i' => 'the correct option',
    '/\bthe correct answer is\s+[A-D]\b/i' => 'the correct answer',
    '/\banswer is\s+[A-D]\b/i' => 'the answer',
];

$remainingWithLetters = [];

foreach ($all as $q) {
    $exp = $q['explanation'];
    
    // Clean specific phrases
    $clean = preg_replace('/\s*\(\s*Option\s+[A-D]\s*\)\.?/i', '.', $exp);
    $clean = preg_replace('/\s*\[\s*Option\s+[A-D]\s*\]\.?/i', '.', $clean);
    $clean = preg_replace('/,\s*which\s+is\s+Option\s+[A-D]\.?/i', '.', $clean);
    $clean = preg_replace('/\s*which\s+corresponds\s+to\s+Option\s+[A-D]\.?/i', '.', $clean);
    $clean = preg_replace('/\s*corresponding\s+to\s+Option\s+[A-D]\.?/i', '.', $clean);
    $clean = preg_replace('/\bHence,?\s*Option\s+[A-D]\s+is\s+correct\.?/i', '', $clean);
    $clean = preg_replace('/\bTherefore,?\s*Option\s+[A-D]\s+is\s+correct\.?/i', '', $clean);
    $clean = preg_replace('/\bOption\s+[A-D]\s+is\s+correct\.?/i', '', $clean);
    $clean = preg_replace('/\bOption\s+[A-D]\s+is\s+the\s+correct\s+choice\.?/i', '', $clean);
    $clean = preg_replace('/\bOption\s+[A-D]\s+is\s+the\s+correct\s+answer\.?/i', '', $clean);
    $clean = preg_replace('/\bOption\s+[A-D]\b/i', 'the correct option', $clean);
    $clean = preg_replace('/\banswer\s+is\s+([A-D])\b/i', 'answer is correct', $clean);
    
    // Normalize double dots or spaces
    $clean = preg_replace('/\s*\.\s*\./', '.', $clean);
    $clean = preg_replace('/\s+/', ' ', $clean);
    $clean = trim($clean);

    // Check if any mention of Option [A-D] or isolated (A), (B), (C), (D) or "answer is [A-D]" remains
    if (preg_match('/\bOption\s+[A-D]\b/i', $clean) ||
        preg_match('/\banswer\s+is\s+[A-D]\b/i', $clean) ||
        preg_match('/\b(choice|select)\s+[A-D]\b/i', $clean) ||
        preg_match('/\([A-D]\)/', $clean)) {
        $remainingWithLetters[] = [
            'id' => $q['id'],
            'original' => $exp,
            'cleaned' => $clean
        ];
    }
}

echo "Remaining with letters: " . count($remainingWithLetters) . "\n";
if (!empty($remainingWithLetters)) {
    echo "First 5 examples:\n";
    for ($i = 0; $i < min(5, count($remainingWithLetters)); $i++) {
        print_r($remainingWithLetters[$i]);
    }
}
