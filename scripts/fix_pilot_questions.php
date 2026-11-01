<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "Fixing pilot questions in Innovature, TCS NQT, and Infosys...\n";

// 1. Innovature: 1306
$db->prepare("
    UPDATE questions 
    SET explanation = 'Default parameter lists in Python are evaluated once when the function is defined, not each time the function is called. The same list instance is reused across invocations, so subsequent calls append to the existing list, resulting in [1], [1, 2] (Option A).'
    WHERE id = 1306
")->execute();

// 2. Innovature: 1320
$db->prepare("
    UPDATE questions 
    SET explanation = 'The Network Layer (Layer 3) of the OSI model handles logical IP addressing, packet forwarding, and path determination (routing) across multiple networks (Option A).'
    WHERE id = 1320
")->execute();

// Helper to fix the 9 questions in TCS NQT and Infosys
// For TCS NQT (ids: 615, 618, 621, 624, 626, 628, 631, 634, 637)
// For Infosys (ids: 640, 643, 646, 649, 651, 653, 656, 659, 662)

$fixAges1 = function($id) use ($db) {
    // Let ratio be 5:6. Present ages 5x and 6x. After 4 years, ratio becomes 6:7.
    // (5x+4)/(6x+4) = 6/7 => 35x + 28 = 36x + 24 => x = 4. Present age of X = 5*4 = 20 years.
    $db->prepare("
        UPDATE questions SET 
            question_text = 'The ratio of present ages of X and Y is 5:6. After 4 years, the ratio of their ages becomes 6:7. What is X\'s present age?',
            option_a = '20 years',
            option_b = '24 years',
            option_c = '26 years',
            option_d = '30 years',
            correct_option = 'A',
            explanation = 'Let present ages of X and Y be 5x and 6x. After 4 years: (5x + 4) / (6x + 4) = 6 / 7. Cross multiplying: 7(5x + 4) = 6(6x + 4) => 35x + 28 = 36x + 24 => x = 4. Therefore, present age of X = 5 * 4 = 20 years (Option A).'
        WHERE id = ?
    ")->execute([$id]);
};

$fixSeries1 = function($id) use ($db) {
    $db->prepare("
        UPDATE questions SET 
            question_text = 'Complete the number series: 10, 20, 40, 80, ?',
            option_a = '160',
            option_b = '120',
            option_c = '180',
            option_d = '200',
            correct_option = 'A',
            explanation = 'Each number in the series is multiplied by 2: 10 * 2 = 20, 20 * 2 = 40, 40 * 2 = 80, 80 * 2 = 160. Therefore, the next term is 160 (Option A).'
        WHERE id = ?
    ")->execute([$id]);
};

$fixRev1 = function($id) use ($db) {
    $db->prepare("
        UPDATE questions SET 
            question_text = 'A company quarterly report shows sales revenue increased from $180M in Q1 to $230M in Q2. Find the percentage growth in revenue.',
            option_a = '27.78%',
            option_b = '32.78%',
            option_c = '24.78%',
            option_d = '37.78%',
            correct_option = 'A',
            explanation = 'Percentage growth = ((Q2 - Q1) / Q1) * 100 = ((230 - 180) / 180) * 100 = (50 / 180) * 100 = 27.78%. Thus, the growth rate is 27.78% (Option A).'
        WHERE id = ?
    ")->execute([$id]);
};

$fixPipe1 = function($id) use ($db) {
    $db->prepare("
        UPDATE questions SET 
            question_text = 'Pipe A can fill a cistern in 21 minutes, while Pipe B can empty it in 31 minutes. If both pipes are opened simultaneously, in how many minutes will the empty cistern be completely filled?',
            option_a = '65.1 mins',
            option_b = '70.1 mins',
            option_c = '58.5 mins',
            option_d = '75.1 mins',
            correct_option = 'A',
            explanation = 'Net filling rate per minute = 1/21 - 1/31 = (31 - 21) / (21 * 31) = 10 / 651. Total time required to fill = 651 / 10 = 65.1 mins (Option A).'
        WHERE id = ?
    ")->execute([$id]);
};

$fixTrain1 = function($id) use ($db) {
    // Speed = 72 km/h = 20 m/s. Crosses 165 m platform in 20 seconds.
    // Total distance = 20 * 20 = 400 m. Train length = 400 - 165 = 235 m.
    // Or let train length be 159 m:
    // If train length = 159 m, total dist = 159 + 165 = 324 m. In 18 seconds => speed = 324/18 = 18 m/s = 64.8 km/h.
    $db->prepare("
        UPDATE questions SET 
            question_text = 'A train running at a uniform speed of 64.8 km/h crosses a platform of length 165 m in 18 seconds. What is the length of the train?',
            option_a = '159 m',
            option_b = '175 m',
            option_c = '190 m',
            option_d = '210 m',
            correct_option = 'A',
            explanation = 'Speed of the train in m/s = 64.8 * (5 / 18) = 18 m/s. Total distance covered in 18 seconds = 18 * 18 = 324 m. Since total distance = Length of train + Length of platform, Length of train = 324 - 165 = 159 m (Option A).'
        WHERE id = ?
    ")->execute([$id]);
};

$fixAges2 = function($id) use ($db) {
    // 3:4. After 6 years 4:5. Present ages: 3*6 = 18 and 4*6 = 24.
    // Let's make options match: 18 years!
    $db->prepare("
        UPDATE questions SET 
            question_text = 'The ratio of present ages of X and Y is 3:4. After 6 years, the ratio becomes 4:5. What is X\'s present age?',
            option_a = '18 years',
            option_b = '24 years',
            option_c = '21 years',
            option_d = '27 years',
            correct_option = 'A',
            explanation = 'Let present ages of X and Y be 3x and 4x. After 6 years: (3x + 6) / (4x + 6) = 4 / 5. Cross multiplying: 5(3x + 6) = 4(4x + 6) => 15x + 30 = 16x + 24 => x = 6. Therefore, present age of X = 3 * 6 = 18 years (Option A).'
        WHERE id = ?
    ")->execute([$id]);
};

$fixSeries2 = function($id) use ($db) {
    $db->prepare("
        UPDATE questions SET 
            question_text = 'Complete the number series: 36, 72, 144, 288, ?',
            option_a = '576',
            option_b = '432',
            option_c = '648',
            option_d = '720',
            correct_option = 'A',
            explanation = 'Each number in the series doubles: 36 * 2 = 72, 72 * 2 = 144, 144 * 2 = 288, 288 * 2 = 576. Therefore, the next number is 576 (Option A).'
        WHERE id = ?
    ")->execute([$id]);
};

$fixRev2 = function($id) use ($db) {
    $db->prepare("
        UPDATE questions SET 
            question_text = 'A software division reported revenue of $310M in Q1 and $360M in Q2. What is the percentage growth in revenue?',
            option_a = '16.13%',
            option_b = '21.13%',
            option_c = '13.13%',
            option_d = '26.13%',
            correct_option = 'A',
            explanation = 'Percentage growth = ((360 - 310) / 310) * 100 = (50 / 310) * 100 = 16.13%. Thus, the revenue growth is 16.13% (Option A).'
        WHERE id = ?
    ")->execute([$id]);
};

$fixPipe2 = function($id) use ($db) {
    $db->prepare("
        UPDATE questions SET 
            question_text = 'Pipe A fills a reservoir in 34 minutes, while Pipe B empties it in 44 minutes. If both pipes are opened simultaneously, in how many minutes will the reservoir be completely filled?',
            option_a = '149.6 mins',
            option_b = '154.6 mins',
            option_c = '146.6 mins',
            option_d = '159.6 mins',
            correct_option = 'A',
            explanation = 'Net filling rate = 1/34 - 1/44 = (44 - 34) / (34 * 44) = 10 / 1496. Time required to fill = 1496 / 10 = 149.6 mins (Option A).'
        WHERE id = ?
    ")->execute([$id]);
};

// Apply to TCS NQT
$fixAges1(615);
$fixSeries1(618);
$fixRev1(621);
$fixPipe1(624);
$fixTrain1(626);
$fixAges2(628);
$fixSeries2(631);
$fixRev2(634);
$fixPipe2(637);

// Apply to Infosys
$fixAges1(640);
$fixSeries1(643);
$fixRev1(646);
$fixPipe1(649);
$fixTrain1(651);
$fixAges2(653);
$fixSeries2(656);
$fixRev2(659);
$fixPipe2(662);

echo "Pilot questions updated. Re-running verification...\n";
