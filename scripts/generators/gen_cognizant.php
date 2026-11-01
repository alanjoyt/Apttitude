<?php
require_once __DIR__ . '/helper.php';

function getCognizantQuestions(): array {
    $b1 = [];
    $b2 = [];
    $c = 'Cognizant';

    // ========================================================
    // BATCH 1 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'Find the highest common factor (HCF) of 36 and 84.',
        '12', '6', '18', '24', 'A',
        'Factors of 36: 1, 2, 3, 4, 6, 9, 12, 18, 36. Factors of 84: 1, 2, 3, 4, 6, 7, 12, 14, 21, 28, 42, 84. Greatest common factor is 12.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'medium',
        'The product of two positive numbers is 2028 and their HCF is 13. What is their LCM?',
        '156', '144', '168', '132', 'A',
        'Product of two numbers = HCF * LCM => 2028 = 13 * LCM => LCM = 2028 / 13 = 156.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the sum of all two-digit numbers divisible by 5.',
        '945', '950', '900', '990', 'A',
        'Two-digit multiples of 5: 10, 15, ..., 95. Number of terms n = (95 - 10)/5 + 1 = 18 terms. Sum = (18 / 2) * (10 + 95) = 9 * 105 = 945.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'easy',
        'A team scored 45 points out of 60 in a sprint review. What is the success rate percentage?',
        '75%', '70%', '80%', '65%', 'A',
        '(45 / 60) * 100 = 3/4 * 100 = 75%.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'A laptop is marked at Rs. 50,000. If two successive discounts of 10% and 5% are given, what is the selling price?',
        'Rs. 42,750', 'Rs. 42,500', 'Rs. 43,000', 'Rs. 41,500', 'A',
        'Price after 10% = 50000 * 0.90 = Rs. 45,000. Price after 5% = 45000 * 0.95 = Rs. 42,750.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'hard',
        'A vendor buys lemons at 6 for Rs. 10 and sells them at 4 for Rs. 8. What is his profit percentage?',
        '20%', '25%', '15%', '16.67%', 'A',
        'CP of 1 lemon = 10 / 6 = 5/3. SP of 1 lemon = 8 / 4 = 2. Profit = 2 - 5/3 = 1/3. Profit % = ((1/3) / (5/3)) * 100 = (1/5) * 100 = 20%.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A car travels 150 km in 2.5 hours. What is its speed in km/h?',
        '60 km/h', '55 km/h', '65 km/h', '70 km/h', 'A',
        'Speed = Distance / Time = 150 / 2.5 = 60 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A train 180 m long crosses a standing man in 9 seconds. What is the speed of the train in km/h?',
        '72 km/h', '60 km/h', '80 km/h', '54 km/h', 'A',
        'Speed in m/s = 180 / 9 = 20 m/s. Speed in km/h = 20 * (18 / 5) = 72 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'hard',
        'A man rows 18 km downstream in 2 hours and 12 km upstream in 3 hours. What is the speed of the boat in still water?',
        '6.5 km/h', '6.0 km/h', '7.0 km/h', '5.5 km/h', 'A',
        'Downstream speed D = 18 / 2 = 9 km/h. Upstream speed U = 12 / 3 = 4 km/h. Still water speed = (D + U) / 2 = (9 + 4) / 2 = 6.5 km/h.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'easy',
        'P and Q together can finish a job in 4 days. If P alone takes 12 days, how many days will Q take alone?',
        '6 days', '8 days', '5 days', '7 days', 'A',
        'Q\'s rate = 1/4 - 1/12 = (3 - 1) / 12 = 2 / 12 = 1/6. Q takes 6 days.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'medium',
        '10 engineers can build a web application in 15 days. If 5 more engineers join after 3 days, how many additional days will it take to finish the project?',
        '8 days', '9 days', '7 days', '10 days', 'A',
        'Total work = 10 * 15 = 150 units. In 3 days, 10 engineers complete 10 * 3 = 30 units. Remaining = 120 units. Total engineers now = 15. Additional days = 120 / 15 = 8 days.');

    $b1[] = makeQ($c, 'DI_BAR_TABLE', 'medium',
        'Test execution results for 3 builds: Build 1: 90 passed out of 100, Build 2: 95 passed out of 100, Build 3: 85 passed out of 100. What is the overall percentage of passed tests?',
        '90%', '88%', '92%', '85%', 'A',
        'Total passed = 90 + 95 + 85 = 270. Total tests = 300. Overall percentage = (270 / 300) * 100 = 90%.');

    // --- Logical Reasoning (10 Qs) ---
    $b1[] = makeQ($c, 'LR_SERIES', 'easy',
        'What is the next number in the series: 6, 12, 18, 24, ?',
        '30', '28', '32', '36', 'A',
        'Multiples of 6: 24 + 6 = 30.');

    $b1[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing number in the series: 2, 6, 14, 30, ?',
        '62', '60', '58', '64', 'A',
        'Pattern is * 2 + 2: 2*2+2=6, 6*2+2=14, 14*2+2=30, 30*2+2=62.');

    $b1[] = makeQ($c, 'LR_CODING', 'easy',
        'If MOON is coded as 13-15-15-14, how is STAR coded?',
        '19-20-1-18', '19-21-1-18', '18-20-1-18', '19-20-2-18', 'A',
        'S=19, T=20, A=1, R=18. Code is 19-20-1-18.');

    $b1[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, BLUE is written as CMVF. How is PINK written in that code?',
        'QJOL', 'QJOK', 'PJOL', 'QIOK', 'A',
        'Each letter shifted forward by 1: P(+1)=Q, I(+1)=J, N(+1)=O, K(+1)=L. Code is QJOL.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'easy',
        'Pointing to a boy, a girl said: "He is the son of my grandfather\'s only son." How is the boy related to the girl?',
        'Brother', 'Cousin', 'Uncle', 'Father', 'A',
        'Grandfather\'s only son is her father. The son of her father is her brother.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'medium',
        'P is the father of Q. R is the son of Q. S is the brother of R. How is P related to S?',
        'Grandfather', 'Father', 'Uncle', 'Brother', 'A',
        'Q is the parent of R and S, and P is the father of Q. Therefore, P is the grandfather of S.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'A person walks 9 km South, turns East and walks 12 km. What is the shortest distance from the start point?',
        '15 km', '21 km', '18 km', '13 km', 'A',
        'Distance = sqrt(9^2 + 12^2) = sqrt(81 + 144) = sqrt(225) = 15 km.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Walking 10 m West, then turning right and walking 10 m. What direction is the final position from the starting point?',
        'North-West', 'North-East', 'South-West', 'South-East', 'A',
        'Displacement is West and North, making the relative direction North-West.');

    $b1[] = makeQ($c, 'LR_SEATING', 'medium',
        'Five people P, Q, R, S, and T are standing in a line. Q is ahead of P. S is behind T. R is between P and T. Who is standing first in line?',
        'Q', 'P', 'R', 'T', 'A',
        'Order from front to back: Q, P, R, T, S. Q is first in line.');

    $b1[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All microservices are scalable. Some scalable systems are fault-tolerant. Conclusions: I. Some microservices are fault-tolerant. II. All scalable systems are microservices.',
        'Neither I nor II follows', 'Only I follows', 'Only II follows', 'Both follow', 'A',
        'The overlap between microservices and fault-tolerant systems is not guaranteed. Neither conclusion follows.');

    // --- Verbal Ability (6 Qs) ---
    $b1[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct preposition: "The deployment was completed ______ schedule without delays."',
        'ahead of', 'prior', 'before to', 'forward of', 'A',
        '"Ahead of schedule" is the correct idiomatic phrase.');

    $b1[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the sentence that contains no grammatical errors:',
        'The committee has submitted its findings to the board.',
        'The committee have submitted its findings to the board.',
        'The committee has submitted their findings to the board.',
        'The committee are submitted its findings to the board.', 'A',
        '"Committee" treated as a single entity takes singular verb "has" and singular pronoun "its".');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: ACCURACY',
        'Precision', 'Flaw', 'Error', 'Variance', 'A',
        'Accuracy means correctness or precision.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: HARMONIOUS',
        'Discordant', 'Melodic', 'Peaceful', 'Congruent', 'A',
        'Harmonious means peaceful and in agreement. Its antonym is Discordant.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Database sharding splits huge datasets across distinct database instances, preventing single-node bottlenecks." What problem does sharding resolve?',
        'Single-node performance and capacity bottlenecks.',
        'Eliminating network connections.',
        'Replacing SQL queries with machine code.',
        'Preventing all application software bugs.', 'A',
        'Sharding distributes data to avoid single-node capacity and throughput bottlenecks.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "Event-driven architectures decouple producers from consumers, allowing services to scale independently and react in real-time." What is the main benefit of decoupling?',
        'Services can scale independently and process events asynchronously.',
        'It eliminates the need for messaging queues.',
        'It forces all services to run on a single operating system.',
        'It removes all database read latency.', 'A',
        'Decoupling enables independent scaling and asynchronous event reaction.');

    // --- Technical & Coding MCQs (12 Qs) ---
    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '20', '10', '30', 'Compilation Error', 'A',
        'Assignment: a = 20. printf prints 20.',
        "#include <stdio.h>\nint main() {\n    int a = 20;\n    printf(\"%d\", a);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print using a for loop?',
        '1 2 3', '0 1 2', '1 2 3 4', '3 2 1', 'A',
        'Loop prints i for values 1, 2, 3: 1 2 3.',
        "#include <stdio.h>\nint main() {\n    for(int i = 1; i <= 3; i++) {\n        printf(\"%d \", i);\n    }\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_CPP', 'medium',
        'Which concept in C++ allows a function to call itself?',
        'Recursion', 'Iteration', 'Encapsulation', 'Polymorphism', 'A',
        'Recursion is the mechanism where a function calls itself directly or indirectly.');

    $b1[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        'Cognizant', 'Hello', 'None', 'Error', 'A',
        'Prints the string literal "Cognizant".',
        "#include <iostream>\nint main() {\n    std::cout << \"Cognizant\";\n    return 0;\n}", 'cpp');

    $b1[] = makeQ($c, 'TECH_JAVA', 'easy',
        'What is the default value of a boolean primitive field in Java?',
        'false', 'true', 'null', '0', 'A',
        'In Java, uninitialized boolean class instance variables default to false.');

    $b1[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What does the following Java snippet output?',
        '10', '20', '30', 'Error', 'A',
        'Math.min(10, 20) returns the smaller value, which is 10.',
        "System.out.println(Math.min(10, 20));", 'java');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What is the output of the following Python expression?',
        '6', '5', '7', '8', 'A',
        '2 * 3 evaluates to 6.',
        "print(2 * 3)", 'python');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python code snippet?',
        '[1, 2, 3]', '[3, 2, 1]', '[1, 2]', 'Error', 'A',
        'The sorted() function returns a new sorted list: [1, 2, 3].',
        "nums = [3, 1, 2]\nprint(sorted(nums))", 'python');

    $b1[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL clause groups rows that have the same values into summary rows?',
        'GROUP BY', 'ORDER BY', 'WHERE', 'HAVING', 'A',
        'GROUP BY arranges identical data into groups for aggregation.');

    $b1[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL statement deletes a table structure and all its data from the database permanently?',
        'DROP TABLE', 'TRUNCATE TABLE', 'DELETE TABLE', 'CLEAR TABLE', 'A',
        'DROP TABLE permanently removes the table schema and data.');

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure is best suited for implementing an undo operation in a text editor?',
        'Stack', 'Queue', 'Hash Map', 'Binary Tree', 'A',
        'A Stack (LIFO) stores actions so the most recent operation can be popped and undone first.');

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the time complexity of inserting a node at the beginning of a singly linked list?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n^2)', 'A',
        'Prepending a node requires updating the new node\'s next pointer and head reference, taking O(1) constant time.');

    // ========================================================
    // BATCH 2 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the sum of angles of a quadrilateral?',
        '360 degrees', '180 degrees', '270 degrees', '540 degrees', 'A',
        'The interior angles of any planar quadrilateral sum to (4 - 2) * 180 = 360 degrees.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'medium',
        'Find the perimeter of a rectangle whose length is 15 cm and breadth is 10 cm.',
        '50 cm', '60 cm', '45 cm', '150 cm', 'A',
        'Perimeter = 2 * (length + breadth) = 2 * (15 + 10) = 2 * 25 = 50 cm.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the area of a right-angled triangle with base 12 cm and hypotenuse 13 cm.',
        '30 cm^2', '60 cm^2', '25 cm^2', '36 cm^2', 'A',
        'Height = sqrt(13^2 - 12^2) = sqrt(169 - 144) = sqrt(25) = 5 cm. Area = (1/2) * base * height = (1/2) * 12 * 5 = 30 cm^2.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 20% of 450?',
        '90', '85', '95', '100', 'A',
        '20% of 450 = 0.20 * 450 = 90.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'medium',
        'A worker\'s wage is increased from Rs. 400 to Rs. 480 per day. What is the percentage increase?',
        '20%', '15%', '25%', '18%', 'A',
        'Increase = 480 - 400 = 80. Percentage = (80 / 400) * 100 = 20%.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'hard',
        'If price of an item decreases by 10% and sales volume increases by 20%, what is the net effect on revenue?',
        '8% increase', '10% increase', '8% decrease', '5% increase', 'A',
        'Net effect = -10 + 20 + (-10 * 20)/100 = 10 - 2 = +8% increase.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'Divide Rs. 600 in the ratio 2 : 3. What is the smaller share?',
        'Rs. 240', 'Rs. 360', 'Rs. 200', 'Rs. 300', 'A',
        'Total parts = 5. 1 part = 600 / 5 = 120. Smaller share = 2 * 120 = Rs. 240.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'medium',
        'The average score of 4 tests is 75. What must be scored on the 5th test to raise the average to 80?',
        '100', '95', '90', '85', 'A',
        'Total for 4 tests = 4 * 75 = 300. Total required for 5 tests = 5 * 80 = 400. 5th score = 400 - 300 = 100.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A cyclist rides 30 km in 2 hours. What is his speed?',
        '15 km/h', '12 km/h', '18 km/h', '20 km/h', 'A',
        'Speed = 30 / 2 = 15 km/h.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A train 200 m long crosses a 300 m bridge in 25 seconds. What is its speed in km/h?',
        '72 km/h', '64 km/h', '80 km/h', '60 km/h', 'A',
        'Total distance = 200 + 300 = 500 m. Speed = 500 / 25 = 20 m/s = 20 * (18 / 5) = 72 km/h.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        'If 6 men complete a project in 20 days, how many days will 12 men take at the same pace?',
        '10 days', '12 days', '8 days', '15 days', 'A',
        'Men doubled => days halved: 20 / 2 = 10 days.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe X fills a cistern in 6 hours and Pipe Y empties it in 8 hours. If both open, how long to fill?',
        '24 hours', '18 hours', '20 hours', '16 hours', 'A',
        'Net rate = 1/6 - 1/8 = (4 - 3)/24 = 1/24. Cistern fills in 24 hours.');

    // --- Logical Reasoning (10 Qs) ---
    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the missing term: 5, 10, 15, 20, ?',
        '25', '24', '26', '30', 'A',
        'Multiples of 5: 20 + 5 = 25.');

    $b2[] = makeQ($c, 'LR_SERIES', 'medium',
        'What comes next: 2, 3, 5, 7, 11, ?',
        '13', '15', '12', '17', 'A',
        'Prime number sequence: next prime after 11 is 13.');

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If RED is coded as 18-5-4, how is BLUE coded?',
        '2-12-21-5', '2-11-21-5', '2-12-20-5', '3-12-21-5', 'A',
        'B=2, L=12, U=21, E=5. Code is 2-12-21-5.');

    $b2[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, FAST is written as GBTU. How is SLOW written in that code?',
        'TMPX', 'TMOX', 'UMPY', 'TNPX', 'A',
        'Each letter shifted forward by 1: S(+1)=T, L(+1)=M, O(+1)=P, W(+1)=X. Code is TMPX.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'easy',
        'A is the brother of B. B is the mother of C. How is A related to C?',
        'Maternal Uncle', 'Father', 'Brother', 'Grandfather', 'A',
        'A is the brother of C\'s mother B, making A the maternal uncle of C.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'medium',
        'P is the sister of Q. Q is the daughter of R. How is P related to R?',
        'Daughter', 'Sister', 'Niece', 'Aunt', 'A',
        'Since Q is the daughter of R and P is Q\'s sister, P is also the daughter of R.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Facing South, turning 90 degrees counter-clockwise orients one towards which direction?',
        'East', 'West', 'North', 'South-East', 'A',
        'South + 90 deg counter-clockwise (to the left) faces East.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'A car travels 8 km East, then 6 km North. What is the straight-line distance from the start?',
        '10 km', '14 km', '12 km', '9 km', 'A',
        'Distance = sqrt(8^2 + 6^2) = sqrt(64 + 36) = sqrt(100) = 10 km.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'In a race of 50 runners, Vikram finishes 15th from the top. What is his rank from the finish line?',
        '36th', '35th', '37th', '34th', 'A',
        'Rank from bottom = 50 - 15 + 1 = 36th.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All algorithms are structured. All structured items are logical. Conclusions: I. All algorithms are logical. II. Some logical items are algorithms.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'Since algorithms are inside structured and structured inside logical, all algorithms are logical. Also some logical items are algorithms. Both follow.');

    // --- Verbal Ability (6 Qs) ---
    $b2[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct word: "The server room temperature should ______ maintained below 20 degrees."',
        'be', 'is', 'was', 'are', 'A',
        'Modal verb "should" takes the base form "be".');

    $b2[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the correct sentence:',
        'She is senior to me in the engineering hierarchy.',
        'She is senior than me in the engineering hierarchy.',
        'She is senior from me in the engineering hierarchy.',
        'She is more senior to me in the hierarchy.', 'A',
        'Adjectives ending in "-ior" such as senior, junior, and superior take "to", not "than".');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: EFFICIENT',
        'Productive', 'Slow', 'Wasteful', 'Ineffective', 'A',
        'Efficient means achieving maximum productivity with minimum wasted effort.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: LENIENT',
        'Strict', 'Forgiving', 'Merciful', 'Mild', 'A',
        'Lenient means permissive and merciful. Its antonym is Strict.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Load balancers distribute incoming network traffic across multiple backend servers to prevent server overload." What is the main purpose of a load balancer?',
        'Distribute incoming traffic across multiple servers.',
        'Delete duplicate database records.',
        'Increase server hardware purchase cost.',
        'Prevent users from accessing websites.', 'A',
        'A load balancer routes incoming requests across a cluster to ensure high availability and prevent overload.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "Microfrontends decompose monolithic frontend applications into smaller, independently deliverable features owned by distinct cross-functional teams." What architectural benefit is described?',
        'Independent feature delivery by cross-functional teams.',
        'Merging frontend code into a single massive bundle.',
        'Preventing modular web design.',
        'Eliminating the use of JavaScript on the web.', 'A',
        'Microfrontends enable independent deployment of UI features across agile teams.');

    // --- Technical & Coding MCQs (12 Qs) ---
    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '5', '10', '0', 'Error', 'A',
        'Division: 10 / 2 = 5.',
        "#include <stdio.h>\nint main() {\n    int a = 10, b = 2;\n    printf(\"%d\", a / b);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print?',
        '100', '50', '200', '0', 'A',
        'Assignment: x = 100. printf prints 100.',
        "#include <stdio.h>\nint main() {\n    int x = 100;\n    printf(\"%d\", x);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_CPP', 'medium',
        'Which C++ feature permits multiple classes to inherit from a single common base class?',
        'Hierarchical Inheritance', 'Single Inheritance', 'Encapsulation', 'Polymorphism', 'A',
        'Hierarchical inheritance occurs when more than one derived class is created from a single base class.');

    $b2[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        '30', '20', '10', '0', 'A',
        'Addition: 10 + 20 = 30.',
        "#include <iostream>\nint main() {\n    int a = 10, b = 20;\n    std::cout << a + b;\n    return 0;\n}", 'cpp');

    $b2[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which keyword prevents a Java variable from having its value reassigned once initialized?',
        'final', 'static', 'const', 'immutable', 'A',
        'The final keyword creates constant variables that cannot be reassigned once initialized.');

    $b2[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        '12', '10', '2', 'Error', 'A',
        'Addition: 10 + 2 = 12.',
        "int a = 10, b = 2;\nSystem.out.println(a + b);", 'java');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What is the output of the following Python operation?',
        '10', '20', '30', 'Error', 'A',
        'min(10, 20) evaluates to 10.',
        "print(min(10, 20))", 'python');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What does the following Python statement output?',
        'True', 'False', 'None', 'Error', 'A',
        '1 in [1, 2, 3] evaluates to True.',
        "print(1 in [1, 2, 3])", 'python');

    $b2[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL keyword counts the total number of rows matching a criteria?',
        'COUNT()', 'TOTAL()', 'SUM()', 'NUMBER()', 'A',
        'The COUNT() function returns the number of rows matching query conditions.');

    $b2[] = makeQ($c, 'TECH_SQL', 'medium',
        'What does a FULL OUTER JOIN in SQL return?',
        'All rows when there is a match in either left or right table.',
        'Only matching rows between both tables.',
        'Only rows from the left table.',
        'Only rows from the right table.', 'A',
        'FULL OUTER JOIN returns all matching and unmatched rows from both participating tables.');

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'What is the time complexity of searching an element in an unsorted array of size n?',
        'O(n)', 'O(1)', 'O(log n)', 'O(n^2)', 'A',
        'Linear search across an unsorted array requires scanning up to n elements: O(n).');

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'In a standard Queue, which operation adds an element to the rear?',
        'Enqueue', 'Dequeue', 'Push', 'Pop', 'A',
        'Enqueue adds an element to the end of a queue.');

    return ['batch1' => $b1, 'batch2' => $b2];
}
