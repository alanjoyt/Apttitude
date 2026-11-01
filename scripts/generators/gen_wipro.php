<?php
require_once __DIR__ . '/helper.php';

function getWiproQuestions(): array {
    $b1 = [];
    $b2 = [];
    $c = 'Wipro NLTH';

    // ========================================================
    // BATCH 1 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'At what angle are the hands of a clock inclined at 3:30?',
        '75 degrees', '70 degrees', '80 degrees', '65 degrees', 'A',
        'Angle = |30*H - (11/2)*M| = |30*3 - (11/2)*30| = |90 - 165| = 75 degrees.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'medium',
        'If 1st January 2024 was a Monday, what day of the week was 31st December 2024 (a leap year)?',
        'Tuesday', 'Monday', 'Wednesday', 'Sunday', 'A',
        'A leap year has 366 days, which is 52 weeks and 2 odd days. If Jan 1 is day 1, Dec 31 is day 366. Difference = 365 days = 52 weeks + 1 day. Monday + 1 day = Tuesday.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'hard',
        'What is the least number which when divided by 5, 6, 7, and 8 leaves a remainder of 3, but when divided by 9 leaves no remainder?',
        '1683', '1680', '843', '2523', 'A',
        'LCM(5, 6, 7, 8) = 840. The number is of the form 840k + 3. For k = 1: 843 (sum of digits 15, not divisible by 9). For k = 2: 840*2 + 3 = 1683. Sum of digits = 1 + 6 + 8 + 3 = 18 (divisible by 9). Hence, 1683.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'easy',
        'A shopkeeper sells an article for Rs. 720 making a 20% profit. What was the cost price of the article?',
        'Rs. 600', 'Rs. 580', 'Rs. 620', 'Rs. 640', 'A',
        'SP = CP * 1.20 => 720 = CP * 1.20 => CP = 720 / 1.20 = Rs. 600.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'The cost of 5 pens and 3 notebooks is Rs. 140, while 3 pens and 5 notebooks cost Rs. 180. What is the cost of 1 pen and 1 notebook together?',
        'Rs. 40', 'Rs. 35', 'Rs. 45', 'Rs. 50', 'A',
        'Adding both equations: 8 pens + 8 notebooks = 320 => 1 pen + 1 notebook = 320 / 8 = Rs. 40.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'hard',
        'A dishonest dealer professes to sell his goods at cost price but uses a weight of 950 grams for a 1 kg weight. Find his real gain percentage.',
        '5.26%', '5.00%', '5.50%', '4.75%', 'A',
        'Gain % = (Error / (True Value - Error)) * 100 = (50 / 950) * 100 = (5 / 95) * 100 = (1 / 19) * 100 ≈ 5.26%.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A bus travels at 54 km/h. How many meters does it cover in 20 seconds?',
        '300 m', '250 m', '320 m', '270 m', 'A',
        'Speed in m/s = 54 * (5/18) = 15 m/s. Distance = 15 * 20 = 300 m.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A train 150 meters long crosses a bridge 250 meters long in 20 seconds. What is the speed of the train in km/h?',
        '72 km/h', '64 km/h', '80 km/h', '54 km/h', 'A',
        'Total distance = 150 + 250 = 400 m. Speed = 400 / 20 = 20 m/s. In km/h = 20 * (18/5) = 72 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'hard',
        'Two men start walking together from point X to point Y, a distance of 21 km, at speeds of 3 km/h and 4 km/h respectively. The faster man reaches Y, turns back immediately, and meets the slower man at Z. What is the distance from X to Z?',
        '18 km', '16 km', '19 km', '17 km', 'A',
        'Total distance covered by both = 2 * 21 = 42 km. Relative speed = 3 + 4 = 7 km/h. Time taken until meeting = 42 / 7 = 6 hours. Distance covered by the slower walker (X to Z) = 3 km/h * 6 hours = 18 km.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'easy',
        'If 15 men can build a wall in 48 hours, how many men will be required to do the same work in 30 hours?',
        '24 men', '20 men', '25 men', '22 men', 'A',
        'M1 * H1 = M2 * H2 => 15 * 48 = M2 * 30 => M2 = 720 / 30 = 24 men.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'medium',
        'A and B can do a piece of work in 10 days and 15 days respectively. They work together for 3 days, and then B leaves. In how many more days will A finish the remaining work?',
        '5 days', '6 days', '4 days', '7 days', 'A',
        'Work done by A and B together in 1 day = 1/10 + 1/15 = 5/30 = 1/6. In 3 days they complete 3 * (1/6) = 1/2 of the work. Remaining work = 1/2. Time taken by A alone = (1/2) / (1/10) = 5 days.');

    $b1[] = makeQ($c, 'DI_BAR_TABLE', 'medium',
        'A software company tests code coverage across 4 modules: Mod 1: 80%, Mod 2: 70%, Mod 3: 90%, Mod 4: 80%. If each module has 500 lines of code, what is the total number of tested lines across all 4 modules?',
        '1600 lines', '1500 lines', '1700 lines', '1800 lines', 'A',
        'Lines per module: Mod 1 = 400, Mod 2 = 350, Mod 3 = 450, Mod 4 = 400. Total tested lines = 400 + 350 + 450 + 400 = 1600 lines.');

    // --- Logical Reasoning (10 Qs) ---
    $b1[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next number in the series: 7, 14, 28, 56, ?',
        '112', '98', '108', '124', 'A',
        'Each number is multiplied by 2: 7 * 2 = 14, 14 * 2 = 28, 28 * 2 = 56, 56 * 2 = 112.');

    $b1[] = makeQ($c, 'LR_SERIES', 'medium',
        'Identify the missing term in the sequence: 4, 18, 48, 100, ?',
        '180', '160', '190', '175', 'A',
        'Pattern is n^2 * (n + 1) or n^3 - n^2: 2^3 - 2^2 = 4; 3^3 - 3^2 = 18; 4^3 - 4^2 = 48; 5^3 - 5^2 = 100; 6^3 - 6^2 = 216 - 36 = 180.');

    $b1[] = makeQ($c, 'LR_CODING', 'easy',
        'If FRIEND is coded as HUMJTK, how is CANDLE coded in that manner?',
        'EDRIRL', 'EDRIRK', 'ECRIRL', 'EDRIRJ', 'A',
        'Each letter is shifted forward: F(+2)=H, R(+3)=U, I(+4)=M, E(+5)=J, N(+6)=T, D(+7)=K. For CANDLE: C(+2)=E, A(+3)=D, N(+4)=R, D(+5)=I, L(+6)=R, E(+7)=L. Code is EDRIRL.');

    $b1[] = makeQ($c, 'LR_CODING', 'medium',
        'In a certain code language, "786" means "study very hard", "958" means "hard work pays", and "645" means "study and work". What digit stands for "very"?',
        '7', '8', '6', '9', 'A',
        'Comparing "786" and "645": common word is "study" -> 6. Comparing "786" and "958": common word is "hard" -> 8. In "786", remaining word is "very" -> 7.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'easy',
        'Pointing to a boy in the park, Sunita says: "He is the son of my grandfather\'s only son." How is the boy related to Sunita?',
        'Brother', 'Cousin', 'Uncle', 'Nephew', 'A',
        'Sunita\'s grandfather\'s only son is Sunita\'s father. The son of Sunita\'s father is her brother.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'medium',
        'If P $ Q means P is the father of Q, P # Q means P is the mother of Q, and P * Q means P is the sister of Q, which expression shows that N is the aunt of M?',
        'N * P $ M', 'N # P $ M', 'N $ P * M', 'N * P # M', 'A',
        'In N * P $ M: N is the sister of P, and P is the father of M. Thus N is the sister of M\'s father, making N the paternal aunt of M.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Rohit walks 12 km West, turns right and walks 9 km. What is the straight-line distance from his starting point?',
        '15 km', '21 km', '18 km', '13 km', 'A',
        'Distance = sqrt(12^2 + 9^2) = sqrt(144 + 81) = sqrt(225) = 15 km.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'A person starts from point A and walks 20 m North, then turns right and walks 30 m, then turns right and walks 35 m, then turns left and walks 15 m, then turns left and walks 15 m to point B. What is the shortest distance between A and B?',
        '45 m', '35 m', '50 m', '40 m', 'A',
        'North-South net: +20 - 35 + 15 = 0 m. East-West net: +30 + 15 = 45 m East. The distance between A and B is 45 m.');

    $b1[] = makeQ($c, 'LR_SEATING', 'medium',
        'Six friends A, B, C, D, E, and F sit in a row facing North. C sits between A and E. D is at an extreme end. B is to the immediate right of E. Who is to the immediate left of C?',
        'A', 'E', 'B', 'D', 'A',
        'C is between A and E. Since B is to the immediate right of E, the order must be A, C, E, B. Thus to the immediate left of C is A.');

    $b1[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All laptops are devices. No device is obsolete. Conclusions: I. No laptop is obsolete. II. Some devices are laptops.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'Since all laptops are inside devices and no device is obsolete, no laptop can be obsolete (I follows). Since all laptops are devices, some devices must be laptops (II follows). Both follow.');

    // --- Verbal Ability (6 Qs) ---
    $b1[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Fill in the blank with the appropriate word: "The database cluster has been operating smoothly ______ last Friday."',
        'since', 'for', 'from', 'during', 'A',
        '"Since" is used with a specific point in past time ("last Friday") in the present perfect continuous tense.');

    $b1[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the sentence free from grammatical errors:',
        'The team of developers has submitted its release notes on time.',
        'The team of developers have submitted their release notes on time.',
        'The team of developers has submitted their release notes on time.',
        'The team of developers have submitted its release notes on time.', 'A',
        '"Team" is a collective noun acting as a single unit here, requiring singular verb "has" and singular pronoun "its".');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: COGNIZANT',
        'Aware', 'Ignorant', 'Unconscious', 'Unfamiliar', 'A',
        'Cognizant means having knowledge or being aware.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Choose the antonym for the word: ENIGMATIC',
        'Clear', 'Mysterious', 'Puzzling', 'Cryptic', 'A',
        'Enigmatic means mysterious and difficult to understand. Its opposite is Clear.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the sentence: "Automating repetitive database migrations frees database administrators to focus on proactive query tuning and capacity planning." What is the main outcome of automating migrations?',
        'DBAs can allocate more time to performance tuning and planning.',
        'It eliminates the need for database administrators entirely.',
        'It completely prevents all database downtime.',
        'It increases manual database maintenance workload.', 'A',
        'The passage notes that automation frees DBAs to concentrate on query tuning and capacity planning.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the excerpt: "While technical debt allows startups to release features rapidly to validate market demand, excessive debt eventually slows feature velocity and increases maintenance costs." What does this passage suggest about technical debt?',
        'It is a strategic compromise between speed-to-market and long-term codebase health.',
        'It should never be incurred under any circumstances.',
        'It permanently guarantees market success for startups.',
        'It decreases maintenance costs over time.', 'A',
        'The excerpt highlights the trade-off between near-term speed and downstream architectural debt.');

    // --- Technical & Coding MCQs (12 Qs) ---
    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What will be printed by this C program?',
        '25', '20', '30', 'Compilation Error', 'A',
        'Function square returns 5 * 5 = 25. printf prints 25.',
        "#include <stdio.h>\nint square(int x) {\n    return x * x;\n}\nint main() {\n    printf(\"%d\", square(5));\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What is the output of this C code snippet with pointers?',
        '40', '10', '20', '30', 'A',
        'ptr points to arr[0]. *(ptr + 3) accesses arr[3], which is 40.',
        "#include <stdio.h>\nint main() {\n    int arr[] = {10, 20, 30, 40};\n    int *ptr = arr;\n    printf(\"%d\", *(ptr + 3));\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_CPP', 'medium',
        'Which feature in C++ enables a derived class to provide a specific implementation of a method that is already defined in its base class?',
        'Method Overriding', 'Method Overloading', 'Encapsulation', 'Abstraction', 'A',
        'Method overriding allows a subclass to provide a specific definition of an inherited virtual method.');

    $b1[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is the output of this C++ snippet demonstrating destructor order?',
        'Destructed', 'Constructed', 'Compilation Error', 'Nothing', 'A',
        'When scope of obj ends at the closing brace of main, its destructor is called, printing "Destructed".',
        "#include <iostream>\nclass Test {\npublic:\n    ~Test() { std::cout << \"Destructed\"; }\n};\nint main() {\n    Test obj;\n    return 0;\n}", 'cpp');

    $b1[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which keyword in Java is used to inherit a class?',
        'extends', 'implements', 'inherits', 'import', 'A',
        'In Java, class inheritance is declared using the "extends" keyword.');

    $b1[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What is the output of the following Java snippet?',
        '6', '5', '4', '7', 'A',
        'The indexOf(\'W\') on string "Hello World" returns index 6 (0-indexed).',
        "String str = \"Hello World\";\nSystem.out.println(str.indexOf('W'));", 'java');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        'True', 'False', 'None', 'Error', 'A',
        'In Python, 5 in [1, 3, 5, 7] evaluates to True because 5 exists in the list.',
        "print(5 in [1, 3, 5, 7])", 'python');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python list operation?',
        '[1, 2, 3, 4]', '[4, 3, 2, 1]', '[1, 2, 3]', 'None', 'A',
        'append(4) adds 4 to the end of list [1, 2, 3], resulting in [1, 2, 3, 4].',
        "nums = [1, 2, 3]\nnums.append(4)\nprint(nums)", 'python');

    $b1[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL keyword sorts the query results in descending order?',
        'DESC', 'ASC', 'DOWN', 'SORT', 'A',
        'The DESC keyword in the ORDER BY clause sorts records in descending order.');

    $b1[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which type of SQL JOIN returns all rows from the left table and matching rows from the right table?',
        'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 'CROSS JOIN', 'A',
        'A LEFT JOIN (or LEFT OUTER JOIN) returns all records from the left table and matched records from the right table.');

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure follows the First In First Out (FIFO) principle?',
        'Queue', 'Stack', 'Tree', 'Graph', 'A',
        'A Queue operates on the First In First Out (FIFO) principle.');

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the time complexity of finding an element in a Hash Table under ideal conditions with no collisions?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n log n)', 'A',
        'In an ideal hash table without collisions, key lookup takes O(1) average constant time.');

    // ========================================================
    // BATCH 2 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the square root of 5184?',
        '72', '68', '74', '76', 'A',
        '72 * 72 = 5184.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'medium',
        'Find the sum of all odd integers between 1 and 20.',
        '100', '90', '110', '120', 'A',
        'Odd integers: 1, 3, 5, 7, 9, 11, 13, 15, 17, 19 (n = 10 numbers). Sum of first n odd numbers = n^2 = 10^2 = 100.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'hard',
        'The difference between simple and compound interest on Rs. 10,000 for 2 years at r% per annum is Rs. 64. What is the value of r?',
        '8%', '10%', '6%', '12%', 'A',
        'For 2 years: CI - SI = P * (r / 100)^2 => 64 = 10000 * (r / 100)^2 => 64 = r^2 => r = 8%.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 15% of 800?',
        '120', '110', '130', '140', 'A',
        '15% of 800 = (15 / 100) * 800 = 15 * 8 = 120.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'medium',
        'If the cost price of 12 items is equal to the selling price of 10 items, what is the profit percentage?',
        '20%', '25%', '16.67%', '15%', 'A',
        'Let CP of 1 item = Rs. 1. CP of 12 items = Rs. 12 = SP of 10 items. Profit on 10 items = 12 - 10 = Rs. 2. Profit % = (2 / 10) * 100 = 20%.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'hard',
        'An alloy contains 40% zinc, 30% copper, and the rest nickel. How many kilograms of nickel are present in 250 kg of this alloy?',
        '75 kg', '80 kg', '70 kg', '65 kg', 'A',
        'Percentage of nickel = 100% - (40% + 30%) = 30%. Amount of nickel = 30% of 250 kg = 0.30 * 250 = 75 kg.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'Two numbers are in the ratio 3 : 5. If their sum is 96, what is the smaller number?',
        '36', '42', '30', '40', 'A',
        'Total parts = 3 + 5 = 8. 1 part = 96 / 8 = 12. Smaller number = 3 * 12 = 36.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'medium',
        'The average of five consecutive integers is 28. What is the largest of these integers?',
        '30', '29', '31', '32', 'A',
        'For 5 consecutive numbers, the middle number is the average: n, n+1, n+2, n+3, n+4. n+2 = 28 => n = 26. The largest is n+4 = 30.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A vehicle covers 120 km in 2 hours. What is its speed in km/h?',
        '60 km/h', '50 km/h', '70 km/h', '55 km/h', 'A',
        'Speed = Distance / Time = 120 / 2 = 60 km/h.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A person covers 60 km at 30 km/h and another 60 km at 20 km/h. What is his average speed for the whole journey?',
        '24 km/h', '25 km/h', '22.5 km/h', '26 km/h', 'A',
        'Total distance = 120 km. Time 1 = 60 / 30 = 2 hours. Time 2 = 60 / 20 = 3 hours. Total time = 5 hours. Average speed = 120 / 5 = 24 km/h.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        'A can do a piece of work in 5 days and B in 10 days. Working together, in how many days will they finish the work?',
        '3.33 days', '3 days', '4 days', '2.5 days', 'A',
        'Combined rate = 1/5 + 1/10 = 3/10. Days = 10 / 3 ≈ 3.33 days.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'medium',
        '8 workers can complete a coding project in 12 days. How many additional workers are needed to finish the project in 8 days?',
        '4 workers', '2 workers', '6 workers', '8 workers', 'A',
        'Total worker-days = 8 * 12 = 96. To complete in 8 days, total workers needed = 96 / 8 = 12 workers. Additional workers = 12 - 8 = 4 workers.');

    // --- Logical Reasoning (10 Qs) ---
    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the missing term: 100, 95, 90, 85, ?',
        '80', '75', '82', '78', 'A',
        'Each term decreases by 5: 85 - 5 = 80.');

    $b2[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the next number in the series: 2, 5, 10, 17, 26, ?',
        '37', '35', '36', '38', 'A',
        'Pattern is n^2 + 1: 1^2+1=2, 2^2+1=5, 3^2+1=10, 4^2+1=17, 5^2+1=26, 6^2+1=37.');

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If CAT is coded as 3120, how is DOG coded?',
        '4157', '4147', '3157', '4158', 'A',
        'Letter positions: D=4, O=15, G=7. Combined: 4157.');

    $b2[] = makeQ($c, 'LR_CODING', 'medium',
        'In a certain code, "ROAD" is written as "URDG". How is "SWAN" written in that code?',
        'VZDQ', 'VZCQ', 'UZDQ', 'VYDQ', 'A',
        'Each letter is shifted forward by 3: S(+3)=V, W(+3)=Z, A(+3)=D, N(+3)=Q. Code is VZDQ.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'easy',
        'Pointing to a lady, a man said: "She is the daughter of my grandmother\'s only child." How is the lady related to the man?',
        'Sister', 'Mother', 'Aunt', 'Niece', 'A',
        'Grandmother\'s only child is the man\'s mother. The daughter of his mother is his sister.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'medium',
        'X is the brother of Y. Y is the wife of Z. Z is the son of W. How is W related to Y?',
        'Father-in-law or Mother-in-law', 'Father', 'Uncle', 'Brother-in-law', 'A',
        'Z is the husband of Y, and W is the parent of Z. Therefore, W is the father-in-law or mother-in-law of Y.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Sunil faces East. He turns 90 degrees clockwise and then 180 degrees counter-clockwise. Which direction is he facing now?',
        'North', 'South', 'West', 'East', 'A',
        'East + 90 deg clockwise = South. South + 180 deg counter-clockwise = North.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'A person travels 6 km North, 8 km East, and 6 km South. How far is he from his starting point?',
        '8 km', '6 km', '10 km', '14 km', 'A',
        'North and South displacements cancel out (6 - 6 = 0). The remaining displacement is 8 km East.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'In a class of 30 students, Rohan ranks 7th from the top. What is his rank from the bottom?',
        '24th', '23rd', '25th', '22nd', 'A',
        'Rank from bottom = 30 - 7 + 1 = 24th.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Four cars Red, Blue, Green, and White participate in a race. Blue finishes ahead of Green but behind Red. White finishes ahead of Red. Which car won the race?',
        'White', 'Red', 'Blue', 'Green', 'A',
        'Order of finish: White > Red > Blue > Green. White finishes first.');

    // --- Verbal Ability (6 Qs) ---
    $b2[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct option: "She ______ working in this technology stack for five years."',
        'has been', 'have been', 'is been', 'was been', 'A',
        'With singular subject "She" over a duration of time, "has been" is the correct present perfect continuous auxiliary.');

    $b2[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the sentence with correct subject-verb agreement:',
        'The quality of these test scripts is exceptional.',
        'The quality of these test scripts are exceptional.',
        'The quality of these test scripts were exceptional on tomorrow.',
        'The quality of these test scripts have been exceptional.', 'A',
        'The true subject is the singular noun "quality", which takes the singular verb "is".');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: RESILIENT',
        'Adaptable', 'Fragile', 'Rigid', 'Weak', 'A',
        'Resilient means able to withstand or recover quickly from difficult conditions; adaptable.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: VOLATILE',
        'Stable', 'Unpredictable', 'Explosive', 'Changeable', 'A',
        'Volatile means liable to change rapidly and unpredictably. The antonym is Stable.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Regular database index reorganization prevents fragmentation, keeping page read I/O operations minimal." What is the benefit of defragmenting indexes?',
        'It minimizes I/O operations by maintaining compact data pages.',
        'It converts relational tables into NoSQL documents.',
        'It reduces the need for primary keys.',
        'It deletes older historical data automatically.', 'A',
        'The statement notes that preventing fragmentation keeps disk I/O operations minimal.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the excerpt: "Encapsulation bundles data with the methods that operate on that data, restricting direct access from outside components to guard against unintended corruption." What core security benefit does encapsulation provide?',
        'It prevents external components from corrupting internal object state.',
        'It guarantees zero compile-time warnings.',
        'It enables multiple inheritance in all programming languages.',
        'It removes memory allocation limits in runtime.', 'A',
        'The text states encapsulation restricts direct access to guard against unintended state corruption.');

    // --- Technical & Coding MCQs (12 Qs) ---
    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C code?',
        '8', '4', '2', '16', 'A',
        'Multiplication: 2 * 4 = 8.',
        "#include <stdio.h>\nint main() {\n    int a = 2, b = 4;\n    printf(\"%d\", a * b);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print using a while loop?',
        '3 2 1', '3 2 1 0', '1 2 3', 'Infinite Loop', 'A',
        'The loop starts at n = 3, prints 3, decrements to 2; prints 2, decrements to 1; prints 1, decrements to 0; condition n > 0 fails. Output is 3 2 1.',
        "#include <stdio.h>\nint main() {\n    int n = 3;\n    while(n > 0) {\n        printf(\"%d \", n);\n        n--;\n    }\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_CPP', 'medium',
        'In C++, which keyword is used to allocate dynamic memory on the heap?',
        'new', 'malloc', 'alloc', 'create', 'A',
        'The new operator in C++ dynamically allocates heap memory and calls the class constructor.');

    $b2[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        '15', '10', '5', '0', 'A',
        'Addition: 5 + 10 = 15.',
        "#include <iostream>\nint main() {\n    int x = 5, y = 10;\n    std::cout << x + y;\n    return 0;\n}", 'cpp');

    $b2[] = makeQ($c, 'TECH_JAVA', 'easy',
        'In Java, which primitive type is used to represent single 16-bit Unicode characters?',
        'char', 'byte', 'short', 'string', 'A',
        'The char primitive data type in Java is a single 16-bit Unicode character.');

    $b2[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        '10', '9', '11', '0', 'A',
        'Math.max(10, 5) evaluates to 10.',
        "System.out.println(Math.max(10, 5));", 'java');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What is the result of the following Python operation?',
        '1', '0', '2', '3', 'A',
        '7 % 3 calculates the integer remainder: 7 divided by 3 is 2 with remainder 1.',
        "print(7 % 3)", 'python');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What does the following Python code output?',
        '3', '2', '4', 'TypeError', 'A',
        'len([10, 20, 30]) counts the number of elements in the list, which is 3.',
        "items = [10, 20, 30]\nprint(len(items))", 'python');

    $b2[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL clause is used to sort the result-set in ascending or descending order?',
        'ORDER BY', 'GROUP BY', 'SORT BY', 'ALIGN BY', 'A',
        'The ORDER BY keyword is used to sort results in ascending or descending order.');

    $b2[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL keyword adds a new row of data into a database table?',
        'INSERT INTO', 'ADD ROW', 'UPDATE', 'CREATE', 'A',
        'The INSERT INTO statement is used to insert new records into a table.');

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'In a binary tree, what is a node called if it has no child nodes?',
        'Leaf node', 'Root node', 'Internal node', 'Sibling node', 'A',
        'A node in a tree with no children is called a leaf node or terminal node.');

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the best-case time complexity of Bubble Sort on an already sorted array with an early termination flag?',
        'O(n)', 'O(n^2)', 'O(log n)', 'O(n log n)', 'A',
        'With a swapped flag, Bubble Sort verifies that no swaps were made in the first pass and finishes in O(n) linear time.');

    return ['batch1' => $b1, 'batch2' => $b2];
}
