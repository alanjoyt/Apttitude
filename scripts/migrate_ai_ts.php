<?php
/**
 * Migration Script: Add TypeScript, LLM, and AI Interview Questions
 * Safe and idempotent.
 */

if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><title>Apttitudee Migration</title></head><body style="background:#111;color:#eee;font-family:monospace;padding:24px;line-height:1.6;"><pre>';
}

require_once __DIR__ . '/../config/db.php';

echo "=================================================================\n";
echo "  APTTITUDEE - TYPESCRIPT, LLM & AI QUESTIONS MIGRATION\n";
echo "=================================================================\n\n";

try {
    $db = Database::getConnection();

    // 1. Ensure Technical Assessment topic exists
    $topicStmt = $db->prepare("SELECT id FROM topics WHERE code = 'TECHNICAL'");
    $topicStmt->execute();
    $techTopicId = $topicStmt->fetchColumn();

    if (!$techTopicId) {
        $db->prepare("INSERT INTO topics (name, code, icon) VALUES ('Technical Assessment', 'TECHNICAL', 'bi-code-slash')")->execute();
        $techTopicId = (int)$db->lastInsertId();
        echo "[+] Created Topic 'Technical Assessment' (ID: {$techTopicId})\n";
    } else {
        echo "[.] Found Topic 'Technical Assessment' (ID: {$techTopicId})\n";
    }

    // 2. Ensure new subtopics exist
    $subtopicsToAdd = [
        ['name' => 'TypeScript & Modern JS', 'code' => 'TECH_TYPESCRIPT'],
        ['name' => 'Large Language Models (LLM) & RAG', 'code' => 'TECH_LLM'],
        ['name' => 'Artificial Intelligence & Generative AI', 'code' => 'TECH_AI']
    ];

    foreach ($subtopicsToAdd as $st) {
        $checkStmt = $db->prepare("SELECT id FROM subtopics WHERE code = ?");
        $checkStmt->execute([$st['code']]);
        $subId = $checkStmt->fetchColumn();
        if (!$subId) {
            $insStmt = $db->prepare("INSERT INTO subtopics (topic_id, name, code) VALUES (?, ?, ?)");
            $insStmt->execute([$techTopicId, $st['name'], $st['code']]);
            $subId = (int)$db->lastInsertId();
            echo "[+] Created subtopic '{$st['name']}' ({$st['code']}) -> ID: {$subId}\n";
        } else {
            echo "[.] Subtopic '{$st['name']}' ({$st['code']}) already exists (ID: {$subId})\n";
        }
    }

    // 3. Ensure questions table has required columns before inserting
    echo "\n[+] Verifying database table schema...\n";
    $cols = $db->query("SHOW COLUMNS FROM `questions`")->fetchAll(PDO::FETCH_COLUMN);
    $requiredCols = [
        'is_published' => "TINYINT(1) DEFAULT 1",
        'verification_status' => "ENUM('practice_style', 'verified_pyq', 'student_reported') DEFAULT 'practice_style'",
        'verification_level' => "VARCHAR(32) DEFAULT 'standard'",
        'company_name' => "VARCHAR(50) DEFAULT NULL",
        'content_hash' => "VARCHAR(64) DEFAULT NULL",
        'round_pattern' => "VARCHAR(128) DEFAULT NULL"
    ];

    foreach ($requiredCols as $colName => $colDef) {
        if (!in_array($colName, $cols)) {
            try {
                $db->exec("ALTER TABLE `questions` ADD COLUMN `{$colName}` {$colDef}");
                echo "  [+] Added missing column '{$colName}' to `questions` table.\n";
            } catch (\Throwable $e) {
                echo "  [!] Note on column '{$colName}': " . $e->getMessage() . "\n";
            }
        }
    }

    // 4. Ingest questions from database/migrations/20261030_add_typescript_llm_ai_questions.sql
    $sqlFile = __DIR__ . '/../database/migrations/20261030_add_typescript_llm_ai_questions.sql';
    if (file_exists($sqlFile)) {
        echo "\n[+] Executing migration SQL file...\n";
        $sqlContent = file_get_contents($sqlFile);
        preg_match_all('/INSERT INTO `questions`[\s\S]+?LIMIT 1;/i', $sqlContent, $matches);
        $queries = $matches[0] ?? [];
        
        $inserted = 0;
        $errors = [];
        foreach ($queries as $qSql) {
            $qSql = trim($qSql);
            if (empty($qSql)) continue;
            try {
                $affected = $db->exec($qSql);
                if ($affected > 0) {
                    $inserted++;
                }
            } catch (\Throwable $e) {
                if (count($errors) < 5) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        echo "[+] Successfully processed migration. Questions newly inserted into DB: {$inserted}\n";

        if (!empty($errors)) {
            echo "\n[!] Errors/Warnings encountered during insertion:\n";
            foreach ($errors as $err) {
                echo "    - " . htmlspecialchars($err) . "\n";
            }
        }
    }

    // 5. Verification Report: Print total question counts per subtopic
    echo "\n-----------------------------------------------------------------\n";
    echo "  CURRENT QUESTION COUNTS IN DATABASE:\n";
    echo "-----------------------------------------------------------------\n";
    $countStmt = $db->query("SELECT s.id, s.name, COUNT(q.id) as cnt FROM subtopics s LEFT JOIN questions q ON s.id = q.subtopic_id WHERE s.id IN (25, 26, 27) GROUP BY s.id ORDER BY s.id");
    while ($row = $countStmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  [*] Subtopic {$row['id']} ({$row['name']}): {$row['cnt']} questions available\n";
    }

    echo "\n>>> MIGRATION COMPLETE! <<<\n";

} catch (\Throwable $e) {
    echo "\n[!] Fatal Migration Error: " . $e->getMessage() . "\n";
    echo "SQL script is saved at: database/migrations/20261030_add_typescript_llm_ai_questions.sql\n";
}

if (php_sapi_name() !== 'cli') {
    echo '</pre></body></html>';
}
