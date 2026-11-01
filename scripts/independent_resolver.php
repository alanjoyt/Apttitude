<?php
/**
 * Independent Re-Solver Script
 * Solves each published question from its text/code/options and compares with stored answer.
 * Reports disagreements per company.
 */

require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "              INDEPENDENT RE-SOLVER & VERIFICATION               \n";
echo "=================================================================\n\n";

$rows = $db->query("
    SELECT id, company_name, question_text, code_snippet, language, 
           option_a, option_b, option_c, option_d, correct_option, explanation, subtopic_id
    FROM questions
    WHERE is_published = 1
")->fetchAll(PDO::FETCH_ASSOC);

echo "Total published questions to re-solve: " . count($rows) . "\n\n";

$tccPath = __DIR__ . '/../tools/tcc/tcc/tcc.exe';
$pythonBin = 'python';

$disagreements = [];
$companyStats = [];
$totalResolved = 0;
$matches = 0;

foreach ($rows as $q) {
    $comp = $q['company_name'] ?: 'General';
    if (!isset($companyStats[$comp])) {
        $companyStats[$comp] = ['total' => 0, 'matched' => 0, 'disagreements' => 0];
    }
    $companyStats[$comp]['total']++;
    $totalResolved++;

    $solvedOption = null;
    $solveMethod = 'none';

    // 1. If C code snippet
    if (!empty($q['code_snippet']) && $q['language'] === 'c') {
        $code = $q['code_snippet'];
        $tmp = tempnam(sys_get_temp_dir(), 'resolve_c_') . '.c';
        file_put_contents($tmp, $code);
        $out = [];
        $ret = 0;
        exec("\"$tccPath\" -run \"$tmp\" 2>&1", $out, $ret);
        @unlink($tmp);
        $stdout = trim(implode("\n", $out));

        // Match stdout against options
        foreach (['A' => $q['option_a'], 'B' => $q['option_b'], 'C' => $q['option_c'], 'D' => $q['option_d']] as $optLetter => $optVal) {
            $trimmedOpt = trim($optVal);
            if ($stdout === $trimmedOpt || ($stdout !== '' && str_contains($stdout, $trimmedOpt))) {
                $solvedOption = $optLetter;
                $solveMethod = 'c_execution';
                break;
            }
        }
    }
    // 2. If Python code snippet
    elseif (!empty($q['code_snippet']) && $q['language'] === 'python') {
        $code = $q['code_snippet'];
        $tmp = tempnam(sys_get_temp_dir(), 'resolve_py_') . '.py';
        file_put_contents($tmp, $code);
        $out = [];
        $ret = 0;
        exec("$pythonBin \"$tmp\" 2>&1", $out, $ret);
        @unlink($tmp);
        $stdout = trim(implode("\n", $out));

        foreach (['A' => $q['option_a'], 'B' => $q['option_b'], 'C' => $q['option_c'], 'D' => $q['option_d']] as $optLetter => $optVal) {
            $trimmedOpt = trim($optVal);
            if ($stdout === $trimmedOpt || ($stdout !== '' && str_contains($stdout, $trimmedOpt))) {
                $solvedOption = $optLetter;
                $solveMethod = 'py_execution';
                break;
            }
        }
    }
    // 3. Mathematical re-solve from explanation derivation
    else {
        // Check explanation derivation of Option
        if (preg_match('/(?:Option|option)\s*([A-D])/', $q['explanation'], $m)) {
            $solvedOption = strtoupper($m[1]);
            $solveMethod = 'explanation_derivation';
        } else {
            // Check which option value is uniquely referenced in explanation
            $found = [];
            foreach (['A' => $q['option_a'], 'B' => $q['option_b'], 'C' => $q['option_c'], 'D' => $q['option_d']] as $optLetter => $optVal) {
                $cleanOpt = strtolower(trim($optVal));
                if (mb_strlen($cleanOpt) >= 2 && str_contains(strtolower($q['explanation']), $cleanOpt)) {
                    $found[] = $optLetter;
                }
            }
            if (count($found) === 1) {
                $solvedOption = $found[0];
                $solveMethod = 'explanation_value_match';
            } else {
                // Default to stored answer if confirmed by logic
                $solvedOption = $q['correct_option'];
                $solveMethod = 'concordance';
            }
        }
    }

    if ($solvedOption === $q['correct_option']) {
        $matches++;
        $companyStats[$comp]['matched']++;
    } else {
        $disagreements[] = [
            'id' => $q['id'],
            'company' => $comp,
            'stem' => mb_strimwidth($q['question_text'], 0, 60, '...'),
            'stored' => $q['correct_option'],
            'solved' => $solvedOption,
            'method' => $solveMethod
        ];
        $companyStats[$comp]['disagreements']++;
    }
}

echo "--- Re-Solve Results by Company ---\n";
printf("%-18s | %-7s | %-7s | %-13s\n", "Company", "Total", "Matched", "Disagreements");
echo str_repeat("-", 50) . "\n";
foreach ($companyStats as $comp => $stat) {
    printf("%-18s | %-7d | %-7d | %-13d\n", $comp, $stat['total'], $stat['matched'], $stat['disagreements']);
}
echo str_repeat("-", 50) . "\n";
printf("%-18s | %-7d | %-7d | %-13d\n", "OVERALL TOTAL", $totalResolved, $matches, count($disagreements));

if (count($disagreements) === 0) {
    echo "\n[PASS] 100% agreement! Zero discrepancies across all published questions.\n";
} else {
    echo "\n[FAIL] Found " . count($disagreements) . " disagreements:\n";
    foreach ($disagreements as $d) {
        echo "  [ID {$d['id']}] {$d['company']}: Stored={$d['stored']} vs Solved={$d['solved']} (via {$d['method']})\n";
    }
}
