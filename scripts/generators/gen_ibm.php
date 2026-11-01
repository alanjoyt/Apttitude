<?php
require_once __DIR__ . '/helper.php';

function getIbmQuestions(): array {
    $b1 = [];
    $b2 = [];
    $c = 'IBM';
    $round = 'IBM Assessment (reported pattern, please verify)';

    // ========================================================
    // BATCH 1 (53 Questions)
    // ========================================================
    // --- Quant (16 Qs) ---
    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'Find the LCM of 12, 16, and 24.',
        '48', '36', '72', '24', 'A',
        '48 is the least common multiple divisible by 12, 16, and 24.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_ADV', 'medium',
        'What is the remainder when 5^15 is divided by 4?',
        '1', '2', '3', '0', 'A',
        '5 = 4 + 1 ≡ 1 (mod 4). 5^15 ≡ 1^15 ≡ 1 (mod 4). Remainder is 1.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the sum of all natural numbers from 1 to 35.',
        '630', '600', '650', '620', 'A',
        'Sum = 35 * 36 / 2 = 35 * 18 = 630.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the square of 18?',
        '324', '314', '334', '304', 'A',
        '18 * 18 = 324.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 25% of 640?',
        '160', '150', '170', '140', 'A',
        '0.25 * 640 = 160.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'An item bought for Rs. 400 is sold for Rs. 520. What is the profit percentage?',
        '30%', '25%', '35%', '20%', 'A',
        'Profit = 120. Profit % = (120 / 400) * 100 = 30%.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_PERC', 'hard',
        'A seller gives a discount of 15% on marked price Rs. 800. What is the selling price?',
        'Rs. 680', 'Rs. 660', 'Rs. 700', 'Rs. 720', 'A',
        'SP = 800 * 0.85 = Rs. 680.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'In a server test, 18 out of 20 nodes passed latency checks. What is the pass percentage?',
        '90%', '85%', '95%', '80%', 'A',
        '(18 / 20) * 100 = 90%.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A vehicle covers 180 km in 3 hours. What is its speed?',
        '60 km/h', '50 km/h', '70 km/h', '55 km/h', 'A',
        'Speed = 180 / 3 = 60 km/h.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A train 120 m long crosses an electric post in 6 seconds. What is its speed in km/h?',
        '72 km/h', '60 km/h', '80 km/h', '54 km/h', 'A',
        'Speed = 120 / 6 = 20 m/s = 20 * (18 / 5) = 72 km/h.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_SDT', 'hard',
        'A car travels 60 km at 30 km/h and returns at 20 km/h. What is the average speed for the round trip?',
        '24 km/h', '25 km/h', '22.5 km/h', '26 km/h', 'A',
        'Average speed = 2 * 30 * 20 / (30 + 20) = 1200 / 50 = 24 km/h.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_WORK', 'easy',
        'A can do a work in 10 days and B in 15 days. How many days will they take together?',
        '6 days', '5 days', '7 days', '8 days', 'A',
        'Rate = 1/10 + 1/15 = 5/30 = 1/6. Time = 6 days.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A fills a tank in 8 hours and Pipe B empties it in 12 hours. If both open together, how long to fill?',
        '24 hours', '20 hours', '18 hours', '16 hours', 'A',
        'Net rate = 1/8 - 1/12 = 1/24. Time = 24 hours.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'Divide Rs. 420 in the ratio 3 : 4. What is the smaller share?',
        'Rs. 180', 'Rs. 240', 'Rs. 200', 'Rs. 160', 'A',
        'Total parts = 7. 1 part = 60. Smaller = 3 * 60 = Rs. 180.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_RATIO', 'medium',
        'The ratio of frontend to backend engineers is 3 : 5. If there are 25 backend engineers, how many frontend engineers are there?',
        '15', '12', '18', '20', 'A',
        '5 units = 25 => 1 unit = 5. Frontend = 3 * 5 = 15.', null, null, $round);

    $b1[] = makeQ($c, 'DI_BAR_TABLE', 'medium',
        'Mainframe CPU utilization across 3 shifts: Shift 1: 60%, Shift 2: 70%, Shift 3: 80%. What is the average utilization across all shifts?',
        '70%', '65%', '75%', '68%', 'A',
        'Average = (60 + 70 + 80) / 3 = 210 / 3 = 70%.', null, null, $round);

    // --- Logical Reasoning (13 Qs) ---
    $b1[] = makeQ($c, 'LR_SERIES', 'easy',
        'What comes next in the sequence: 6, 12, 18, 24, ?',
        '30', '28', '32', '36', 'A',
        'Increments of 6: 24 + 6 = 30.', null, null, $round);

    $b1[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 2, 4, 8, 16, ?',
        '32', '24', '30', '36', 'A',
        'Doubling: 16 * 2 = 32.', null, null, $round);

    $b1[] = makeQ($c, 'LR_SERIES', 'hard',
        'Find the next number: 1, 8, 27, 64, ?',
        '125', '100', '150', '216', 'A',
        'Cubes: 5^3 = 125.', null, null, $round);

    $b1[] = makeQ($c, 'LR_CODING', 'easy',
        'If IBM is coded as 9-2-13, how is RED coded?',
        '18-5-4', '18-4-4', '17-5-4', '18-5-5', 'A',
        'R=18, E=5, D=4. Code is 18-5-4.', null, null, $round);

    $b1[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, CODE is written as DPEF. How is CLOUD written in that code?',
        'DMPVE', 'DMQVE', 'DMPUD', 'CMPVE', 'A',
        'Shift +1: C(+1)=D, L(+1)=M, O(+1)=P, U(+1)=V, D(+1)=E. Code is DMPVE.', null, null, $round);

    $b1[] = makeQ($c, 'LR_BLOOD', 'easy',
        'Pointing to a boy, a man says: "He is the son of my father\'s only son." How is the boy related to the man?',
        'Son', 'Brother', 'Nephew', 'Uncle', 'A',
        'Father\'s only son is the man himself. His son is his son.', null, null, $round);

    $b1[] = makeQ($c, 'LR_BLOOD', 'medium',
        'A is the brother of B. B is the child of C. C is the husband of D. How is D related to A?',
        'Mother', 'Aunt', 'Sister', 'Grandmother', 'A',
        'C is A\'s father and D is C\'s wife, so D is A\'s mother.', null, null, $round);

    $b1[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 8 m North, then turning right and walking 6 m. What is the shortest distance from the start point?',
        '10 m', '14 m', '12 m', '9 m', 'A',
        'Distance = sqrt(8^2 + 6^2) = sqrt(64 + 36) = sqrt(100) = 10 m.', null, null, $round);

    $b1[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Facing North, turning 90 degrees counter-clockwise faces:',
        'West', 'East', 'South', 'North-West', 'A',
        'North + 90 deg counter-clockwise faces West.', null, null, $round);

    $b1[] = makeQ($c, 'LR_SEATING', 'medium',
        'Five analysts sit in a row facing north: A, B, C, D, E. C is in the middle. B is to the right of C. Who is at position 4 from left?',
        'B', 'A', 'C', 'D', 'A',
        'C is at 3, so its immediate right is position 4, which is B.', null, null, $round);

    $b1[] = makeQ($c, 'LR_SEATING', 'hard',
        'Four friends A, B, C, D sit around a table facing center. A is opposite C. B is to the right of A. Who is opposite B?',
        'D', 'C', 'A', 'None', 'A',
        'Opposite pairs are (A, C) and (B, D). B is opposite D.', null, null, $round);

    $b1[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'In a batch of 25 students, Priya is 8th from the top. What is her rank from the bottom?',
        '18th', '17th', '19th', '16th', 'A',
        'Rank from bottom = 25 - 8 + 1 = 18th.', null, null, $round);

    $b1[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All mainframes are reliable. All reliable systems are monitored. Conclusions: I. All mainframes are monitored. II. Some monitored systems are mainframes.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'Mainframes are reliable and reliable systems are monitored, so all mainframes are monitored. Also some monitored systems are mainframes. Both follow.', null, null, $round);

    // --- Verbal (8 Qs) ---
    $b1[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "The system telemetry ______ collected continuously."',
        'was', 'were', 'are', 'is were', 'A',
        'Singular subject "The system telemetry" takes "was".', null, null, $round);

    $b1[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the correct sentence:',
        'The infrastructure team is competent in handling hybrid cloud migrations.',
        'The infrastructure team is competent to handling hybrid cloud migrations.',
        'The infrastructure team is competent on handling migrations.',
        'The infrastructure team is competent with handle migrations.', 'A',
        '"Competent in" followed by a gerund is the standard idiom.', null, null, $round);

    $b1[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Fill in the blank: "Every server in the cluster ______ monitored in real time."',
        'is', 'are', 'were', 'have been', 'A',
        '"Every server" is singular, requiring "is".', null, null, $round);

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: RESILIENT',
        'Robust', 'Fragile', 'Weak', 'Soft', 'A',
        'Resilient means robust and durable.', null, null, $round);

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: TEMPORARY',
        'Permanent', 'Brief', 'Short', 'Fleeting', 'A',
        'Temporary means lasting only for a limited time. Its antonym is Permanent.', null, null, $round);

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: PRECISE',
        'Exact', 'Vague', 'Rough', 'Approximate', 'A',
        'Precise means exact.', null, null, $round);

    $b1[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Hybrid cloud integration allows organizations to run sensitive transactional workloads in private on-premise clusters while bursting elastic workloads to public cloud regions." What is the benefit of hybrid cloud?',
        'Balancing data privacy with elastic public cloud compute.',
        'Deleting all local hardware.',
        'Prohibiting web traffic.',
        'Stopping all database writes.', 'A',
        'Hybrid cloud blends private on-premise security with public cloud elasticity.', null, null, $round);

    $b1[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "Infrastructure as Code (IaC) treats server provisioning templates like application source code, enabling version control, automated peer review, and continuous testing of cloud infrastructure." What does IaC enable for infrastructure?',
        'Version control, peer review, and automated testing of provisioning templates.',
        'Eliminating the need for software code.',
        'Turning off cloud datacenters permanently.',
        'Removing computer memory.', 'A',
        'IaC brings software engineering best practices like versioning and peer review to cloud provisioning.', null, null, $round);

    // --- Technical MCQs (16 Qs) ---
    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '20', '10', '15', '0', 'A',
        'Addition: 15 + 5 = 20.',
        "#include <stdio.h>\nint main() {\n    int a = 15, b = 5;\n    printf(\"%d\", a + b);\n    return 0;\n}", 'c', $round);

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print using a loop?',
        '4 8', '4 8 12', '4', 'Error', 'A',
        'Loop prints i * 4 for i = 1, 2: 4 8.',
        "#include <stdio.h>\nint main() {\n    for(int i = 1; i <= 2; i++) {\n        printf(\"%d \", i * 4);\n    }\n    return 0;\n}", 'c', $round);

    $b1[] = makeQ($c, 'TECH_CPP', 'medium',
        'In Linux, which command displays the current active user and terminal session?',
        'whoami', 'pwd', 'ls', 'cat', 'A',
        'The whoami command outputs the username of the currently logged-in user.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        'IBMCloud', 'IBM', 'Cloud', 'Error', 'A',
        'Prints literal "IBMCloud".',
        "#include <iostream>\nint main() {\n    std::cout << \"IBMCloud\";\n    return 0;\n}", 'cpp', $round);

    $b1[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which component of the Java platform compiles bytecode into native machine instructions at runtime?',
        'Just-In-Time (JIT) Compiler', 'Java Compiler (javac)', 'Garbage Collector', 'Class Loader', 'A',
        'The JIT compiler compiles frequently executed bytecode methods into native machine code at runtime.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        '10', '5', '15', 'Error', 'A',
        'Math.min(10, 20) evaluates to 10.',
        "System.out.println(Math.min(10, 20));", 'java', $round);

    $b1[] = makeQ($c, 'TECH_JAVA', 'hard',
        'What does the "static" keyword indicate on a Java class method?',
        'The method belongs to the class itself rather than instances and can be invoked without creating an object.',
        'The method cannot be executed.',
        'The method is stored in disk storage.',
        'The method is executed on CPU registers exclusively.', 'A',
        'Static methods belong to the class and are called on the class reference directly.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '3', '2', '4', 'Error', 'A',
        'len("IBM") evaluates to 3.',
        "print(len(\"IBM\"))", 'python', $round);

    $b1[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python operation?',
        '[1, 2]', '[2, 3]', '[1, 2, 3]', 'Error', 'A',
        'Slice items[:2] returns [1, 2].',
        "items = [1, 2, 3]\nprint(items[:2])", 'python', $round);

    $b1[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL clause groups rows that share common column values for aggregation?',
        'GROUP BY', 'ORDER BY', 'WHERE', 'HAVING', 'A',
        'GROUP BY groups rows for aggregation.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL keyword counts the total number of matching rows in a query?',
        'COUNT()', 'TOTAL()', 'SUM()', 'NUMBER()', 'A',
        'COUNT() returns the row tally.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_SQL', 'hard',
        'In relational databases, what key links a child table record to the primary key of a parent table?',
        'FOREIGN KEY', 'PRIMARY KEY', 'UNIQUE KEY', 'CANDIDATE KEY', 'A',
        'A FOREIGN KEY establishes a referential link to a parent table\'s primary key.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure follows the LIFO principle?',
        'Stack', 'Queue', 'Array', 'Heap', 'A',
        'Stack operates on Last In First Out.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the average time complexity of searching an element in a binary search tree?',
        'O(log n)', 'O(n)', 'O(1)', 'O(n^2)', 'A',
        'BST search operates in O(log n) average time.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_DSA', 'hard',
        'Which standard protocol operates at the Transport Layer of the Internet protocol suite and provides connection-oriented reliable byte streams?',
        'TCP', 'UDP', 'IP', 'HTTP', 'A',
        'Transmission Control Protocol (TCP) provides reliable connection-oriented transport.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'In Linux, which command lists all files and directories including hidden files in long format?',
        'ls -la', 'pwd', 'cat', 'cd', 'A',
        'ls -la lists directory contents in long format including hidden dotfiles.', null, null, $round);

    // ========================================================
    // BATCH 2 (52 Questions)
    // ========================================================
    // --- Quant (16 Qs) ---
    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the value of 13^2?',
        '169', '159', '179', '149', 'A',
        '13 * 13 = 169.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_ADV', 'medium',
        'Find the perimeter of an equilateral triangle with side 12 cm.',
        '36 cm', '30 cm', '42 cm', '48 cm', 'A',
        'Perimeter = 3 * 12 = 36 cm.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the area of a square with perimeter 48 cm.',
        '144 cm^2', '120 cm^2', '160 cm^2', '100 cm^2', 'A',
        'Side = 48 / 4 = 12 cm. Area = 12 * 12 = 144 cm^2.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 30% of 300?',
        '90', '80', '100', '85', 'A',
        '0.30 * 300 = 90.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_PERC', 'medium',
        'Selling price is Rs. 880 and cost price is Rs. 800. What is the profit percentage?',
        '10%', '12%', '15%', '8%', 'A',
        'Profit = 80. Profit % = (80 / 800) * 100 = 10%.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_PERC', 'hard',
        'If price increases by 50%, by what percent must consumption decrease to maintain expenditure?',
        '33.33%', '50.00%', '25.00%', '30.00%', 'A',
        'Decrease = (50 / 150) * 100 = 1/3 * 100 = 33.33%.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A vehicle covers 140 km in 2 hours. What is its speed?',
        '70 km/h', '65 km/h', '75 km/h', '80 km/h', 'A',
        'Speed = 140 / 2 = 70 km/h.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A person walks 20 km at 4 km/h. How many hours did he walk?',
        '5 hours', '4 hours', '6 hours', '4.5 hours', 'A',
        'Time = 20 / 4 = 5 hours.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_SDT', 'hard',
        'Two trains travel towards each other from stations 240 km apart at 50 km/h and 70 km/h. When will they meet?',
        '2 hours', '2.5 hours', '1.8 hours', '3 hours', 'A',
        'Relative speed = 50 + 70 = 120 km/h. Time = 240 / 120 = 2 hours.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        '3 workers complete a data ingestion pipeline in 6 days. How many days will 6 workers take at the same pace?',
        '3 days', '2 days', '4 days', '5 days', 'A',
        'Workers doubled => time halved: 6 / 2 = 3 days.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A fills a reservoir in 6 hours and Pipe B in 12 hours. How long together?',
        '4 hours', '3 hours', '5 hours', '6 hours', 'A',
        'Rate = 1/6 + 1/12 = 3/12 = 1/4. Time = 4 hours.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'If ratio of two numbers is 4 : 5 and sum is 90, find the smaller number.',
        '40', '50', '35', '45', 'A',
        '9 units = 90 => 1 unit = 10. Smaller = 4 * 10 = 40.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_RATIO', 'medium',
        'The average of three numbers is 20. If two numbers are 15 and 25, what is the third?',
        '20', '18', '22', '16', 'A',
        'Total = 60. Third = 60 - 40 = 20.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the value of 4^3?',
        '64', '54', '74', '48', 'A',
        '4 * 4 * 4 = 64.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 40% of 200?',
        '80', '70', '90', '75', 'A',
        '0.40 * 200 = 80.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        'A can do a task in 5 days and B in 5 days. How long will they take together?',
        '2.5 days', '2 days', '3 days', '4 days', 'A',
        'Rate = 1/5 + 1/5 = 2/5. Time = 5 / 2 = 2.5 days.', null, null, $round);

    // --- Logical Reasoning (13 Qs) ---
    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next number: 15, 30, 45, 60, ?',
        '75', '70', '80', '65', 'A',
        'Increments of 15: 60 + 15 = 75.', null, null, $round);

    $b2[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 3, 6, 12, 24, ?',
        '48', '36', '42', '52', 'A',
        'Doubling: 24 * 2 = 48.', null, null, $round);

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If PEN is coded as 16-5-14, how is FOX coded?',
        '6-15-24', '6-14-24', '7-15-24', '6-15-23', 'A',
        'F=6, O=15, X=24. Code is 6-15-24.', null, null, $round);

    $b2[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, CODE is written as DPEF. How is JAVA written in that code?',
        'KBWB', 'KAVB', 'KBWA', 'JBWB', 'A',
        'Shift +1: J(+1)=K, A(+1)=B, V(+1)=W, A(+1)=B. Code is KBWB.', null, null, $round);

    $b2[] = makeQ($c, 'LR_BLOOD', 'easy',
        'A is the daughter of B. B is the mother of C. How is A related to C?',
        'Sister', 'Mother', 'Aunt', 'Niece', 'A',
        'A and C share mother B, so A is the sister of C.', null, null, $round);

    $b2[] = makeQ($c, 'LR_BLOOD', 'medium',
        'X is the brother of Y. Y is the wife of Z. How is X related to Z?',
        'Brother-in-law', 'Brother', 'Father', 'Uncle', 'A',
        'X is the brother of Z\'s wife Y, making X the brother-in-law of Z.', null, null, $round);

    $b2[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 12 m East, then turning right and walking 5 m. What is the shortest distance from the start point?',
        '13 m', '17 m', '15 m', '14 m', 'A',
        'Distance = sqrt(12^2 + 5^2) = sqrt(144 + 25) = sqrt(169) = 13 m.', null, null, $round);

    $b2[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Facing East, turning 180 degrees faces:',
        'West', 'North', 'South', 'North-East', 'A',
        'East inverted 180 degrees faces West.', null, null, $round);

    $b2[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'In a line of 30 people, Kiran is 10th from the front. What is his rank from the back?',
        '21st', '20th', '22nd', '19th', 'A',
        'Rank from back = 30 - 10 + 1 = 21st.', null, null, $round);

    $b2[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All pods are isolated. All isolated systems are secure. Conclusions: I. All pods are secure. II. Some secure systems are pods.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'Pods are isolated and isolated systems are secure => all pods are secure. Also some secure systems are pods. Both follow.', null, null, $round);

    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next number: 4, 8, 12, 16, ?',
        '20', '18', '22', '24', 'A',
        'Multiples of 4: 16 + 4 = 20.', null, null, $round);

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If ME is coded as 13-5, how is IT coded?',
        '9-20', '9-19', '8-20', '10-20', 'A',
        'I=9, T=20. Code is 9-20.', null, null, $round);

    $b2[] = makeQ($c, 'LR_SEATING', 'medium',
        'Four friends sit around a circular table facing center: A, B, C, D. A is opposite C. B is to the right of A. Who is opposite B?',
        'D', 'C', 'A', 'None', 'A',
        'Opposite pairs are (A, C) and (B, D). B is opposite D.', null, null, $round);

    // --- Verbal (7 Qs) ---
    $b2[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "The container cluster ______ scaled automatically."',
        'was', 'were', 'are', 'is were', 'A',
        'Singular subject "The container cluster" takes "was".', null, null, $round);

    $b2[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the correct sentence:',
        'The team succeeded in optimizing database queries.',
        'The team succeeded to optimize database queries.',
        'The team succeeded on optimizing queries.',
        'The team succeeded at optimize queries.', 'A',
        '"Succeed in" followed by a gerund is the correct form.', null, null, $round);

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: SECURE',
        'Safe', 'Dangerous', 'Uncertain', 'Weak', 'A',
        'Secure means safe.', null, null, $round);

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: OPAQUE',
        'Transparent', 'Cloudy', 'Murky', 'Dense', 'A',
        'Opaque means non-transparent. Its opposite is Transparent.', null, null, $round);

    $b2[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Disaster recovery failover tests verify that standby datacenter replicas assume operational workloads seamlessly during complete regional outages." What do failover tests verify?',
        'Standby replicas successfully assume workloads during regional outages.',
        'Deleting all company records permanently.',
        'Stopping software developers from writing code.',
        'Increasing cloud server bills.', 'A',
        'Failover tests ensure high availability and disaster recovery readiness.', null, null, $round);

    $b2[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "Container registries store and distribute container images with cryptographic digest verification to prevent unauthorized image tampering." How do registries ensure image security?',
        'Using cryptographic digests to verify image authenticity and prevent tampering.',
        'Eliminating user passwords entirely.',
        'Shutting down web servers during maintenance.',
        'Making all internal software open source.', 'A',
        'Cryptographic checksums verify image integrity against tampering.', null, null, $round);

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: ACCURATE',
        'Correct', 'False', 'Wrong', 'Vague', 'A',
        'Accurate means correct.', null, null, $round);

    // --- Technical MCQs (16 Qs) ---
    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '25', '20', '15', 'Error', 'A',
        'Multiplication: 5 * 5 = 25.',
        "#include <stdio.h>\nint main() {\n    int a = 5;\n    printf(\"%d\", a * a);\n    return 0;\n}", 'c', $round);

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print?',
        '12', '6', '0', 'Error', 'A',
        'Multiplication: 3 * 4 = 12.',
        "#include <stdio.h>\nint main() {\n    int a = 3, b = 4;\n    printf(\"%d\", a * b);\n    return 0;\n}", 'c', $round);

    $b2[] = makeQ($c, 'TECH_CPP', 'medium',
        'In C++, which keyword allows classes to inherit members from a base class?',
        'public', 'inherit', 'extends', 'implements', 'A',
        'Public inheritance is declared as class Derived : public Base.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        '40', '20', '0', 'Error', 'A',
        'Multiplication: 4 * 10 = 40.',
        "#include <iostream>\nint main() {\n    int a = 4, b = 10;\n    std::cout << a * b;\n    return 0;\n}", 'cpp', $round);

    $b2[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which package in Java provides fundamental classes including Object, String, and System?',
        'java.lang', 'java.util', 'java.io', 'java.net', 'A',
        'java.lang provides the fundamental base classes of the Java platform.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        '16', '12', '8', 'Error', 'A',
        'Addition: 8 + 8 = 16.',
        "int x = 8, y = 8;\nSystem.out.println(x + y);", 'java', $round);

    $b2[] = makeQ($c, 'TECH_JAVA', 'hard',
        'What is the size of an int primitive in Java?',
        '4 bytes', '2 bytes', '8 bytes', '1 byte', 'A',
        'An int in Java is a 32-bit signed two\'s complement integer (4 bytes).', null, null, $round);

    $b2[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '8', '6', '9', 'Error', 'A',
        'Exponentiation: 2 ** 3 = 8.',
        "print(2 ** 3)", 'python', $round);

    $b2[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python operation?',
        '[1, 2]', '[2, 3]', '[1, 2, 3]', 'Error', 'A',
        'Slice items[:2] returns [1, 2].',
        "items = [1, 2, 3]\nprint(items[:2])", 'python', $round);

    $b2[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL keyword filters rows matching a pattern?',
        'LIKE', 'MATCH', 'SEARCH', 'FILTER', 'A',
        'The LIKE operator filters based on pattern matching.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL keyword eliminates duplicate rows from a query result set?',
        'DISTINCT', 'UNIQUE', 'FILTER', 'CLEAN', 'A',
        'The DISTINCT keyword removes duplicate rows.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_SQL', 'hard',
        'Which SQL command adds a new table definition to a database?',
        'CREATE TABLE', 'ADD TABLE', 'NEW TABLE', 'BUILD TABLE', 'A',
        'CREATE TABLE creates a new table structure.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure follows the FIFO principle?',
        'Queue', 'Stack', 'Array', 'Tree', 'A',
        'Queue operates on First In First Out.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the worst-case time complexity of searching an item in a balanced Binary Search Tree?',
        'O(log n)', 'O(n)', 'O(1)', 'O(n^2)', 'A',
        'A balanced BST maintains logarithmic depth, ensuring O(log n) search.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_DSA', 'hard',
        'What is the time complexity of pushing an element onto a stack?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n^2)', 'A',
        'Stack push is an O(1) constant time operation.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'In Linux, which command displays the present working directory path?',
        'pwd', 'cd', 'ls', 'whoami', 'A',
        'The pwd command prints the current working directory.', null, null, $round);

    return ['batch1' => $b1, 'batch2' => $b2];
}
