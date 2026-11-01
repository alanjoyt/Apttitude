<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

function normalizeText($str) {
    $str = preg_replace('/^\[[^\]]+\]\s*/', '', $str);
    $str = strtolower($str);
    $str = str_replace(['```c', '```cpp', '```java', '```python', '```sql', '```'], '', $str);
    $str = preg_replace('/[0-9]+/', '#', $str);
    $str = preg_replace('/[^a-z#\s]/', ' ', $str);
    $str = preg_replace('/\s+/', ' ', $str);
    return trim($str);
}

foreach (['Capgemini', 'TCS NQT', 'Cognizant', 'Accenture'] as $comp) {
    $stmt = $db->prepare("SELECT id, question_text, is_published FROM questions WHERE company_name = ? ORDER BY id");
    $stmt->execute([$comp]);
    $qs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $seen = [];
    echo "=== $comp ===\n";
    foreach ($qs as $q) {
        $norm = normalizeText($q['question_text']);
        if (isset($seen[$norm])) {
            echo "Match found:\n";
            echo "  Original ID {$seen[$norm]['id']}: {$seen[$norm]['question_text']}\n";
            echo "  Duplicate ID {$q['id']}: {$q['question_text']}\n\n";
        } else {
            $seen[$norm] = $q;
        }
    }
}
