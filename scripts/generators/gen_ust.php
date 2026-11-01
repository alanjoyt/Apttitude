<?php
require_once __DIR__ . '/helper.php';

function getUstQuestions(): array {
    $b1 = [];
    $b2 = [];
    $c = 'UST';
    $round = 'UST Assessment (reported pattern, please verify)';

    // ========================================================
    // BATCH 1 (53 Questions)
    // ========================================================
    // --- Quant (16 Qs) ---
    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the greatest common factor of 18 and 42?',
        '6', '3', '9', '12', 'A',
        'Factors of 18 and 42 share 6 as their highest common factor.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_ADV', 'medium',
        'Find the smallest number which when divided by 8, 12, and 16 leaves a remainder of 3 in each case.',
        '51', '48', '54', '45', 'A',
        'LCM(8, 12, 16) = 48. Required number = 48 + 3 = 51.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the sum of all natural numbers from 1 to 30.',
        '465', '450', '480', '435', 'A',
        'Sum = 30 * 31 / 2 = 15 * 31 = 465.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the square of 17?',
        '289', '279', '299', '269', 'A',
        '17 * 17 = 289.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 20% of 750?',
        '150', '140', '160', '130', 'A',
        '0.20 * 750 = 150.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'An item bought for Rs. 500 is sold for Rs. 650. Find the profit percentage.',
        '30%', '25%', '35%', '20%', 'A',
        'Profit = 150. Profit % = (150 / 500) * 100 = 30%.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_PERC', 'hard',
        'A merchant offers two successive discounts of 10% and 20% on an item marked at Rs. 1,000. What is the selling price?',
        'Rs. 720', 'Rs. 700', 'Rs. 750', 'Rs. 780', 'A',
        'After 10%: 1000 * 0.90 = 900. After 20%: 900 * 0.80 = Rs. 720.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'In an exam, a candidate scores 240 marks out of 300. What is the percentage?',
        '80%', '75%', '85%', '70%', 'A',
        '(240 / 300) * 100 = 80%.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A bus covers 200 km in 4 hours. What is its speed?',
        '50 km/h', '45 km/h', '55 km/h', '60 km/h', 'A',
        'Speed = 200 / 4 = 50 km/h.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A train 140 m long crosses an electric post in 7 seconds. What is its speed in km/h?',
        '72 km/h', '60 km/h', '80 km/h', '54 km/h', 'A',
        'Speed in m/s = 140 / 7 = 20 m/s = 20 * (18 / 5) = 72 km/h.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_SDT', 'hard',
        'A cyclist travels 30 km at 15 km/h and returns at 10 km/h. What is the average speed for the round trip?',
        '12 km/h', '12.5 km/h', '11 km/h', '13 km/h', 'A',
        'Total distance = 60 km. Total time = 30/15 + 30/10 = 2 + 3 = 5 hours. Average speed = 60 / 5 = 12 km/h.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_WORK', 'easy',
        'A can do a work in 8 days and B in 12 days. How many days will they take together?',
        '4.8 days', '5.0 days', '4.5 days', '5.2 days', 'A',
        'Rate = 1/8 + 1/12 = 5/24. Time = 24 / 5 = 4.8 days.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A fills a tank in 6 hours and Pipe B empties it in 10 hours. If both open together, how long to fill?',
        '15 hours', '12 hours', '18 hours', '20 hours', 'A',
        'Net rate = 1/6 - 1/10 = (5 - 3)/30 = 2/30 = 1/15. Time = 15 hours.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'Divide Rs. 350 in the ratio 2 : 5. What is the smaller share?',
        'Rs. 100', 'Rs. 250', 'Rs. 120', 'Rs. 80', 'A',
        'Total parts = 7. 1 part = 50. Smaller = 2 * 50 = Rs. 100.', null, null, $round);

    $b1[] = makeQ($c, 'QUANT_RATIO', 'medium',
        'The ratio of boys to girls in a batch is 4 : 3. If there are 28 boys, how many girls are there?',
        '21', '24', '18', '20', 'A',
        '4 units = 28 => 1 unit = 7. Girls = 3 * 7 = 21.', null, null, $round);

    $b1[] = makeQ($c, 'DI_BAR_TABLE', 'medium',
        'Cloud instances across regions: US: 40, EU: 30, APAC: 30. What percentage of instances are in the US?',
        '40%', '35%', '45%', '50%', 'A',
        'Total = 40 + 30 + 30 = 100. US percentage = (40 / 100) * 100 = 40%.', null, null, $round);

    // --- Logical Reasoning (13 Qs) ---
    $b1[] = makeQ($c, 'LR_SERIES', 'easy',
        'What comes next in the sequence: 5, 10, 15, 20, ?',
        '25', '24', '26', '30', 'A',
        'Increments of 5: 20 + 5 = 25.', null, null, $round);

    $b1[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 2, 6, 12, 20, ?',
        '30', '28', '32', '36', 'A',
        'Pattern is n * (n + 1): 1*2=2, 2*3=6, 3*4=12, 4*5=20, 5*6=30.', null, null, $round);

    $b1[] = makeQ($c, 'LR_SERIES', 'hard',
        'Find the next number: 1, 4, 9, 16, 25, ?',
        '36', '49', '30', '40', 'A',
        'Squares: 6^2 = 36.', null, null, $round);

    $b1[] = makeQ($c, 'LR_CODING', 'easy',
        'If CAT is coded as 3-1-20, how is DOG coded?',
        '4-15-7', '4-14-7', '3-15-7', '4-15-8', 'A',
        'D=4, O=15, G=7. Code is 4-15-7.', null, null, $round);

    $b1[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, CODE is written as DPEF. How is UST written in that code?',
        'VTU', 'VTV', 'WTU', 'VSU', 'A',
        'Shift +1: U(+1)=V, S(+1)=T, T(+1)=U. Code is VTU.', null, null, $round);

    $b1[] = makeQ($c, 'LR_BLOOD', 'easy',
        'Pointing to a boy, a woman says: "He is the son of my only son." How is the boy related to the woman?',
        'Grandson', 'Son', 'Nephew', 'Brother', 'A',
        'The son of her son is her grandson.', null, null, $round);

    $b1[] = makeQ($c, 'LR_BLOOD', 'medium',
        'A is the brother of B. B is the daughter of C. How is A related to C?',
        'Son', 'Brother', 'Father', 'Uncle', 'A',
        'B is C\'s daughter and A is B\'s brother, so A is C\'s son.', null, null, $round);

    $b1[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 10 m East, then turning right and walking 10 m. What direction is the final position from the starting point?',
        'South-East', 'North-East', 'South-West', 'North-West', 'A',
        'Displacement is East and South: South-East.', null, null, $round);

    $b1[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Facing South, turning 90 degrees clockwise faces:',
        'West', 'East', 'North', 'South-East', 'A',
        'South + 90 deg clockwise faces West.', null, null, $round);

    $b1[] = makeQ($c, 'LR_SEATING', 'medium',
        'Five people sit in a row facing north: P, Q, R, S, T. R is in the middle. P is on the left end. Q is to the immediate right of R. Who is at position 4 from left?',
        'Q', 'P', 'R', 'S', 'A',
        'R is at position 3, so its immediate right is position 4, which is Q.', null, null, $round);

    $b1[] = makeQ($c, 'LR_SEATING', 'hard',
        'Four friends A, B, C, D sit around a square table facing center. A is opposite C. B is to the right of A. Who is opposite B?',
        'D', 'C', 'A', 'None', 'A',
        'Opposite pairs are (A, C) and (B, D). B is opposite D.', null, null, $round);

    $b1[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'In a row of 30 students, Rahul is 12th from the left. What is his position from the right end?',
        '19th', '18th', '20th', '17th', 'A',
        'Position from right = 30 - 12 + 1 = 19th.', null, null, $round);

    $b1[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All services are scalable. All scalable items are cloud-native. Conclusions: I. All services are cloud-native. II. Some cloud-native items are services.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'All services are scalable and scalable items are cloud-native => all services are cloud-native. Also some cloud-native items are services. Both follow.', null, null, $round);

    // --- Verbal (8 Qs) ---
    $b1[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "The code deployment ______ completed successfully."',
        'was', 'were', 'are', 'is were', 'A',
        'Singular subject "The code deployment" takes "was".', null, null, $round);

    $b1[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the grammatically correct sentence:',
        'She is skilled in backend system development.',
        'She is skilled at to backend development.',
        'She is skilled on backend development.',
        'She is skilled with to backend development.', 'A',
        '"Skilled in" is the standard idiomatic usage.', null, null, $round);

    $b1[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Fill in the blank: "Neither the engineer nor the architects ______ present at the kickoff meeting."',
        'were', 'was', 'is', 'has been', 'A',
        'Subject closest to verb is plural "architects", requiring "were".', null, null, $round);

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: EXCELLENT',
        'Superb', 'Poor', 'Average', 'Inferior', 'A',
        'Excellent means extremely good; superb.', null, null, $round);

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: EXPAND',
        'Contract', 'Enlarge', 'Broaden', 'Extend', 'A',
        'Expand means grow larger. The antonym is Contract.', null, null, $round);

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: SECURE',
        'Protected', 'Exposed', 'Dangerous', 'Weak', 'A',
        'Secure means protected.', null, null, $round);

    $b1[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Continuous testing throughout the agile development sprint detects defects early, lowering total cost of remediation." What is the benefit of continuous testing?',
        'Early defect detection reducing remediation costs.',
        'Eliminating the need for software developers.',
        'Stopping all code releases.',
        'Increasing hardware costs.', 'A',
        'Continuous testing detects bugs early to minimize fixing costs.', null, null, $round);

    $b1[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "A microservices architecture separates business capabilities into autonomous services that communicate through well-defined APIs." What defines communication between microservices?',
        'Well-defined APIs.',
        'Direct shared database tables.',
        'Manual phone calls.',
        'Physical paper printouts.', 'A',
        'Microservices communicate over defined API boundaries.', null, null, $round);

    // --- Technical MCQs (16 Qs) ---
    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '15', '10', '5', '0', 'A',
        'Addition: 10 + 5 = 15.',
        "#include <stdio.h>\nint main() {\n    int a = 10, b = 5;\n    printf(\"%d\", a + b);\n    return 0;\n}", 'c', $round);

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print using a loop?',
        '2 4', '2 4 6', '2', 'Error', 'A',
        'Loop prints i * 2 for i = 1, 2: 2 4.',
        "#include <stdio.h>\nint main() {\n    for(int i = 1; i <= 2; i++) {\n        printf(\"%d \", i * 2);\n    }\n    return 0;\n}", 'c', $round);

    $b1[] = makeQ($c, 'TECH_CPP', 'medium',
        'In C++, which keyword is used to define a class template?',
        'template', 'generic', 'typename', 'type', 'A',
        'The template keyword defines generic functions and classes in C++.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        'UST', 'Global', 'None', 'Error', 'A',
        'Prints literal "UST".',
        "#include <iostream>\nint main() {\n    std::cout << \"UST\";\n    return 0;\n}", 'cpp', $round);

    $b1[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which access modifier makes a class member visible only within its own class in Java?',
        'private', 'public', 'protected', 'default', 'A',
        'The private modifier restricts visibility strictly to the declaring class.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        '10', '5', '15', 'Error', 'A',
        'Math.max(10, 5) returns 10.',
        "System.out.println(Math.max(10, 5));", 'java', $round);

    $b1[] = makeQ($c, 'TECH_JAVA', 'hard',
        'What is the purpose of the garbage collector in Java virtual machine (JVM)?',
        'To automatically reclaim heap memory occupied by unreferenced objects.',
        'To compile Java code into C++ code.',
        'To execute SQL queries.',
        'To prevent network traffic.', 'A',
        'The JVM garbage collector reclaims memory from unreachable objects.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '3', '2', '4', 'Error', 'A',
        'len("UST") evaluates to 3.',
        "print(len(\"UST\"))", 'python', $round);

    $b1[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python operation?',
        '[1, 2]', '[2, 3]', '[1, 2, 3]', 'Error', 'A',
        'Slice items[:2] returns [1, 2].',
        "items = [1, 2, 3]\nprint(items[:2])", 'python', $round);

    $b1[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL clause is used to filter records?',
        'WHERE', 'ORDER BY', 'GROUP BY', 'HAVING', 'A',
        'The WHERE clause filters rows.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL keyword counts the total number of matching rows?',
        'COUNT()', 'TOTAL()', 'SUM()', 'NUMBER()', 'A',
        'COUNT() returns the number of rows.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_SQL', 'hard',
        'What type of SQL join returns rows when there is a match in one of the tables?',
        'FULL OUTER JOIN', 'INNER JOIN', 'LEFT JOIN', 'CROSS JOIN', 'A',
        'FULL OUTER JOIN returns all matching and non-matching rows from both tables.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure follows the LIFO principle?',
        'Stack', 'Queue', 'Array', 'Heap', 'A',
        'A Stack operates on Last In First Out.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the average time complexity of searching an element in a binary search tree?',
        'O(log n)', 'O(n)', 'O(1)', 'O(n^2)', 'A',
        'BST search operates in O(log n) average time.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_DSA', 'hard',
        'Which data structure is optimal for implementing a FIFO queue?',
        'Linked List with head and tail pointers', 'Array with shift operations', 'Stack only', 'Binary Heap', 'A',
        'A linked list with head and tail pointers provides O(1) enqueue and dequeue.', null, null, $round);

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'What is the worst-case time complexity of accessing an array element by index?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n^2)', 'A',
        'Direct index access in an array is O(1) constant time.', null, null, $round);

    // ========================================================
    // BATCH 2 (52 Questions)
    // ========================================================
    // --- Quant (16 Qs) ---
    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the value of 14^2?',
        '196', '186', '206', '176', 'A',
        '14 * 14 = 196.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_ADV', 'medium',
        'Find the perimeter of a rectangle with length 20 cm and breadth 15 cm.',
        '70 cm', '60 cm', '75 cm', '65 cm', 'A',
        'Perimeter = 2 * (20 + 15) = 2 * 35 = 70 cm.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the area of a square with perimeter 40 cm.',
        '100 cm^2', '80 cm^2', '120 cm^2', '90 cm^2', 'A',
        'Side = 40 / 4 = 10 cm. Area = 10 * 10 = 100 cm^2.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 10% of 500?',
        '50', '40', '60', '45', 'A',
        '0.10 * 500 = 50.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_PERC', 'medium',
        'Selling an item for Rs. 450 results in a 10% loss. What was the cost price?',
        'Rs. 500', 'Rs. 480', 'Rs. 520', 'Rs. 490', 'A',
        'CP = 450 / 0.90 = Rs. 500.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_PERC', 'hard',
        'If price increases by 20%, by what percent must consumption decrease to maintain expenditure?',
        '16.67%', '20.00%', '15.00%', '18.00%', 'A',
        'Decrease = (20 / 120) * 100 = 16.67%.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A vehicle covers 120 km in 2 hours. What is its speed?',
        '60 km/h', '50 km/h', '70 km/h', '55 km/h', 'A',
        'Speed = 120 / 2 = 60 km/h.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A person walks 15 km at 5 km/h. How many hours did he walk?',
        '3 hours', '2.5 hours', '4 hours', '3.5 hours', 'A',
        'Time = 15 / 5 = 3 hours.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_SDT', 'hard',
        'Two cars travel towards each other from points 150 km apart at 40 km/h and 60 km/h. When will they meet?',
        '1.5 hours', '2.0 hours', '1.8 hours', '1.2 hours', 'A',
        'Relative speed = 40 + 60 = 100 km/h. Time = 150 / 100 = 1.5 hours.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        '2 workers finish a report in 6 days. How many days will 4 workers take at the same pace?',
        '3 days', '2 days', '4 days', '5 days', 'A',
        'Workers doubled => days halved: 6 / 2 = 3 days.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A fills a reservoir in 5 hours and Pipe B in 20 hours. How long together?',
        '4 hours', '3.5 hours', '4.5 hours', '5 hours', 'A',
        'Rate = 1/5 + 1/20 = 5/20 = 1/4. Time = 4 hours.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'If ratio of two numbers is 3 : 5 and sum is 80, find the smaller number.',
        '30', '50', '25', '35', 'A',
        '8 units = 80 => 1 unit = 10. Smaller = 3 * 10 = 30.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_RATIO', 'medium',
        'The average of three numbers is 25. If two numbers are 20 and 30, what is the third?',
        '25', '22', '28', '24', 'A',
        'Total = 75. Third = 75 - 50 = 25.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the value of 5^3?',
        '125', '115', '135', '105', 'A',
        '5 * 5 * 5 = 125.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 50% of 180?',
        '90', '80', '100', '85', 'A',
        '0.50 * 180 = 90.', null, null, $round);

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        'A can do a task in 4 days and B in 4 days. How long will they take together?',
        '2 days', '1 day', '3 days', '4 days', 'A',
        'Rate = 1/4 + 1/4 = 2/4 = 1/2. Time = 2 days.', null, null, $round);

    // --- Logical Reasoning (13 Qs) ---
    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next number: 10, 30, 50, 70, ?',
        '90', '80', '100', '85', 'A',
        'Increments of 20: 70 + 20 = 90.', null, null, $round);

    $b2[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 1, 2, 4, 8, 16, ?',
        '32', '24', '30', '36', 'A',
        'Doubling: 16 * 2 = 32.', null, null, $round);

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If KEY is coded as 11-5-25, how is CAR coded?',
        '3-1-18', '3-2-18', '2-1-18', '3-1-19', 'A',
        'C=3, A=1, R=18. Code is 3-1-18.', null, null, $round);

    $b2[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, CODE is written as DPEF. How is RUN written in that code?',
        'SVO', 'SWO', 'TVO', 'SUN', 'A',
        'Shift +1: R(+1)=S, U(+1)=V, N(+1)=O. Code is SVO.', null, null, $round);

    $b2[] = makeQ($c, 'LR_BLOOD', 'easy',
        'A is the daughter of B. B is the mother of C. How is A related to C?',
        'Sister', 'Mother', 'Aunt', 'Niece', 'A',
        'A and C share mother B, so A is the sister of C.', null, null, $round);

    $b2[] = makeQ($c, 'LR_BLOOD', 'medium',
        'X is the father of Y. Z is the son of Y. How is X related to Z?',
        'Grandfather', 'Father', 'Uncle', 'Brother', 'A',
        'X is the father of Z\'s parent Y, so X is the grandfather of Z.', null, null, $round);

    $b2[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 8 m North, then turning right and walking 6 m. What is the shortest distance from the start point?',
        '10 m', '14 m', '12 m', '9 m', 'A',
        'Distance = sqrt(8^2 + 6^2) = sqrt(64 + 36) = sqrt(100) = 10 m.', null, null, $round);

    $b2[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Facing North, turning 180 degrees faces:',
        'South', 'East', 'West', 'North-East', 'A',
        'North inverted 180 degrees faces South.', null, null, $round);

    $b2[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'In a line of 20 people, Ajay is 7th from the front. What is his rank from the back?',
        '14th', '13th', '15th', '12th', 'A',
        'Rank from back = 20 - 7 + 1 = 14th.', null, null, $round);

    $b2[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All cloud nodes are virtual. All virtual systems are managed. Conclusions: I. All cloud nodes are managed. II. Some managed systems are cloud nodes.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'Cloud nodes are virtual and virtual systems are managed => all cloud nodes are managed. Also some managed systems are cloud nodes. Both follow.', null, null, $round);

    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next number: 3, 6, 9, 12, ?',
        '15', '14', '16', '18', 'A',
        'Multiples of 3: 12 + 3 = 15.', null, null, $round);

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If GO is coded as 7-15, how is UP coded?',
        '21-16', '21-15', '20-16', '22-16', 'A',
        'U=21, P=16. Code is 21-16.', null, null, $round);

    $b2[] = makeQ($c, 'LR_SEATING', 'medium',
        'Four friends sit around a circular table facing center: A, B, C, D. A is opposite C. B is to the right of A. Who is opposite B?',
        'D', 'C', 'A', 'None', 'A',
        'Opposite pairs are (A, C) and (B, D). B is opposite D.', null, null, $round);

    // --- Verbal (7 Qs) ---
    $b2[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "The testing suite ______ executed without errors."',
        'was', 'were', 'are', 'is were', 'A',
        'Singular subject "The testing suite" takes "was".', null, null, $round);

    $b2[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the correct sentence:',
        'The team succeeded in deploying the patch on time.',
        'The team succeeded to deploy the patch on time.',
        'The team succeeded on deploying the patch on time.',
        'The team succeeded at deploy the patch on time.', 'A',
        '"Succeed in" followed by a gerund is the correct idiom.', null, null, $round);

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: RELIABLE',
        'Dependable', 'Unstable', 'Erratic', 'Weak', 'A',
        'Reliable means dependable.', null, null, $round);

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: STATIC',
        'Dynamic', 'Fixed', 'Stationary', 'Still', 'A',
        'Static means unmoving. Its antonym is Dynamic.', null, null, $round);

    $b2[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Containerization packages application code and dependencies together, ensuring uniform execution across development and production environments." What does containerization guarantee?',
        'Consistent execution across varied computing environments.',
        'The elimination of all software bugs.',
        'Hardware price reductions.',
        'Stopping all web requests.', 'A',
        'Containers package dependencies to ensure identical runtime behavior anywhere.', null, null, $round);

    $b2[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "A reverse proxy sits in front of web servers and forwards client requests to them, providing load balancing, SSL termination, and caching." What are functions of a reverse proxy?',
        'Load balancing, SSL termination, and response caching.',
        'Deleting user databases.',
        'Writing application source code.',
        'Preventing client web traffic.', 'A',
        'A reverse proxy manages load distribution, encryption termination, and caching.', null, null, $round);

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: ADAPT',
        'Adjust', 'Refuse', 'Break', 'Remain', 'A',
        'Adapt means adjust.', null, null, $round);

    // --- Technical MCQs (16 Qs) ---
    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '16', '12', '8', '0', 'A',
        'Multiplication: 4 * 4 = 16.',
        "#include <stdio.h>\nint main() {\n    int a = 4;\n    printf(\"%d\", a * a);\n    return 0;\n}", 'c', $round);

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print?',
        '10', '5', '0', 'Error', 'A',
        'Assignment: a = 10. printf prints 10.',
        "#include <stdio.h>\nint main() {\n    int a = 10;\n    printf(\"%d\", a);\n    return 0;\n}", 'c', $round);

    $b2[] = makeQ($c, 'TECH_CPP', 'medium',
        'Which concept in C++ allows a function to operate on different data types using the same code?',
        'Templates', 'Inheritance', 'Encapsulation', 'Abstraction', 'A',
        'Templates provide generic programming capabilities across types in C++.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        '30', '15', '0', 'Error', 'A',
        'Multiplication: 5 * 6 = 30.',
        "#include <iostream>\nint main() {\n    int a = 5, b = 6;\n    std::cout << a * b;\n    return 0;\n}", 'cpp', $round);

    $b2[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which keyword is used in Java to inherit an interface?',
        'implements', 'extends', 'inherits', 'import', 'A',
        'In Java, a class implements an interface.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        '15', '10', '5', 'Error', 'A',
        'Addition: 10 + 5 = 15.',
        "int x = 10, y = 5;\nSystem.out.println(x + y);", 'java', $round);

    $b2[] = makeQ($c, 'TECH_JAVA', 'hard',
        'Which Java data structure maintains insertion order while ensuring unique elements?',
        'LinkedHashSet', 'HashSet', 'TreeSet', 'ArrayList', 'A',
        'LinkedHashSet combines hash table uniqueness with linked list insertion order.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '9', '6', '3', 'Error', 'A',
        'Exponentiation: 3 ** 2 = 9.',
        "print(3 ** 2)", 'python', $round);

    $b2[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python operation?',
        '[1, 2, 3]', '[3, 2, 1]', '[1, 2]', 'Error', 'A',
        'Sorted: [1, 2, 3].',
        "nums = [3, 1, 2]\nprint(sorted(nums))", 'python', $round);

    $b2[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL keyword sorts results in ascending order by default?',
        'ORDER BY', 'GROUP BY', 'SORT BY', 'ALIGN BY', 'A',
        'ORDER BY sorts ascending by default.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL keyword removes duplicate records from query output?',
        'DISTINCT', 'UNIQUE', 'FILTER', 'CLEAN', 'A',
        'DISTINCT eliminates duplicate rows.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_SQL', 'hard',
        'Which SQL constraint ensures that all values in a column are unique across the table?',
        'UNIQUE', 'CHECK', 'DEFAULT', 'FOREIGN KEY', 'A',
        'The UNIQUE constraint prohibits duplicate values in a column.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure follows the FIFO principle?',
        'Queue', 'Stack', 'Array', 'Tree', 'A',
        'Queue operates on First In First Out.', null, null, $round);

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the time complexity of searching an element in an array using Linear Search in the worst case?',
        'O(n)', 'O(1)', 'O(log n)', 'O(n^2)', 'A',
        'Linear search checks up to n elements: O(n).', null, null, $round);

    $b2[] = makeQ($c, 'TECH_DSA', 'hard',
        'What is the worst-case time complexity of QuickSort on an already sorted array without random pivot selection?',
        'O(n^2)', 'O(n log n)', 'O(n)', 'O(log n)', 'A',
        'Extreme pivots on sorted input cause QuickSort to degenerate to O(n^2).', null, null, $round);

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure organizes elements in key-value pairs with fast average lookup?',
        'Hash Map', 'Linked List', 'Stack', 'Queue', 'A',
        'Hash Maps map keys to values with O(1) average lookup.', null, null, $round);

    return ['batch1' => $b1, 'batch2' => $b2];
}
