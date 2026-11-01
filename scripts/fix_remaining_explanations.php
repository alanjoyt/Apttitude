<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

// Fix 1965
$db->prepare("UPDATE questions SET explanation = ? WHERE id = 1965")->execute([
    "By Fermat's Little Theorem or modular powers: 2^3 = 8 ≡ 1 (mod 7). Therefore 2^20 = (2^3)^6 * 2^2 = 1^6 * 4 = 4. The remainder is 4."
]);

// Fix 1289 & 3087
foreach ([1289, 3087] as $id) {
    $db->prepare("UPDATE questions SET explanation = ? WHERE id = ?")->execute([
        "This phrasing avoids a dangling participle because 'the QA team' is the subject performing the action 'having completed'.",
        $id
    ]);
}

echo "Cleaned remaining edge cases.\n";
