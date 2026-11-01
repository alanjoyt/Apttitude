<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

$updates = [
    1602 => "Positions: 1 (A), 2 (D or E), 3 (C), 4 (B), 5 (E or D). Position 2 is occupied by D or E. Therefore, the seated person is D or E.",
    2529 => "Opposite C is D. Therefore, the person opposite C is D.",
    1760 => "Opposite pairs are (A, C) and (B, D). B is opposite D. Therefore, the person opposite B is D.",
    2131 => "Opposite pairs are (A, C) and (B, D). B is opposite D. Therefore, the person opposite B is D.",
    2171 => "Opposite pairs are (A, C) and (B, D). B is opposite D. Therefore, the person opposite B is D.",
    2709 => "Order: A, B, C, D, E. Between C and E is D. Therefore, the person between C and E is D.",
    2395 => "C is at 3, so its immediate right is position 4, which is B. Therefore, the person at position 4 is B.",
    2396 => "Opposite pairs are (A, C) and (B, D). B is opposite D. Therefore, the person opposite B is D.",
    2791 => "Clockwise positions from A at 12: A (12 o'clock), B is right of A (2 o'clock). D is opposite A (6 o'clock). C is not adjacent to D (so not at 4 or 8 o'clock), meaning C must be at 10 o'clock. Left of D (facing center) is 8 o'clock or 4 o'clock depending on orientation. Looking at positions, C sits opposite B at 10 o'clock, adjacent to left of D. Therefore, the person to the left of D is C.",
    2833 => "Order: A, D, C, B, E. D is adjacent to A. Therefore, the person adjacent to A is D.",
    2852 => "'A' precedes consonant sound 'comprehensive'. Therefore, the required indefinite article is 'a'.",
    1911 => "Opposite pairs are (A, C) and (B, D). B is opposite D. Therefore, the person opposite B is D.",
    2296 => "Opposite pairs are (A, C) and (B, D). B is opposite D. Therefore, the person opposite B is D.",
    1522 => "C is between A and E. Since B is to the immediate right of E, the order must be A, C, E, B. Thus to the immediate left of C is A. Therefore, the person to the left of C is A.",
    1981 => "Position 2 between A (pos 1) and C (pos 3) is D or E. Therefore, the person between A and C is D or E.",
    2021 => "C is at 3, so immediate right is position 4, which is B. Therefore, the person at position 4 is B.",
    2989 => "Opposite C is D. Therefore, the person opposite C is D.",
    2724 => "Call stack depth equals tree depth D: O(D). Therefore, the worst-case space complexity is O(D).",
    2809 => "DFS requires recursion stack space proportional to the maximum height or depth D of the tree. Therefore, the auxiliary space complexity is O(D).",
    2149 => "B-Tree indexes maintain sorted balanced tree structures optimal for range queries and equality lookups. Therefore, the appropriate structure is B-Tree Index."
];

$stmt = $db->prepare("UPDATE questions SET explanation = ? WHERE id = ?");
foreach ($updates as $id => $exp) {
    $stmt->execute([$exp, $id]);
}

echo "Successfully updated " . count($updates) . " puzzle/grammar explanations.\n";
