<?php
require_once __DIR__ . '/helper.php';

function getInfosysQuestions(): array {
    $b1 = [];
    $b2 = [];
    $c = 'Infosys';

    // ========================================================
    // BATCH 1 (40 Questions)
    // ========================================================
    // --- Quant / Math Ability (12 Qs) ---
    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'Find the probability of getting a sum of 9 when two fair six-sided dice are rolled simultaneously.',
        '1/9', '1/6', '1/12', '5/36', 'A',
        'Total possible outcomes = 6 * 6 = 36. Favorable outcomes for sum 9 are: (3,6), (4,5), (5,4), (6,3) = 4 pairs. Probability = 4 / 36 = 1/9.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'medium',
        'In how many ways can the letters of the word "CORPORATION" be arranged so that all the vowels always come together?',
        '50,400', '25,200', '72,000', '12,600', 'A',
        'Vowels in CORPORATION are O, O, A, I, O (5 vowels: 3 Os, 1 A, 1 I). Consonants are C, R, P, R, T, N (6 consonants: 2 Rs, 1 C, 1 P, 1 T, 1 N). Treating the vowels as 1 single block gives 6 + 1 = 7 items. Arrangements of 7 items with 2 Rs = 7! / 2! = 5040 / 2 = 2520. Arrangements of the 5 vowels with 3 Os = 5! / 3! = 120 / 6 = 20. Total arrangements = 2520 * 20 = 50,400.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'hard',
        'A bag contains 5 red, 4 blue, and 3 green marbles. If 3 marbles are drawn at random without replacement, what is the probability that all 3 are of different colors?',
        '3/11', '1/11', '6/55', '9/44', 'A',
        'Total marbles = 5 + 4 + 3 = 12. Total ways to select 3 marbles = 12C3 = (12 * 11 * 10) / (3 * 2 * 1) = 220. Favorable ways to choose 1 of each color = 5C1 * 4C1 * 3C1 = 5 * 4 * 3 = 60. Probability = 60 / 220 = 6 / 22 = 3/11.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'easy',
        'An analyst’s salary was reduced by 20% during restructuring and later increased by 25%. What is the net change in his salary?',
        'No change (0%)', '5% increase', '5% decrease', '2% increase', 'A',
        'Let initial salary = 100. After 20% reduction = 80. After 25% increase on 80: 80 * 1.25 = 100. Net change is 0%.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'A merchant sold a monitor at a gain of 15%. If he had sold it for Rs. 450 more, he would have gained 20%. What is the cost price of the monitor?',
        'Rs. 9,000', 'Rs. 8,500', 'Rs. 9,500', 'Rs. 10,000', 'A',
        'Difference in percentage = 20% - 15% = 5%. 5% of CP = Rs. 450. Therefore, CP = 450 * (100 / 5) = Rs. 9,000.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'hard',
        'In a mixture of 80 liters, milk and water are in the ratio 7 : 3. How many liters of water must be added to make the ratio 2 : 1?',
        '4 liters', '6 liters', '8 liters', '5 liters', 'A',
        'Milk = (7/10) * 80 = 56 L. Water = (3/10) * 80 = 24 L. Let w liters of water be added: 56 / (24 + w) = 2 / 1 => 56 = 48 + 2w => 2w = 8 => w = 4 liters.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A person covers a distance of 180 km in 3 hours by car. What is the speed in meters per second?',
        '16.67 m/s', '20 m/s', '15 m/s', '18.5 m/s', 'A',
        'Speed in km/h = 180 / 3 = 60 km/h. Speed in m/s = 60 * (5/18) = 300 / 18 ≈ 16.67 m/s.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A train 240 m long passes a pole in 24 seconds. How long will it take to pass a platform 650 m long?',
        '89 seconds', '85 seconds', '92 seconds', '80 seconds', 'A',
        'Speed of train = 240 / 24 = 10 m/s. Total distance to cross platform = 240 + 650 = 890 m. Time required = 890 / 10 = 89 seconds.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'hard',
        'A boat takes 90 minutes less to travel 36 km downstream than to travel the same distance upstream. If the speed of the boat in still water is 10 km/h, what is the speed of the stream?',
        '2 km/h', '3 km/h', '1.5 km/h', '2.5 km/h', 'A',
        'Let speed of stream = s. Time upstream - Time downstream = 90 mins = 1.5 hrs. 36/(10 - s) - 36/(10 + s) = 1.5. Testing s = 2: 36/8 - 36/12 = 4.5 - 3.0 = 1.5 hrs. Matches perfectly. Speed of stream is 2 km/h.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'easy',
        'Worker P can complete a task in 8 days and Q can do the same task in 12 days. In how many days can they complete the task together?',
        '4.8 days', '5.2 days', '4.5 days', '5.0 days', 'A',
        'Combined rate = 1/8 + 1/12 = (3 + 2) / 24 = 5 / 24. Total days = 24 / 5 = 4.8 days.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A can fill a cistern in 12 minutes and Pipe B in 15 minutes. A waste pipe C can drain the cistern in 10 minutes. If all three pipes are opened together in an empty tank, how many minutes will it take to fill the tank?',
        '20 minutes', '25 minutes', '18 minutes', '30 minutes', 'A',
        'Net rate = 1/12 + 1/15 - 1/10 = (5 + 4 - 6) / 60 = 3 / 60 = 1/20. The tank will fill in 20 minutes.');

    $b1[] = makeQ($c, 'DI_BAR_TABLE', 'medium',
        'A software company reports quarterly bugs logged vs resolved: Q1: 120 logged, 100 resolved; Q2: 150 logged, 120 resolved; Q3: 180 logged, 160 resolved; Q4: 200 logged, 180 resolved. What is the total resolution rate percentage over the year?',
        '86.15%', '82.50%', '88.20%', '84.00%', 'A',
        'Total logged = 120 + 150 + 180 + 200 = 650. Total resolved = 100 + 120 + 160 + 180 = 560. Resolution rate = (560 / 650) * 100 ≈ 86.15%.');

    // --- Logical Reasoning / Critical Thinking (10 Qs) ---
    $b1[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'In the cryptarithmetic problem: SEND + MORE = MONEY, what single digit does the letter M represent (M != 0)?',
        '1', '2', '9', '8', 'A',
        'Adding two 4-digit numbers can produce at most a 5-digit number starting with a carry of 1. Hence, M must be 1.');

    $b1[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Four friends A, B, C, and D wear shirts of different colors: Red, Blue, Green, and Yellow. A does not wear Red or Blue. B wears Yellow. C does not wear Red. What color shirt is A wearing?',
        'Green', 'Red', 'Blue', 'Yellow', 'A',
        'B wears Yellow. C does not wear Red or Yellow, and cannot wear Green if A takes it. A cannot wear Red, Blue, or Yellow, so A must wear Green.');

    $b1[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next number in the sequence: 5, 11, 23, 47, 95, ?',
        '191', '190', '192', '189', 'A',
        'Each term is multiplied by 2 and incremented by 1: 5 * 2 + 1 = 11, 11 * 2 + 1 = 23, 23 * 2 + 1 = 47, 47 * 2 + 1 = 95, 95 * 2 + 1 = 191.');

    $b1[] = makeQ($c, 'LR_SERIES', 'medium',
        'Determine the missing term: 2, 6, 12, 20, 30, 42, ?',
        '56', '54', '58', '52', 'A',
        'Pattern is n * (n + 1): 1*2=2, 2*3=6, 3*4=12, 4*5=20, 5*6=30, 6*7=42, 7*8=56.');

    $b1[] = makeQ($c, 'LR_CODING', 'easy',
        'If CLOUD is written as ENQWF, how is RAIN written in that code?',
        'TCKP', 'TCKQ', 'TBKP', 'SCKP', 'A',
        'Each letter is shifted forward by 2 positions: R (+2) = T, A (+2) = C, I (+2) = K, N (+2) = P. Code is TCKP.');

    $b1[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, LEADER is coded as 20-13-9-12-13-26. Each letter position in the alphabet is added to 8. What is the code for TEAM?',
        '28-13-9-21', '28-12-9-21', '27-13-9-21', '28-13-8-21', 'A',
        'T = 20 + 8 = 28; E = 5 + 8 = 13; A = 1 + 8 = 9; M = 13 + 8 = 21. Code is 28-13-9-21.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'easy',
        'Pointing to a gentleman, Deepak said: "His only brother is the father of my daughter\'s father." How is the gentleman related to Deepak?',
        'Uncle', 'Father', 'Brother', 'Grandfather', 'A',
        '"My daughter\'s father" is Deepak himself. The father of Deepak is his only brother, meaning the gentleman is Deepak\'s father\'s brother, which is his uncle.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'medium',
        'A and B are brothers. C and D are sisters. A\'s son is D\'s brother. How is B related to C?',
        'Uncle', 'Father', 'Brother', 'Grandfather', 'A',
        'Since A\'s son is D\'s brother, and C and D are sisters, A is the father of C and D. B is the brother of A, so B is the uncle of C.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Kunal walks 10 km towards North. From there, he walks 6 km towards South. Then, he walks 3 km towards East. How far and in what direction is he with reference to his starting point?',
        '5 km North-East', '5 km North-West', '7 km East', '5 km South-East', 'A',
        'Net displacement: North = 10 - 6 = 4 km; East = 3 km. Shortest distance = sqrt(4^2 + 3^2) = sqrt(16 + 9) = 5 km in North-East direction.');

    $b1[] = makeQ($c, 'LR_SEATING', 'medium',
        'Five students P, Q, R, S, and T sit in a row facing north. S sits between T and Q. Q is to the immediate left of R. P is to the immediate left of T. Who sits in the exact middle of the row?',
        'S', 'T', 'Q', 'R', 'A',
        'Placing according to constraints: P is left of T (P, T). S is between T and Q (P, T, S, Q). Q is left of R (P, T, S, Q, R). S sits in the middle.');

    // --- Verbal Ability (6 Qs) ---
    $b1[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Select the correctly punctuated and capitalized sentence:',
        'The tech lead asked, "Have all unit tests passed?"',
        'The tech lead asked "Have all unit tests passed"?',
        'The tech lead asked, "have all unit tests passed?"',
        'The tech lead asked, Have all unit tests passed?', 'A',
        'A direct quotation starts with a capital letter and is preceded by a comma, with closing punctuation inside the quotation marks.');

    $b1[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the option that correctly rectifies the error in: "Each of the software modules have undergone strict regression testing."',
        'Each of the software modules has undergone strict regression testing.',
        'Each of the software modules were undergone strict regression testing.',
        'Each of the software modules are undergoing strict regression testing.',
        'Each of the software modules have been undergoing strict regression testing.', 'A',
        'The subject is the indefinite pronoun "Each", which is singular and requires the singular verb "has undergone".');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: CANDID',
        'Frank', 'Deceitful', 'Evasive', 'Shy', 'A',
        'Candid means truthful and straightforward; frank.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Choose the antonym for the word: METICULOUS',
        'Careless', 'Accurate', 'Painstaking', 'Detailed', 'A',
        'Meticulous means showing great attention to detail. The opposite is Careless.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Adopting code review guidelines reduces production bugs by over 40% while promoting knowledge sharing across team members." What is a dual advantage mentioned?',
        'Defect reduction and cross-team knowledge dissemination.',
        'Lower hardware costs and increased developer count.',
        'Elimination of compilation phases.',
        'Automated code generation without supervision.', 'A',
        'The passage explicitly mentions defect reduction (40% fewer bugs) and shared domain knowledge.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "Serverless architectures abstract infrastructure management, but introduce potential cold start delays and complex local debugging workflows." What is a key operational challenge of serverless?',
        'Debugging complexity and latency spikes during cold starts.',
        'The requirement to manually patch operating systems.',
        'The total inability to run cloud workloads.',
        'Excessive upfront server purchase costs.', 'A',
        'The passage highlights cold start delays and local debugging difficulties as operational challenges.');

    // --- Technical / Pseudocode MCQs (12 Qs) ---
    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of the following C code snippet?',
        '6', '5', '4', 'Compilation Error', 'A',
        'The ternary operator checks if a > b (10 > 5 is true), so it evaluates a - 4 = 10 - 4 = 6.',
        "#include <stdio.h>\nint main() {\n    int a = 10, b = 5;\n    int res = (a > b) ? (a - 4) : (b + 4);\n    printf(\"%d\", res);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What is the output of the bitwise shift operations in this C program?',
        '16', '8', '32', '4', 'A',
        'The left shift operator x << 2 shifts the bits of 4 left by 2 positions, multiplying 4 by 2^2 = 16.',
        "#include <stdio.h>\nint main() {\n    int x = 4;\n    int y = x << 2;\n    printf(\"%d\", y);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_CPP', 'medium',
        'In C++, what is the size of an empty class object created using sizeof?',
        '1 byte', '0 bytes', '4 bytes', 'Compiler dependent error', 'A',
        'In C++, an empty class has a non-zero size (typically 1 byte) to ensure each object instance has a distinct memory address.');

    $b1[] = makeQ($c, 'TECH_CPP', 'hard',
        'What will this C++ snippet output?',
        '100', '10', '0', 'Garbage Value', 'A',
        'Static member count is incremented once to 100 and shared across all instances. c.count prints 100.',
        "#include <iostream>\nclass Counter {\npublic:\n    static int count;\n};\nint Counter::count = 0;\nint main() {\n    Counter::count = 100;\n    Counter c;\n    std::cout << c.count;\n    return 0;\n}", 'cpp');

    $b1[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which access modifier allows access within the same package and by subclasses in other packages?',
        'protected', 'public', 'private', 'default', 'A',
        'The protected access modifier allows access within the package and by subclasses outside the package.');

    $b1[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code output?',
        '30', '1020', 'Compilation Error', 'Runtime Error', 'A',
        'In Java, a + b evaluates integer addition 10 + 20 = 30.',
        "int a = 10;\nint b = 20;\nSystem.out.println(a + b);", 'java');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        'olleh', 'hello', 'h', 'o', 'A',
        'The slice [::-1] reverses the string "hello" to "olleh".',
        "s = \"hello\"\nprint(s[::-1])", 'python');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python set operation?',
        '{3}', '{1, 2, 3, 4, 5}', '{1, 2}', '{4, 5}', 'A',
        'The & operator computes set intersection. Common element between {1, 2, 3} and {3, 4, 5} is {3}.',
        "s1 = {1, 2, 3}\ns2 = {3, 4, 5}\nprint(s1 & s2)", 'python');

    $b1[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL keyword is used to eliminate duplicate values from the result set of a query?',
        'DISTINCT', 'UNIQUE', 'DIFFERENT', 'REMOVE', 'A',
        'The DISTINCT keyword in SQL filters duplicate rows from the returned result set.');

    $b1[] = makeQ($c, 'TECH_SQL', 'medium',
        'What does the SQL command ROLLBACK do within a database transaction?',
        'It undoes all database changes made since the transaction began.',
        'It saves all changes permanently to storage.',
        'It removes the transaction table from the database.',
        'It restarts the database server.', 'A',
        'ROLLBACK restores database state to the last consistent checkpoint before transaction initiation.');

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'What is the time complexity of pushing an element onto an array-based stack with available capacity?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n^2)', 'A',
        'Inserting at the top of a stack with allocated capacity takes constant time O(1).');

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'In a Min-Heap binary tree, where is the smallest element always located?',
        'At the root node', 'At the deepest leaf node', 'At the leftmost leaf node', 'Anywhere in the tree', 'A',
        'By the Min-Heap property, the key of every node is greater than or equal to the key of its parent, placing the minimum at the root.');

    // ========================================================
    // BATCH 2 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the average of the first 25 natural numbers (from 1 to 25)?',
        '13', '12.5', '13.5', '14', 'A',
        'Average of first n natural numbers is (n + 1) / 2 = (25 + 1) / 2 = 26 / 2 = 13.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'medium',
        'If log10(2) = 0.3010 and log10(3) = 0.4771, find the value of log10(6).',
        '0.7781', '0.7881', '0.7681', '0.7981', 'A',
        'log10(6) = log10(2 * 3) = log10(2) + log10(3) = 0.3010 + 0.4771 = 0.7781.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Three bells toll together at intervals of 9, 12, and 15 minutes respectively. If they toll together now, after how many hours will they toll together next?',
        '3 hours', '2.5 hours', '4 hours', '3.5 hours', 'A',
        'LCM(9, 12, 15): 9 = 3^2, 12 = 2^2 * 3, 15 = 3 * 5. LCM = 2^2 * 3^2 * 5 = 4 * 9 * 5 = 180 minutes = 3 hours.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'An item bought for Rs. 500 was sold for Rs. 625. What was the profit percentage?',
        '25%', '20%', '30%', '15%', 'A',
        'Profit = 625 - 500 = 125. Profit % = (125 / 500) * 100 = 25%.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'medium',
        'What single discount is equivalent to two successive discounts of 20% and 15%?',
        '32%', '35%', '30%', '28%', 'A',
        'Equivalent discount = d1 + d2 - (d1 * d2) / 100 = 20 + 15 - (20 * 15) / 100 = 35 - 3 = 32%.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'hard',
        'A sum of money invested at compound interest doubles itself in 4 years. In how many years will it amount to 8 times the original principal at the same annual rate?',
        '12 years', '8 years', '16 years', '10 years', 'A',
        'Under compound interest, if principal becomes 2x in 4 years, it becomes 2^3 = 8x in 3 * 4 = 12 years.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'If A : B = 3 : 4 and B : C = 8 : 9, find the continuous ratio A : B : C.',
        '6 : 8 : 9', '3 : 4 : 9', '6 : 7 : 9', '3 : 8 : 9', 'A',
        'Multiply first ratio by 2 to align B: A : B = 6 : 8. B : C = 8 : 9. Thus A : B : C = 6 : 8 : 9.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'medium',
        'Divide Rs. 1,400 among A, B, and C such that A gets half of what B gets, and B gets half of what C gets. How much does A receive?',
        'Rs. 200', 'Rs. 400', 'Rs. 800', 'Rs. 150', 'A',
        'Let A = x. Then B = 2x, and C = 4x. Total = x + 2x + 4x = 7x = 1400 => x = 200. A receives Rs. 200.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'easy',
        'How many seconds does a car taking 10 minutes to cover a certain distance take in seconds?',
        '600 seconds', '500 seconds', '120 seconds', '360 seconds', 'A',
        '10 minutes * 60 seconds/minute = 600 seconds.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'medium',
        'Walking at 3/4 of his usual speed, an engineer reaches office 15 minutes late. What is his usual travel time to the office?',
        '45 minutes', '60 minutes', '30 minutes', '50 minutes', 'A',
        'Speed ratio usual:new = 1 : 3/4 = 4 : 3. Time ratio usual:new = 3 : 4. Difference in time = 1 unit = 15 minutes. Usual time = 3 units = 3 * 15 = 45 minutes.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        '12 workers can assemble 60 servers in 5 days. How many servers can 6 workers assemble in 5 days at the same pace?',
        '30 servers', '25 servers', '35 servers', '20 servers', 'A',
        'Number of workers is halved while days remain identical, so output is halved: 60 / 2 = 30 servers.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'medium',
        'A cistern has two pipes. One can fill it with water in 8 hours and other can empty it in 5 hours. In how many hours will the full cistern be emptied if both pipes are opened?',
        '13.33 hours', '12 hours', '15 hours', '10.5 hours', 'A',
        'Net emptying rate = 1/5 - 1/8 = (8 - 5) / 40 = 3/40. Time to empty full tank = 40 / 3 ≈ 13.33 hours.');

    // --- Logical Reasoning (10 Qs) ---
    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next term in the series: 3, 9, 27, 81, ?',
        '243', '216', '162', '256', 'A',
        'Each term is multiplied by 3: 3 * 3 = 9, 9 * 3 = 27, 27 * 3 = 81, 81 * 3 = 243.');

    $b2[] = makeQ($c, 'LR_SERIES', 'medium',
        'Complete the sequence: 1, 4, 9, 16, 25, ?',
        '36', '49', '30', '40', 'A',
        'The sequence consists of consecutive squares: 1^2=1, 2^2=4, 3^2=9, 4^2=16, 5^2=25, 6^2=36.');

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'In a code, FLOWER is coded as 6-12-15-23-5-18. How is TREE coded?',
        '20-18-5-5', '19-18-5-5', '20-17-5-5', '20-18-6-6', 'A',
        'Letter positions: T=20, R=18, E=5, E=5. Code is 20-18-5-5.');

    $b2[] = makeQ($c, 'LR_CODING', 'medium',
        'If ROBUST is coded as QNATRS in a code, how is ZEBRA coded?',
        'YDAQZ', 'YDAPZ', 'YCAPZ', 'ZDAQZ', 'A',
        'Each letter is shifted backward by 1 position: Z-1=Y, E-1=D, B-1=A, R-1=Q, A-1=Z. Code is YDAQZ.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'easy',
        'A is the mother of B. B is the sister of C. How is A related to C?',
        'Mother', 'Aunt', 'Sister', 'Grandmother', 'A',
        'Since B is the sister of C, B and C share the same mother. A is the mother of C.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'medium',
        'M is the sister of N. N is married to O. P is the father of O. How is P related to N?',
        'Father-in-law', 'Father', 'Uncle', 'Brother-in-law', 'A',
        'P is the father of O, and N is married to O. Therefore, P is the father-in-law of N.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'A person walks 15 meters towards West, turns right and walks 15 meters. In which direction is he now facing?',
        'North', 'South', 'East', 'West', 'A',
        'Facing West, a right turn orients the person towards North.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'A cyclist rides 4 km East, then 3 km North. What is the shortest distance from the start point?',
        '5 km', '7 km', '6 km', '4.5 km', 'A',
        'Distance = sqrt(4^2 + 3^2) = sqrt(16 + 9) = sqrt(25) = 5 km.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'In a row of 40 trees, a mango tree is 14th from the left. What is its position from the right end?',
        '27th', '26th', '28th', '25th', 'A',
        'Position from right = Total - Position from left + 1 = 40 - 14 + 1 = 27th.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: Some developers are designers. All designers are creative. Conclusions: I. Some developers are creative. II. No developer is creative.',
        'Only I follows', 'Only II follows', 'Neither follows', 'Both follow', 'A',
        'Since developers overlap with designers and all designers are creative, the developers who are designers must be creative. Conclusion I follows.');

    // --- Verbal Ability (6 Qs) ---
    $b2[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Select the sentence that uses the correct preposition:',
        'She is proficient in Python and database management.',
        'She is proficient at Python and database management.',
        'She is proficient with Python and database management.',
        'She is proficient on Python and database management.', 'A',
        'The adjective "proficient" is correctly paired with the preposition "in".');

    $b2[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Choose the grammatically correct sentence:',
        'The committee agreed on the proposed architectural framework.',
        'The committee agreed to the proposed architectural framework with unanimous.',
        'The committee was agreeing on proposed architectural framework.',
        'The committee have agree on the proposed framework.', 'A',
        '"The committee agreed on the proposed architectural framework" is standard, correct English.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the antonym for: EXPEDITE',
        'Delay', 'Accelerate', 'Hasten', 'Quicken', 'A',
        'Expedite means to speed up. The antonym is Delay.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the word that means "having mixed or contradictory feelings about something":',
        'Ambivalent', 'Ambiguous', 'Equivocal', 'Apathetic', 'A',
        'Ambivalent means having mixed or contradictory feelings towards someone or something.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the notice: "Strict idempotency ensures that duplicate API requests resulting from network timeouts do not cause duplicate financial transactions." What is the purpose of idempotency?',
        'Preventing duplicate financial operations when requests are retried.',
        'Increasing network timeout limits to maximum values.',
        'Disabling retries entirely across payment gateways.',
        'Speeding up packet routing over satellite internet.', 'A',
        'Idempotency ensures that identical retried requests yield the same outcome without duplicate execution.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the summary: "Test-Driven Development (TDD) shifts debugging left by forcing requirements clarification into executable unit specifications before writing implementation code." What does "shifting debugging left" imply?',
        'Identifying defects and requirements gaps early in the development lifecycle.',
        'Moving code repositories to left-aligned formatting standards.',
        'Postponing testing until after customer deployment.',
        'Writing documentation in place of automated tests.', 'A',
        '"Shift left" refers to verifying quality early in the lifecycle rather than waiting for downstream testing.');

    // --- Technical / Pseudocode MCQs (12 Qs) ---
    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of the following C code demonstrating array indexing?',
        '30', '10', '20', '40', 'A',
        'arr[2] accesses the third element (0-indexed), which is 30.',
        "#include <stdio.h>\nint main() {\n    int arr[] = {10, 20, 30, 40};\n    printf(\"%d\", arr[2]);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What is the output of this C function with static local variable?',
        '1 2 3', '1 1 1', '3 2 1', '0 1 2', 'A',
        'Static variable count retains its value between function calls. count is incremented: 1, 2, 3.',
        "#include <stdio.h>\nvoid increment() {\n    static int count = 0;\n    count++;\n    printf(\"%d \", count);\n}\nint main() {\n    increment();\n    increment();\n    increment();\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_CPP', 'medium',
        'In C++, which operator cannot be overloaded?',
        ':: (Scope Resolution Operator)', '+ (Addition)', '== (Equality)', '[] (Subscript)', 'A',
        'The scope resolution operator (::), member access (.), and ternary (?:) cannot be overloaded in C++.');

    $b2[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ template function?',
        '50', '25', '10', 'Compilation Error', 'A',
        'Template function square computes 5 * 5 = 25... wait! The code calculates a * 2 = 10, or a * a? Let us write clean: template <typename T> T mulTwo(T x) { return x * 2; } with 25 returning 50.',
        "#include <iostream>\ntemplate <typename T>\nT mulTwo(T x) {\n    return x * 2;\n}\nint main() {\n    std::cout << mulTwo(25);\n    return 0;\n}", 'cpp');

    $b2[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which method of the Object class in Java returns a hash code value for the object?',
        'hashCode()', 'getHash()', 'hash()', 'toHash()', 'A',
        'The hashCode() method in Java Object class returns the integer hash code.');

    $b2[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will be the output of the following Java snippet?',
        '5', '4', '6', 'Compilation Error', 'A',
        'String "Infosys" length is 7... wait: "Hello" has length 5. s.length() prints 5.',
        "String s = \"Hello\";\nSystem.out.println(s.length());", 'java');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What is the output of the following Python expression?',
        '8', '6', '9', '2', 'A',
        '2 ** 3 in Python represents exponentiation (2 cubed), which is 8.',
        "print(2 ** 3)", 'python');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python code snippet?',
        'Hello World', 'helloworld', 'Hello', 'TypeError', 'A',
        'join() concatenates elements of the list with a space delimiter, producing "Hello World".',
        "words = ['Hello', 'World']\nprint(' '.join(words))", 'python');

    $b2[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL aggregate function computes the total sum of a numeric column?',
        'SUM()', 'TOTAL()', 'ADD()', 'COUNT()', 'A',
        'The SUM() function returns the total sum of numerical values in a specified column.');

    $b2[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which constraint in SQL enforces that all column values in a table are non-null and uniquely identify each row?',
        'PRIMARY KEY', 'FOREIGN KEY', 'CHECK', 'DEFAULT', 'A',
        'A PRIMARY KEY constraint uniquely identifies each record in a database table and enforces NOT NULL and UNIQUE.');

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which sorting algorithm operates on the divide-and-conquer paradigm and has a guaranteed worst-case time complexity of O(n log n)?',
        'Merge Sort', 'Quick Sort', 'Bubble Sort', 'Insertion Sort', 'A',
        'Merge Sort divides the array in halves recursively and merges them in linear time, guaranteeing O(n log n) in all cases.');

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'In a singly linked list, what is the time complexity of deleting the first node (head)?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n^2)', 'A',
        'Deleting the head node only requires updating the head pointer to head.next, which takes O(1) constant time.');

    return ['batch1' => $b1, 'batch2' => $b2];
}
