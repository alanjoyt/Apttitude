<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "       BALANCED DATABASE OPTION SHUFFLING & REMAPPING            \n";
echo "=================================================================\n\n";

// SAFETY GUARD: Refuse to run unless a backup newer than 10 minutes exists
$backupDir = __DIR__ . '/../backups';
$recentBackupFound = false;
$mostRecentTime = 0;
$mostRecentFile = '';

if (is_dir($backupDir)) {
    foreach (scandir($backupDir) as $file) {
        if (str_ends_with($file, '.sql')) {
            $mtime = filemtime($backupDir . '/' . $file);
            if ($mtime > $mostRecentTime) {
                $mostRecentTime = $mtime;
                $mostRecentFile = $file;
            }
        }
    }
}

$now = time();
$age = $now - $mostRecentTime;
if ($mostRecentTime === 0 || $age > 600) {
    $ageMin = round($age / 60, 1);
    die("ERROR: Safety guard triggered. No fresh database backup found within the last 10 minutes!\n" .
        "Most recent backup: " . ($mostRecentFile ?: 'None') . " ({$ageMin} minutes old).\n" .
        "Please run `php scripts/backup_db.php` first before running option shuffling.\n");
}
echo "[SAFETY CHECK PASSED] Recent backup found: {$mostRecentFile} (" . round($age / 60, 1) . " minutes old).\n\n";

// Target: Every company and difficulty gets a balanced ~25% distribution across A, B, C, D
$companies = $db->query("SELECT DISTINCT company_name FROM questions WHERE is_published = 1 ORDER BY company_name")->fetchAll(PDO::FETCH_COLUMN);


$targetLetters = ['A', 'B', 'C', 'D'];
$updateStmt = $db->prepare("
    UPDATE questions 
    SET option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ?, explanation = ?
    WHERE id = ?
");

$totalUpdated = 0;

foreach ($companies as $compName) {
    $compParam = $compName ?: null;
    $stmt = $db->prepare("
        SELECT id, difficulty, option_a, option_b, option_c, option_d, correct_option, explanation
        FROM questions
        WHERE is_published = 1 AND (company_name = ? OR (company_name IS NULL AND ? IS NULL))
        ORDER BY difficulty, id
    ");
    $stmt->execute([$compParam, $compParam]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by difficulty to ensure balance within each difficulty tier
    $byDiff = [];
    foreach ($questions as $q) {
        $byDiff[$q['difficulty']][] = $q;
    }

    foreach ($byDiff as $diff => $qList) {
        // Shuffle the list order to randomize which specific question gets which letter
        shuffle($qList);
        $letterIdx = 0;

        foreach ($qList as $q) {
            $id = $q['id'];
            $targetLetter = $targetLetters[$letterIdx % 4];
            $letterIdx++;

            $currentOptions = [
                'A' => $q['option_a'],
                'B' => $q['option_b'],
                'C' => $q['option_c'],
                'D' => $q['option_d'],
            ];

            $oldCorrectLetter = strtoupper($q['correct_option'] ?: 'A');
            $correctText = $currentOptions[$oldCorrectLetter] ?? $currentOptions['A'];

            // Generate new options map with $correctText placed at $targetLetter
            $otherLetters = array_diff($targetLetters, [$targetLetter]);
            $otherTexts = [];
            foreach ($targetLetters as $l) {
                if ($l !== $oldCorrectLetter) {
                    $otherTexts[] = $currentOptions[$l];
                }
            }
            shuffle($otherTexts);

            $newOptions = [];
            $newOptions[$targetLetter] = $correctText;
            $idx = 0;
            foreach ($otherLetters as $l) {
                $newOptions[$l] = $otherTexts[$idx++];
            }

            // Remap explanation: replace references to Option A / old letter with target letter
            $explanation = $q['explanation'];
            // If explanation mentions Option A or the old letter
            $explanation = preg_replace('/\bOption\s+[A-D]\b/i', 'Option ' . $targetLetter, $explanation);
            $explanation = preg_replace('/\(Option\s+[A-D]\)/i', '(Option ' . $targetLetter . ')', $explanation);

            $updateStmt->execute([
                $newOptions['A'],
                $newOptions['B'],
                $newOptions['C'],
                $newOptions['D'],
                $targetLetter,
                $explanation,
                $id
            ]);
            $totalUpdated++;
        }
    }
}

echo "Successfully shuffled and remapped $totalUpdated published questions in DB.\n\n";

// Also shuffle draft questions so they don't hold 100% A
$draftQuestions = $db->query("
    SELECT id, difficulty, option_a, option_b, option_c, option_d, correct_option, explanation
    FROM questions
    WHERE is_published = 0
")->fetchAll(PDO::FETCH_ASSOC);

$draftUpdated = 0;
$letterIdx = 0;
foreach ($draftQuestions as $q) {
    $id = $q['id'];
    $targetLetter = $targetLetters[$letterIdx % 4];
    $letterIdx++;

    $currentOptions = [
        'A' => $q['option_a'],
        'B' => $q['option_b'],
        'C' => $q['option_c'],
        'D' => $q['option_d'],
    ];

    $oldCorrectLetter = strtoupper($q['correct_option'] ?: 'A');
    $correctText = $currentOptions[$oldCorrectLetter] ?? $currentOptions['A'];

    $otherLetters = array_diff($targetLetters, [$targetLetter]);
    $otherTexts = [];
    foreach ($targetLetters as $l) {
        if ($l !== $oldCorrectLetter) {
            $otherTexts[] = $currentOptions[$l];
        }
    }
    shuffle($otherTexts);

    $newOptions = [];
    $newOptions[$targetLetter] = $correctText;
    $idx = 0;
    foreach ($otherLetters as $l) {
        $newOptions[$l] = $otherTexts[$idx++];
    }

    $explanation = $q['explanation'];
    if ($explanation) {
        $explanation = preg_replace('/\bOption\s+[A-D]\b/i', 'Option ' . $targetLetter, $explanation);
        $explanation = preg_replace('/\(Option\s+[A-D]\)/i', '(Option ' . $targetLetter . ')', $explanation);
    }

    $updateStmt->execute([
        $newOptions['A'],
        $newOptions['B'],
        $newOptions['C'],
        $newOptions['D'],
        $targetLetter,
        $explanation,
        $id
    ]);
    $draftUpdated++;
}

echo "Successfully shuffled and remapped $draftUpdated draft questions in DB.\n";
