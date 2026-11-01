<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$replacements = [
    2541 => [
        'company' => 'Amazon',
        'text' => '[Amazon Style Practice] What will be the output of the following C function when counting set bits in an integer?',
        'code' => "#include <stdio.h>\nint count_set_bits(int n) {\n    int count = 0;\n    while (n > 0) {\n        n &= (n - 1);\n        count++;\n    }\n    return count;\n}\nint main() {\n    printf(\"%d\", count_set_bits(29));\n    return 0;\n}",
        'language' => 'c',
        'ans' => '4',
        'distractors' => ['3', '5', '6'],
        'explanation' => "Brian Kernighan's algorithm clears the least significant set bit in each iteration (n &= n - 1). 29 in binary is 11101, which has 4 set bits. The loop executes exactly 4 times, returning 4.",
        'difficulty' => 'hard',
        'est_time' => 75
    ],
    2550 => [
        'company' => 'Amazon',
        'text' => '[Amazon Style Practice] What will be printed by dereferencing the flattened 2D array pointer?',
        'code' => "#include <stdio.h>\nint main() {\n    int matrix[2][3] = {{1, 2, 3}, {4, 5, 6}};\n    int *p = (int *)matrix;\n    printf(\"%d\", *(p + 4));\n    return 0;\n}",
        'language' => 'c',
        'ans' => '5',
        'distractors' => ['4', '6', '3'],
        'explanation' => "In C, multi-dimensional arrays are stored contiguously in row-major order: {1, 2, 3, 4, 5, 6}. Offset *(p + 4) accesses index 4, which is matrix[1][1] = 5.",
        'difficulty' => 'medium',
        'est_time' => 60
    ],
    2554 => [
        'company' => 'Amazon',
        'text' => '[Amazon Style Practice] What does the following double-step recursive function return for func(5)?',
        'code' => "#include <stdio.h>\nint func(int n) {\n    if (n <= 1) return 1;\n    return n * func(n - 2);\n}\nint main() {\n    printf(\"%d\", func(5));\n    return 0;\n}",
        'language' => 'c',
        'ans' => '15',
        'distractors' => ['120', '30', '25'],
        'explanation' => "Step-by-step recursion: func(5) evaluates 5 * func(3) = 5 * (3 * func(1)). Base case func(1) returns 1. 5 * 3 * 1 = 15.",
        'difficulty' => 'medium',
        'est_time' => 60
    ],
    2718 => [
        'company' => 'HCL',
        'text' => '[HCL Style Practice] What value is output by pointer subtraction after traversing the null-terminated string?',
        'code' => "#include <stdio.h>\nint main() {\n    char str[] = \"HCLTech\";\n    char *start = str;\n    char *end = str;\n    while (*end != '\\0') {\n        end++;\n    }\n    printf(\"%d\", (int)(end - start));\n    return 0;\n}",
        'language' => 'c',
        'ans' => '7',
        'distractors' => ['8', '6', '0'],
        'explanation' => "The pointer end advances until it encounters the null character '\\0' at index 7. Pointer difference (end - start) calculates the total character count: 7.",
        'difficulty' => 'medium',
        'est_time' => 60
    ],
    3000 => [
        'company' => 'Zoho',
        'text' => '[Zoho Style Practice] What is the output of the nested function pointer array evaluation?',
        'code' => "#include <stdio.h>\nint add(int a, int b) { return a + b; }\nint sub(int a, int b) { return a - b; }\nint main() {\n    int (*op[])(int, int) = {add, sub};\n    printf(\"%d\", op[1](20, op[0](5, 3)));\n    return 0;\n}",
        'language' => 'c',
        'ans' => '12',
        'distractors' => ['18', '22', '15'],
        'explanation' => "op[0] points to add and op[1] points to sub. Inner call op[0](5, 3) yields 8. Outer call op[1](20, 8) computes 20 - 8 = 12.",
        'difficulty' => 'hard',
        'est_time' => 75
    ]
];

$upd = $db->prepare("
    UPDATE questions 
    SET question_text = ?, code_snippet = ?, language = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?,
        correct_option = ?, explanation = ?, difficulty = ?, est_time_seconds = ?
    WHERE id = ?
");

foreach ($replacements as $id => $r) {
    // Preserve current correct_option letter to keep DB distribution balanced
    $curLetter = $db->query("SELECT correct_option FROM questions WHERE id = $id")->fetchColumn() ?: 'A';
    $curLetter = strtoupper($curLetter);

    $opts = [];
    $opts[$curLetter] = $r['ans'];
    $otherLetters = array_diff(['A', 'B', 'C', 'D'], [$curLetter]);
    $idx = 0;
    foreach ($otherLetters as $ol) {
        $opts[$ol] = $r['distractors'][$idx++];
    }

    $explanation = $r['explanation'] . " Therefore, the correct answer is {$r['ans']} (Option {$curLetter}).";

    $upd->execute([
        $r['text'],
        $r['code'],
        $r['language'],
        $opts['A'],
        $opts['B'],
        $opts['C'],
        $opts['D'],
        $curLetter,
        $explanation,
        $r['difficulty'],
        $r['est_time'],
        $id
    ]);
    echo "Updated Question #$id for {$r['company']}: placed correct answer {$r['ans']} at Option {$curLetter}.\n";
}
