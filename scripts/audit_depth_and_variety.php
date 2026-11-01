<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "           DEPTH, VARIETY & DIFFICULTY DISTRIBUTION AUDIT        \n";
echo "=================================================================\n\n";

$rows = $db->query("
    SELECT q.id, q.company_name, q.difficulty, q.code_snippet, q.language, 
           q.question_text, s.name as subtopic_name, t.name as topic_name
    FROM questions q
    JOIN subtopics s ON q.subtopic_id = s.id
    JOIN topics t ON s.topic_id = t.id
    WHERE q.is_published = 1
    ORDER BY q.company_name, q.difficulty
")->fetchAll(PDO::FETCH_ASSOC);

$byCompany = [];
foreach ($rows as $r) {
    $comp = $r['company_name'] ?: 'General';
    if (!isset($byCompany[$comp])) {
        $byCompany[$comp] = [
            'total' => 0,
            'easy' => 0,
            'medium' => 0,
            'hard' => 0,
            'subtopics' => [],
            'weak_questions' => []
        ];
    }
    $byCompany[$comp]['total']++;
    $byCompany[$comp][$r['difficulty']]++;
    $byCompany[$comp]['subtopics'][$r['subtopic_name']] = ($byCompany[$comp]['subtopics'][$r['subtopic_name']] ?? 0) + 1;

    // Detect weak one-step questions in Zoho, Amazon, HCL, Innovature
    if (in_array($comp, ['Zoho', 'Amazon', 'HCL', 'Innovature'])) {
        $snippet = $r['code_snippet'] ?? '';
        if (preg_match('/printf\s*\(\s*"%d"\s*,\s*\d+\s*[\*\+\-\/]\s*\d+\s*\)/', $snippet) ||
            preg_match('/int\s+x\s*=\s*\d+\s*;\s*printf\s*\(\s*"%d"\s*,\s*x\s*\)/', $snippet) ||
            preg_match('/\(10\s*>\s*5\)\s*\?\s*\d+\s*:\s*\d+/', $snippet) ||
            preg_match('/len\(\[\]\)/', $snippet) ||
            preg_match('/type\(\[\]\)/', $r['question_text'])) {
            $byCompany[$comp]['weak_questions'][] = [
                'id' => $r['id'],
                'text' => $r['question_text'],
                'snippet' => $snippet
            ];
        }
    }
}

printf("%-18s | %-5s | %-12s | %-12s | %-12s | %-10s\n", "Company", "Total", "Easy (%)", "Medium (%)", "Hard (%)", "Subtopics");
echo str_repeat("-", 80) . "\n";

foreach ($byCompany as $comp => $d) {
    $tot = $d['total'];
    $pEasy = $tot ? round(($d['easy'] / $tot) * 100, 1) : 0;
    $pMed = $tot ? round(($d['medium'] / $tot) * 100, 1) : 0;
    $pHard = $tot ? round(($d['hard'] / $tot) * 100, 1) : 0;
    $subtopicCount = count($d['subtopics']);

    printf("%-18s | %5d | %3d (%4.1f%%) | %3d (%4.1f%%) | %3d (%4.1f%%) | %5d types\n",
        $comp, $tot, $d['easy'], $pEasy, $d['medium'], $pMed, $d['hard'], $pHard, $subtopicCount);
}

echo "\nWeak One-Step Questions Identified in Technical Companies:\n";
foreach (['Zoho', 'Amazon', 'HCL', 'Innovature'] as $comp) {
    $weaks = $byCompany[$comp]['weak_questions'] ?? [];
    echo "  $comp: " . count($weaks) . " weak questions\n";
    foreach ($weaks as $w) {
        echo "    - ID {$w['id']}: " . substr(str_replace("\n", " ", $w['snippet'] ?: $w['text']), 0, 70) . "...\n";
    }
}
