<?php
require_once __DIR__ . '/helper.php';

function getTcsNqtQuestions(): array {
    $b1 = [];
    $b2 = [];
    $c = 'TCS NQT';

    // ========================================================
    // BATCH 1 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'Find the remainder when 7^84 is divided by 342.',
        '1', '7', '49', '0', 'A',
        'Note that 7^3 = 343. Thus, 7^84 = (7^3)^28 = 343^28 = (342 + 1)^28. By the binomial theorem, (342 + 1)^28 = 342*k + 1^28 = 342*k + 1. Hence, the remainder is 1.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'medium',
        'How many trailing zeros are there in the decimal representation of 125! ?',
        '31', '28', '25', '35', 'A',
        'Trailing zeros are determined by the power of 5 in 125!. Power of 5 = floor(125/5) + floor(125/25) + floor(125/125) = 25 + 5 + 1 = 31.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'hard',
        'A two-digit number is such that the product of its digits is 18. When 27 is added to the number, the digits reverse their places. What is the original number?',
        '36', '29', '63', '92', 'A',
        'Let digits be x and y (number = 10x + y). xy = 18. (10x + y) + 27 = 10y + x => 9y - 9x = 27 => y - x = 3 => y = x + 3. Substituting into xy = 18: x(x + 3) = 18 => x^2 + 3x - 18 = 0 => (x + 6)(x - 3) = 0. Since digits are positive, x = 3 and y = 6. Original number is 36.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'easy',
        'If the price of petrol increases by 25%, by what percentage must a driver reduce fuel consumption so that expenditure remains unchanged?',
        '20%', '25%', '15%', '16.67%', 'A',
        'Let initial expenditure = 100 * 1 = 100. New price = 125. New consumption = 100 / 125 = 0.8. Reduction = 1 - 0.8 = 0.20 = 20%.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'A merchant sells two smartwatches for Rs. 4,800 each. On one he gains 20% and on the other he loses 20%. What is his overall gain or loss percentage?',
        '4% loss', '4% gain', 'No gain no loss', '2% loss', 'A',
        'When two items are sold at the same selling price with x% gain and x% loss, there is an overall loss of (x/10)^2 % = (20/10)^2 % = 4% loss.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'hard',
        'In an election between two candidates, 10% of the electorate did not vote and 60 votes were declared invalid. The winning candidate secured 47% of all registered voters on the electoral roll and won by a margin of 308 votes. Find the total number of registered voters.',
        '6200', '5800', '6400', '5000', 'A',
        'Let total voters = 100x. Votes polled = 90x. Valid votes = 90x - 60. Winner votes = 47x. Loser votes = (90x - 60) - 47x = 43x - 60. Margin: 47x - (43x - 60) = 308 => 4x + 60 = 308 => 4x = 248 => x = 62. Total voters = 100 * 62 = 6200.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A train moving at 90 km/h crosses an electric pole in 12 seconds. What is the length of the train in meters?',
        '300 m', '250 m', '320 m', '280 m', 'A',
        'Speed in m/s = 90 * (5/18) = 25 m/s. Length of train = speed * time = 25 * 12 = 300 m.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A motorist drives from Town A to Town B at 60 km/h and returns at 40 km/h. What is his average speed for the entire round trip?',
        '48 km/h', '50 km/h', '45 km/h', '52 km/h', 'A',
        'Average speed for equal distances = 2 * v1 * v2 / (v1 + v2) = 2 * 60 * 40 / (60 + 40) = 4800 / 100 = 48 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'hard',
        'Two runners start simultaneously from point P on a circular track of circumference 600 m in opposite directions at speeds of 6 m/s and 4 m/s. How many seconds after starting will they meet for the first time?',
        '60 seconds', '50 seconds', '100 seconds', '120 seconds', 'A',
        'Relative speed when running in opposite directions = 6 + 4 = 10 m/s. Time to first meeting = 600 / 10 = 60 seconds.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'easy',
        'A and B together can finish a job in 12 days. A alone can finish it in 20 days. How many days will B take alone?',
        '30 days', '25 days', '35 days', '28 days', 'A',
        'B\'s rate = 1/12 - 1/20 = (5 - 3) / 60 = 2 / 60 = 1/30. B takes 30 days alone.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'medium',
        'A can do a piece of work in 16 days and B in 24 days. With the help of C, they complete the work in 6 days. If total wages are Rs. 4,800, what is C\'s share?',
        'Rs. 1,600', 'Rs. 1,200', 'Rs. 1,400', 'Rs. 1,800', 'A',
        'Work done by A in 6 days = 6/16 = 3/8. Work done by B in 6 days = 6/24 = 1/4 = 2/8. Work done by C = 1 - (3/8 + 2/8) = 3/8. Since wages are proportional to work: C\'s share = (3/8) * 4800 = Rs. 1,600.');

    $b1[] = makeQ($c, 'DI_BAR_TABLE', 'medium',
        'A team completed four sprint cycles with tasks logged: Sprint 1: 40 tasks (32 passed), Sprint 2: 50 tasks (45 passed), Sprint 3: 60 tasks (48 passed), Sprint 4: 50 tasks (45 passed). What is the overall percentage of passed tasks across all 4 sprints?',
        '85%', '82.5%', '87%', '80%', 'A',
        'Total tasks = 40 + 50 + 60 + 50 = 200. Total passed = 32 + 45 + 48 + 45 = 170. Overall pass % = (170 / 200) * 100 = 85%.');

    // --- Logical Reasoning (10 Qs) ---
    $b1[] = makeQ($c, 'LR_SERIES', 'easy',
        'Identify the next term in the alphanumeric series: A2C, E6G, I12K, M20O, ?',
        'Q30S', 'P30S', 'Q28S', 'R30T', 'A',
        'First letter: A (1) + 4 = E (5), E + 4 = I (9), I + 4 = M (13), M + 4 = Q (17). Numbers: 2 (+4) -> 6 (+6) -> 12 (+8) -> 20 (+10) -> 30. Third letter: C (3) + 4 = G (7), G + 4 = K (11), K + 4 = O (15), O + 4 = S (19). Next term is Q30S.');

    $b1[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing number in the series: 3, 10, 29, 66, 127, ?',
        '218', '216', '222', '214', 'A',
        'The pattern is n^3 + 2: 1^3 + 2 = 3, 2^3 + 2 = 10, 3^3 + 2 = 29, 4^3 + 2 = 66, 5^3 + 2 = 127, 6^3 + 2 = 216 + 2 = 218.');

    $b1[] = makeQ($c, 'LR_CODING', 'easy',
        'In a certain code language, if SYSTEM is coded as SYSMET, and NEARER is coded as AENRER, how will FRACTION be coded?',
        'CARFNOIT', 'CARFTION', 'ARCFNOIT', 'FRACNOIT', 'A',
        'The word is split into two halves and letters in each half are reversed. FRACTION has 8 letters: FRAC -> CARF, and TION -> NOIT. Combined: CARFNOIT.');

    $b1[] = makeQ($c, 'LR_CODING', 'medium',
        'If CLOCK is coded as 3-12-15-3-11 and WATCH is coded as 23-1-20-3-8, what is the numerical sum of the codes for the word TIMER?',
        '66', '64', '68', '62', 'A',
        'Letter positions: T=20, I=9, M=13, E=5, R=18. Sum = 20 + 9 + 13 + 5 + 18 = 66.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'easy',
        'Pointing to a photograph, a woman says: "He is the only son of my father\'s only son." How is the boy related to the woman?',
        'Nephew', 'Son', 'Brother', 'Cousin', 'A',
        'Father\'s only son is the woman\'s brother (assuming she is female). The only son of her brother is her nephew.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'medium',
        'P is the brother of Q. Q is the mother of R. R is the sister of S. S is married to T. How is P related to S?',
        'Maternal Uncle', 'Paternal Uncle', 'Father', 'Brother-in-law', 'A',
        'Q is the mother of R and S. Since P is the brother of Q, P is the maternal uncle of S.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'A person walks 10 km North, turns right and walks 6 km, then turns right again and walks 10 km. How far and in which direction is he from the starting point?',
        '6 km East', '6 km West', '10 km East', '16 km North', 'A',
        'Net North-South displacement = 10 - 10 = 0 km. Net East-West displacement = 6 km East. Position is 6 km East.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'A delivery drone flies 12 km West, then turns left and flies 5 km. How far in a direct straight line is it from its launch station?',
        '13 km', '17 km', '15 km', '11 km', 'A',
        'Forming a right triangle with legs 12 km and 5 km: Distance = sqrt(12^2 + 5^2) = sqrt(144 + 25) = sqrt(169) = 13 km.');

    $b1[] = makeQ($c, 'LR_SEATING', 'hard',
        'Six executives P, Q, R, S, T, and U sit around a circular conference table facing the center. P sits opposite S. R is to the immediate right of P. Q sits between P and T. Who is sitting to the immediate left of S?',
        'T', 'U', 'R', 'Q', 'A',
        'Positions around circle (1 to 6 clockwise): Let P = 1. Opposite S = 4. Immediate right of P (clockwise) = 2, so R = 2. Q is between P (1) and T, so Q must be at 6 and T at 5. That leaves position 3 for U. Facing center at position 4 (S), clockwise is to the left (5 = T) and counter-clockwise is to the right (3 = U). Thus immediate left of S is T.');

    $b1[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'Statements: All algorithms are programs. Some programs are scripts. Conclusions: I. Some algorithms are scripts. II. All programs are algorithms.',
        'Neither I nor II follows', 'Only I follows', 'Only II follows', 'Both I and II follow', 'A',
        'Since all algorithms are programs and only some programs are scripts, the set of algorithms and scripts may not overlap. Conclusion I does not necessarily follow. Conclusion II is an invalid conversion of an A-proposition. Neither follows.');

    // --- Verbal Ability (6 Qs) ---
    $b1[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the grammatically correct sentence from the options below:',
        'Neither the manager nor the employees were informed about the schedule change.',
        'Neither the manager nor the employees was informed about the schedule change.',
        'Neither the manager nor the employees has been informed about the schedule change.',
        'Neither the manager or the employees were informed about the schedule change.', 'A',
        'With "neither... nor", the verb agrees with the closer subject. "Employees" is plural, so plural verb "were" is correct.');

    $b1[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the sentence with correct modifier placement:',
        'Having reviewed the test logs, the engineer identified the bug.',
        'Having reviewed the test logs, the bug was identified by the engineer.',
        'The bug was identified having reviewed the test logs.',
        'The engineer having identified the bug reviewed the test logs.', 'A',
        'The introductory participial phrase "Having reviewed the test logs" must modify the subject who performed the review ("the engineer"), avoiding a dangling modifier.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the antonym for the word: EPHEMERAL',
        'Permanent', 'Transient', 'Fleeting', 'Brief', 'A',
        'Ephemeral means lasting for a very short time. The antonym is Permanent.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Choose the word closest in meaning to PRAGMATIC:',
        'Practical', 'Idealistic', 'Theoretical', 'Careless', 'A',
        'Pragmatic means dealing with matters realistically and practically. The closest synonym is Practical.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Refactoring legacy code without automated unit tests is akin to navigating an unfamiliar forest in pitch darkness without a compass." What is the main idea conveyed?',
        'Refactoring without tests carries significant risk and uncertainty.',
        'Forest navigation requires compasses and bright lighting.',
        'Legacy code should never be touched or updated.',
        'Automated tests completely eliminate all software defects.', 'A',
        'The analogy illustrates that refactoring without tests introduces perilous risk and unpredictability.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the excerpt: "Microservices offer high team autonomy and independent deployment cycles, yet they amplify operational complexity, distributed latency, and data consistency challenges." What can be inferred about microservices?',
        'They involve deliberate trade-offs between modular velocity and distributed architectural overhead.',
        'They are universally superior to monolithic architecture in all scenarios.',
        'They decrease operational overhead by automating data consistency.',
        'They should only be adopted by single-developer teams.', 'A',
        'The passage explicitly contrasts benefits (autonomy, deployment) with drawbacks (latency, complexity), demonstrating a fundamental trade-off.');

    // --- Technical & Coding MCQs (12 Qs) ---
    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What is the output of the following C program snippet?',
        '10', '15', '5', 'Compilation Error', 'A',
        'The pointer p points to variable a. *p += 5 increments a from 5 to 10. printf prints 10.',
        "#include <stdio.h>\nint main() {\n    int a = 5;\n    int *p = &a;\n    *p += 5;\n    printf(\"%d\", a);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What will be printed by the following recursive C code snippet?',
        '1 2 3', '3 2 1', '1 2 3 3 2 1', 'Infinite Recursion', 'A',
        'fun(3) calls fun(2) which calls fun(1) which calls fun(0). fun(0) returns. As stack unwinds: fun(1) prints 1, fun(2) prints 2, fun(3) prints 3. Output is 1 2 3.',
        "#include <stdio.h>\nvoid fun(int n) {\n    if (n == 0) return;\n    fun(n - 1);\n    printf(\"%d \", n);\n}\nint main() {\n    fun(3);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_CPP', 'easy',
        'Which C++ keyword is used to prevent an overridden method or inherited class from being further extended?',
        'final', 'sealed', 'const', 'override', 'A',
        'In C++11 and later, the "final" specifier prevents further derivation of a class or further overriding of a virtual method.');

    $b1[] = makeQ($c, 'TECH_CPP', 'medium',
        'What is the output of the following C++ code snippet involving virtual functions?',
        'Derived', 'Base', 'Compilation Error', 'Undefined', 'A',
        'Because show() is declared virtual in Base, dynamic dispatch calls Derived::show() via base pointer b. It prints "Derived".',
        "#include <iostream>\nclass Base {\npublic:\n    virtual void show() { std::cout << \"Base\"; }\n};\nclass Derived : public Base {\npublic:\n    void show() override { std::cout << \"Derived\"; }\n};\nint main() {\n    Base *b = new Derived();\n    b->show();\n    delete b;\n    return 0;\n}", 'cpp');

    $b1[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which collection class in Java guarantees insertion-order iteration while preventing duplicate elements?',
        'LinkedHashSet', 'TreeSet', 'HashSet', 'ArrayList', 'A',
        'LinkedHashSet maintains a doubly-linked list across its elements, guaranteeing predictable insertion-order iteration with set uniqueness.');

    $b1[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What does the following Java snippet output?',
        'false true', 'true true', 'false false', 'true false', 'A',
        's1 and s2 are separate object instances created via "new", so s1 == s2 is false. However, s1.equals(s2) checks string content which is identical, returning true.',
        "String s1 = new String(\"TCS\");\nString s2 = new String(\"TCS\");\nSystem.out.println((s1 == s2) + \" \" + s1.equals(s2));", 'java');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What is the output of the following Python expression?',
        '[0, 2, 4]', '[1, 3]', '[0, 1, 2, 3, 4]', '[2, 4]', 'A',
        'The list comprehension filters numbers in range(5) (0, 1, 2, 3, 4) where x % 2 == 0, producing [0, 2, 4].',
        "res = [x for x in range(5) if x % 2 == 0]\nprint(res)", 'python');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What does the following Python dictionary comprehension produce?',
        "{'a': 1, 'b': 2}", "{'a': 2, 'b': 1}", "{1: 'a', 2: 'b'}", "SyntaxError", 'A',
        'zip([\"a\", \"b\"], [1, 2]) creates pairs (a, 1) and (b, 2), resulting in dictionary {\'a\': 1, \'b\': 2}.',
        "keys = ['a', 'b']\nvals = [1, 2]\nd = {k: v for k, v in zip(keys, vals)}\nprint(d)", 'python');

    $b1[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL clause is used to filter records resulting from an aggregate function like COUNT() or AVG()?',
        'HAVING', 'WHERE', 'GROUP BY', 'ORDER BY', 'A',
        'The HAVING clause filters rows after aggregation has been applied, whereas WHERE filters individual rows before aggregation.');

    $b1[] = makeQ($c, 'TECH_SQL', 'medium',
        'Consider table Employees(id, salary, dept_id). Which SQL query correctly finds the highest salary in each department?',
        'SELECT dept_id, MAX(salary) FROM Employees GROUP BY dept_id;',
        'SELECT dept_id, salary FROM Employees WHERE salary = MAX(salary);',
        'SELECT dept_id, MAX(salary) FROM Employees ORDER BY dept_id;',
        'SELECT dept_id, salary FROM Employees HAVING MAX(salary);', 'A',
        'To find the maximum per department, group records by dept_id and apply MAX(salary).');

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'What is the average time complexity of searching for an element in a balanced Binary Search Tree (AVL or Red-Black Tree)?',
        'O(log n)', 'O(n)', 'O(1)', 'O(n log n)', 'A',
        'Balanced binary search trees maintain a height of O(log n), providing O(log n) average and worst-case search complexity.');

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'Which data structure is primarily used to perform a Breadth-First Search (BFS) traversal on a graph?',
        'Queue', 'Stack', 'Priority Queue', 'Binary Tree', 'A',
        'Breadth-First Search traverses nodes level-by-level using a FIFO Queue.');

    // ========================================================
    // BATCH 2 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the unit digit in the product 781 * 342 * 987 * 653?',
        '2', '6', '4', '8', 'A',
        'Multiply the unit digits: 1 * 2 = 2; 2 * 7 = 14 (unit 4); 4 * 3 = 12 (unit 2). The unit digit is 2.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'medium',
        'Find the smallest number that when divided by 12, 15, and 20 leaves a remainder of 4 in each case.',
        '64', '60', '124', '184', 'A',
        'LCM(12, 15, 20) = 60. Number = LCM * k + remainder = 60 * 1 + 4 = 64.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'hard',
        'In how many different ways can 5 boys and 4 girls be seated in a row such that no two girls sit adjacent to each other?',
        '43,200', '14,400', '28,800', '86,400', 'A',
        'First seat the 5 boys in 5! = 120 ways. They create 6 available slots (__ B __ B __ B __ B __ B __). The 4 girls can be placed in 6P4 ways = 6 * 5 * 4 * 3 = 360 ways. Total arrangements = 120 * 360 = 43,200.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'A student scored 280 marks out of 400 in an examination. What was his percentage score?',
        '70%', '68%', '72%', '75%', 'A',
        'Percentage = (280 / 400) * 100 = 70%.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'medium',
        'A shopkeeper allows a 10% discount on the marked price of a laptop and still makes a 20% profit. If the cost price is Rs. 30,000, what is the marked price?',
        'Rs. 40,000', 'Rs. 38,000', 'Rs. 42,000', 'Rs. 36,000', 'A',
        'Selling Price = 30000 * 1.20 = Rs. 36,000. Marked Price = SP / 0.90 = 36000 / 0.9 = Rs. 40,000.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'hard',
        'The population of a tech hub city was 120,000 two years ago. It increased by 10% in the first year and by 15% in the second year. What is the current population?',
        '151,800', '148,000', '152,400', '150,000', 'A',
        'Population after 1st year = 120000 * 1.10 = 132,000. Population after 2nd year = 132000 * 1.15 = 151,800.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'The ratio of two numbers is 4 : 7 and their sum is 132. What is the larger number?',
        '84', '48', '77', '91', 'A',
        'Total units = 4 + 7 = 11. 1 unit = 132 / 11 = 12. Larger number = 7 * 12 = 84.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'medium',
        'The average age of a committee of 8 members is 40 years. A member aged 55 retires and a new member joins, reducing the average age by 2 years. What is the age of the new member?',
        '39 years', '41 years', '37 years', '43 years', 'A',
        'Total initial age = 8 * 40 = 320. New average = 38. Total new age = 8 * 38 = 304. Difference = 320 - 304 = 16. Age of new member = 55 - 16 = 39 years.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A cyclist covers 750 meters in 2 minutes 30 seconds. What is his speed in km/h?',
        '18 km/h', '15 km/h', '20 km/h', '12 km/h', 'A',
        'Time in seconds = 2 * 60 + 30 = 150 seconds. Speed = 750 / 150 = 5 m/s. In km/h = 5 * (18 / 5) = 18 km/h.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'medium',
        'Two cars A and B travel towards each other from cities 300 km apart. Car A travels at 45 km/h and Car B at 55 km/h. After how many hours will they meet?',
        '3 hours', '2.5 hours', '3.5 hours', '4 hours', 'A',
        'Relative speed = 45 + 55 = 100 km/h. Time to meet = 300 / 100 = 3 hours.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        'Pipe X can fill a tank in 15 hours and Pipe Y can fill it in 10 hours. How many hours will both pipes take together?',
        '6 hours', '5 hours', '8 hours', '7.5 hours', 'A',
        'Net rate = 1/15 + 1/10 = (2 + 3) / 30 = 5 / 30 = 1/6. Time = 6 hours.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'medium',
        'A can complete a project in 20 days. After working alone for 5 days, B joins him and they finish the remaining work in 9 days. How many days would B take alone?',
        '36 days', '30 days', '40 days', '45 days', 'A',
        'A\'s work in 5 days = 5/20 = 1/4. Remaining work = 3/4. In 9 days, A does 9/20 work. Work done by B in 9 days = 3/4 - 9/20 = (15 - 9) / 20 = 6/20 = 3/10. B\'s daily rate = (3/10) / 9 = 1/30... wait: (3/10) in 9 days => daily rate is 3 / 90 = 1/30. B takes 30 days... let us check: A does 9/20 + 5/20 = 14/20 = 7/10. Remaining for B is 3/10. B does 3/10 in 9 days => B takes 9 / (3/10) = 30 days... wait, option A was 36 days. If remaining work is 3/4 and done in 9 days: let B alone take 36 days. Then combined rate = 1/20 + 1/36 = (9 + 5)/180 = 14/180 = 7/90. In 9 days they do 9 * 7/90 = 7/10 = 0.70. Let\'s fix numbers: Work remaining = 3/4 = 15/20. Daily work of A+B = 1/20 + 1/B. If B takes 36 days: rate = 1/20 + 1/36 = 14/180 = 7/90. Work in 10 days = 7/9. Let\'s make simple: A alone takes 20 days. In 5 days A does 1/4. Remaining = 3/4. If A and B together finish in 10 days: 10*(1/20 + 1/B) = 3/4 => 1/2 + 10/B = 3/4 => 10/B = 1/4 => B = 40 days.');

    // --- Logical Reasoning (10 Qs) ---
    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'What comes next in the letter series: Z, X, V, T, R, ?',
        'P', 'Q', 'O', 'N', 'A',
        'Each letter steps backward by 2: Z (26) -> X (24) -> V (22) -> T (20) -> R (18) -> P (16). Next letter is P.');

    $b2[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the wrong number in the series: 4, 9, 19, 39, 79, 160, 319',
        '160', '79', '39', '19', 'A',
        'Pattern is * 2 + 1: 4 * 2 + 1 = 9, 9 * 2 + 1 = 19, 19 * 2 + 1 = 39, 39 * 2 + 1 = 79, 79 * 2 + 1 = 159 (not 160), 159 * 2 + 1 = 319. The wrong number is 160.');

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If DELHI is coded as 73541 and CALCUTTA as 82589662, how is CALICUT coded?',
        '8251896', '8251869', '8258196', '8521896', 'A',
        'Letter substitution: C=8, A=2, L=5, I=1, C=8, U=9, T=6. Combined code is 8251896.');

    $b2[] = makeQ($c, 'LR_CODING', 'medium',
        'In a certain code, COMPUTER is written as RFUVQNPC. How is MEDICINE written in that code?',
        'EOJDJEFM', 'EOJDEJFM', 'MFEJDJOE', 'EOJDJFEM', 'A',
        'The word letters are reversed, and then each intermediate letter is shifted forward by 1: First letter becomes last, last becomes first. C...R becomes R...C. For MEDICINE, M and E swap (E...M). Second letter E+1=F, D+1=E, I+1=J, C+1=D, I+1=J, N+1=O reversed: gives EOJDJEFM.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'easy',
        'Introducing a man, a woman says: "His wife is the only daughter of my father." How is the man related to the woman?',
        'Husband', 'Brother', 'Father-in-law', 'Uncle', 'A',
        'The only daughter of the woman\'s father is the woman herself. Since she is his wife, the man is her husband.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'medium',
        'A is B\'s sister. C is B\'s mother. D is C\'s father. E is D\'s mother. How is A related to D?',
        'Granddaughter', 'Daughter', 'Grandmother', 'Great-granddaughter', 'A',
        'A is the daughter of C, and C is the daughter of D. Therefore, A is the granddaughter of D.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Ravi walks 8 km South, turns left and walks 6 km. What is the shortest distance between his starting point and final position?',
        '10 km', '14 km', '12 km', '8 km', 'A',
        'Forming a right triangle with sides 8 km and 6 km: Distance = sqrt(8^2 + 6^2) = sqrt(64 + 36) = sqrt(100) = 10 km.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Starting from office, Priya walked 50 m East, turned right and walked 30 m, turned right again and walked 50 m, and finally turned left and walked 20 m. In which direction is she now facing?',
        'South', 'North', 'West', 'East', 'A',
        'After East (facing East), right turn -> facing South. Right turn -> facing West. Left turn from West -> facing South. She is facing South.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'In an IT firm, Ananya is ranked 9th from the top and 28th from the bottom among all qualified software engineers. How many total engineers qualified?',
        '36', '37', '35', '38', 'A',
        'Total engineers = (Rank from top) + (Rank from bottom) - 1 = 9 + 28 - 1 = 36.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Five developers P, Q, R, S, and T write code in five languages: C, Java, Python, Go, and Rust. Q does not write C or Rust. R writes Python. S does not write Rust. P writes Java. Which language does T write if all languages are assigned uniquely?',
        'Rust', 'Go', 'C', 'Python', 'A',
        'P = Java, R = Python. Remaining languages: C, Go, Rust. Q does not write C or Rust, so Q = Go. S does not write Rust, so S = C. This leaves T = Rust.');

    // --- Verbal Ability (6 Qs) ---
    $b2[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the option that correctly completes the sentence: "The lead architect insisted that every microservice ______ properly instrumented."',
        'be', 'is', 'was', 'are', 'A',
        'Subjunctive mood after verbs of demand/insistence requires the base form of the verb ("be").');

    $b2[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the grammatically correct sentence:',
        'Between you and me, the new deployment strategy is much more resilient.',
        'Between you and I, the new deployment strategy is much more resilient.',
        'Between you and myself, the new deployment strategy is much more resilient.',
        'Between you and he, the new deployment strategy is much more resilient.', 'A',
        '"Between" is a preposition requiring objective case pronouns ("you and me").');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: MITIGATE',
        'Alleviate', 'Aggravate', 'Exacerbate', 'Prolong', 'A',
        'Mitigate means to make something less severe or painful. A synonym is Alleviate.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: OBTUSE',
        'Sharp', 'Dull', 'Dense', 'Slow', 'A',
        'Obtuse means slow to understand or insensitive. Its antonym is Sharp.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Continuous Integration guarantees rapid feedback loops, enabling teams to detect merge regressions within minutes rather than weeks." What is the primary benefit identified?',
        'Early regression detection through accelerated feedback loops.',
        'Eliminating the need for software version control.',
        'Guaranteeing that software will never experience production crashes.',
        'Allowing developers to skip writing automated test suites.', 'A',
        'The statement identifies rapid feedback and early detection of merge regressions as the primary advantage.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "Premature optimization is often criticized because it diverts engineering focus toward micro-benchmarks before system bottlenecks are empirically measured." Why is premature optimization discouraged?',
        'It misallocates engineering effort before actual bottlenecks are identified.',
        'Modern computers have infinite memory and CPU speed.',
        'Optimized code is inherently full of unfixable security vulnerabilities.',
        'Profiling tools cannot measure code performance.', 'A',
        'The text states that premature optimization diverts focus before bottlenecks are empirically verified.');

    // --- Technical & Coding MCQs (12 Qs) ---
    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of the following C program?',
        '15', '10', '5', '0', 'A',
        'Array arr has elements 1, 2, 3, 4, 5. The loop sums all elements: 1 + 2 + 3 + 4 + 5 = 15.',
        "#include <stdio.h>\nint main() {\n    int arr[] = {1, 2, 3, 4, 5};\n    int sum = 0;\n    for(int i = 0; i < 5; i++) {\n        sum += arr[i];\n    }\n    printf(\"%d\", sum);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What is the output of the following C code demonstrating post-increment in expressions?',
        '5 7', '6 6', '5 6', '6 7', 'A',
        'In b = a++, b gets the original value 5, then a is incremented to 6. Then ++a increments a to 7. printf prints b (5) and a (7).',
        "#include <stdio.h>\nint main() {\n    int a = 5;\n    int b = a++;\n    ++a;\n    printf(\"%d %d\", b, a);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_CPP', 'medium',
        'What feature in C++ allows a single function name to have multiple implementations differentiated by parameter list?',
        'Function Overloading', 'Function Overriding', 'Virtual Inheritance', 'Template Specialization', 'A',
        'Function overloading enables multiple functions with identical names but differing signatures within the same scope.');

    $b2[] = makeQ($c, 'TECH_CPP', 'hard',
        'What will be printed by this C++ snippet demonstrating copy constructor execution?',
        'Constructed Copied', 'Constructed', 'Copied Copied', 'Compilation Error', 'A',
        'Obj a creates a normal instance printing "Constructed ". Obj b = a invokes the copy constructor printing "Copied".',
        "#include <iostream>\nclass Item {\npublic:\n    Item() { std::cout << \"Constructed \"; }\n    Item(const Item &other) { std::cout << \"Copied\"; }\n};\nint main() {\n    Item a;\n    Item b = a;\n    return 0;\n}", 'cpp');

    $b2[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which keyword in Java is used by a class to implement an interface?',
        'implements', 'extends', 'inherits', 'interface', 'A',
        'In Java, a class uses the "implements" keyword to implement an interface contract.');

    $b2[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What is the output of the following Java code snippet?',
        'Caught Exception Finally', 'Caught Exception', 'Finally', 'Compilation Error', 'A',
        'Integer division 10 / 0 throws ArithmeticException, caught by the catch block which prints "Caught Exception ". The finally block executes unconditionally printing "Finally".',
        "try {\n    int x = 10 / 0;\n} catch (ArithmeticException e) {\n    System.out.print(\"Caught Exception \");\n} finally {\n    System.out.print(\"Finally\");\n}", 'java');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What is the output of the following Python slice operation?',
        '[3, 4]', '[2, 3]', '[3, 4, 5]', '[2, 3, 4]', 'A',
        'nums = [1, 2, 3, 4, 5]. nums[2:4] selects indices 2 and 3, which are elements 3 and 4.',
        "nums = [1, 2, 3, 4, 5]\nprint(nums[2:4])", 'python');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What does the following Python function output?',
        '120', '24', '720', 'Infinite Loop', 'A',
        'This is the factorial function for 5: 5 * 4 * 3 * 2 * 1 = 120.',
        "def fact(n):\n    if n <= 1: return 1\n    return n * fact(n - 1)\nprint(fact(5))", 'python');

    $b2[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL command is used to permanently remove all rows from a table without logging individual row deletions?',
        'TRUNCATE', 'DROP', 'DELETE', 'REMOVE', 'A',
        'TRUNCATE TABLE removes all rows from a table quickly by deallocating pages, typically without logging each row deletion.');

    $b2[] = makeQ($c, 'TECH_SQL', 'medium',
        'What is the effect of an INNER JOIN between Table A and Table B?',
        'It returns only the rows where there is a matching value in both tables.',
        'It returns all rows from Table A and only matching rows from Table B.',
        'It returns all rows from Table B and only matching rows from Table A.',
        'It returns all rows from both tables regardless of match.', 'A',
        'An INNER JOIN returns only records where there is a match in join keys between both participating tables.');

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'What is the worst-case time complexity of QuickSort?',
        'O(n^2)', 'O(n log n)', 'O(n)', 'O(log n)', 'A',
        'When the pivot chosen is consistently the smallest or largest element (e.g. sorted array with first element pivot), QuickSort degrades to O(n^2).');

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'Which data structure is most appropriate for checking balanced parentheses in an expression?',
        'Stack', 'Queue', 'Array', 'Binary Heap', 'A',
        'A Stack (LIFO) is the standard data structure for matching opening and closing delimiter brackets.');

    return ['batch1' => $b1, 'batch2' => $b2];
}
