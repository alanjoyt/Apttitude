<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$rows = $db->query("
    SELECT id, question_text, code_snippet, correct_option, option_a, option_b, option_c, option_d 
    FROM questions 
    WHERE is_published = 1 AND company_name = 'Innovature' AND code_snippet IS NOT NULL
")->fetchAll(PDO::FETCH_ASSOC);

echo "Innovature published code questions: " . count($rows) . "\n";
$tccPath = __DIR__ . '/../tools/tcc/tcc/tcc.exe';

$cSuccess = 0;
$cFail = 0;
$nonC = 0;
$toDraft = [];

foreach ($rows as $r) {
    $code = $r['code_snippet'];
    $id = $r['id'];
    
    // Check if C code
    if (strpos($code, '#include') !== false || strpos($code, 'printf(') !== false || strpos($code, 'int main(') !== false) {
        // Run TCC
        $tmp = tempnam(sys_get_temp_dir(), 'tcc_') . '.c';
        file_put_contents($tmp, $code);
        $out = [];
        $ret = 0;
        exec("\"$tccPath\" -run \"$tmp\" 2>&1", $out, $ret);
        @unlink($tmp);
        $outputStr = trim(implode("\n", $out));
        
        $optKey = 'option_' . strtolower($r['correct_option']);
        $expected = trim($r[$optKey]);
        
        if ($ret === 0 && ($outputStr === $expected || str_contains($outputStr, $expected))) {
            $cSuccess++;
        } else {
            $cFail++;
            echo "C Mismatch/Error on [ID $id]: got '$outputStr', expected '$expected'\n";
            $toDraft[] = $id;
        }
    } else {
        $nonC++;
        echo "Non-C snippet on [ID $id]: " . mb_strimwidth($code, 0, 40, '...') . "\n";
        $toDraft[] = $id;
    }
}

echo "Summary: C Executed Cleanly = $cSuccess, C Fail/Mismatch = $cFail, Non-C = $nonC\n";
echo "IDs sent to draft: " . implode(', ', $toDraft) . "\n";

if (!empty($toDraft)) {
    $in = implode(',', $toDraft);
    $db->query("UPDATE questions SET is_published = 0, verified_by_script = 0, verification_notes = 'Unexecuted non-C or compilation mismatch' WHERE id IN ($in)");
    echo "Updated " . count($toDraft) . " questions to draft.\n";
}

$newCount = $db->query("SELECT COUNT(*) FROM questions WHERE company_name = 'Innovature' AND is_published = 1")->fetchColumn();
echo "Innovature updated published count: $newCount\n";
