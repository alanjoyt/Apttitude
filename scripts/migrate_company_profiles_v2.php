<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "Updating company_profiles table with last_checked and reported pattern notices...\n";

// Add last_checked if not present
try {
    $db->exec("ALTER TABLE company_profiles ADD COLUMN last_checked DATE DEFAULT '2026-10-18'");
    echo "Added last_checked column.\n";
} catch (PDOException $e) {
    echo "Column last_checked already exists or: " . $e->getMessage() . "\n";
}

try {
    $db->exec("ALTER TABLE company_profiles ADD COLUMN is_reported_pattern TINYINT(1) DEFAULT 1");
    echo "Added is_reported_pattern column.\n";
} catch (PDOException $e) {
    echo "Column is_reported_pattern already exists or: " . $e->getMessage() . "\n";
}

// Update all company profiles to ensure pattern text includes '(Reported pattern, please verify)'
$stmt = $db->query("SELECT id, test_pattern, syllabus FROM company_profiles");
$profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$upd = $db->prepare("UPDATE company_profiles SET test_pattern = ?, syllabus = ?, last_checked = '2026-10-18' WHERE id = ?");

foreach ($profiles as $p) {
    $pattern = $p['test_pattern'];
    $syllabus = $p['syllabus'];

    if (!str_contains($pattern, 'Reported pattern, please verify')) {
        $pattern .= " (Reported pattern, please verify)";
    }
    if (!str_contains($syllabus, 'Reported pattern, please verify')) {
        $syllabus .= " (Reported pattern, please verify)";
    }

    $upd->execute([$pattern, $syllabus, $p['id']]);
}

echo "Successfully updated " . count($profiles) . " company profiles with verified disclaimers and last_checked = '2026-10-18'.\n";
