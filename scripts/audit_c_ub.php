<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$cQuestions = $db->query("
    SELECT id, company_name, question_text, code_snippet, option_a, option_b, option_c, option_d, correct_option, explanation
    FROM questions
    WHERE is_published = 1 AND language = 'c' AND code_snippet IS NOT NULL
")->fetchAll(PDO::FETCH_ASSOC);

echo "Auditing " . count($cQuestions) . " published C questions for Undefined / Implementation-Defined Behavior:\n\n";

$ubIssues = [];

foreach ($cQuestions as $q) {
    $code = $q['code_snippet'];
    $id = $q['id'];
    $reasons = [];

    // 1. Unsequenced side effects (multiple increments in same expression or function call)
    // e.g. printf("%d %d", x++, x++) or x++ + ++x or func(x++, x++)
    if (preg_match('/(?:printf|print)\s*\([^;]*\b([a-zA-Z_]\w*)\+\+[^;]*\b\1\+\+/s', $code) ||
        preg_match('/(?:printf|print)\s*\([^;]*\+\+\b([a-zA-Z_]\w*)\b[^;]*\+\+\b\1\b/s', $code) ||
        preg_match('/\b([a-zA-Z_]\w*)\+\+[^;]*\+\+\b\1\b/s', $code) ||
        preg_match('/\b([a-zA-Z_]\w*)\+\+[^;]*\b\1\+\+/s', $code)) {
        $reasons[] = "Unsequenced side effect / multiple modifications of same variable without sequence point";
    }

    // 2. Modifying string literal: char *s = "..."; s[0] = ...;
    if (preg_match('/char\s*\*\s*([a-zA-Z_]\w*)\s*=\s*"[^"]*";.*?\b\1\[\d+\]\s*=/s', $code)) {
        $reasons[] = "Modifying string literal (Undefined Behavior / Segmentation Fault)";
    }

    // 3. sizeof('A') - implementation defined / C vs C++ difference
    if (preg_match('/sizeof\s*\(\s*\'[^\']\'\s*\)/', $code)) {
        $reasons[] = "sizeof('A') character constant (sizeof(int) in C vs sizeof(char) in C++)";
    }

    // 4. Uninitialized variable read
    if (preg_match('/int\s+([a-zA-Z_]\w*)\s*;(?!\s*int)(?!.*?\1\s*=)/s', $code)) {
        $reasons[] = "Potentially uninitialized variable read";
    }

    // 5. printf with %p and integer cast
    if (preg_match('/%p/', $code) && !preg_match('/\(void\s*\*\)/', $code)) {
        $reasons[] = "Pointer printing %p without casting to (void*)";
    }

    // 6. Signed integer overflow
    if (preg_match('/2147483647\s*\+/i', $code) || preg_match('/INT_MAX\s*\+/i', $code)) {
        $reasons[] = "Signed integer overflow";
    }

    if (!empty($reasons)) {
        $ubIssues[] = [
            'id' => $id,
            'company' => $q['company_name'],
            'reasons' => $reasons,
            'code' => $code,
            'explanation' => $q['explanation']
        ];
    }
}

echo "Found " . count($ubIssues) . " C questions with potential UB / implementation-defined nuances:\n\n";
foreach ($ubIssues as $issue) {
    echo "ID: {$issue['id']} ({$issue['company']})\n";
    foreach ($issue['reasons'] as $r) {
        echo "  - $r\n";
    }
    echo "  Code:\n" . implode("\n    ", explode("\n", trim($issue['code']))) . "\n";
    echo "--------------------------------------------------------\n";
}
