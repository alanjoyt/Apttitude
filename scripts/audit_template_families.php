<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "              TEMPLATE FAMILY DISTRIBUTION AUDIT                 \n";
echo "=================================================================\n\n";

$rows = $db->query("
    SELECT id, company_name, question_text, subtopic_id, difficulty
    FROM questions
    WHERE is_published = 1 AND company_name IS NOT NULL
    ORDER BY company_name, id
")->fetchAll(PDO::FETCH_ASSOC);

function detectTemplateFamily($text) {
    $t = strtolower($text);
    if (str_contains($t, 'is coded as') && (str_contains($t, '+1') || str_contains($t, 'incrementing each character by 1') || str_contains($t, 'shifting each letter forward by one'))) return 'coding_shift_plus_1';
    if (str_contains($t, 'is coded as') && (str_contains($t, '+2') || str_contains($t, 'fcvc') || str_contains($t, 'shifted forward by 2'))) return 'coding_shift_plus_2';
    if (str_contains($t, 'is represented by') || str_contains($t, 'alphabet is added to') || str_contains($t, 'numeric representation of')) return 'coding_letter_sum';
    if (str_contains($t, 'sit around a') || str_contains($t, 'sit around the') || str_contains($t, 'circular table') || str_contains($t, 'square table')) return 'seating_circle_or_table';
    if (str_contains($t, 'sit in a row') || str_contains($t, 'seated in a row')) return 'seating_linear_row';
    if (str_contains($t, 'pointing to a') || str_contains($t, 'introducing a') || str_contains($t, 'says: \'his') || str_contains($t, 'says: \"his')) return 'blood_relation_pointing';
    if (str_contains($t, 'ratio of ages') || str_contains($t, 'present age')) return 'quant_ages_ratio';
    if (str_contains($t, 'marks an item') || str_contains($t, 'marked price') || str_contains($t, 'discount')) return 'quant_markup_discount';
    if (str_contains($t, 'pipe a fills') || str_contains($t, 'pump x fills') || str_contains($t, 'empties it in')) return 'quant_pipes_cisterns';
    if (str_contains($t, 'working together can complete') || str_contains($t, 'can do a work in')) return 'quant_time_work';
    if (str_contains($t, 'average speed') || str_contains($t, 'returns along the same route')) return 'quant_average_speed';
    if (str_contains($t, 'drives') && str_contains($t, 'east') && str_contains($t, 'north')) return 'reasoning_direction_drive';
    if (str_contains($t, 'walks') && str_contains($t, 'north') && str_contains($t, 'east')) return 'reasoning_direction_walk';
    if (str_contains($t, 'number series') || str_contains($t, 'missing term in the sequence') || str_contains($t, 'complete the pattern') || str_contains($t, 'complete the number series')) return 'reasoning_number_series';
    if (str_starts_with($t, 'statements:') || str_contains($t, 'statements:\n') || str_contains($t, 'given the statements')) return 'reasoning_syllogism';
    if (str_contains($t, 'synonym of') || str_contains($t, 'closest synonym')) return 'verbal_synonym';
    if (str_contains($t, 'antonym of') || str_contains($t, 'most nearly opposite')) return 'verbal_antonym';
    if (str_contains($t, 'identify the error') || str_contains($t, 'identify the grammatical error')) return 'verbal_error_spotting';
    if (str_contains($t, 'read:') || str_contains($t, 'passage:') || str_contains($t, 'read the statement:') || str_contains($t, 'read the passage:') || str_contains($t, 'read the summary:')) return 'verbal_reading_comprehension';
    if (str_contains($t, 'what will be the exact console output of this c') || str_contains($t, 'what does this c code print') || str_contains($t, 'what will be the console output of this c')) return 'tech_c_execution';
    if (str_contains($t, 'what will be the output of the following python') || str_contains($t, 'what does python print') || str_contains($t, 'what does this python')) return 'tech_python_execution';
    if (str_contains($t, 'time complexity') || str_contains($t, 'space complexity') || str_contains($t, 'balanced bst') || str_contains($t, 'min-heap')) return 'tech_dsa_complexity';
    if (str_contains($t, 'sql clause') || str_contains($t, 'sql command') || str_contains($t, 'sql query')) return 'tech_sql_concept';
    if (str_contains($t, 'deadlock') || str_contains($t, 'paging') || str_contains($t, 'context switch') || str_contains($t, 'operating system')) return 'tech_os_core';
    if (str_contains($t, 'acid') || str_contains($t, 'normalization') || str_contains($t, 'database') || str_contains($t, 'foreign key')) return 'tech_dbms_core';
    
    return 'domain_specific_other';
}

$byCompany = [];
$familyCompanies = [];

foreach ($rows as $r) {
    $comp = $r['company_name'];
    $fam = detectTemplateFamily($r['question_text']);
    $byCompany[$comp][$fam] = ($byCompany[$comp][$fam] ?? 0) + 1;
    $familyCompanies[$fam][$comp] = true;
}

echo "--- Top 5 Template Families Per Company ---\n\n";
foreach ($byCompany as $comp => $families) {
    arsort($families);
    $totalComp = array_sum($families);
    echo "Company: $comp (Total: $totalComp)\n";
    $i = 0;
    foreach ($families as $fam => $cnt) {
        $pct = round(($cnt / $totalComp) * 100, 1);
        $flag = $pct > 8.0 && $fam !== 'domain_specific_other' ? " [>8% HIGH]" : "";
        echo "  " . str_pad($fam, 32) . " : " . str_pad($cnt, 3, " ", STR_PAD_LEFT) . " (" . $pct . "%)" . $flag . "\n";
        if (++$i >= 5) break;
    }
    echo "\n";
}

echo "--- Template Families Appearing in > 3 Companies ---\n";
foreach ($familyCompanies as $fam => $compList) {
    $compNames = array_keys($compList);
    if (count($compNames) > 3 && $fam !== 'domain_specific_other') {
        echo "Family: " . str_pad($fam, 30) . " in " . count($compNames) . " companies (" . implode(', ', array_slice($compNames, 0, 5)) . "...)\n";
    }
}
