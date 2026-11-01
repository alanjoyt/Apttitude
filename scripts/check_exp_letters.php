<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$rows = $db->query("
    SELECT id, correct_option, explanation 
    FROM questions 
    WHERE is_published = 1 AND explanation REGEXP 'Option [A-D]'
")->fetchAll(PDO::FETCH_ASSOC);

$mismatches = 0;
foreach ($rows as $r) {
    if (preg_match('/Option\s+([A-D])/i', $r['explanation'], $m)) {
        $expLetter = strtoupper($m[1]);
        $correctLetter = strtoupper($r['correct_option']);
        if ($expLetter !== $correctLetter) {
            $mismatches++;
            // Fix it!
            $fixedExp = preg_replace('/Option\s+[A-D]/i', 'Option ' . $correctLetter, $r['explanation']);
            $db->prepare("UPDATE questions SET explanation = ? WHERE id = ?")->execute([$fixedExp, $r['id']]);
        }
    }
}
echo "Audited " . count($rows) . " explanations with Option [A-D]. Mismatches found and corrected: $mismatches\n";
