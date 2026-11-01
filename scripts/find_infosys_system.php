<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "--- Searching Database ---\n";
$stmt = $db->query("SELECT id, company_name, is_published, question_text, option_a, option_b, option_c, option_d, correct_option, explanation FROM questions WHERE question_text LIKE '%SYSTEM%' OR option_a LIKE '%PWUQV%' OR option_b LIKE '%PWUQV%' OR option_c LIKE '%PWUQV%' OR option_d LIKE '%PWUQV%'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Database matches found: " . count($rows) . "\n";
foreach ($rows as $r) {
    echo "ID: {$r['id']} | Comp: {$r['company_name']} | Published: {$r['is_published']}\n";
    echo "Q: {$r['question_text']}\n";
    echo "A: {$r['option_a']} | B: {$r['option_b']} | C: {$r['option_c']} | D: {$r['option_d']}\n";
    echo "Correct: {$r['correct_option']}\n\n";
}

echo "--- Searching files ---\n";
$files = [
    __DIR__ . '/five_samples_per_company.json',
    __DIR__ . '/../database/seeders/innovature_100_questions.json'
];
foreach ($files as $f) {
    if (file_exists($f)) {
        $content = file_get_contents($f);
        if (strpos($content, 'PWUQV') !== false || strpos($content, 'OUTPUT') !== false) {
            echo "Found match in file: $f\n";
        }
    }
}
