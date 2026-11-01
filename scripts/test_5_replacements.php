<?php
$tcc = __DIR__ . '/../tools/tcc/tcc/tcc.exe';

$snippets = [
    'amazon_1' => [
        'code' => '#include <stdio.h>
int count_set_bits(int n) {
    int count = 0;
    while (n > 0) {
        n &= (n - 1);
        count++;
    }
    return count;
}
int main() {
    printf("%d", count_set_bits(29));
    return 0;
}',
        'expected' => '4'
    ],
    'amazon_2' => [
        'code' => '#include <stdio.h>
int main() {
    int matrix[2][3] = {{1, 2, 3}, {4, 5, 6}};
    int *p = (int *)matrix;
    printf("%d", *(p + 4));
    return 0;
}',
        'expected' => '5'
    ],
    'amazon_3' => [
        'code' => '#include <stdio.h>
int func(int n) {
    if (n <= 1) return 1;
    return n * func(n - 2);
}
int main() {
    printf("%d", func(5));
    return 0;
}',
        'expected' => '15'
    ],
    'hcl_1' => [
        'code' => '#include <stdio.h>
int main() {
    char str[] = "HCLTech";
    char *start = str;
    char *end = str;
    while (*end != \'\\0\') {
        end++;
    }
    printf("%d", (int)(end - start));
    return 0;
}',
        'expected' => '7'
    ],
    'zoho_1' => [
        'code' => '#include <stdio.h>
int add(int a, int b) { return a + b; }
int sub(int a, int b) { return a - b; }
int main() {
    int (*op[])(int, int) = {add, sub};
    printf("%d", op[1](20, op[0](5, 3)));
    return 0;
}',
        'expected' => '12'
    ],
];

foreach ($snippets as $name => $s) {
    $tmp = tempnam(sys_get_temp_dir(), 'test_c_') . '.c';
    file_put_contents($tmp, $s['code']);
    $out = [];
    exec("\"$tcc\" -run \"$tmp\" 2>&1", $out);
    @unlink($tmp);
    $res = trim(implode("\n", $out));
    echo "Snippet $name: Expected '{$s['expected']}', Got '$res' -> " . ($res === $s['expected'] ? "[PASS]" : "[FAIL]") . "\n";
}
