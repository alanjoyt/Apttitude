<?php
require_once __DIR__ . '/helper.php';

function getAccentureQuestions(): array {
    $b1 = [];
    $b2 = [];
    $c = 'Accenture';

    // ========================================================
    // BATCH 1 (40 Questions)
    // ========================================================
    // --- Quant / Numerical Ability (12 Qs) ---
    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'If a number x is divisible by both 6 and 8, what is the smallest positive integer that x must be divisible by?',
        '24', '48', '14', '12', 'A',
        'The smallest positive integer divisible by both 6 and 8 is their LCM. LCM(6, 8) = 24.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'medium',
        'Find the value of (256)^0.16 * (256)^0.09.',
        '4', '16', '64', '2', 'A',
        'Adding exponents: (256)^(0.16 + 0.09) = (256)^0.25 = (256)^(1/4). Since 4^4 = 256, (256)^(1/4) = 4.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'hard',
        'A merchant has 100 kg of sugar, part of which he sells at 7% profit and the rest at 17% profit. He gains 10% on the whole. How much sugar was sold at 7% profit?',
        '70 kg', '60 kg', '80 kg', '75 kg', 'A',
        'Using rule of alligation: (17 - 10) : (10 - 7) = 7 : 3. The ratio of sugar sold at 7% to 17% is 7 : 3. Sugar sold at 7% = (7 / 10) * 100 = 70 kg.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'easy',
        'In a batch of 80 applicants, 60 passed the coding assessment. What percentage of applicants passed?',
        '75%', '70%', '80%', '65%', 'A',
        'Pass percentage = (60 / 80) * 100 = 3/4 * 100 = 75%.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'A developer bought a mechanical keyboard for Rs. 4,000 and sold it for Rs. 4,600. What was the percentage profit?',
        '15%', '12%', '18%', '20%', 'A',
        'Profit = 4600 - 4000 = Rs. 600. Profit % = (600 / 4000) * 100 = 15%.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'hard',
        'A dealer marks an item 30% above the cost price and gives a discount of 10% on the marked price. What is his net gain percentage?',
        '17%', '20%', '15%', '18%', 'A',
        'Let CP = 100. Marked Price = 130. SP = 130 * 0.90 = 117. Profit = 117 - 100 = 17%.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A vehicle covers 300 meters in 15 seconds. What is its speed in km/h?',
        '72 km/h', '60 km/h', '80 km/h', '64 km/h', 'A',
        'Speed in m/s = 300 / 15 = 20 m/s. Speed in km/h = 20 * (18 / 5) = 72 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A train running at 54 km/h crosses a platform of length 200 m in 20 seconds. What is the length of the train?',
        '100 m', '120 m', '150 m', '80 m', 'A',
        'Speed = 54 * (5/18) = 15 m/s. Total distance in 20 s = 15 * 20 = 300 m. Train length = 300 - 200 = 100 m.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'hard',
        'A car travels from City A to City B at 40 km/h and returns from City B to City A at 60 km/h. If total travel time is 5 hours, find the distance between City A and City B.',
        '120 km', '100 km', '150 km', '140 km', 'A',
        'Average speed = (2 * 40 * 60) / (40 + 60) = 4800 / 100 = 48 km/h. Total distance (round trip) = 48 * 5 = 240 km. One-way distance = 240 / 2 = 120 km.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'easy',
        'A can do a piece of work in 10 days, and B can do it in 15 days. How many days will they take working together?',
        '6 days', '5 days', '7 days', '8 days', 'A',
        'Combined rate = 1/10 + 1/15 = 5/30 = 1/6. They will take 6 days.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A can fill a tank in 16 hours and Pipe B can empty it in 24 hours. If both operate simultaneously, in how many hours will the tank fill?',
        '48 hours', '36 hours', '40 hours', '52 hours', 'A',
        'Net filling rate = 1/16 - 1/24 = (3 - 2) / 48 = 1/48. The tank fills in 48 hours.');

    $b1[] = makeQ($c, 'DI_BAR_TABLE', 'medium',
        'Cloud storage consumption across 3 environments: Dev: 200 GB, Test: 300 GB, Prod: 500 GB. What percentage of total cloud storage is consumed by Prod?',
        '50%', '40%', '60%', '45%', 'A',
        'Total storage = 200 + 300 + 500 = 1000 GB. Prod percentage = (500 / 1000) * 100 = 50%.');

    // --- Logical Reasoning (10 Qs) ---
    $b1[] = makeQ($c, 'LR_SERIES', 'easy',
        'Identify the next number in the pattern: 2, 4, 8, 16, 32, ?',
        '64', '48', '56', '72', 'A',
        'Each term doubles: 32 * 2 = 64.');

    $b1[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 1, 8, 27, 64, 125, ?',
        '216', '343', '225', '256', 'A',
        'The sequence is cubes of integers: 1^3=1, 2^3=8, 3^3=27, 4^3=64, 5^3=125, 6^3=216.');

    $b1[] = makeQ($c, 'LR_CODING', 'easy',
        'If GAME is coded as 7-1-13-5, how is PLAY coded?',
        '16-12-1-25', '15-12-1-25', '16-11-1-25', '16-12-2-25', 'A',
        'P=16, L=12, A=1, Y=25. Code is 16-12-1-25.');

    $b1[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, SMART is written as TNBSU. How is CLEAN written in that code?',
        'DMFBO', 'DMEBN', 'DLFBO', 'EMFBO', 'A',
        'Each letter is shifted forward by 1: C(+1)=D, L(+1)=M, E(+1)=F, A(+1)=B, N(+1)=O. Code is DMFBO.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'easy',
        'A man points to a woman and says: "Her mother is the only daughter of my mother." How is the woman related to the man?',
        'Niece', 'Daughter', 'Sister', 'Aunt', 'A',
        'Only daughter of the man\'s mother is his sister. The daughter of his sister is his niece.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'medium',
        'Introducing Ramesh, Suman said: "His father is the only son of my grandfather." How is Suman related to Ramesh?',
        'Sister or Brother', 'Mother', 'Aunt', 'Cousin', 'A',
        'Grandfather\'s only son is Suman\'s father. Since Ramesh\'s father is also that same person, Suman is Ramesh\'s sister or brother.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'A person walks 20 meters towards South, then turns right and walks 15 meters. What is the straight-line distance from the starting position?',
        '25 meters', '30 meters', '35 meters', '20 meters', 'A',
        'Distance = sqrt(20^2 + 15^2) = sqrt(400 + 225) = sqrt(625) = 25 meters.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Starting at office, Ankit drives 10 km East, turns right and drives 5 km, then turns right again and drives 10 km. In which direction is he from the office?',
        'South', 'North', 'West', 'East', 'A',
        'East-West displacements cancel (10 km East then 10 km West). He is 5 km directly South of his office.');

    $b1[] = makeQ($c, 'LR_SEATING', 'medium',
        'Five people A, B, C, D, and E are seated in a row facing North. C sits in the middle. A sits at the left extreme. B is to the immediate right of C. Who is seated between A and C?',
        'D or E', 'B', 'C', 'None', 'A',
        'Positions: 1 (A), 2 (D or E), 3 (C), 4 (B), 5 (E or D). Position 2 is occupied by D or E.');

    $b1[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All APIs are endpoints. All endpoints are secure. Conclusions: I. All APIs are secure. II. Some secure entities are endpoints.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'Since all APIs are endpoints and all endpoints are secure, all APIs are secure (I follows). Since all endpoints are secure, some secure entities are endpoints (II follows). Both follow.');

    // --- Verbal Ability (6 Qs) ---
    $b1[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct form of the verb: "The manager, along with his team, ______ attending the global summit."',
        'is', 'are', 'were', 'have been', 'A',
        'When the subject is modified by "along with", the verb agrees with the primary subject ("The manager", singular). Hence "is" is correct.');

    $b1[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Select the sentence that is grammatically correct:',
        'Hardly had the server booted when the load test started.',
        'Hardly had the server booted than the load test started.',
        'Hardly the server had booted when the load test started.',
        'Hardly had the server booted then the load test started.', 'A',
        '"Hardly... when" is the correct correlative conjunction pair with inverted subject-auxiliary order.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'What is the synonym of the word: DILIGENT',
        'Hardworking', 'Careless', 'Lazy', 'Inactive', 'A',
        'Diligent means having or showing care and conscientiousness in one\'s work; hardworking.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Find the antonym of: TRANSPARENT',
        'Opaque', 'Clear', 'Lucid', 'Translucent', 'A',
        'Transparent means clear and see-through. Its antonym is Opaque.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Continuous delivery automates the release process so that new software builds can be deployed to production with one click at any time." What is the main goal of continuous delivery?',
        'Enabling reliable, single-click deployments to production at any moment.',
        'Eliminating all human programmers from the development process.',
        'Replacing relational databases with text spreadsheets.',
        'Bypassing all software security audits.', 'A',
        'Continuous delivery automates the deployment pipeline to make releases predictable and single-click.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the excerpt: "Role-Based Access Control (RBAC) simplifies permission management by assigning privileges to roles rather than individual users, reducing the risk of privilege creep." Why does RBAC reduce security risk?',
        'It decouples privileges from individuals, preventing accumulated unnecessary access.',
        'It makes all system passwords public to all employees.',
        'It disables encryption over the internal corporate network.',
        'It eliminates user authentication requirements.', 'A',
        'RBAC assigns privileges to defined organizational roles, preventing accumulated excess permissions.');

    // --- Technical & Pseudocode (12 Qs) ---
    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C code?',
        '10', '20', '30', 'Compilation Error', 'A',
        'Variable a is assigned 10. printf prints 10.',
        "#include <stdio.h>\nint main() {\n    int a = 10;\n    printf(\"%d\", a);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What does the following C code print?',
        '2 4 6', '1 2 3', '2 3 4', '0 2 4', 'A',
        'The loop iterates i = 1, 2, 3 and prints i * 2: 2 4 6.',
        "#include <stdio.h>\nint main() {\n    for(int i = 1; i <= 3; i++) {\n        printf(\"%d \", i * 2);\n    }\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_CPP', 'medium',
        'What does the "virtual" keyword in C++ function declaration enable?',
        'Dynamic Polymorphism (Run-time binding)', 'Static Polymorphism', 'Multiple Inheritance', 'Memory Deallocation', 'A',
        'Virtual functions enable dynamic run-time dispatch (polymorphism) through base class pointers.');

    $b1[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        'Hello Accenture', 'Accenture', 'Hello', 'Compilation Error', 'A',
        'Outputs string literal "Hello Accenture".',
        "#include <iostream>\nint main() {\n    std::cout << \"Hello Accenture\";\n    return 0;\n}", 'cpp');

    $b1[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which package is automatically imported into every Java program by default without an import statement?',
        'java.lang', 'java.util', 'java.io', 'java.net', 'A',
        'The java.lang package is imported automatically into every compilation unit in Java.');

    $b1[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What is the output of the following Java snippet?',
        '15', '10', '5', '0', 'A',
        'Addition: 5 + 10 = 15.',
        "int x = 5;\nint y = 10;\nSystem.out.println(x + y);", 'java');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What is the output of the following Python code snippet?',
        'ACCENTURE', 'accenture', 'Accenture', 'Error', 'A',
        'The upper() method converts string characters to uppercase, outputting "ACCENTURE".',
        "s = \"accenture\"\nprint(s.upper())", 'python');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python expression?',
        '[1, 4, 9]', '[1, 2, 3]', '[2, 4, 6]', '[1, 8, 27]', 'A',
        'List comprehension [x**2 for x in [1, 2, 3]] squares each element, producing [1, 4, 9].',
        "res = [x**2 for x in [1, 2, 3]]\nprint(res)", 'python');

    $b1[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL clause is used to filter records based on a specific condition?',
        'WHERE', 'ORDER BY', 'GROUP BY', 'SELECT', 'A',
        'The WHERE clause filters rows satisfying a specified condition.');

    $b1[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL keyword modifies existing data inside a database table?',
        'UPDATE', 'MODIFY', 'ALTER', 'CHANGE', 'A',
        'The UPDATE command modifies existing records in a table.');

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure follows the Last In First Out (LIFO) order?',
        'Stack', 'Queue', 'Array', 'Linked List', 'A',
        'A Stack operates on the Last In First Out (LIFO) principle.');

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the worst-case time complexity of searching an element in a linear Array of size n?',
        'O(n)', 'O(log n)', 'O(1)', 'O(n^2)', 'A',
        'Linear search checks each element one-by-one in the worst case, requiring O(n) time.');

    // ========================================================
    // BATCH 2 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the value of 15^2 - 12^2?',
        '81', '75', '90', '64', 'A',
        'Using identity a^2 - b^2 = (a - b)(a + b) = (15 - 12)(15 + 12) = 3 * 27 = 81.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'medium',
        'If a, b, c are in arithmetic progression with common difference 4, and a = 10, what is the value of c?',
        '18', '14', '16', '20', 'A',
        'In an AP, b = a + d = 10 + 4 = 14, and c = b + d = 14 + 4 = 18.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the sum of all multiples of 4 between 1 and 50.',
        '312', '300', '320', '288', 'A',
        'Multiples of 4: 4, 8, 12, ..., 48 (n = 12 terms). Sum = (n / 2) * (first + last) = (12 / 2) * (4 + 48) = 6 * 52 = 312.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'Convert 3/5 into a percentage.',
        '60%', '50%', '65%', '70%', 'A',
        '(3 / 5) * 100 = 60%.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'medium',
        'An item priced at Rs. 1,200 is offered at a 25% discount. What is the discounted price?',
        'Rs. 900', 'Rs. 950', 'Rs. 850', 'Rs. 1,000', 'A',
        'Discount = 25% of 1200 = 300. Price = 1200 - 300 = Rs. 900.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'hard',
        'If the length of a rectangle is increased by 20% and breadth is decreased by 10%, what is the net percentage change in area?',
        '8% increase', '10% increase', '8% decrease', '5% increase', 'A',
        'Net change = +20 - 10 + (20 * -10) / 100 = 10 - 2 = +8% increase.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'The ratio of apples to oranges in a fruit basket is 4 : 5. If there are 20 apples, how many oranges are there?',
        '25', '24', '30', '28', 'A',
        '4 units = 20 => 1 unit = 5. Oranges = 5 * 5 = 25.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'medium',
        'The average of three numbers is 20. If two of the numbers are 16 and 22, what is the third number?',
        '22', '20', '24', '18', 'A',
        'Total sum = 3 * 20 = 60. Third number = 60 - (16 + 22) = 60 - 38 = 22.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A train covers 180 km in 3 hours. What is its speed?',
        '60 km/h', '50 km/h', '70 km/h', '65 km/h', 'A',
        'Speed = 180 / 3 = 60 km/h.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A runner covers a 400 m track in 50 seconds. What is his speed in km/h?',
        '28.8 km/h', '25.0 km/h', '30.0 km/h', '24.5 km/h', 'A',
        'Speed in m/s = 400 / 50 = 8 m/s. Speed in km/h = 8 * (18 / 5) = 144 / 5 = 28.8 km/h.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        'If 5 workers complete a task in 10 days, how many days will 10 workers take at the same rate?',
        '5 days', '4 days', '6 days', '3 days', 'A',
        'Total worker-days = 5 * 10 = 50. With 10 workers, days = 50 / 10 = 5 days.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A fills a reservoir in 4 hours, and Pipe B fills it in 6 hours. How many hours will both take together?',
        '2.4 hours', '2.0 hours', '3.0 hours', '2.5 hours', 'A',
        'Combined rate = 1/4 + 1/6 = 5/12. Time = 12 / 5 = 2.4 hours.');

    // --- Logical Reasoning (10 Qs) ---
    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'What is the next number: 10, 20, 30, 40, ?',
        '50', '60', '45', '55', 'A',
        'The numbers increment by 10 each step: 40 + 10 = 50.');

    $b2[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 1, 3, 7, 15, 31, ?',
        '63', '60', '62', '64', 'A',
        'Pattern is * 2 + 1: 1*2+1=3, 3*2+1=7, 7*2+1=15, 15*2+1=31, 31*2+1=63.');

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If PEN is coded as 16-5-14, how is BOOK coded?',
        '2-15-15-11', '2-14-14-11', '2-15-15-10', '3-15-15-11', 'A',
        'B=2, O=15, O=15, K=11. Combined: 2-15-15-11.');

    $b2[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, LEAD is written as MDZE. How is GOLD written in that code?',
        'HPME', 'HPLE', 'HOME', 'IPME', 'A',
        'Each letter shifted forward by 1: G(+1)=H, O(+1)=P, L(+1)=M, D(+1)=E. Code is HPME.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'easy',
        'X is the brother of Y. Y is the mother of Z. How is X related to Z?',
        'Maternal Uncle', 'Father', 'Brother', 'Paternal Uncle', 'A',
        'X is the brother of Z\'s mother Y, making X the maternal uncle of Z.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'medium',
        'A is the father of B and grandfather of C. B is the brother of D. How is D related to A if D is female?',
        'Daughter', 'Son', 'Niece', 'Granddaughter', 'A',
        'Since B is the child of A and D is B\'s sister, D is A\'s daughter.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'A person faces North and turns 180 degrees. Which direction is he facing now?',
        'South', 'East', 'West', 'North-West', 'A',
        'A 180-degree turn inverts orientation from North to South.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Walking 5 km East, then 5 km North. What direction is the final position from the starting point?',
        'North-East', 'North-West', 'South-East', 'South-West', 'A',
        'With positive East and North displacement, the position is North-East.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'In a line of 25 trees, a pine tree is 10th from the left. What is its position from the right end?',
        '16th', '15th', '17th', '14th', 'A',
        'Position from right = 25 - 10 + 1 = 16th.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All servers are computers. All computers are electronic. Conclusions: I. All servers are electronic. II. Some electronic devices are servers.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'Since all servers are computers and all computers are electronic, all servers are electronic. Also, some electronic devices are servers. Both follow.');

    // --- Verbal Ability (6 Qs) ---
    $b2[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Fill in the blank: "Neither of the two candidates ______ selected for the role."',
        'was', 'were', 'are', 'have been', 'A',
        '"Neither" takes a singular verb: "was".');

    $b2[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Choose the grammatically correct sentence:',
        'She performed better than expected on the evaluation.',
        'She performed more better than expected on the evaluation.',
        'She performed best than expected on the evaluation.',
        'She performed good than expected on the evaluation.', 'A',
        '"Better than" is the correct comparative form.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: INNOVATIVE',
        'Creative', 'Traditional', 'Customary', 'Old-fashioned', 'A',
        'Innovative means featuring new methods; creative.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: FRAGILE',
        'Sturdy', 'Delicate', 'Brittle', 'Weak', 'A',
        'Fragile means easily broken. Its opposite is Sturdy.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the notice: "Automated vulnerability scanning detects unpatched library dependencies across repositories before pull requests are merged." What is the benefit of dependency scanning?',
        'Detecting unpatched library vulnerabilities before code is merged.',
        'Rewriting application source code automatically.',
        'Increasing cloud server bills.',
        'Preventing developers from writing unit tests.', 'A',
        'The scanner identifies vulnerable library dependencies prior to code merging.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "Stateless microservices store session state in external caches rather than in-memory, enabling seamless horizontal auto-scaling across elastic cloud instances." What enables seamless horizontal scaling?',
        'Storing session state externally rather than in local memory.',
        'Using static IP addresses for all virtual servers.',
        'Hardcoding credentials in source files.',
        'Limiting application execution to a single CPU core.', 'A',
        'Externalizing state allows stateless service instances to scale horizontally without losing sessions.');

    // --- Technical & Pseudocode (12 Qs) ---
    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '7', '10', '5', '0', 'A',
        'Subtraction: 10 - 3 = 7.',
        "#include <stdio.h>\nint main() {\n    int a = 10, b = 3;\n    printf(\"%d\", a - b);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What will this C loop print?',
        '0 1 2', '1 2 3', '0 1 2 3', 'Infinite', 'A',
        'Loop runs for i = 0, 1, 2 and terminates when i == 3. Output is 0 1 2.',
        "#include <stdio.h>\nint main() {\n    for(int i = 0; i < 3; i++) {\n        printf(\"%d \", i);\n    }\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_CPP', 'medium',
        'Which access specifier in C++ makes class members accessible only within the class itself?',
        'private', 'public', 'protected', 'internal', 'A',
        'Private members are accessible only by member functions of the class.');

    $b2[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ code?',
        '20', '10', '0', 'Error', 'A',
        'Multiplication: 4 * 5 = 20.',
        "#include <iostream>\nint main() {\n    int x = 4, y = 5;\n    std::cout << x * y;\n    return 0;\n}", 'cpp');

    $b2[] = makeQ($c, 'TECH_JAVA', 'easy',
        'What is the size of an int data type in Java?',
        '4 bytes', '2 bytes', '8 bytes', '1 byte', 'A',
        'An int in Java is a 32-bit signed integer (4 bytes).');

    $b2[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What does the following Java snippet output?',
        'true', 'false', 'null', 'Error', 'A',
        'Boolean expression 10 > 5 evaluates to true.',
        "boolean b = (10 > 5);\nSystem.out.println(b);", 'java');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '5', '4', '6', 'Error', 'A',
        'len("World") evaluates to 5.',
        "print(len(\"World\"))", 'python');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of this Python slice operation?',
        '[1, 2]', '[2, 3]', '[1, 2, 3]', '[3]', 'A',
        'items[:2] selects indices 0 and 1, producing [1, 2].',
        "items = [1, 2, 3]\nprint(items[:2])", 'python');

    $b2[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL command creates a new table in a database?',
        'CREATE TABLE', 'ADD TABLE', 'NEW TABLE', 'BUILD TABLE', 'A',
        'CREATE TABLE creates a new table with specified column definitions.');

    $b2[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL keyword deletes specific records from a table based on a condition?',
        'DELETE FROM', 'DROP', 'REMOVE', 'CLEAR', 'A',
        'The DELETE FROM statement removes rows satisfying a WHERE clause.');

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'What is the time complexity of accessing an element in an array by its index?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n^2)', 'A',
        'Array indexing computes base address + index * element_size in O(1) constant time.');

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'Which searching algorithm requires the input array to be sorted before execution?',
        'Binary Search', 'Linear Search', 'Breadth-First Search', 'Depth-First Search', 'A',
        'Binary Search relies on sorted elements to halve the search interval each step.');

    return ['batch1' => $b1, 'batch2' => $b2];
}
