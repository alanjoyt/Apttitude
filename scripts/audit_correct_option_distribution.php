<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "           DISTRIBUTION OF CORRECT_OPTION IN PUBLISHED           \n";
echo "=================================================================\n\n";

$rows = $db->query("
    SELECT company_name, correct_option, COUNT(*) as cnt
    FROM questions
    WHERE is_published = 1
    GROUP BY company_name, correct_option
    ORDER BY company_name, correct_option
")->fetchAll(PDO::FETCH_ASSOC);

$byCompany = [];
$totalByLetter = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
$totalPublished = 0;

foreach ($rows as $r) {
    $comp = $r['company_name'] ?: 'General';
    $letter = strtoupper($r['correct_option']);
    $cnt = (int)$r['cnt'];
    $byCompany[$comp][$letter] = $cnt;
    $totalByLetter[$letter] = ($totalByLetter[$letter] ?? 0) + $cnt;
    $totalPublished += $cnt;
}

printf("%-18s | %-10s | %-10s | %-10s | %-10s | %-6s\n", "Company", "A", "B", "C", "D", "Total");
echo str_repeat("-", 75) . "\n";

foreach ($byCompany as $comp => $counts) {
    $cA = $counts['A'] ?? 0;
    $cB = $counts['B'] ?? 0;
    $cC = $counts['C'] ?? 0;
    $cD = $counts['D'] ?? 0;
    $compTotal = $cA + $cB + $cC + $cD;
    $pA = $compTotal ? round(($cA / $compTotal) * 100, 1) : 0;
    $pB = $compTotal ? round(($cB / $compTotal) * 100, 1) : 0;
    $pC = $compTotal ? round(($cC / $compTotal) * 100, 1) : 0;
    $pD = $compTotal ? round(($cD / $compTotal) * 100, 1) : 0;

    printf("%-18s | %3d (%4.1f%%) | %3d (%4.1f%%) | %3d (%4.1f%%) | %3d (%4.1f%%) | %4d\n",
        $comp, $cA, $pA, $cB, $pB, $cC, $pC, $cD, $pD, $compTotal);
}

echo str_repeat("-", 75) . "\n";
printf("%-18s | %3d (%4.1f%%) | %3d (%4.1f%%) | %3d (%4.1f%%) | %3d (%4.1f%%) | %4d\n",
    "TOTAL",
    $totalByLetter['A'], round(($totalByLetter['A'] / $totalPublished) * 100, 1),
    $totalByLetter['B'], round(($totalByLetter['B'] / $totalPublished) * 100, 1),
    $totalByLetter['C'], round(($totalByLetter['C'] / $totalPublished) * 100, 1),
    $totalByLetter['D'], round(($totalByLetter['D'] / $totalPublished) * 100, 1),
    $totalPublished);
