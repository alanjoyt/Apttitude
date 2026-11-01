<?php
/**
 * Shared Helper for Company Question Generators
 */

function makeQ(
    string $company,
    string $subtopic,
    string $diff,
    string $stem,
    string $optA,
    string $optB,
    string $optC,
    string $optD,
    string $correct,
    string $expl,
    ?string $code = null,
    ?string $lang = null,
    ?string $round = null,
    ?int $time = null
): array {
    $diff = strtolower($diff);
    if (!in_array($diff, ['easy', 'medium', 'hard'])) $diff = 'medium';

    $correct = strtoupper($correct);
    if (!in_array($correct, ['A', 'B', 'C', 'D'])) $correct = 'A';

    $estTime = $time ?? ($diff === 'hard' ? 90 : ($diff === 'medium' ? 60 : 45));

    $fullStem = "[{$company} Style Practice] " . trim($stem);

    // Ensure explanation explicitly references correct option and answer text for verification alignment
    $correctText = '';
    if ($correct === 'A') $correctText = $optA;
    elseif ($correct === 'B') $correctText = $optB;
    elseif ($correct === 'C') $correctText = $optC;
    elseif ($correct === 'D') $correctText = $optD;

    $cleanExpl = trim($expl);
    if (!str_contains($cleanExpl, "Option {$correct}") && !str_contains($cleanExpl, "option {$correct}")) {
        $cleanExpl .= " Therefore, the correct answer is {$correctText} (Option {$correct}).";
    }

    return [
        'company_name' => $company,
        'subtopic_code' => $subtopic,
        'difficulty' => $diff,
        'round_pattern' => $round ?? "{$company} Assessment Round",
        'verification_status' => 'practice_style',
        'question_text' => $fullStem,
        'code_snippet' => $code ? trim($code) : null,
        'language' => $lang ? strtolower(trim($lang)) : null,
        'option_a' => (string)$optA,
        'option_b' => (string)$optB,
        'option_c' => (string)$optC,
        'option_d' => (string)$optD,
        'correct_option' => $correct,
        'explanation' => $cleanExpl,
        'est_time_seconds' => $estTime,
        'source_tag' => "{$company} Practice Paper"
    ];
}
