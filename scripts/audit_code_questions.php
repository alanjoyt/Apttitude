<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$rows = $db->query("
    SELECT language, COUNT(*) as cnt, SUM(is_published) as pub 
    FROM questions 
    WHERE code_snippet IS NOT NULL AND TRIM(code_snippet) != '' 
    GROUP BY language
")->fetchAll(PDO::FETCH_ASSOC);

echo "Code Questions in DB by Language:\n";
foreach ($rows as $r) {
    printf("  %-12s : %3d total | %3d published\n", $r['language'] ?: 'unspecified', $r['cnt'], $r['pub']);
}

// Also count by company
$compRows = $db->query("
    SELECT company_name, language, COUNT(*) as cnt 
    FROM questions 
    WHERE code_snippet IS NOT NULL AND TRIM(code_snippet) != '' 
    GROUP BY company_name, language 
    ORDER BY company_name, language
")->fetchAll(PDO::FETCH_ASSOC);

echo "\nCode Questions by Company and Language:\n";
$currentCompany = '';
foreach ($compRows as $cr) {
    if ($cr['company_name'] !== $currentCompany) {
        $currentCompany = $cr['company_name'];
        echo ">>> {$currentCompany}:\n";
    }
    printf("    %-10s : %d\n", $cr['language'] ?: 'unspecified', $cr['cnt']);
}
