<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$rows = $db->query("
    SELECT COALESCE(language, 'none') as lang, COUNT(*) as cnt,
           SUM(CASE WHEN code_snippet IS NOT NULL AND TRIM(code_snippet) != '' THEN 1 ELSE 0 END) as with_code
    FROM questions 
    WHERE is_published = 1 
    GROUP BY language
")->fetchAll(PDO::FETCH_ASSOC);

echo "Published questions by language:\n";
foreach ($rows as $r) {
    printf("  %-12s : %4d (with code snippet: %d)\n", $r['lang'], $r['cnt'], $r['with_code']);
}

$draftRows = $db->query("
    SELECT COALESCE(language, 'none') as lang, COUNT(*) as cnt,
           SUM(CASE WHEN code_snippet IS NOT NULL AND TRIM(code_snippet) != '' THEN 1 ELSE 0 END) as with_code
    FROM questions 
    WHERE is_published = 0
    GROUP BY language
")->fetchAll(PDO::FETCH_ASSOC);

echo "\nDraft questions by language:\n";
foreach ($draftRows as $r) {
    printf("  %-12s : %4d (with code snippet: %d)\n", $r['lang'], $r['cnt'], $r['with_code']);
}
