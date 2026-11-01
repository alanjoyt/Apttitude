<?php
require_once __DIR__ . '/helper.php';

function getCapgeminiQuestions(): array {
    $b1 = [];
    $b2 = [];
    $c = 'Capgemini';

    // ========================================================
    // BATCH 1 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the value of 2^5 * 2^3?',
        '256', '128', '512', '64', 'A',
        '2^(5 + 3) = 2^8 = 256.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'medium',
        'Find the value of x if 3^(x + 2) = 81.',
        '2', '3', '4', '1', 'A',
        '81 = 3^4. Therefore x + 2 = 4 => x = 2.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'hard',
        'How many numbers between 100 and 300 are completely divisible by 7?',
        '28', '29', '27', '30', 'A',
        'First multiple after 100 is 105 (7*15). Last multiple before 300 is 294 (7*42). Count = 42 - 15 + 1 = 28.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 40% of 250?',
        '100', '90', '110', '120', 'A',
        '40% of 250 = 0.40 * 250 = 100.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'If cost price is Rs. 800 and profit is 25%, what is the selling price?',
        'Rs. 1,000', 'Rs. 950', 'Rs. 1,050', 'Rs. 900', 'A',
        'SP = 800 * 1.25 = Rs. 1,000.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'hard',
        'A trader sells an article at 10% loss. Had he sold it for Rs. 90 more, he would have gained 5%. What is the cost price?',
        'Rs. 600', 'Rs. 500', 'Rs. 700', 'Rs. 550', 'A',
        'Difference in percentages = 5% - (-10%) = 15%. 15% of CP = 90 => CP = 90 / 0.15 = Rs. 600.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A vehicle covers 240 km in 4 hours. What is its speed?',
        '60 km/h', '50 km/h', '70 km/h', '55 km/h', 'A',
        'Speed = 240 / 4 = 60 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A train 120 m long crosses an electric pole in 6 seconds. What is its speed in km/h?',
        '72 km/h', '60 km/h', '80 km/h', '54 km/h', 'A',
        'Speed = 120 / 6 = 20 m/s = 20 * (18 / 5) = 72 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'hard',
        'A man walks at 4 km/h and reaches office 5 minutes late. If he walks at 5 km/h, he reaches 5 minutes early. What is the distance to his office?',
        '3.33 km', '4.00 km', '2.50 km', '5.00 km', 'A',
        'Time difference = 10 minutes = 1/6 hour. Distance = (s1 * s2 / (s2 - s1)) * (diff) = (4 * 5 / 1) * (1/6) = 20 / 6 = 10/3 ≈ 3.33 km.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'easy',
        'A and B complete a task in 6 days. A alone takes 10 days. How many days will B take alone?',
        '15 days', '12 days', '18 days', '20 days', 'A',
        'B\'s rate = 1/6 - 1/10 = 2/30 = 1/15. B takes 15 days.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'medium',
        'A can do a work in 12 days and B in 15 days. They work together for 4 days. What fraction of the work is left?',
        '2/5', '1/3', '3/5', '1/4', 'A',
        '1 day work = 1/12 + 1/15 = 9/60 = 3/20. In 4 days: 4 * (3/20) = 12/20 = 3/5. Work left = 1 - 3/5 = 2/5.');

    $b1[] = makeQ($c, 'DI_BAR_TABLE', 'medium',
        'Daily active users over 3 months: Jan: 10,000, Feb: 12,000, Mar: 14,000. What is the average monthly DAU?',
        '12,000', '11,000', '13,000', '12,500', 'A',
        'Total = 10000 + 12000 + 14000 = 36000. Average = 36000 / 3 = 12,000.');

    // --- Logical Reasoning (10 Qs) ---
    $b1[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next number: 11, 22, 33, 44, ?',
        '55', '50', '66', '45', 'A',
        'Increments of 11: 44 + 11 = 55.');

    $b1[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 3, 6, 12, 24, ?',
        '48', '36', '42', '52', 'A',
        'Doubling pattern: 24 * 2 = 48.');

    $b1[] = makeQ($c, 'LR_CODING', 'easy',
        'If COLD is coded as 3-15-12-4, how is WARM coded?',
        '23-1-18-13', '23-2-18-13', '22-1-18-13', '23-1-19-13', 'A',
        'W=23, A=1, R=18, M=13. Code is 23-1-18-13.');

    $b1[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, CODE is written as DPEF. How is JAVA written in that code?',
        'KBWB', 'KAVB', 'KBWA', 'JBWB', 'A',
        'Each letter shifted forward by 1: J(+1)=K, A(+1)=B, V(+1)=W, A(+1)=B. Code is KBWB.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'easy',
        'Pointing to a boy, Vinod said: "He is the son of my father\'s only son." How is the boy related to Vinod?',
        'Son', 'Brother', 'Nephew', 'Cousin', 'A',
        'Father\'s only son is Vinod himself. The son of Vinod is his son.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'medium',
        'A is the brother of B. B is the child of C. C is the wife of D. How is D related to A?',
        'Father', 'Uncle', 'Brother', 'Grandfather', 'A',
        'C is the mother of A, and D is the husband of C. Therefore, D is the father of A.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 8 m North, then turning right and walking 6 m. What is the shortest distance from the start point?',
        '10 m', '14 m', '12 m', '9 m', 'A',
        'Distance = sqrt(8^2 + 6^2) = sqrt(64 + 36) = sqrt(100) = 10 m.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Facing South, turning left and walking 10 m. Which direction is one traveling?',
        'East', 'West', 'North', 'South', 'A',
        'Left turn from South faces East.');

    $b1[] = makeQ($c, 'LR_SEATING', 'medium',
        'Four friends A, B, C, and D sit around a square table facing center. A is opposite C. B is to the right of A. Who is opposite B?',
        'D', 'C', 'A', 'None', 'A',
        'Opposite pairs are (A, C) and (B, D). B is opposite D.');

    $b1[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All tests are automated. All automated tasks save time. Conclusions: I. All tests save time. II. Some time-saving tasks are tests.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'All tests are automated, and all automated tasks save time. Therefore all tests save time (I follows). Also some tasks that save time are tests (II follows). Both follow.');

    // --- Verbal Ability (6 Qs) ---
    $b1[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Fill in the blank: "The team ______ working on the new microservice."',
        'is', 'are', 'were', 'have', 'A',
        '"Team" acts as a singular collective subject: "is".');

    $b1[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the correct sentence:',
        'She prefer to write code in Python than in Java... wait: She prefers writing code in Python to Java.',
        'She prefers writing code in Python to Java.',
        'She prefers writing code in Python than Java.',
        'She prefer write code in Python than Java.',
        'She is preferring writing code in Python than Java.', 'A',
        '"Prefer... to" is the correct prepositional combination.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: RELIABLE',
        'Dependable', 'Erratic', 'Uncertain', 'Dubious', 'A',
        'Reliable means consistently good in quality or performance; dependable.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: EXPLICIT',
        'Vague', 'Clear', 'Precise', 'Exact', 'A',
        'Explicit means stated clearly and in detail. Its antonym is Vague.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Database replication creates copies of data across geographically distributed clusters to guarantee high availability." What is the objective of data replication?',
        'Ensuring high availability through redundant data copies.',
        'Slowing down query responses.',
        'Deleting duplicate records permanently.',
        'Restricting users to a single datacenter.', 'A',
        'Replication distributes copies across clusters to ensure high system availability.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "Feature flags allow software engineering teams to deploy code changes to production while keeping them dormant until ready for release." What do feature flags facilitate?',
        'Decoupling code deployment from feature release.',
        'Preventing all production deployments.',
        'Eliminating the need for source version control.',
        'Automating employee performance reviews.', 'A',
        'Feature flags decouple deployment from release, enabling safer rollouts.');

    // --- Technical & Pseudocode (12 Qs) ---
    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '14', '10', '4', '0', 'A',
        'Addition: 10 + 4 = 14.',
        "#include <stdio.h>\nint main() {\n    int a = 10, b = 4;\n    printf(\"%d\", a + b);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print using a loop?',
        '0 2 4', '0 1 2', '2 4 6', 'Error', 'A',
        'The loop steps i by 2: i = 0, 2, 4. Output is 0 2 4.',
        "#include <stdio.h>\nint main() {\n    for(int i = 0; i <= 4; i += 2) {\n        printf(\"%d \", i);\n    }\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_CPP', 'medium',
        'Which C++ feature enables a class to have multiple constructors with different argument types?',
        'Constructor Overloading', 'Constructor Overriding', 'Virtual Constructors', 'Inheritance', 'A',
        'Constructor overloading defines multiple constructors differentiated by parameter lists.');

    $b1[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        'Capgemini', 'Cap', 'Gemini', 'Error', 'A',
        'Prints literal "Capgemini".',
        "#include <iostream>\nint main() {\n    std::cout << \"Capgemini\";\n    return 0;\n}", 'cpp');

    $b1[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which Java keyword is used to explicitly throw an exception?',
        'throw', 'throws', 'catch', 'try', 'A',
        'The "throw" keyword is used to explicitly throw an exception instance.');

    $b1[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code output?',
        'Capgemini', 'capgemini', 'CAPGEMINI', 'Error', 'A',
        'The code prints the string literal "Capgemini".',
        "String s = \"Capgemini\";\nSystem.out.println(s);", 'java');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '15', '10', '5', 'Error', 'A',
        'sum([5, 10]) evaluates to 15.',
        "print(sum([5, 10]))", 'python');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python expression?',
        '[2, 4, 6]', '[1, 2, 3]', '[4, 5, 6]', 'Error', 'A',
        'List comprehension [x * 2 for x in [1, 2, 3]] multiplies each element by 2: [2, 4, 6].',
        "res = [x * 2 for x in [1, 2, 3]]\nprint(res)", 'python');

    $b1[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL keyword is used to extract data from a database?',
        'SELECT', 'GET', 'EXTRACT', 'PULL', 'A',
        'The SELECT statement queries and retrieves records from database tables.');

    $b1[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL clause is used with aggregate functions to filter groups of records?',
        'HAVING', 'WHERE', 'GROUP BY', 'ORDER BY', 'A',
        'The HAVING clause filters aggregated group results.');

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure organizes elements in a hierarchical parent-child structure?',
        'Tree', 'Queue', 'Stack', 'Array', 'A',
        'A Tree is a non-linear hierarchical data structure consisting of nodes with parent-child relationships.');

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the time complexity of searching an element in a Hash Table on average?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n^2)', 'A',
        'Hash table lookup operates in average constant time O(1).');

    // ========================================================
    // BATCH 2 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is 8^2 + 6^2?',
        '100', '90', '110', '120', 'A',
        '64 + 36 = 100.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'medium',
        'Find the average of 10, 20, 30, 40, and 50.',
        '30', '25', '35', '40', 'A',
        'Sum = 150. Average = 150 / 5 = 30.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the perimeter of an equilateral triangle with side 12 cm.',
        '36 cm', '30 cm', '24 cm', '48 cm', 'A',
        'Perimeter = 3 * side = 3 * 12 = 36 cm.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 30% of 150?',
        '45', '40', '50', '35', 'A',
        '30% of 150 = 0.30 * 150 = 45.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'medium',
        'Selling an item for Rs. 540 results in a 10% loss. What was the cost price?',
        'Rs. 600', 'Rs. 580', 'Rs. 620', 'Rs. 550', 'A',
        'CP = SP / 0.90 = 540 / 0.9 = Rs. 600.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'hard',
        'In an exam, a student requires 40% to pass. He gets 178 marks and fails by 22 marks. What are the maximum marks?',
        '500', '450', '550', '600', 'A',
        'Passing marks = 178 + 22 = 200. 40% of Max = 200 => Max = 200 / 0.40 = 500.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'If ratio of two numbers is 5 : 6 and smaller is 25, what is the larger?',
        '30', '35', '28', '32', 'A',
        '5 units = 25 => 1 unit = 5. Larger = 6 * 5 = 30.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'medium',
        'The ratio of length to breadth of a hall is 4 : 3. If breadth is 15 m, what is the length?',
        '20 m', '18 m', '22 m', '24 m', 'A',
        '3 units = 15 => 1 unit = 5. Length = 4 * 5 = 20 m.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A car covers 100 km in 2 hours. What is its speed?',
        '50 km/h', '60 km/h', '45 km/h', '55 km/h', 'A',
        'Speed = 100 / 2 = 50 km/h.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A cyclist rides 45 km at 15 km/h. How many hours did he ride?',
        '3 hours', '2.5 hours', '4 hours', '3.5 hours', 'A',
        'Time = Distance / Speed = 45 / 15 = 3 hours.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        '4 workers complete a project in 10 days. How many workers complete it in 5 days?',
        '8 workers', '6 workers', '10 workers', '7 workers', 'A',
        'Worker-days = 4 * 10 = 40. In 5 days: 40 / 5 = 8 workers.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A fills a tank in 5 hours and Pipe B fills it in 20 hours. How long together?',
        '4 hours', '3.5 hours', '4.5 hours', '5 hours', 'A',
        'Rate = 1/5 + 1/20 = 5/20 = 1/4. Time = 4 hours.');

    // --- Logical Reasoning (10 Qs) ---
    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the missing term: 100, 200, 300, 400, ?',
        '500', '600', '450', '550', 'A',
        'Increments of 100: 400 + 100 = 500.');

    $b2[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the next number: 1, 2, 4, 8, 16, ?',
        '32', '24', '30', '36', 'A',
        'Each term doubles: 16 * 2 = 32.');

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If KEY is coded as 11-5-25, how is LOCK coded?',
        '12-15-3-11', '12-14-3-11', '11-15-3-11', '12-15-4-11', 'A',
        'L=12, O=15, C=3, K=11. Code is 12-15-3-11.');

    $b2[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, STOP is written as TUPQ. How is GO written in that code?',
        'HP', 'HO', 'IP', 'GP', 'A',
        'Shift +1: G(+1)=H, O(+1)=P. Code is HP.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'easy',
        'X is the sister of Y. Y is the mother of Z. How is X related to Z?',
        'Maternal Aunt', 'Mother', 'Sister', 'Grandmother', 'A',
        'X is the sister of Z\'s mother Y, making X the maternal aunt of Z.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'medium',
        'M is the father of N. O is the mother of N. How is M related to O?',
        'Husband', 'Brother', 'Son', 'Uncle', 'A',
        'M and O are the parents of N, so M is the husband of O.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 10 m East, then turning left and walking 10 m. What direction is the final position from the starting point?',
        'North-East', 'North-West', 'South-East', 'South-West', 'A',
        'Displacement is East and North: North-East.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Facing West, turning 90 degrees clockwise orients one towards:',
        'North', 'South', 'East', 'North-West', 'A',
        'West + 90 deg clockwise faces North.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'In a row of 20 cars, a red car is 6th from the left. What is its position from the right?',
        '15th', '14th', '16th', '13th', 'A',
        'Position from right = 20 - 6 + 1 = 15th.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All pods are containers. All containers are isolated. Conclusions: I. All pods are isolated. II. Some isolated items are pods.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'All pods are containers, and all containers are isolated => all pods are isolated. Also, some isolated items are pods. Both follow.');

    // --- Verbal Ability (6 Qs) ---
    $b2[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "Every developer ______ a personal workspace assigned."',
        'has', 'have', 'are', 'were', 'A',
        '"Every developer" is singular, requiring "has".');

    $b2[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the correct sentence:',
        'He is capable of solving complex architectural bottlenecks.',
        'He is capable to solve complex architectural bottlenecks.',
        'He is capable in solving complex bottlenecks.',
        'He is capable on solving complex bottlenecks.', 'A',
        '"Capable of" followed by a gerund is the correct idiom.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: PRECISE',
        'Accurate', 'Vague', 'Careless', 'Rough', 'A',
        'Precise means exact and accurate.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: COMPACT',
        'Sprawling', 'Dense', 'Small', 'Tight', 'A',
        'Compact means neatly packed into a small space. Its antonym is Sprawling.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Immutable infrastructure replaces servers entirely upon update rather than making in-place modifications, eliminating configuration drift." What is the benefit of immutable infrastructure?',
        'It eliminates configuration drift by replacing servers on updates.',
        'It increases manual server patching frequency.',
        'It removes automated testing requirements.',
        'It prohibits code deployments.', 'A',
        'Replacing instances on every change prevents configuration drift over time.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "A distributed cache stores frequent query results in high-speed RAM, offloading pressure from underlying relational database storage." How does a distributed cache improve performance?',
        'By serving frequent queries from memory to offload relational databases.',
        'By deleting old data records permanently.',
        'By writing all queries directly to tape storage.',
        'By slowing down network traffic.', 'A',
        'Serving hot data from in-memory caches reduces load on persistent databases.');

    // --- Technical & Pseudocode (12 Qs) ---
    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '6', '5', '7', 'Error', 'A',
        'Multiplication: 2 * 3 = 6.',
        "#include <stdio.h>\nint main() {\n    int x = 2, y = 3;\n    printf(\"%d\", x * y);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print?',
        '50', '25', '10', '0', 'A',
        'Assignment: a = 50. printf prints 50.',
        "#include <stdio.h>\nint main() {\n    int a = 50;\n    printf(\"%d\", a);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_CPP', 'medium',
        'Which keyword in C++ is used to define a blueprint or user-defined data type?',
        'class', 'type', 'blueprint', 'structure_type', 'A',
        'The class keyword defines an object-oriented blueprint containing state and behavior.');

    $b2[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        '40', '20', '0', 'Error', 'A',
        'Addition: 20 + 20 = 40.',
        "#include <iostream>\nint main() {\n    int x = 20, y = 20;\n    std::cout << x + y;\n    return 0;\n}", 'cpp');

    $b2[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which keyword in Java is used to allocate new object memory on the heap?',
        'new', 'alloc', 'malloc', 'create', 'A',
        'The new operator instantiates objects in Java.');

    $b2[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        'Java', 'java', 'JAVA', 'Error', 'A',
        'Prints the string literal "Java".',
        "String s = \"Java\";\nSystem.out.println(s);", 'java');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '10', '5', '15', 'Error', 'A',
        'max(5, 10) returns 10.',
        "print(max(5, 10))", 'python');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python operation?',
        'Capgemini', 'capgemini', 'CAPGEMINI', 'Error', 'A',
        'strip() returns the trimmed string "Capgemini".',
        "s = '  Capgemini  '\nprint(s.strip())", 'python');

    $b2[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL clause specifies the table to retrieve records from?',
        'FROM', 'WHERE', 'SELECT', 'INTO', 'A',
        'The FROM clause defines the source table for a query.');

    $b2[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL constraint prevents null values from being stored in a column?',
        'NOT NULL', 'UNIQUE', 'DEFAULT', 'CHECK', 'A',
        'The NOT NULL constraint enforces that a column cannot store NULL values.');

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure uses pointers to link separate nodes sequentially in memory?',
        'Linked List', 'Array', 'Stack', 'Queue', 'A',
        'A Linked List consists of nodes where each node contains data and a pointer to the next node.');

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the time complexity of searching an element in a sorted array of size n using Binary Search?',
        'O(log n)', 'O(n)', 'O(1)', 'O(n log n)', 'A',
        'Binary Search operates in O(log n) time complexity.');

    return ['batch1' => $b1, 'batch2' => $b2];
}
