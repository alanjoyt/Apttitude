<?php
require_once __DIR__ . '/config/db.php';

echo "========================================================\n";
echo " Seeding 200+ Technical and Hard Questions from JSON\n";
echo "========================================================\n";

try {
    $db = Database::getConnection();

    $jsonPath = __DIR__ . '/data/technical_and_hard_questions.json';
    if (!file_exists($jsonPath)) {
        die("Error: JSON file not found at {$jsonPath}\n");
    }

    $raw = file_get_contents($jsonPath);
    $questions = json_decode($raw, true);

    if (!is_array($questions)) {
        die("Error: Invalid JSON structure\n");
    }

    // Clean existing technical/hard questions first to avoid duplicates
    $db->exec("DELETE FROM questions WHERE subtopic_id >= 14");
    echo "[+] Reset existing questions in subtopics 14+.\n";

    $stmt = $db->prepare("
        INSERT INTO `questions` 
        (`subtopic_id`, `difficulty`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `est_time_seconds`, `source_tag`, `is_company_pyq`, `company_name`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $inserted = 0;
    $techCount = 0;
    $innovatureCount = 0;

    foreach ($questions as $q) {
        $stmt->execute([
            $q['subtopic_id'],
            $q['difficulty'],
            $q['question_text'],
            $q['code_snippet'] ?? null,
            $q['language'] ?? null,
            $q['option_a'],
            $q['option_b'],
            $q['option_c'],
            $q['option_d'],
            $q['correct_option'],
            $q['explanation'],
            $q['est_time_seconds'] ?? 60,
            $q['source_tag'] ?? 'Technical Assessment',
            $q['is_company_pyq'] ?? 0,
            $q['company_name'] ?? null
        ]);
        $inserted++;

        if (in_array((int)$q['subtopic_id'], range(14, 22))) {
            $techCount++;
        }
        if ($q['language'] === 'innovature' || $q['company_name'] === 'Innovature Labs') {
            $innovatureCount++;
        }
    }

    echo ">>> SUCCESSFULLY INSERTED {$inserted} QUESTIONS! <<<\n";
    echo "    - Total Technical Questions (Subtopics 14-22): {$techCount}\n";
    echo "    - Innovature Labs PYQs: {$innovatureCount}\n";
    echo "    - Hard Logical Reasoning & Advanced Quant (Subtopics 23-24): " . ($inserted - $techCount) . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
