<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "       STRIPPING OPTION LETTER REFERENCES FROM EXPLANATIONS      \n";
echo "=================================================================\n\n";

$stmt = $db->query("SELECT id, explanation, option_a, option_b, option_c, option_d, correct_option FROM questions WHERE is_published = 1");
$all = $stmt->fetchAll(PDO::FETCH_ASSOC);

$countFound = 0;
$countUpdated = 0;

$updateStmt = $db->prepare("UPDATE questions SET explanation = ? WHERE id = ?");

foreach ($all as $q) {
    $exp = $q['explanation'];
    
    // Check if mentions Option A/B/C/D
    if (preg_match('/\bOption\s+[A-D]\b/i', $exp) || 
        preg_match('/\(\s*Option\s+[A-D]\s*\)/i', $exp) ||
        preg_match('/\bcorrect\s+answer\s+is\s+Option\s+[A-D]\b/i', $exp) ||
        preg_match('/\banswer\s+is\s+Option\s+[A-D]\b/i', $exp) ||
        preg_match('/\bcorrect\s+option\s+is\s+[A-D]\b/i', $exp)) {
        
        $countFound++;
        
        // Strip out letter references while preserving option text
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
        $clean = preg_replace('/\bOption\s+[A-D]\s+is\s+the\s+right\s+answer\.?/i', '', $clean);
        $clean = preg_replace('/\bcorrect\s+option\s+is\s+[A-D]\.?/i', 'correct option.', $clean);
        $clean = preg_replace('/\bOption\s+[A-D]\b/i', 'the correct option', $clean);
        
        // Clean double punctuation and spaces
        $clean = preg_replace('/\s*\.\s*\./', '.', $clean);
        $clean = preg_replace('/\s+/', ' ', $clean);
        $clean = trim($clean);

        if ($clean !== $exp) {
            $updateStmt->execute([$clean, $q['id']]);
            $countUpdated++;
        }
    }
}

echo "Total published explanations with option letters found: $countFound\n";
echo "Total explanations updated in DB: $countUpdated\n\n";

// Audit remaining
$remaining = $db->query("
    SELECT id, explanation 
    FROM questions 
    WHERE is_published = 1 AND (
        explanation REGEXP 'Option [A-D]' OR 
        explanation REGEXP '\\(Option [A-D]\\)'
    )
")->fetchAll(PDO::FETCH_ASSOC);

echo "Remaining published explanations mentioning 'Option [A-D]': " . count($remaining) . "\n";
if (count($remaining) > 0) {
    foreach ($remaining as $r) {
        echo "  - [ID {$r['id']}] {$r['explanation']}\n";
    }
} else {
    echo "[SUCCESS] Zero published explanations contain option letter references!\n";
}
