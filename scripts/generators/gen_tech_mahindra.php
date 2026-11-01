<?php
require_once __DIR__ . '/helper.php';

function getTechMahindraQuestions(): array {
    $b1 = [];
    $b2 = [];
    $c = 'Tech Mahindra';

    // ========================================================
    // BATCH 1 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the greatest common divisor (GCD) of 48 and 72?',
        '24', '12', '18', '36', 'A',
        'Factors of 48: 1, 2, 3, 4, 6, 8, 12, 16, 24, 48. Factors of 72: 1, 2, 3, 4, 6, 8, 9, 12, 18, 24, 36, 72. The GCD is 24.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'medium',
        'If the product of two numbers is 300 and their HCF is 5, what is their LCM?',
        '60', '50', '70', '65', 'A',
        'Product = HCF * LCM => 300 = 5 * LCM => LCM = 300 / 5 = 60.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the sum of all natural numbers from 1 to 50.',
        '1275', '1250', '1300', '1225', 'A',
        'Sum = n * (n + 1) / 2 = 50 * 51 / 2 = 25 * 51 = 1275.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 25% of 600?',
        '150', '125', '160', '140', 'A',
        '0.25 * 600 = 150.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'An item bought for Rs. 400 is sold for Rs. 500. Find the profit percentage.',
        '25%', '20%', '30%', '15%', 'A',
        'Profit = 500 - 400 = 100. Profit % = (100 / 400) * 100 = 25%.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'hard',
        'The price of sugar rises by 20%. By what percentage should a household reduce consumption to keep expenditure constant?',
        '16.67%', '20.00%', '15.00%', '18.50%', 'A',
        'Reduction % = (r / (100 + r)) * 100 = (20 / 120) * 100 = (1 / 6) * 100 ≈ 16.67%.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A bus travels 150 km in 3 hours. What is its speed?',
        '50 km/h', '45 km/h', '55 km/h', '60 km/h', 'A',
        'Speed = 150 / 3 = 50 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A train 180 m long crosses an electric post in 12 seconds. Find the speed of the train in km/h.',
        '54 km/h', '60 km/h', '48 km/h', '72 km/h', 'A',
        'Speed = 180 / 12 = 15 m/s = 15 * (18 / 5) = 54 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'hard',
        'Two cyclists start simultaneously from the same point in opposite directions at 12 km/h and 18 km/h. How far apart will they be after 2 hours?',
        '60 km', '50 km', '70 km', '55 km', 'A',
        'Relative speed = 12 + 18 = 30 km/h. Distance = 30 * 2 = 60 km.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'easy',
        'A can do a work in 10 days and B in 15 days. How many days will they take together?',
        '6 days', '5 days', '7 days', '8 days', 'A',
        'Combined rate = 1/10 + 1/15 = 5/30 = 1/6. They take 6 days.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'medium',
        'A and B together can complete a job in 8 days. A alone can do it in 12 days. In how many days can B alone complete the job?',
        '24 days', '20 days', '18 days', '16 days', 'A',
        'B\'s rate = 1/8 - 1/12 = 1/24. B takes 24 days.');

    $b1[] = makeQ($c, 'DI_BAR_TABLE', 'medium',
        'Network ticket resolution across three weeks: W1: 40, W2: 50, W3: 60. What is the average weekly resolution count?',
        '50', '45', '55', '48', 'A',
        'Average = (40 + 50 + 60) / 3 = 150 / 3 = 50.');

    // --- Logical Reasoning (10 Qs) ---
    $b1[] = makeQ($c, 'LR_SERIES', 'easy',
        'What comes next in the sequence: 4, 8, 12, 16, ?',
        '20', '18', '22', '24', 'A',
        'Increments of 4: 16 + 4 = 20.');

    $b1[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 1, 4, 9, 16, ?',
        '25', '20', '30', '36', 'A',
        'Squares: 1^2, 2^2, 3^2, 4^2, 5^2 = 25.');

    $b1[] = makeQ($c, 'LR_CODING', 'easy',
        'If LOG is coded as 12-15-7, how is CAT coded?',
        '3-1-20', '3-2-20', '4-1-20', '3-1-19', 'A',
        'C=3, A=1, T=20. Code is 3-1-20.');

    $b1[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, STAR is written as TUBS. How is MOON written in that code?',
        'NPP O', 'NPP N', 'NPP P', 'NPQ O', 'A',
        'Shift +1: M(+1)=N, O(+1)=P, O(+1)=P, N(+1)=O. Code is NPP O.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'easy',
        'Pointing to a gentleman, a woman says: "His mother is the only daughter of my mother." How is the gentleman related to the woman?',
        'Son', 'Brother', 'Nephew', 'Husband', 'A',
        'Only daughter of the woman\'s mother is the woman herself. The gentleman is her son.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'medium',
        'A is B\'s sister. C is B\'s mother. D is C\'s father. How is A related to D?',
        'Granddaughter', 'Daughter', 'Mother', 'Aunt', 'A',
        'A is the daughter of C, and C is the daughter of D. Therefore, A is the granddaughter of D.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'A person walks 5 km East, then turns right and walks 12 km. What is the straight-line distance from the start point?',
        '13 km', '17 km', '15 km', '14 km', 'A',
        'Distance = sqrt(5^2 + 12^2) = sqrt(25 + 144) = sqrt(169) = 13 km.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Facing North, turning 90 degrees counter-clockwise faces:',
        'West', 'East', 'South', 'North-West', 'A',
        'North + 90 deg counter-clockwise faces West.');

    $b1[] = makeQ($c, 'LR_SEATING', 'medium',
        'Four friends sit around a circular table facing center: A, B, C, D. A is opposite C. B is to the right of A. Who is opposite B?',
        'D', 'C', 'A', 'None', 'A',
        'Opposite pairs are (A, C) and (B, D). B is opposite D.');

    $b1[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All engineers are problem solvers. All problem solvers are analytical. Conclusions: I. All engineers are analytical. II. Some analytical people are engineers.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'Since engineers are problem solvers and problem solvers are analytical, all engineers are analytical. Also some analytical people are engineers. Both follow.');

    // --- Verbal Ability (6 Qs) ---
    $b1[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb form: "The software update ______ completed successfully."',
        'was', 'were', 'are', 'is were', 'A',
        'Singular subject "The software update" takes "was".');

    $b1[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the correct sentence:',
        'He is confident in his technical presentation.',
        'He is confident with his technical presentation.',
        'He is confident at his technical presentation.',
        'He is confident on his technical presentation.', 'A',
        '"Confident in" is the correct prepositional combination.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: ADAPT',
        'Adjust', 'Refuse', 'Remain', 'Break', 'A',
        'Adapt means make suitable for a new use or purpose; adjust.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: HOSTILE',
        'Friendly', 'Aggressive', 'Angry', 'Violent', 'A',
        'Hostile means unfriendly. Its antonym is Friendly.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Centralized logging collects telemetry from distributed servers into an aggregated index to facilitate rapid root cause analysis." What is the benefit of centralized logging?',
        'Aggregating telemetry across servers for rapid debugging.',
        'Deleting application source code.',
        'Eliminating the need for databases.',
        'Increasing cloud server bills unnecessarily.', 'A',
        'Centralized logging aggregates logs to accelerate root cause analysis.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "Container orchestration tools automate scheduling, health monitoring, and rolling updates for distributed containerized microservices." What do orchestration tools automate?',
        'Scheduling, health monitoring, and zero-downtime rolling updates.',
        'Writing business requirement documents.',
        'Eliminating all software development teams.',
        'Disabling security certificates.', 'A',
        'Orchestration platforms manage container scheduling, health, and rolling deployments.');

    // --- Technical MCQs (12 Qs) ---
    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '18', '12', '15', 'Error', 'A',
        'Multiplication: 3 * 6 = 18.',
        "#include <stdio.h>\nint main() {\n    int a = 3, b = 6;\n    printf(\"%d\", a * b);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print using a loop?',
        '5 10', '5 10 15', '5', 'Error', 'A',
        'Loop prints i * 5 for i = 1, 2: 5 10.',
        "#include <stdio.h>\nint main() {\n    for(int i = 1; i <= 2; i++) {\n        printf(\"%d \", i * 5);\n    }\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_CPP', 'medium',
        'In C++, what is the default access specifier for members of a class?',
        'private', 'public', 'protected', 'package', 'A',
        'Members of a C++ class are private by default.');

    $b1[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        'TechMahindra', 'Tech', 'None', 'Error', 'A',
        'Prints literal "TechMahindra".',
        "#include <iostream>\nint main() {\n    std::cout << \"TechMahindra\";\n    return 0;\n}", 'cpp');

    $b1[] = makeQ($c, 'TECH_JAVA', 'easy',
        'What is the root class of the entire class hierarchy in Java?',
        'Object', 'Class', 'String', 'System', 'A',
        'java.lang.Object is the ultimate superclass of all classes in Java.');

    $b1[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        '10', '5', '20', 'Error', 'A',
        'Multiplication: 2 * 5 = 10.',
        "int x = 2 * 5;\nSystem.out.println(x);", 'java');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '3', '2', '4', 'Error', 'A',
        'Integer division: 7 // 2 = 3.',
        "print(7 // 2)", 'python');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python operation?',
        '[1, 2, 3]', '[3, 2, 1]', '[1, 2]', 'Error', 'A',
        'Sorted: [1, 2, 3].',
        "nums = [2, 1, 3]\nprint(sorted(nums))", 'python');

    $b1[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL command modifies the structure of an existing database table?',
        'ALTER TABLE', 'UPDATE TABLE', 'CHANGE TABLE', 'MODIFY TABLE', 'A',
        'ALTER TABLE adds, drops, or modifies columns in an existing table.');

    $b1[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL constraint uniquely identifies each record in a database table?',
        'PRIMARY KEY', 'FOREIGN KEY', 'CHECK', 'DEFAULT', 'A',
        'A PRIMARY KEY uniquely identifies every record in a table.');

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure is suitable for matching opening and closing HTML tags?',
        'Stack', 'Queue', 'Array', 'Heap', 'A',
        'A Stack (LIFO) matches nested opening and closing tags in order.');

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the average time complexity of searching an element in a binary search tree?',
        'O(log n)', 'O(n)', 'O(1)', 'O(n^2)', 'A',
        'Searching a balanced BST takes O(log n) average time.');

    // ========================================================
    // BATCH 2 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the value of 11^2?',
        '121', '111', '131', '141', 'A',
        '11 * 11 = 121.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'medium',
        'Find the perimeter of a regular hexagon with side 8 cm.',
        '48 cm', '42 cm', '54 cm', '40 cm', 'A',
        'Perimeter = 6 * side = 6 * 8 = 48 cm.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'hard',
        'What is the area of a circle with radius 7 cm (take pi = 22/7)?',
        '154 cm^2', '144 cm^2', '164 cm^2', '134 cm^2', 'A',
        'Area = pi * r^2 = (22 / 7) * 7 * 7 = 22 * 7 = 154 cm^2.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 10% of 900?',
        '90', '80', '100', '85', 'A',
        '0.10 * 900 = 90.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'medium',
        'Selling price is Rs. 700 and cost price is Rs. 500. Find the profit percentage.',
        '40%', '35%', '45%', '50%', 'A',
        'Profit = 700 - 500 = 200. Profit % = (200 / 500) * 100 = 40%.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'hard',
        'If an item marked at Rs. 1,000 is sold with a 15% discount, what is the selling price?',
        'Rs. 850', 'Rs. 800', 'Rs. 900', 'Rs. 880', 'A',
        'SP = 1000 * 0.85 = Rs. 850.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'If ratio of two numbers is 1 : 4 and smaller is 15, what is the larger?',
        '60', '50', '55', '65', 'A',
        '1 unit = 15 => 4 units = 60.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'medium',
        'The average of three numbers is 30. If two numbers are 25 and 35, what is the third?',
        '30', '28', '32', '34', 'A',
        'Total = 3 * 30 = 90. Third = 90 - (25 + 35) = 90 - 60 = 30.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A vehicle covers 200 km in 4 hours. What is its speed?',
        '50 km/h', '45 km/h', '55 km/h', '60 km/h', 'A',
        'Speed = 200 / 4 = 50 km/h.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A person walks 15 km at 3 km/h. How many hours did he walk?',
        '5 hours', '4 hours', '6 hours', '4.5 hours', 'A',
        'Time = 15 / 3 = 5 hours.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        '2 workers finish a report in 4 days. How many days will 4 workers take at the same pace?',
        '2 days', '1 day', '3 days', '4 days', 'A',
        'Workers doubled => time halved: 4 / 2 = 2 days.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A fills a reservoir in 6 hours and Pipe B in 12 hours. How long together?',
        '4 hours', '3 hours', '5 hours', '6 hours', 'A',
        'Rate = 1/6 + 1/12 = 3/12 = 1/4. Time = 4 hours.');

    // --- Logical Reasoning (10 Qs) ---
    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next number: 20, 40, 60, 80, ?',
        '100', '90', '110', '120', 'A',
        'Increments of 20: 80 + 20 = 100.');

    $b2[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 2, 4, 8, 16, ?',
        '32', '24', '30', '36', 'A',
        'Doubling pattern: 16 * 2 = 32.');

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If PIN is coded as 16-9-14, how is ZIP coded?',
        '26-9-16', '25-9-16', '26-8-16', '26-9-15', 'A',
        'Z=26, I=9, P=16. Code is 26-9-16.');

    $b2[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, RUN is written as SVO. How is FLY written in that code?',
        'GMZ', 'GLZ', 'HMZ', 'GNZ', 'A',
        'Shift +1: F(+1)=G, L(+1)=M, Y(+1)=Z. Code is GMZ.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'easy',
        'A is the daughter of B. B is the mother of C. How is A related to C?',
        'Sister', 'Mother', 'Aunt', 'Niece', 'A',
        'A and C share mother B, so A is the sister of C.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'medium',
        'X is the father of Y. Z is the son of Y. How is X related to Z?',
        'Grandfather', 'Father', 'Uncle', 'Brother', 'A',
        'X is the father of Z\'s parent Y, so X is the grandfather of Z.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 6 m East, then turning right and walking 8 m. What is the shortest distance from the start point?',
        '10 m', '14 m', '12 m', '9 m', 'A',
        'Distance = sqrt(6^2 + 8^2) = sqrt(36 + 64) = sqrt(100) = 10 m.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Facing South, turning 180 degrees faces:',
        'North', 'East', 'West', 'South-East', 'A',
        'South inverted 180 degrees faces North.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'In a row of 35 people, Ajay is 15th from the front. What is his rank from the back?',
        '21st', '20th', '22nd', '19th', 'A',
        'Rank from back = 35 - 15 + 1 = 21st.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All cloud servers are virtualized. All virtualized systems are monitored. Conclusions: I. All cloud servers are monitored. II. Some monitored systems are cloud servers.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'All cloud servers are virtualized and all virtualized systems are monitored, so all cloud servers are monitored. Also some monitored systems are cloud servers. Both follow.');

    // --- Verbal Ability (6 Qs) ---
    $b2[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "Every test case ______ executed automatically."',
        'was', 'were', 'are', 'is were', 'A',
        '"Every test case" is singular, requiring "was".');

    $b2[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the correct sentence:',
        'The team succeeded in delivering the project on time.',
        'The team succeeded to deliver the project on time.',
        'The team succeeded on delivering project on time.',
        'The team succeeded at delivering project on time.', 'A',
        '"Succeed in" followed by a gerund is the correct idiom.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: SECURE',
        'Safe', 'Risky', 'Dangerous', 'Uncertain', 'A',
        'Secure means fixed or protected; safe.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: CONVERGE',
        'Diverge', 'Merge', 'Unite', 'Combine', 'A',
        'Converge means meet at a point. The opposite is Diverge.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Network firewalls filter incoming and outgoing packets according to predetermined security rules to block malicious traffic." What is the role of a firewall?',
        'Filtering packets against security rules to prevent malicious traffic.',
        'Deleting user databases.',
        'Powering off client workstations.',
        'Increasing network latency intentionally.', 'A',
        'Firewalls inspect and filter traffic against configured security rules.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "Automated test suites enable rapid refactoring by providing instant regression feedback whenever code changes are introduced." Why is instant feedback valuable during refactoring?',
        'It immediately alerts developers to any introduced regressions.',
        'It eliminates the need for software code.',
        'It removes compilation steps.',
        'It prevents code from being edited.', 'A',
        'Immediate regression notifications allow developers to refactor with confidence.');

    // --- Technical MCQs (12 Qs) ---
    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '15', '10', '5', '0', 'A',
        'Addition: 10 + 5 = 15.',
        "#include <stdio.h>\nint main() {\n    int a = 10, b = 5;\n    printf(\"%d\", a + b);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print?',
        '6', '5', '4', '0', 'A',
        'Subtraction: 10 - 4 = 6.',
        "#include <stdio.h>\nint main() {\n    int a = 10, b = 4;\n    printf(\"%d\", a - b);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_CPP', 'medium',
        'Which keyword in C++ creates an instance of a class dynamically on the heap?',
        'new', 'malloc', 'alloc', 'create', 'A',
        'The new operator allocates heap memory in C++.');

    $b2[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        '25', '10', '5', '0', 'A',
        'Multiplication: 5 * 5 = 25.',
        "#include <iostream>\nint main() {\n    int x = 5;\n    std::cout << x * x;\n    return 0;\n}", 'cpp');

    $b2[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which keyword in Java prevents a class from being subclassed?',
        'final', 'static', 'abstract', 'private', 'A',
        'Declaring a class "final" prevents it from being extended.');

    $b2[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        '7', '5', '10', 'Error', 'A',
        'Addition: 3 + 4 = 7.',
        "int x = 3, y = 4;\nSystem.out.println(x + y);", 'java');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '4', '2', '8', 'Error', 'A',
        'Division: 8 / 2 evaluates to 4.0 or 4.',
        "print(int(8 / 2))", 'python');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python operation?',
        '[1, 2]', '[2, 3]', '[1, 2, 3]', 'Error', 'A',
        'Slice nums[:2] returns [1, 2].',
        "nums = [1, 2, 3]\nprint(nums[:2])", 'python');

    $b2[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL keyword removes duplicate records from a query result set?',
        'DISTINCT', 'UNIQUE', 'FILTER', 'CLEAN', 'A',
        'The DISTINCT keyword removes duplicate rows.');

    $b2[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL clause is used to filter records based on pattern matching?',
        'LIKE', 'MATCH', 'SEARCH', 'EQUALS', 'A',
        'The LIKE operator searches for specified patterns in columns.');

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure is based on the LIFO principle?',
        'Stack', 'Queue', 'Array', 'Linked List', 'A',
        'Stack operates on Last In First Out.');

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the best-case time complexity of Linear Search in an array?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n^2)', 'A',
        'When the target element is at index 0, Linear Search terminates in O(1) time.');

    return ['batch1' => $b1, 'batch2' => $b2];
}
