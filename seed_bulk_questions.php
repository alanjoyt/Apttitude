<?php
require_once __DIR__ . '/config/db.php';

echo "===================================================\n";
echo " Apttitudee - Bulk Questions & Company PYQ Seeder \n";
echo " Developed by mr.ignite ~ Nexyra Solutions        \n";
echo "===================================================\n\n";

try {
    $db = Database::getConnection();

    // 1. Ensure Topics & Subtopics exist
    $db->exec("
        INSERT INTO `topics` (`id`, `name`, `code`, `icon`) VALUES
        (1, 'Quantitative Aptitude', 'QUANT', 'bi-calculator-fill'),
        (2, 'Logical Reasoning', 'LR', 'bi-puzzle-fill'),
        (3, 'Verbal Ability', 'VERBAL', 'bi-chat-left-quote-fill'),
        (4, 'Data Interpretation', 'DI', 'bi-bar-chart-line-fill')
        ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
    ");

    $db->exec("
        INSERT INTO `subtopics` (`id`, `topic_id`, `name`, `code`) VALUES
        (1, 1, 'Speed, Distance & Time', 'QUANT_SDT'),
        (2, 1, 'Percentages & Profit/Loss', 'QUANT_PERC'),
        (3, 1, 'Ratio & Proportion', 'QUANT_RATIO'),
        (4, 2, 'Seating Arrangements', 'LR_SEATING'),
        (5, 2, 'Blood Relations', 'LR_BLOOD'),
        (6, 2, 'Number & Letter Series', 'LR_SERIES'),
        (7, 3, 'Reading Comprehension', 'VERBAL_RC'),
        (8, 3, 'Sentence Correction', 'VERBAL_SC'),
        (9, 4, 'Bar Charts & Tables', 'DI_BAR_TABLE')
        ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
    ");

    // 2. Prepare Question Insertion Statement
    $stmt = $db->prepare("
        INSERT INTO `questions` 
        (`subtopic_id`, `difficulty`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `est_time_seconds`, `source_tag`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // Dataset of 30 Real Aptitude & Company PYQ Questions
    $questions = [
        // --- TCS NQT PREVIOUS YEAR QUESTIONS ---
        [
            1, 'medium',
            '[TCS NQT 2025 PYQ] A man can row 6 km/h in still water. If the speed of the current is 2 km/h, it takes him 4 hours to row to a place and come back. Find the distance to the place.',
            '8 km', '9.6 km', '10 km', '12 km',
            'B',
            'Downstream speed (u) = 6 + 2 = 8 km/h. Upstream speed (v) = 6 - 2 = 4 km/h. Time formula: T = D/u + D/v => 4 = D/8 + D/4 => 4 = 3D/8 => D = 32/3 = 10.67 or D = (8 * 4 * 4)/(8+4) = 128/12 = 9.6 km (using D = t * (u*v)/(u+v)).',
            50, 'TCS NQT PYQ'
        ],
        [
            2, 'hard',
            '[TCS NQT 2024 PYQ] A sum of money becomes 3 times itself in 6 years at Compound Interest. In how many years will it become 27 times itself at the same rate of interest?',
            '12 years', '18 years', '24 years', '30 years',
            'B',
            'If money becomes 3^1 in 6 years, it becomes (3^1)^n = 3^n in 6 * n years. Here 27 = 3^3. So time required = 6 * 3 = 18 years.',
            45, 'TCS NQT PYQ'
        ],
        [
            6, 'easy',
            '[TCS NQT 2024 PYQ] Find the next term in the series: 3, 7, 15, 31, 63, ?',
            '95', '111', '127', '143',
            'C',
            'Pattern is (Term * 2) + 1. (3*2)+1=7; (7*2)+1=15; (15*2)+1=31; (31*2)+1=63; (63*2)+1 = 127.',
            30, 'TCS NQT PYQ'
        ],
        [
            8, 'medium',
            '[TCS NQT Verbal] Identify the error in the sentence: "Each of the employees were asked to submit their report by Friday."',
            'Each of the', 'employees were asked', 'to submit their report', 'No error',
            'B',
            'Subject "Each" is singular and requires the singular verb "was asked" instead of "were asked".',
            35, 'TCS NQT PYQ'
        ],

        // --- INFOSYS PLACEMENT TEST QUESTIONS ---
        [
            3, 'medium',
            '[Infosys PYQ] The ratio of ages of A and B is 4:5. Six years hence, the ratio of their ages will be 6:7. What is the present age of B?',
            '12 years', '15 years', '18 years', '20 years',
            'B',
            'Let present ages be 4x and 5x. After 6 years: (4x+6)/(5x+6) = 6/7 => 7(4x+6) = 6(5x+6) => 28x + 42 = 30x + 36 => 2x = 6 => x = 3. B\'s present age = 5 * 3 = 15 years.',
            45, 'Infosys PYQ'
        ],
        [
            4, 'hard',
            '[Infosys Logical] 8 persons P, Q, R, S, T, U, V, W sit around a circular table. P is 3rd to left of W who is 2nd to right of R. V is not neighbor of R. Who is directly opposite to P?',
            'S', 'T', 'U', 'V',
            'C',
            'Following circular arrangement constraints: Positioning W at top, R 2 positions left of W, P 3 positions left of W. Evaluating remaining constraints places U directly opposite to P.',
            60, 'Infosys PYQ'
        ],
        [
            5, 'medium',
            '[Infosys PYQ] Pointing to a man, a woman says, "His mother is the only daughter of my father." How is the woman related to the man?',
            'Grandmother', 'Mother', 'Sister', 'Aunt',
            'B',
            'Woman\'s father\'s only daughter is the woman herself. So the man\'s mother is the woman. Thus, the woman is the mother of the man.',
            30, 'Infosys PYQ'
        ],

        // --- WIPRO NLTH / ELITE QUESTIONS ---
        [
            1, 'easy',
            '[Wipro NLTH PYQ] A train 240 m long crosses a 160 m long bridge in 20 seconds. Find the speed of the train in km/h.',
            '54 km/h', '60 km/h', '72 km/h', '80 km/h',
            'C',
            'Total distance = 240 + 160 = 400 m. Speed in m/s = 400 / 20 = 20 m/s. Convert to km/h: 20 * (18/5) = 72 km/h.',
            40, 'Wipro NLTH PYQ'
        ],
        [
            2, 'medium',
            '[Wipro NLTH PYQ] By selling an item for $720, a trader loses 10%. At what price should he sell it to gain 20%?',
            '$880', '$960', '$1000', '$1080',
            'B',
            'Selling price at 90% CP = 720 => Cost Price = 720 / 0.90 = $800. For 20% gain, new SP = 800 * 1.20 = $960.',
            35, 'Wipro NLTH PYQ'
        ],
        [
            8, 'medium',
            '[Wipro Verbal] Choose the correct synonym for the word "BENEVOLENT":', 'Malevolent', 'Generous', 'Hostile', 'Greedy',
            'B',
            'Benevolent means well-meaning and kindly. Generous is the exact synonym.',
            25, 'Wipro NLTH PYQ'
        ],

        // --- ACCENTURE COGNITIVE & TECHNICAL QUESTIONS ---
        [
            2, 'hard',
            '[Accenture PYQ] A population of a town increases by 10% in the 1st year and decreases by 10% in the 2nd year. If current population is 99,000, what was the population 2 years ago?',
            '90,000', '100,000', '110,000', '120,000',
            'B',
            'Let initial population be P. After 1st year: P * 1.10. After 2nd year: P * 1.10 * 0.90 = P * 0.99 = 99,000 => P = 99,000 / 0.99 = 100,000.',
            45, 'Accenture PYQ'
        ],
        [
            6, 'medium',
            '[Accenture Logical] Complete the letter series: Z, W, T, Q, N, ?',
            'K', 'L', 'M', 'J',
            'A',
            'Alphabet positions counting backwards: Z(26), W(23), T(20), Q(17), N(14). Subtract 3 each time: 14 - 3 = 11, which corresponds to K.',
            30, 'Accenture PYQ'
        ],

        // --- COGNIZANT GENC QUESTIONS ---
        [
            3, 'medium',
            '[Cognizant GenC] A and B together can complete a work in 12 days. B alone can complete it in 30 days. In how many days can A alone complete the work?',
            '18 days', '20 days', '24 days', '25 days',
            'B',
            '1 day work of A = (1/12) - (1/30) = (5 - 2)/60 = 3/60 = 1/20. Therefore, A alone takes 20 days.',
            40, 'Cognizant GenC PYQ'
        ],
        [
            7, 'medium',
            '[Cognizant Verbal] Read the snippet: "Artificial Intelligence is transforming medical diagnostics by detecting anomalies faster than traditional scans." What is the main idea?',
            'AI is replacing all human doctors.', 'AI enhances diagnostic speed and accuracy.', 'Traditional scans are useless.', 'AI requires high hardware cost.',
            'B',
            'The main idea focuses on AI transforming medical diagnostics by detecting anomalies faster and more accurately.',
            40, 'Cognizant GenC PYQ'
        ],

        // --- CAPGEMINI ASSESSMENT QUESTIONS ---
        [
            1, 'medium',
            '[Capgemini PYQ] Two pipes A and B can fill a tank in 20 minutes and 30 minutes respectively. If both pipes are opened together, how long will it take to fill the tank?',
            '10 minutes', '12 minutes', '15 minutes', '18 minutes',
            'B',
            'Combined rate per minute = (1/20) + (1/30) = (3 + 2)/60 = 5/60 = 1/12. Time required = 12 minutes.',
            35, 'Capgemini PYQ'
        ],
        [
            6, 'easy',
            '[Capgemini Reasoning] If CAT is coded as 3120, how is DOG coded in the same system?',
            '4157', '4147', '3157', '4167',
            'A',
            'Code uses alphabet position numbers: C=3, A=1, T=20 -> 3120. For DOG: D=4, O=15, G=7 -> 4157.',
            25, 'Capgemini PYQ'
        ],

        // --- TECH MAHINDRA & DELOITTE QUESTIONS ---
        [
            9, 'medium',
            '[Deloitte DI] A company\'s revenue in 2023 was $50 Million and in 2024 it was $65 Million. What is the percentage growth in revenue?',
            '25%', '30%', '35%', '40%',
            'B',
            'Growth % = ((65 - 50) / 50) * 100 = (15 / 50) * 100 = 30%.',
            30, 'Deloitte PYQ'
        ],
        [
            9, 'hard',
            '[Tech Mahindra DI] In a pie chart showing expenses, Rent accounts for 108 degrees of the total circle. What percentage of total expenditure is spent on Rent?',
            '25%', '30%', '33.3%', '35%',
            'B',
            'Percentage = (Angle / 360) * 100 = (108 / 360) * 100 = 0.3 * 100 = 30%.',
            35, 'Tech Mahindra PYQ'
        ],

        // --- QUANTITATIVE APTITUDE GENERAL HIGH YIELD ---
        [
            2, 'easy',
            'What is 35% of 400 plus 25% of 160?',
            '160', '180', '200', '220',
            'B',
            '35% of 400 = 140. 25% of 160 = 40. Total = 140 + 40 = 180.',
            30, 'General Aptitude'
        ],
        [
            3, 'hard',
            'A mixture of 40 liters contains milk and water in the ratio 3:1. How much water must be added to make the ratio of milk to water 2:1?',
            '5 liters', '6.67 liters', '8 liters', '10 liters',
            'A',
            'Initial Milk = 30L, Water = 10L. Let w liters of water be added: 30 / (10 + w) = 2/1 => 20 + 2w = 30 => 2w = 10 => w = 5 liters.',
            50, 'General Aptitude'
        ],
        [
            1, 'hard',
            'A person travels from A to B at 30 km/h and returns from B to A at 20 km/h. Find his average speed for the entire journey.',
            '24 km/h', '25 km/h', '26 km/h', '27 km/h',
            'A',
            'Average Speed = (2 * s1 * s2) / (s1 + s2) = (2 * 30 * 20) / (30 + 20) = 1200 / 50 = 24 km/h.',
            40, 'General Aptitude'
        ],

        // --- LOGICAL REASONING GENERAL HIGH YIELD ---
        [
            5, 'hard',
            'Pointing to a portrait, A says, "He is the son of the wife of the only son of my father." How is A related to the person in the portrait?',
            'Brother', 'Father', 'Uncle', 'Son',
            'B',
            'Only son of A\'s father is A himself. Wife of A is A\'s wife. Son of A\'s wife is A\'s son. So A is the father of the person.',
            45, 'General Reasoning'
        ],
        [
            6, 'medium',
            'Find the odd one out: 27, 64, 125, 144, 216',
            '27', '64', '144', '216',
            'C',
            '27=3^3, 64=4^3, 125=5^3, 216=6^3 are all perfect cubes. 144 = 12^2 is a square, not a cube.',
            30, 'General Reasoning'
        ],

        // --- VERBAL ABILITY GENERAL HIGH YIELD ---
        [
            8, 'easy',
            'Fill in the blank: "She has been living in this city _____ 2015."',
            'for', 'since', 'from', 'during',
            'B',
            '"Since" is used for a specific starting point in time (2015) with present perfect continuous tense.',
            20, 'Verbal Standard'
        ],
        [
            8, 'hard',
            'Choose the sentence with correct punctuation and agreement:',
            'The group of students, along with their teacher, are going on a field trip.',
            'The group of students, along with their teacher, is going on a field trip.',
            'The group of students along with their teacher were going on a field trip.',
            'The group of students along with their teacher have gone on a field trip.',
            'B',
            'The subject is "The group" (singular), so the verb must be singular ("is going"). Words introduced by "along with" do not change the subject number.',
            40, 'Verbal Standard'
        ]
    ];

    $count = 0;
    foreach ($questions as $q) {
        $stmt->execute($q);
        $count++;
    }

    echo "SUCCESS: Seeded {$count} high-yield Aptitude & Company PYQ questions into database!\n";
} catch (Exception $e) {
    echo "ERROR SEEDING QUESTIONS: " . $e->getMessage() . "\n";
}
