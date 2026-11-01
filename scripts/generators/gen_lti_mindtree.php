<?php
require_once __DIR__ . '/helper.php';

function getLtiMindtreeQuestions(): array {
    $b1 = [];
    $b2 = [];
    $c = 'LTI Mindtree';

    // ========================================================
    // BATCH 1 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'Find the LCM of 15, 20, and 30.',
        '60', '90', '120', '45', 'A',
        'Multiples of 30: 30, 60. 60 is divisible by 15, 20, and 30. LCM is 60.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'medium',
        'What is the remainder when 4^13 is divided by 3?',
        '1', '2', '0', '3', 'A',
        '4 = 3 + 1 ≡ 1 (mod 3). 4^13 ≡ 1^13 ≡ 1 (mod 3). Remainder is 1.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the sum of all odd numbers from 1 to 30.',
        '225', '200', '250', '210', 'A',
        'There are 15 odd numbers between 1 and 30. Sum of first n odd numbers = n^2 = 15^2 = 225.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 30% of 400?',
        '120', '110', '130', '100', 'A',
        '0.30 * 400 = 120.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'Cost price is Rs. 450 and selling price is Rs. 540. What is the profit percentage?',
        '20%', '18%', '22%', '25%', 'A',
        'Profit = 540 - 450 = 90. Profit % = (90 / 450) * 100 = 20%.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'hard',
        'A trader sells two monitors at Rs. 9,900 each, gaining 10% on one and losing 10% on the other. What is his net gain or loss percentage?',
        '1% loss', '1% gain', 'No gain no loss', '2% loss', 'A',
        'When selling prices are equal and gain % = loss % = x, net outcome is a loss of (x / 10)^2 % = (10 / 10)^2 % = 1% loss.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A vehicle travels 120 km in 2 hours. What is its speed?',
        '60 km/h', '50 km/h', '70 km/h', '55 km/h', 'A',
        'Speed = 120 / 2 = 60 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A train 160 m long crosses an electric post in 8 seconds. What is its speed in km/h?',
        '72 km/h', '64 km/h', '80 km/h', '54 km/h', 'A',
        'Speed = 160 / 8 = 20 m/s = 20 * (18 / 5) = 72 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'hard',
        'A person covers half of his journey at 20 km/h and the remaining half at 30 km/h. What is his average speed for the entire trip?',
        '24 km/h', '25 km/h', '22 km/h', '26 km/h', 'A',
        'Average speed for equal distances = 2 * v1 * v2 / (v1 + v2) = 2 * 20 * 30 / (20 + 30) = 1200 / 50 = 24 km/h.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'easy',
        'A and B together can complete a project in 6 days. A alone takes 9 days. How many days will B take alone?',
        '18 days', '15 days', '12 days', '20 days', 'A',
        'B\'s rate = 1/6 - 1/9 = (3 - 2)/18 = 1/18. B takes 18 days.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A fills a reservoir in 4 hours and Pipe B in 6 hours. Both are opened together in an empty tank. In how many hours will the tank fill?',
        '2.4 hours', '2.0 hours', '3.0 hours', '2.5 hours', 'A',
        'Rate = 1/4 + 1/6 = 5/12. Time = 12 / 5 = 2.4 hours.');

    $b1[] = makeQ($c, 'DI_BAR_TABLE', 'medium',
        'Software license audit across three departments: Eng: 50, Sales: 30, HR: 20. What percentage of licenses are assigned to Engineering?',
        '50%', '45%', '55%', '40%', 'A',
        'Total = 50 + 30 + 20 = 100. Eng percentage = (50 / 100) * 100 = 50%.');

    // --- Logical Reasoning (10 Qs) ---
    $b1[] = makeQ($c, 'LR_SERIES', 'easy',
        'What is the next number: 7, 14, 21, 28, ?',
        '35', '32', '36', '42', 'A',
        'Multiples of 7: 28 + 7 = 35.');

    $b1[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 1, 3, 9, 27, ?',
        '81', '72', '90', '64', 'A',
        'Powers of 3: 27 * 3 = 81.');

    $b1[] = makeQ($c, 'LR_CODING', 'easy',
        'If WEB is coded as 23-5-2, how is DEV coded?',
        '4-5-22', '4-5-21', '3-5-22', '4-6-22', 'A',
        'D=4, E=5, V=22. Code is 4-5-22.');

    $b1[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, MIND is written as NJOE. How is TREE written in that code?',
        'USFF', 'USEF', 'VSFF', 'TRFF', 'A',
        'Each letter shifted forward by 1: T(+1)=U, R(+1)=S, E(+1)=F, E(+1)=F. Code is USFF.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'easy',
        'Pointing to a photo, a man says: "She is the only daughter of my mother." How is she related to the man?',
        'Sister', 'Daughter', 'Mother', 'Aunt', 'A',
        'The only daughter of the man\'s mother is his sister.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'medium',
        'P is the brother of Q. R is the father of P. S is the sister of R. How is S related to Q?',
        'Paternal Aunt', 'Mother', 'Sister', 'Grandmother', 'A',
        'R is the father of Q, and S is R\'s sister. Therefore S is the paternal aunt of Q.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 15 m West, then turning right and walking 20 m. What is the shortest distance from the start point?',
        '25 m', '30 m', '35 m', '20 m', 'A',
        'Distance = sqrt(15^2 + 20^2) = sqrt(225 + 400) = sqrt(625) = 25 m.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Facing North, turning 90 degrees counter-clockwise faces:',
        'West', 'East', 'South', 'North-West', 'A',
        'North + 90 deg counter-clockwise faces West.');

    $b1[] = makeQ($c, 'LR_SEATING', 'medium',
        'Five engineers A, B, C, D, E sit in a row facing north. C sits in the middle. D is at the right end. A is at the left end. B is between A and C. Who sits between C and D?',
        'E', 'B', 'A', 'None', 'A',
        'Positions: A (1), B (2), C (3), E (4), D (5). E is between C and D.');

    $b1[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All nodes are connected. All connected items communicate. Conclusions: I. All nodes communicate. II. Some communicating items are nodes.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'All nodes are connected, and all connected items communicate, so all nodes communicate. Also some communicating items are nodes. Both follow.');

    // --- Verbal Ability (6 Qs) ---
    $b1[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "The production release ______ scheduled for midnight."',
        'is', 'are', 'were', 'have', 'A',
        'Singular subject "The production release" takes "is".');

    $b1[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the correct sentence:',
        'He is confident in handling high-traffic production workloads.',
        'He is confident to handling high-traffic production workloads.',
        'He is confident at handling high-traffic production workloads.',
        'He is confident on handling production workloads.', 'A',
        '"Confident in" followed by a gerund is the standard grammatical form.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: EMINENT',
        'Distinguished', 'Unknown', 'Ordinary', 'Obscure', 'A',
        'Eminent means famous and respected; distinguished.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: TRANSIENT',
        'Permanent', 'Temporary', 'Brief', 'Passing', 'A',
        'Transient means lasting for only a short time. Its antonym is Permanent.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Database replication creates standby replicas to ensure automatic failover and prevent downtime during hardware failures." What is the purpose of replicas?',
        'Enabling automatic failover to prevent downtime during failures.',
        'Deleting old customer data.',
        'Slowing down query responses.',
        'Increasing electricity usage unnecessarily.', 'A',
        'Replication enables standby replicas to assume operations upon primary failure.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "A distributed tracing framework instruments inter-service HTTP requests with unique correlation IDs, allowing engineers to diagnose latency bottlenecks across deep microservice dependency graphs." How do correlation IDs assist debugging?',
        'By tracing requests across distributed service calls to identify latency hotspots.',
        'By deleting database log entries.',
        'By preventing developers from writing code.',
        'By eliminating network encryption.', 'A',
        'Correlation IDs track requests through service chains to pinpoint latency bottlenecks.');

    // --- Technical MCQs (12 Qs) ---
    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '21', '14', '7', '0', 'A',
        'Multiplication: 7 * 3 = 21.',
        "#include <stdio.h>\nint main() {\n    int a = 7, b = 3;\n    printf(\"%d\", a * b);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print using a loop?',
        '3 6', '3 6 9', '3', 'Error', 'A',
        'Loop prints i * 3 for i = 1, 2: 3 6.',
        "#include <stdio.h>\nint main() {\n    for(int i = 1; i <= 2; i++) {\n        printf(\"%d \", i * 3);\n    }\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_CPP', 'medium',
        'In C++, what is a destructor\'s primary role?',
        'To deallocate resources when an object goes out of scope.',
        'To initialize object variables.',
        'To copy object state.',
        'To overload operators.', 'A',
        'Destructors release dynamically allocated memory and system handles upon object destruction.');

    $b1[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        'LTIMindtree', 'LTI', 'Mindtree', 'Error', 'A',
        'Prints literal "LTIMindtree".',
        "#include <iostream>\nint main() {\n    std::cout << \"LTIMindtree\";\n    return 0;\n}", 'cpp');

    $b1[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which collection class in Java implements a dynamically resizing array?',
        'ArrayList', 'LinkedList', 'HashSet', 'TreeSet', 'A',
        'ArrayList provides a resizable array implementation of the List interface in Java.');

    $b1[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        '14', '10', '4', 'Error', 'A',
        'Addition: 10 + 4 = 14.',
        "int x = 10, y = 4;\nSystem.out.println(x + y);", 'java');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '2', '3', '1', 'Error', 'A',
        'Integer remainder: 5 % 3 = 2.',
        "print(5 % 3)", 'python');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python operation?',
        '[1, 2, 3]', '[3, 2, 1]', '[1, 2]', 'Error', 'A',
        'sorted([2, 3, 1]) returns [1, 2, 3].',
        "print(sorted([2, 3, 1]))", 'python');

    $b1[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL keyword is used to modify values in existing database rows?',
        'UPDATE', 'ALTER', 'CHANGE', 'MODIFY', 'A',
        'The UPDATE statement updates data in existing rows.');

    $b1[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL clause is used to filter records resulting from a GROUP BY aggregation?',
        'HAVING', 'WHERE', 'ORDER BY', 'LIMIT', 'A',
        'HAVING filters grouped aggregates.');

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure follows the First In First Out (FIFO) rule?',
        'Queue', 'Stack', 'Array', 'Tree', 'A',
        'Queue operates on First In First Out.');

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the average time complexity of QuickSort?',
        'O(n log n)', 'O(n^2)', 'O(n)', 'O(log n)', 'A',
        'QuickSort has an expected average time complexity of O(n log n).');

    // ========================================================
    // BATCH 2 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the value of 12^2?',
        '144', '134', '154', '124', 'A',
        '12 * 12 = 144.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'medium',
        'Find the perimeter of an equilateral triangle with side 10 cm.',
        '30 cm', '25 cm', '35 cm', '40 cm', 'A',
        'Perimeter = 3 * 10 = 30 cm.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'hard',
        'What is the area of a square with diagonal 10 cm?',
        '50 cm^2', '40 cm^2', '60 cm^2', '100 cm^2', 'A',
        'Area = (diagonal^2) / 2 = 100 / 2 = 50 cm^2.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 20% of 350?',
        '70', '65', '75', '80', 'A',
        '0.20 * 350 = 70.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'medium',
        'An item bought for Rs. 200 is sold for Rs. 250. What is the profit percentage?',
        '25%', '20%', '30%', '15%', 'A',
        'Profit = 50. Profit % = (50 / 200) * 100 = 25%.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'hard',
        'If price increases by 25%, by what percent must consumption decrease to maintain expenditure?',
        '20%', '25%', '15%', '18%', 'A',
        'Decrease % = (25 / 125) * 100 = 20%.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'If ratio of two numbers is 2 : 3 and smaller is 16, what is the larger?',
        '24', '20', '28', '32', 'A',
        '2 units = 16 => 1 unit = 8. Larger = 3 * 8 = 24.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'medium',
        'The average of three numbers is 18. If two numbers are 14 and 20, what is the third?',
        '20', '18', '22', '16', 'A',
        'Total = 3 * 18 = 54. Third = 54 - (14 + 20) = 54 - 34 = 20.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A vehicle covers 150 km in 3 hours. What is its speed?',
        '50 km/h', '45 km/h', '55 km/h', '60 km/h', 'A',
        'Speed = 150 / 3 = 50 km/h.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A person walks 12 km at 4 km/h. How many hours did he walk?',
        '3 hours', '2.5 hours', '4 hours', '3.5 hours', 'A',
        'Time = 12 / 4 = 3 hours.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        '3 developers complete a module in 4 days. How many days will 6 developers take at the same pace?',
        '2 days', '1 day', '3 days', '4 days', 'A',
        'Developer count doubled => time halved: 4 / 2 = 2 days.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A fills a tank in 8 hours and Pipe B in 12 hours. How long together?',
        '4.8 hours', '4.0 hours', '5.0 hours', '5.2 hours', 'A',
        'Rate = 1/8 + 1/12 = 5/24. Time = 24 / 5 = 4.8 hours.');

    // --- Logical Reasoning (10 Qs) ---
    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next number: 10, 25, 40, 55, ?',
        '70', '65', '75', '80', 'A',
        'Increments of 15: 55 + 15 = 70.');

    $b2[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 1, 2, 4, 8, ?',
        '16', '12', '14', '18', 'A',
        'Doubling: 8 * 2 = 16.');

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If CUP is coded as 3-21-16, how is MUG coded?',
        '13-21-7', '13-20-7', '12-21-7', '13-21-8', 'A',
        'M=13, U=21, G=7. Code is 13-21-7.');

    $b2[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, LEAD is written as MFB E. How is TALK written in that code?',
        'UBML', 'UBMK', 'VBML', 'UAML', 'A',
        'Shift +1: T(+1)=U, A(+1)=B, L(+1)=M, K(+1)=L. Code is UBML.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'easy',
        'A is the mother of B. B is the sister of C. How is A related to C?',
        'Mother', 'Aunt', 'Sister', 'Grandmother', 'A',
        'B and C are siblings, so A is the mother of C.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'medium',
        'X is the brother of Y. Z is the mother of X. How is Z related to Y?',
        'Mother', 'Aunt', 'Sister', 'Grandmother', 'A',
        'X and Y are siblings with mother Z.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 10 m East, then turning right and walking 10 m. What direction is the final position from the starting point?',
        'South-East', 'North-East', 'South-West', 'North-West', 'A',
        'Displacement is East and South: South-East.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Facing North, turning 180 degrees faces:',
        'South', 'East', 'West', 'North-East', 'A',
        'North inverted 180 degrees faces South.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'In a row of 24 students, Priya is 8th from the left. What is her position from the right?',
        '17th', '16th', '18th', '15th', 'A',
        'Position from right = 24 - 8 + 1 = 17th.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All services are scalable. All scalable systems are resilient. Conclusions: I. All services are resilient. II. Some resilient systems are services.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'Services are scalable and scalable systems are resilient, so all services are resilient. Also some resilient systems are services. Both follow.');

    // --- Verbal Ability (6 Qs) ---
    $b2[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "The code coverage ______ exceeded 90%."',
        'has', 'have', 'are', 'is were', 'A',
        'Singular subject "The code coverage" takes "has".');

    $b2[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the correct sentence:',
        'He is adept at designing distributed cloud architectures.',
        'He is adept in to designing distributed cloud architectures.',
        'He is adept on designing distributed cloud architectures.',
        'He is adept with design distributed architectures.', 'A',
        '"Adept at" followed by a gerund is the standard idiom.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: PROFICIENT',
        'Skilled', 'Incompetent', 'Untrained', 'Clumsy', 'A',
        'Proficient means competent or skilled in doing or using something.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: COMPLEX',
        'Simple', 'Intricate', 'Complicated', 'Convoluted', 'A',
        'Complex means consisting of many different and connected parts. Its antonym is Simple.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Database normalization organizes tables to reduce data redundancy and improve data integrity across relational schemas." What is the goal of normalization?',
        'Eliminating redundancy and maintaining data integrity.',
        'Deleting all database records.',
        'Converting relational tables to plain text files.',
        'Slowing down query responses.', 'A',
        'Normalization reduces redundancy and guarantees relational integrity.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "Microservice circuit breakers monitor remote service failure rates and trip automatically when errors exceed thresholds to prevent cascading failures." What is the purpose of a circuit breaker?',
        'Preventing cascading failures by failing fast during remote outages.',
        'Permanently deleting unavailable services.',
        'Increasing database latency intentionally.',
        'Disabling security certificates.', 'A',
        'Circuit breakers stop cascading failures across distributed architectures.');

    // --- Technical MCQs (12 Qs) ---
    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '12', '7', '10', '0', 'A',
        'Addition: 7 + 5 = 12.',
        "#include <stdio.h>\nint main() {\n    int a = 7, b = 5;\n    printf(\"%d\", a + b);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print?',
        '30', '15', '0', 'Error', 'A',
        'Multiplication: 5 * 6 = 30.',
        "#include <stdio.h>\nint main() {\n    int a = 5, b = 6;\n    printf(\"%d\", a * b);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_CPP', 'medium',
        'In C++, which keyword allows a derived class to access protected members of its base class?',
        'protected', 'public', 'private', 'friend', 'A',
        'Protected members are accessible within the base class and by derived classes.');

    $b2[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        '50', '25', '10', '0', 'A',
        'Multiplication: 10 * 5 = 50.',
        "#include <iostream>\nint main() {\n    int a = 10, b = 5;\n    std::cout << a * b;\n    return 0;\n}", 'cpp');

    $b2[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which keyword is used in Java to instantiate an object?',
        'new', 'alloc', 'create', 'instantiate', 'A',
        'The new keyword allocates memory and invokes constructors in Java.');

    $b2[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        '18', '9', '10', 'Error', 'A',
        'Addition: 9 + 9 = 18.',
        "int x = 9, y = 9;\nSystem.out.println(x + y);", 'java');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '6', '5', '7', 'Error', 'A',
        'len("Python") evaluates to 6.',
        "print(len(\"Python\"))", 'python');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python operation?',
        '[1, 2]', '[2, 3]', '[1, 2, 3]', 'Error', 'A',
        'Slice items[:2] returns [1, 2].',
        "items = [1, 2, 3]\nprint(items[:2])", 'python');

    $b2[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL clause is used to filter records in a table query?',
        'WHERE', 'ORDER BY', 'GROUP BY', 'HAVING', 'A',
        'WHERE filters rows satisfying criteria.');

    $b2[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL keyword counts the total number of records matching a condition?',
        'COUNT()', 'TOTAL()', 'SUM()', 'NUMBER()', 'A',
        'COUNT() tallies records.');

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure follows the LIFO principle?',
        'Stack', 'Queue', 'Array', 'Heap', 'A',
        'Stack operates on Last In First Out.');

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the worst-case time complexity of Linear Search in an array of size n?',
        'O(n)', 'O(1)', 'O(log n)', 'O(n^2)', 'A',
        'Linear search must check up to all n items: O(n).');

    return ['batch1' => $b1, 'batch2' => $b2];
}
