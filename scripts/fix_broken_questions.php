<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "Fixing Question 3012 (Zoho Syllogism missing conclusions)...\n";
$q3012Text = "Given the statements:\n1. All widgets are tools.\n2. Some tools are metal.\n\nConclusions:\nI. Some widgets are metal.\nII. Some tools are widgets.\n\nWhich of the following conclusions logically follows?";
$db->prepare("UPDATE questions SET question_text = ? WHERE id = 3012")->execute([$q3012Text]);
echo "Updated Q3012.\n";

echo "Fixing Question 1906 (Tech Mahindra stray space in options)...\n";
$db->prepare("
    UPDATE questions 
    SET option_a = 'NPPO', option_b = 'NPPN', option_c = 'NPPP', option_d = 'NPQO',
        explanation = REPLACE(explanation, 'NPP O', 'NPPO')
    WHERE id = 1906
")->execute();
echo "Updated Q1906.\n";

// Audit all questions for any other stray spaces in options (e.g. "[A-Z]{3,}\s+[A-Z]")
$rows = $db->query("SELECT id, option_a, option_b, option_c, option_d, explanation FROM questions")->fetchAll(PDO::FETCH_ASSOC);
$fixedSpaces = 0;
foreach ($rows as $r) {
    $updated = false;
    $opts = [
        'option_a' => $r['option_a'],
        'option_b' => $r['option_b'],
        'option_c' => $r['option_c'],
        'option_d' => $r['option_d'],
    ];
    foreach ($opts as $k => $val) {
        if (preg_match('/^([A-Z]{3,})\s+([A-Z])$/', trim($val), $m)) {
            $newVal = $m[1] . $m[2];
            $opts[$k] = $newVal;
            $updated = true;
        }
    }
    if ($updated) {
        $db->prepare("UPDATE questions SET option_a = ?, option_b = ?, option_c = ?, option_d = ? WHERE id = ?")
           ->execute([$opts['option_a'], $opts['option_b'], $opts['option_c'], $opts['option_d'], $r['id']]);
        $fixedSpaces++;
    }
}
echo "Cleaned stray spaces in $fixedSpaces additional questions.\n";
