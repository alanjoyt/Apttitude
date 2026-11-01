<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "           QUESTION BANK COMPREHENSIVE LINT AUDIT                \n";
echo "=================================================================\n\n";

$questions = $db->query("
    SELECT id, company_name, question_text, option_a, option_b, option_c, option_d, 
           correct_option, explanation, subtopic_id, difficulty
    FROM questions
    WHERE is_published = 1
")->fetchAll(PDO::FETCH_ASSOC);

echo "Total published questions scanned: " . count($questions) . "\n\n";

$issues = [
    'missing_conclusions' => [],
    'missing_figures' => [],
    'duplicate_options' => [],
    'stray_spaces_options' => [],
    'option_letters_in_explanation' => [],
    'glitch_or_typo' => [],
];

foreach ($questions as $q) {
    $id = $q['id'];
    $text = $q['question_text'];
    $exp = $q['explanation'];
    $opts = [
        'A' => $q['option_a'],
        'B' => $q['option_b'],
        'C' => $q['option_c'],
        'D' => $q['option_d']
    ];

    // 1. Missing conclusions check
    // If options or text mention "conclusion I" / "conclusion II" but question text doesn't define I and II
    $optTextCombined = implode(' ', $opts);
    if ((preg_match('/conclusion\s+I\b/i', $optTextCombined) || preg_match('/conclusion\s+I\b/i', $text) || preg_match('/conclusions\s+I\s+and\s+II/i', $optTextCombined)) 
        && !preg_match('/(?:I\.\s+|I\)\s+|Conclusion\s*I:)/i', $text)) {
        $issues['missing_conclusions'][] = [
            'id' => $id,
            'company' => $q['company_name'],
            'text' => $text,
            'explanation' => $exp
        ];
    }

    // 2. Missing figures or diagrams
    if (preg_match('/\b(?:in the figure|in the diagram|shown below|in the image|given picture|following table)\b/i', $text) 
        && !str_contains($text, '|') && !str_contains($text, '---') && !str_contains($text, '+---')) {
        $issues['missing_figures'][] = [
            'id' => $id,
            'company' => $q['company_name'],
            'text' => $text
        ];
    }

    // 3. Duplicate options within a single question
    $trimmedOpts = array_map('trim', array_map('strtolower', $opts));
    $uniqueOpts = array_unique($trimmedOpts);
    if (count($uniqueOpts) < 4) {
        $issues['duplicate_options'][] = [
            'id' => $id,
            'company' => $q['company_name'],
            'options' => $opts
        ];
    }

    // 4. Stray spaces or weird formatting in options (e.g., "PWUQV V", double space)
    foreach ($opts as $k => $v) {
        if (preg_match('/\s{2,}/', $v) || preg_match('/^[A-Z]{3,}\s+[A-Z]$/', trim($v))) {
            $issues['stray_spaces_options'][] = [
                'id' => $id,
                'company' => $q['company_name'],
                'option' => $k,
                'val' => $v
            ];
        }
    }

    // 5. Option letters in explanation (Option A/B/C/D, (Option A), answer is A/B/C/D, etc.)
    if (preg_match('/(?:\bOption\s*[A-D]\b|\(Option\s*[A-D]\)|\b(?:answer|choice)\s+is\s+[A-D]\b)/i', $exp)) {
        $issues['option_letters_in_explanation'][] = [
            'id' => $id,
            'company' => $q['company_name'],
            'exp_snippet' => substr($exp, -60)
        ];
    }


    // 6. Glitch or prompt leakage (like "wait: She prefers...")
    if (preg_match('/(?:wait:|sorry,|correction:)/i', $text) || preg_match('/(?:wait:|sorry,|correction:)/i', $optTextCombined)) {
        $issues['glitch_or_typo'][] = [
            'id' => $id,
            'company' => $q['company_name'],
            'text' => $text,
            'options' => $opts
        ];
    }
}

echo "Summary of lint issues found:\n";
echo "1. Missing conclusions: " . count($issues['missing_conclusions']) . "\n";
echo "2. Missing figures/tables: " . count($issues['missing_figures']) . "\n";
echo "3. Duplicate options: " . count($issues['duplicate_options']) . "\n";
echo "4. Stray spaces in options: " . count($issues['stray_spaces_options']) . "\n";
echo "5. Option letters in explanation: " . count($issues['option_letters_in_explanation']) . "\n";
echo "6. Glitch / prompt leakage: " . count($issues['glitch_or_typo']) . "\n\n";

if (!empty($issues['missing_conclusions'])) {
    echo "--- MISSING CONCLUSIONS SAMPLES ---\n";
    print_r($issues['missing_conclusions']);
}

if (!empty($issues['missing_figures'])) {
    echo "--- MISSING FIGURES SAMPLES ---\n";
    print_r(array_slice($issues['missing_figures'], 0, 5));
}

if (!empty($issues['duplicate_options'])) {
    echo "--- DUPLICATE OPTIONS SAMPLES ---\n";
    print_r(array_slice($issues['duplicate_options'], 0, 5));
}

if (!empty($issues['stray_spaces_options'])) {
    echo "--- STRAY SPACES IN OPTIONS ---\n";
    print_r($issues['stray_spaces_options']);
}

if (!empty($issues['glitch_or_typo'])) {
    echo "--- GLITCH OR PROMPT LEAKAGE ---\n";
    print_r($issues['glitch_or_typo']);
}
