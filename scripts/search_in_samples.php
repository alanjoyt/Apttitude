<?php
$file = __DIR__ . '/five_samples_per_company.json';
if (!file_exists($file)) {
    die("File not found: $file\n");
}
$data = json_decode(file_get_contents($file), true);
foreach ($data as $comp => $list) {
    foreach ($list as $q) {
        $encoded = json_encode($q);
        if (stripos($encoded, 'PWUQV') !== false || stripos($encoded, 'OUTPUT') !== false || stripos($encoded, 'SYSTEM') !== false) {
            echo "Company: $comp\n";
            echo "Question: " . $q['question_text'] . "\n";
            echo "A: " . ($q['option_a'] ?? '') . "\n";
            echo "B: " . ($q['option_b'] ?? '') . "\n";
            echo "C: " . ($q['option_c'] ?? '') . "\n";
            echo "D: " . ($q['option_d'] ?? '') . "\n";
            echo "Correct: " . ($q['correct_option'] ?? '') . "\n";
            echo "Explanation: " . ($q['explanation'] ?? '') . "\n";
            echo "--------------------------------------------------------\n";
        }
    }
}
