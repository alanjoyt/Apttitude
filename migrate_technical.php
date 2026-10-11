<?php
require_once __DIR__ . '/config/db.php';

echo "========================================================\n";
echo " Apttitudee - Technical & Hard Questions Schema Migration\n";
echo "========================================================\n";

try {
    $db = Database::getConnection();

    // 1. Add code_snippet and language columns to questions table if missing
    $cols = $db->query("DESCRIBE questions")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('code_snippet', $cols)) {
        $db->exec("ALTER TABLE `questions` ADD COLUMN `code_snippet` TEXT DEFAULT NULL AFTER `question_text`");
        echo "[+] Added `code_snippet` column to `questions`.\n";
    } else {
        echo "[.] `code_snippet` column already exists.\n";
    }

    if (!in_array('language', $cols)) {
        $db->exec("ALTER TABLE `questions` ADD COLUMN `language` VARCHAR(30) DEFAULT NULL AFTER `code_snippet`");
        $db->exec("ALTER TABLE `questions` ADD INDEX `idx_questions_language` (`language`)");
        echo "[+] Added `language` column and index to `questions`.\n";
    } else {
        echo "[.] `language` column already exists.\n";
    }

    // 2. Ensure Topic 5: Technical Assessment
    $topicStmt = $db->prepare("SELECT id FROM topics WHERE code = 'TECHNICAL'");
    $topicStmt->execute();
    $techTopicId = $topicStmt->fetchColumn();

    if (!$techTopicId) {
        $db->prepare("INSERT INTO topics (name, code, icon) VALUES ('Technical Assessment', 'TECHNICAL', 'bi-code-slash')")->execute();
        $techTopicId = (int)$db->lastInsertId();
        echo "[+] Created Topic 'Technical Assessment' (ID: {$techTopicId}).\n";
    } else {
        echo "[.] Topic 'Technical Assessment' exists (ID: {$techTopicId}).\n";
    }

    // 3. Ensure Subtopics under Technical Assessment
    $techSubtopics = [
        ['name' => 'C Programming', 'code' => 'TECH_C'],
        ['name' => 'C++ Object-Oriented', 'code' => 'TECH_CPP'],
        ['name' => 'Java Core & OOP', 'code' => 'TECH_JAVA'],
        ['name' => 'PHP & Web Architecture', 'code' => 'TECH_PHP'],
        ['name' => 'C# & .NET Platform', 'code' => 'TECH_CSHARP'],
        ['name' => 'Python Programming', 'code' => 'TECH_PYTHON'],
        ['name' => 'SQL & Relational Databases', 'code' => 'TECH_SQL'],
        ['name' => 'Data Structures & Algorithms', 'code' => 'TECH_DSA'],
        ['name' => 'Innovature Labs Technical & PYQ', 'code' => 'TECH_INNOVATURE']
    ];

    foreach ($techSubtopics as $st) {
        $check = $db->prepare("SELECT id FROM subtopics WHERE code = ?");
        $check->execute([$st['code']]);
        if (!$check->fetchColumn()) {
            $ins = $db->prepare("INSERT INTO subtopics (topic_id, name, code) VALUES (?, ?, ?)");
            $ins->execute([$techTopicId, $st['name'], $st['code']]);
            echo "[+] Created subtopic: {$st['name']} ({$st['code']}).\n";
        }
    }

    // 4. Ensure Subtopic for Advanced Logical Puzzles under Topic 2 (Logical Reasoning)
    $lrTopicId = $db->query("SELECT id FROM topics WHERE code = 'LR'")->fetchColumn() ?: 2;
    $checkLR = $db->prepare("SELECT id FROM subtopics WHERE code = 'LR_PUZZLES'");
    $checkLR->execute();
    if (!$checkLR->fetchColumn()) {
        $db->prepare("INSERT INTO subtopics (topic_id, name, code) VALUES (?, 'Advanced Logic Puzzles & Deductions', 'LR_PUZZLES')")
           ->execute([$lrTopicId]);
        echo "[+] Created subtopic: Advanced Logic Puzzles & Deductions (LR_PUZZLES).\n";
    }

    // 5. Ensure Subtopic for Advanced Quantitative under Topic 1 (Quantitative Aptitude)
    $quantTopicId = $db->query("SELECT id FROM topics WHERE code = 'QUANT'")->fetchColumn() ?: 1;
    $checkQuant = $db->prepare("SELECT id FROM subtopics WHERE code = 'QUANT_ADV'");
    $checkQuant->execute();
    if (!$checkQuant->fetchColumn()) {
        $db->prepare("INSERT INTO subtopics (topic_id, name, code) VALUES (?, 'Advanced Quantitative & Number Theory', 'QUANT_ADV')")
           ->execute([$quantTopicId]);
        echo "[+] Created subtopic: Advanced Quantitative & Number Theory (QUANT_ADV).\n";
    }

    // 6. Ensure Innovature Labs Mock Pack in unlockables
    $checkUnlock = $db->prepare("SELECT id FROM unlockables WHERE code = 'INNOVATURE_MOCK_PACK'");
    $checkUnlock->execute();
    if (!$checkUnlock->fetchColumn()) {
        $db->prepare("
            INSERT INTO unlockables (code, title, description, item_type, coin_cost, icon)
            VALUES ('INNOVATURE_MOCK_PACK', 'Innovature Labs Placement Mock Pack', 
                    'Simulated 30-question placement paper with authentic Innovature Labs technical & high-order reasoning questions.', 
                    'company_mock', 150, 'bi-building-fill-gear')
        ")->execute();
        echo "[+] Created unlockable item: Innovature Labs Placement Mock Pack.\n";
    }

    echo "\n>>> MIGRATION SUCCESSFUL! <<<\n";
} catch (Exception $e) {
    echo "ERROR during migration: " . $e->getMessage() . "\n";
}
