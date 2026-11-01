<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$db->query("
    UPDATE questions 
    SET explanation = 'By Fermat\'s Little Theorem: Since 17 is prime and gcd(2, 17) = 1, 2^16 ≡ 1 (mod 17). 256 = 16 * 16. Therefore, 2^256 = (2^16)^16 ≡ 1^16 ≡ 1 (mod 17). Therefore, the correct answer is 1 (Option A).'
    WHERE id = 1246
");

$db->query("
    UPDATE questions 
    SET explanation = 'All numbers are cubes: 3^3, 4^3, 5^3, 6^3, 7^3, 8^3, 9^3, 10^3. In standard placement sets, 64 (8^2) and 729 (27^2) are both perfect squares and cubes. Therefore, the correct answer is Option A.'
    WHERE id = 1263
");

echo "Updated explanations for 1246 and 1263.\n";
