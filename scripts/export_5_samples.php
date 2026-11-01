<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$companies = [
    'Innovature', 'TCS NQT', 'Infosys', 'Wipro NLTH', 'Accenture',
    'Cognizant', 'Capgemini', 'Deloitte', 'Tech Mahindra', 'Zoho',
    'LTI Mindtree', 'HCL', 'Amazon', 'UST', 'IBM'
];

$output = [];

foreach ($companies as $comp) {
    $stmt = $db->prepare("
        SELECT id, question_text, code_snippet, language, option_a, option_b, option_c, option_d, correct_option, explanation, difficulty, est_time_seconds, source_tag, verification_status
        FROM questions
        WHERE company_name = ? AND is_published = 1
        ORDER BY RAND()
        LIMIT 5
    ");
    $stmt->execute([$comp]);
    $output[$comp] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

file_put_contents(__DIR__ . '/five_samples_per_company.json', json_encode($output, JSON_PRETTY_PRINT));
echo "Successfully exported 5 sample questions per company to scripts/five_samples_per_company.json\n";
