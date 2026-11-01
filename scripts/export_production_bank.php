<?php
/**
 * Export Production Question Bank with Content Hashes
 */

require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "       EXPORTING PRODUCTION QUESTION BANK WITH CONTENT HASHES    \n";
echo "=================================================================\n\n";

function computeContentHash(?string $company, string $text): string {
    $cleanComp = strtolower(trim($company ?? ''));
    $cleanText = strtolower(trim(preg_replace('/^\[[^\]]+\]\s*/', '', $text)));
    $cleanText = preg_replace('/\s+/', ' ', $cleanText);
    return sha1($cleanComp . '|' . $cleanText);
}

$rows = $db->query("
    SELECT q.*, s.code as subtopic_code, s.name as subtopic_name
    FROM questions q
    LEFT JOIN subtopics s ON q.subtopic_id = s.id
    WHERE q.is_published = 1
    ORDER BY q.company_name, q.id
")->fetchAll(PDO::FETCH_ASSOC);

echo "Total published questions to export: " . count($rows) . "\n";

$updateHashStmt = $db->prepare("UPDATE questions SET content_hash = ? WHERE id = ?");

$exportData = [];
foreach ($rows as $q) {
    $hash = computeContentHash($q['company_name'], $q['question_text']);
    
    // Update hash in live DB
    $updateHashStmt->execute([$hash, $q['id']]);

    $exportData[] = [
        'content_hash' => $hash,
        'company_name' => $q['company_name'],
        'subtopic_code' => $q['subtopic_code'] ?? 'TECH_GENERAL',
        'subtopic_name' => $q['subtopic_name'] ?? 'General Technical',
        'difficulty' => $q['difficulty'],
        'question_text' => $q['question_text'],
        'code_snippet' => $q['code_snippet'],
        'language' => $q['language'],
        'option_a' => $q['option_a'],
        'option_b' => $q['option_b'],
        'option_c' => $q['option_c'],
        'option_d' => $q['option_d'],
        'correct_option' => $q['correct_option'],
        'explanation' => $q['explanation'],
        'est_time_seconds' => (int)$q['est_time_seconds'],
        'round_pattern' => $q['round_pattern'],
        'verification_status' => $q['verification_status'],
        'verification_level' => $q['verification_level']
    ];
}

$targetFile = __DIR__ . '/../database/seeders/production_question_bank.json';
file_put_contents($targetFile, json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
$fileSizeKb = round(filesize($targetFile) / 1024, 1);

echo "Exported " . count($exportData) . " questions to:\n  $targetFile ({$fileSizeKb} KB)\n\n";
