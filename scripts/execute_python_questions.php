<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$rows = $db->query("
    SELECT id, company_name, question_text, code_snippet, correct_option, option_a, option_b, option_c, option_d, is_published
    FROM questions
    WHERE is_published = 1 AND language = 'python' AND code_snippet IS NOT NULL AND TRIM(code_snippet) != ''
")->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($rows) . " published Python code questions.\n";

$passed = 0;
$failed = 0;
$tempFile = __DIR__ . '/../tools/temp_runner.py';

foreach ($rows as $r) {
    $code = trim($r['code_snippet']);
    file_put_contents($tempFile, $code);

    $cmd = "python " . escapeshellarg($tempFile);
    $descriptors = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];
    $process = proc_open($cmd, $descriptors, $pipes);
    $output = '';
    $ret = -1;

    if (is_resource($process)) {
        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);
        $start = microtime(true);
        while (true) {
            $status = proc_get_status($process);
            $output .= stream_get_contents($pipes[1]);
            if (!$status['running']) {
                $ret = $status['exitcode'];
                break;
            }
            if (microtime(true) - $start > 2.0) {
                proc_terminate($process);
                $ret = 124;
                break;
            }
            usleep(10000);
        }
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);
    }
    $output = trim($output);

    $expectedKey = 'option_' . strtolower($r['correct_option']);
    $expectedVal = trim($r[$expectedKey]);

    $matches = ($ret === 0 && ($output === $expectedVal || strpos($expectedVal, $output) !== false || strpos($output, $expectedVal) !== false));

    if ($matches) {
        $passed++;
        echo "  [PASS] ID {$r['id']} ({$r['company_name']}): Output '$output' matches expected '$expectedVal'\n";
    } else {
        $failed++;
        echo "  [FAIL/MISMATCH] ID {$r['id']} ({$r['company_name']}): Output '$output', expected '$expectedVal' (ret=$ret)\n";
    }
}

@unlink($tempFile);
echo "\nTotal Python: " . count($rows) . " | Executed & Verified: $passed | Mismatch: $failed\n";
