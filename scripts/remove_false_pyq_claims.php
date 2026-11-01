<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "              REMOVING FALSE PYQ & AUTHENTIC CLAIMS              \n";
echo "=================================================================\n\n";

// 1. Update questions table
$stmt = $db->query("
    SELECT id, question_text, source_tag, explanation, verification_status 
    FROM questions 
    WHERE question_text LIKE '%PYQ%' 
       OR source_tag LIKE '%PYQ%' 
       OR question_text LIKE '%Authentic%' 
       OR explanation LIKE '%Authentic%'
       OR question_text LIKE '%recruitment rounds%'
       OR explanation LIKE '%recruitment rounds%'
");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($questions) . " questions with PYQ/Authentic/recruitment round claims.\n";

$updateStmt = $db->prepare("
    UPDATE questions 
    SET question_text = ?, source_tag = ?, explanation = ?, verification_status = 'practice_style'
    WHERE id = ?
");

$updatedCount = 0;
foreach ($questions as $q) {
    $text = $q['question_text'];
    $source = $q['source_tag'] ?? '';
    $exp = $q['explanation'];

    // Clean question text
    $text = preg_replace('/\[([^\]]+?)\s+(?:Technical|Quant|Logic|Verbal)?\s*PYQ\]/i', '[$1 Style Practice]', $text);
    $text = str_replace('PYQ', 'Practice', $text);
    $text = str_replace('Authentic', 'Standard', $text);
    $text = str_replace('authentic', 'standard', $text);
    $text = str_replace('asked in recruitment rounds', 'modeled on recruitment rounds', $text);

    // Clean source tag
    $source = str_replace('PYQ', 'Practice Question', $source);
    $source = str_replace('Placement Paper', 'Practice Paper', $source);

    // Clean explanation
    $exp = str_replace('Authentic', 'Standard', $exp);
    $exp = str_replace('authentic', 'standard', $exp);
    $exp = str_replace('asked in recruitment rounds', 'modeled on recruitment rounds', $exp);

    $updateStmt->execute([$text, $source, $exp, $q['id']]);
    $updatedCount++;
}

echo "Cleaned $updatedCount questions in database.\n";

// Ensure all questions are marked practice_style
$db->exec("UPDATE questions SET verification_status = 'practice_style' WHERE verification_status = 'verified_pyq' OR verification_status IS NULL");
echo "Verified: 0 questions marked as verified_pyq.\n";
