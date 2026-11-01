<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "       INSPECTING THE THREE BUG FIX QUESTIONS IN DATABASE        \n";
echo "=================================================================\n\n";

// 1. Capgemini question
echo "[1] Capgemini Coding Question:\n";
$stmt = $db->query("SELECT id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation FROM questions WHERE company_name = 'Capgemini' AND (question_text LIKE '%code%' OR question_text LIKE '%DARK%' OR question_text LIKE '%LIGHT%')");
$cap = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cap as $r) {
    echo "ID: {$r['id']}\n";
    echo "Question: {$r['question_text']}\n";
    echo "A: {$r['option_a']}\nB: {$r['option_b']}\nC: {$r['option_c']}\nD: {$r['option_d']}\n";
    echo "Correct: {$r['correct_option']}\n";
    echo "Explanation: {$r['explanation']}\n-----------------------------------\n";
}

// 2. Zoho syllogism (ID 3012)
echo "\n[2] Zoho Syllogism (ID 3012):\n";
$stmt = $db->query("SELECT id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation FROM questions WHERE id = 3012 OR (company_name = 'Zoho' AND question_text LIKE '%cameras%')");
$zoho = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($zoho as $r) {
    echo "ID: {$r['id']}\n";
    echo "Question: {$r['question_text']}\n";
    echo "A: {$r['option_a']}\nB: {$r['option_b']}\nC: {$r['option_c']}\nD: {$r['option_d']}\n";
    echo "Correct: {$r['correct_option']}\n";
    echo "Explanation: {$r['explanation']}\n-----------------------------------\n";
}

// 3. Infosys Coding Question
echo "\n[3] Infosys Coding Question:\n";
$stmt = $db->query("SELECT id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation FROM questions WHERE company_name = 'Infosys' AND (question_text LIKE '%code%' OR question_text LIKE '%cryptography%' OR question_text LIKE '%LOGIC%')");
$inf = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($inf as $r) {
    echo "ID: {$r['id']}\n";
    echo "Question: {$r['question_text']}\n";
    echo "A: {$r['option_a']}\nB: {$r['option_b']}\nC: {$r['option_c']}\nD: {$r['option_d']}\n";
    echo "Correct: {$r['correct_option']}\n";
    echo "Explanation: {$r['explanation']}\n-----------------------------------\n";
}
