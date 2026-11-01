<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "       WITHIN-COMPANY DUPLICATE & NEAR-DUPLICATE AUDIT          \n";
echo "=================================================================\n\n";

// First, clean up the accidental test batch in Innovature
$accidentalBatch = 'BATCH_20261019_045649_ca1455';
$deletedAccidental = $db->exec("DELETE FROM questions WHERE import_batch_id = '{$accidentalBatch}'");
echo "Cleaned accidental test import batch '{$accidentalBatch}': removed {$deletedAccidental} duplicate questions.\n\n";

// Now query all published questions grouped by company
$companies = $db->query("SELECT DISTINCT company_name FROM questions WHERE is_published = 1 AND company_name IS NOT NULL ORDER BY company_name")->fetchAll(PDO::FETCH_COLUMN);

function normalizeText($str) {
    // Strip company tags like [TCS NQT Style Practice]
    $str = preg_replace('/^\[[^\]]+\]\s*/', '', $str);
    // Lowercase
    $str = strtolower($str);
    // Remove code blocks markers
    $str = str_replace(['```c', '```cpp', '```java', '```python', '```sql', '```'], '', $str);
    // Remove numbers and punctuation to detect number-swapped duplicates
    $str = preg_replace('/[0-9]+/', '#', $str);
    $str = preg_replace('/[^a-z#\s]/', ' ', $str);
    $str = preg_replace('/\s+/', ' ', $str);
    return trim($str);
}

$totalDuplicatesUnpublished = 0;
$report = [];

foreach ($companies as $comp) {
    $stmt = $db->prepare("SELECT id, question_text, difficulty, subtopic_id, verified_by_script, is_published FROM questions WHERE company_name = ? AND is_published = 1 ORDER BY id");
    $stmt->execute([$comp]);
    $qs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $seenExact = [];
    $seenNormalized = [];
    $duplicates = [];

    foreach ($qs as $q) {
        $id = $q['id'];
        $exact = strtolower(trim(preg_replace('/^\[[^\]]+\]\s*/', '', $q['question_text'])));
        $norm = normalizeText($q['question_text']);

        if (isset($seenExact[$exact])) {
            $duplicates[] = [
                'type' => 'exact',
                'id' => $id,
                'orig_id' => $seenExact[$exact],
                'text' => substr($q['question_text'], 0, 70)
            ];
            continue;
        }

        if (isset($seenNormalized[$norm])) {
            $duplicates[] = [
                'type' => 'near_duplicate',
                'id' => $id,
                'orig_id' => $seenNormalized[$norm],
                'text' => substr($q['question_text'], 0, 70)
            ];
            continue;
        }

        $seenExact[$exact] = $id;
        $seenNormalized[$norm] = $id;
    }

    // Unpublish duplicates if any found
    if (!empty($duplicates)) {
        $unpubStmt = $db->prepare("UPDATE questions SET is_published = 0 WHERE id = ?");
        foreach ($duplicates as $d) {
            $unpubStmt->execute([$d['id']]);
            $totalDuplicatesUnpublished++;
        }
    }

    // Also unpublish any question where verified_by_script = 0
    $unverifiedStmt = $db->prepare("UPDATE questions SET is_published = 0 WHERE company_name = ? AND is_published = 1 AND verified_by_script = 0");
    $unverifiedStmt->execute([$comp]);
    $unverifiedCount = $unverifiedStmt->rowCount();
    $totalDuplicatesUnpublished += $unverifiedCount;

    // Remaining count
    $remCount = $db->prepare("SELECT COUNT(*) FROM questions WHERE company_name = ? AND is_published = 1");
    $remCount->execute([$comp]);
    $count = (int)$remCount->fetchColumn();

    $report[] = [
        'company' => $comp,
        'original_total' => count($qs),
        'duplicates_found' => count($duplicates),
        'unverified_found' => $unverifiedCount,
        'final_published' => $count,
        'duplicate_samples' => array_slice($duplicates, 0, 2)
    ];
}

echo "Summary of Within-Company Duplicate Audit:\n";
echo str_pad("Company", 18) . " | " . str_pad("Scanned", 8) . " | " . str_pad("Duplicates", 10) . " | " . str_pad("Unverified", 10) . " | " . str_pad("Final Published", 15) . "\n";
echo str_repeat("-", 75) . "\n";
foreach ($report as $r) {
    echo str_pad($r['company'], 18) . " | " . 
         str_pad($r['original_total'], 8, " ", STR_PAD_LEFT) . " | " . 
         str_pad($r['duplicates_found'], 10, " ", STR_PAD_LEFT) . " | " . 
         str_pad($r['unverified_found'], 10, " ", STR_PAD_LEFT) . " | " . 
         str_pad($r['final_published'], 15, " ", STR_PAD_LEFT) . "\n";
}
echo "\nTotal questions unpublished in audit: {$totalDuplicatesUnpublished}\n";
