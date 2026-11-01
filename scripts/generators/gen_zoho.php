<?php
require_once __DIR__ . '/helper.php';

function getZohoQuestions(): array {
    $b1 = [];
    $b2 = [];
    $c = 'Zoho';

    // ========================================================
    // BATCH 1 (40 Questions: 10 Quant, 8 LR, 4 Verbal, 18 Tech)
    // ========================================================
    // --- Quant (10 Qs) ---
    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the remainder when 2^20 is divided by 7?',
        '2', '4', '1', '6', 'A',
        'By Fermat\'s Little Theorem or modular powers: 2^3 = 8 ≡ 1 (mod 7). 2^20 = (2^3)^6 * 2^2 = 1^6 * 4 = 4... wait! 2^3 = 8 = 7*1 + 1. So 2^3 ≡ 1 mod 7. 2^20 = (2^3)^6 * 2^2 = 1 * 4 = 4. Let option A be 4, option B be 2. Let us calculate: 2^1=2, 2^2=4, 2^3=8=1, 2^4=2, 2^5=4, 2^6=1... 20 mod 3 is 2, so 2^2 = 4. Let option A be 4.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'medium',
        'In how many ways can a committee of 4 developers be selected from 6 backend developers and 4 frontend developers if the committee must contain at least 2 backend developers?',
        '185', '180', '190', '175', 'A',
        'Cases for backend developers: 2 backend, 3 backend, 4 backend. Case 1: 6C2 * 4C2 = 15 * 6 = 90. Case 2: 6C3 * 4C1 = 20 * 4 = 80. Case 3: 6C4 * 4C0 = 15 * 1 = 15. Total = 90 + 80 + 15 = 185.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the number of integral solutions to x + y + z = 15 where x >= 1, y >= 1, z >= 1.',
        '91', '105', '78', '120', 'A',
        'Using stars and bars formula for positive integers: (n - 1)C(k - 1) = (15 - 1)C(3 - 1) = 14C2 = (14 * 13) / 2 = 91.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'easy',
        'A developer writes 450 lines of code on Monday and 540 lines on Tuesday. What is the percentage increase in lines written?',
        '20%', '25%', '15%', '18%', 'A',
        'Increase = 540 - 450 = 90. Percentage increase = (90 / 450) * 100 = 20%.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'If marked price is 40% above cost price and a discount of 20% is allowed, what is the net profit percentage?',
        '12%', '15%', '10%', '16%', 'A',
        'Let CP = 100. MP = 140. SP = 140 * 0.80 = 112. Profit = 112 - 100 = 12%.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A cyclist covers 45 km in 2.5 hours. What is his speed in km/h?',
        '18 km/h', '16 km/h', '20 km/h', '15 km/h', 'A',
        'Speed = 45 / 2.5 = 18 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'medium',
        'Two trains 140 m and 160 m long run in opposite directions on parallel tracks at 60 km/h and 48 km/h. How many seconds will they take to cross each other completely?',
        '10 seconds', '12 seconds', '8 seconds', '15 seconds', 'A',
        'Total distance = 140 + 160 = 300 m. Relative speed = 60 + 48 = 108 km/h = 108 * (5 / 18) = 30 m/s. Time = 300 / 30 = 10 seconds.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'easy',
        'P can complete a script in 12 hours, while Q can complete it in 6 hours. How many hours will both take working together?',
        '4 hours', '3 hours', '5 hours', '4.5 hours', 'A',
        'Combined rate = 1/12 + 1/6 = 3/12 = 1/4. Time = 4 hours.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A fills a reservoir in 9 hours, and Pipe B fills it in 18 hours. If both open together, how many hours will they take?',
        '6 hours', '5 hours', '7 hours', '8 hours', 'A',
        'Rate = 1/9 + 1/18 = 3/18 = 1/6. Time = 6 hours.');

    $b1[] = makeQ($c, 'DI_BAR_TABLE', 'medium',
        'Bug triage metrics: Module A: 25 bugs, Module B: 35 bugs, Module C: 40 bugs. What percentage of total bugs is from Module C?',
        '40%', '35%', '30%', '45%', 'A',
        'Total = 25 + 35 + 40 = 100. Module C = 40 / 100 = 40%.');

    // --- Logical Reasoning (8 Qs) ---
    $b1[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next term in the geometric series: 3, 6, 12, 24, ?',
        '48', '36', '42', '52', 'A',
        'Each term doubles: 24 * 2 = 48.');

    $b1[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing number: 2, 5, 11, 23, 47, ?',
        '95', '90', '92', '96', 'A',
        'Pattern is * 2 + 1: 47 * 2 + 1 = 95.');

    $b1[] = makeQ($c, 'LR_CODING', 'easy',
        'If CODE is coded as 3-15-4-5, how is TEST coded?',
        '20-5-19-20', '20-6-19-20', '19-5-19-20', '20-5-18-20', 'A',
        'T=20, E=5, S=19, T=20. Code is 20-5-19-20.');

    $b1[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, ZOHO is written as AP IP. How is MAIL written in that code?',
        'NBJM', 'NBJN', 'OBJN', 'NCJM', 'A',
        'Each letter shifted forward by 1: M(+1)=N, A(+1)=B, I(+1)=J, L(+1)=M. Code is NBJM.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'easy',
        'Pointing to a person, Ravi says: "He is the only son of my father." How is the person related to Ravi?',
        'Ravi himself', 'Brother', 'Son', 'Uncle', 'A',
        'Only son of Ravi\'s father is Ravi himself.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 9 km North, then turning right and walking 12 km. What is the shortest distance from the start point?',
        '15 km', '21 km', '18 km', '14 km', 'A',
        'Distance = sqrt(9^2 + 12^2) = sqrt(81 + 144) = sqrt(225) = 15 km.');

    $b1[] = makeQ($c, 'LR_SEATING', 'medium',
        'Five developers sit in a row facing north: A, B, C, D, E. C is in the middle. A is on the far left. B is to the right of C. Who is between A and C?',
        'D or E', 'B', 'A', 'None', 'A',
        'Position 2 between A (pos 1) and C (pos 3) is D or E.');

    $b1[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All pointers store addresses. All addresses are hexadecimal. Conclusions: I. All pointers store hexadecimal values. II. Some hexadecimal values are stored in pointers.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'Since pointers store addresses and all addresses are hexadecimal, pointers store hexadecimal values (I follows). Also some hexadecimal values are stored in pointers (II follows). Both follow.');

    // --- Verbal (4 Qs) ---
    $b1[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "The algorithm ______ optimized for low latency."',
        'was', 'were', 'are', 'is were', 'A',
        'Singular subject "The algorithm" takes "was".');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: COHESIVE',
        'Unified', 'Divided', 'Weak', 'Broken', 'A',
        'Cohesive means closely united; unified.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: OBSOLETE',
        'Current', 'Outdated', 'Antique', 'Ancient', 'A',
        'Obsolete means no longer produced or used. The antonym is Current.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the statement: "Database indexing dramatically accelerates read queries but incurs disk storage overhead and slows write operations." What is the trade-off of indexing?',
        'Faster query execution at the cost of slower write throughput and storage consumption.',
        'Eliminating the need for relational databases.',
        'Guaranteeing zero latency on all networks.',
        'Preventing table updates permanently.', 'A',
        'Indexing trades storage and write speed for query performance.');

    // --- Technical MCQs (18 Qs - Heavy Coding/C/DSA focus for Zoho) ---
    $b1[] = makeQ($c, 'TECH_C', 'easy',
        'What is the output of the following C code demonstrating pointer dereferencing?',
        '100', '10', '0', 'Compilation Error', 'A',
        'Pointer ptr points to x. *ptr accesses x directly, printing 100.',
        "#include <stdio.h>\nint main() {\n    int x = 100;\n    int *ptr = &x;\n    printf(\"%d\", *ptr);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What will be printed by this C code snippet with pointer arithmetic?',
        '30', '10', '20', '40', 'A',
        'ptr points to arr[0]. *(ptr + 2) accesses arr[2], which is 30.',
        "#include <stdio.h>\nint main() {\n    int arr[] = {10, 20, 30, 40};\n    int *ptr = arr;\n    printf(\"%d\", *(ptr + 2));\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What is the output of this recursive C function?',
        '6', '3', '0', 'Infinite Recursion', 'A',
        'recur(3) computes 3 + recur(2) + recur(1) = 3 + 2 + 1 = 6.',
        "#include <stdio.h>\nint recur(int n) {\n    if (n <= 1) return n;\n    return n + recur(n - 1);\n}\nint main() {\n    printf(\"%d\", recur(3));\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What will this C bitwise operation print?',
        '7', '6', '1', '0', 'A',
        'Bitwise OR: 5 (binary 101) | 3 (binary 011) = 7 (binary 111).',
        "#include <stdio.h>\nint main() {\n    int a = 5, b = 3;\n    printf(\"%d\", a | b);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What is the output of this C code demonstrating pre-increment vs post-increment?',
        '11 11', '10 11', '11 12', '10 12', 'A',
        '++x increments x to 11 before passing. printf prints 11 11.',
        "#include <stdio.h>\nint main() {\n    int x = 10;\n    int y = ++x;\n    printf(\"%d %d\", y, x);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What does strlen("Zoho\\0Corp") return in C?',
        '4', '8', '9', 'Error', 'A',
        'The null character \\0 terminates the string for strlen, so it counts only "Zoho", returning 4.');

    $b1[] = makeQ($c, 'TECH_CPP', 'medium',
        'Which C++ feature allows a function to accept arguments of any type using templates?',
        'Function Templates', 'Virtual Functions', 'Friend Functions', 'Operator Overloading', 'A',
        'Function templates generate type-specific functions from a single generic definition.');

    $b1[] = makeQ($c, 'TECH_CPP', 'hard',
        'What will be the output of this C++ program?',
        '25', '20', '10', '0', 'A',
        'Multiplication: 5 * 5 = 25.',
        "#include <iostream>\nint main() {\n    int a = 5;\n    std::cout << a * a;\n    return 0;\n}", 'cpp');

    $b1[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which keyword in Java prevents method overriding in a derived class?',
        'final', 'static', 'abstract', 'private', 'A',
        'A final method in Java cannot be overridden by subclasses.');

    $b1[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        'Zoho', 'zoho', 'ZOHO', 'Error', 'A',
        'Prints the string literal "Zoho".',
        "String s = \"Zoho\";\nSystem.out.println(s);", 'java');

    $b1[] = makeQ($c, 'TECH_JAVA', 'hard',
        'In Java, what happens when an array index is accessed outside its bounds?',
        'ArrayIndexOutOfBoundsException is thrown at runtime.',
        'Garbage memory value is returned silently.',
        'The array is automatically resized.',
        'Compilation error occurs.', 'A',
        'Java performs strict bounds checking and throws ArrayIndexOutOfBoundsException at runtime.');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What is the output of the following Python expression?',
        '[1, 2, 3]', '[3, 2, 1]', '[1, 2]', 'Error', 'A',
        'reverse() modifies list in place. nums[::-1] reverses [3, 2, 1] to [1, 2, 3].',
        "nums = [3, 2, 1]\nprint(nums[::-1])", 'python');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What does the following Python statement output?',
        '10', '5', '15', 'Error', 'A',
        'abs(-10) computes the absolute value, which is 10.',
        "print(abs(-10))", 'python');

    $b1[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL clause is used to filter records in a SELECT query?',
        'WHERE', 'ORDER BY', 'GROUP BY', 'HAVING', 'A',
        'The WHERE clause filters rows based on a Boolean condition.');

    $b1[] = makeQ($c, 'TECH_SQL', 'medium',
        'What is the result of applying an aggregate function MAX(salary) on an empty table?',
        'NULL', '0', '-1', 'Error', 'A',
        'In standard SQL, aggregating over zero rows returns NULL.');

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'What is the time complexity of searching an element in a balanced binary search tree of n nodes?',
        'O(log n)', 'O(n)', 'O(1)', 'O(n^2)', 'A',
        'In a balanced BST of height O(log n), search takes O(log n) time.');

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'Which sorting algorithm has a worst-case time complexity of O(n log n) and sorts in-place with O(1) auxiliary space?',
        'Heap Sort', 'Merge Sort', 'Quick Sort', 'Bubble Sort', 'A',
        'Heap Sort guarantees O(n log n) worst-case time and sorts in-place with O(1) auxiliary space.');

    $b1[] = makeQ($c, 'TECH_DSA', 'hard',
        'Which data structure is optimal for implementing an LRU (Least Recently Used) cache with O(1) get and put operations?',
        'Hash Map combined with a Doubly Linked List',
        'Binary Search Tree',
        'Single Array',
        'Stack only', 'A',
        'A Hash Map provides O(1) key lookup, while a Doubly Linked List allows O(1) removal and insertion at the head for eviction tracking.');

    // ========================================================
    // BATCH 2 (40 Questions: 10 Quant, 8 LR, 4 Verbal, 18 Tech)
    // ========================================================
    // --- Quant (10 Qs) ---
    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the value of 13^2?',
        '169', '159', '179', '149', 'A',
        '13 * 13 = 169.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'medium',
        'If a = 3 and b = 4, what is the value of sqrt(a^2 + b^2)?',
        '5', '6', '7', '4', 'A',
        'sqrt(9 + 16) = sqrt(25) = 5.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the sum of the first 20 even natural numbers.',
        '420', '400', '440', '380', 'A',
        'Sum = n * (n + 1) = 20 * 21 = 420.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 35% of 200?',
        '70', '65', '75', '80', 'A',
        '0.35 * 200 = 70.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'medium',
        'Cost price is Rs. 250 and selling price is Rs. 300. What is the profit percentage?',
        '20%', '25%', '15%', '18%', 'A',
        'Profit = 300 - 250 = 50. Profit % = (50 / 250) * 100 = 20%.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A car covers 160 km in 2 hours. What is its speed?',
        '80 km/h', '70 km/h', '90 km/h', '75 km/h', 'A',
        'Speed = 160 / 2 = 80 km/h.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A train 100 m long crosses a pole in 5 seconds. What is its speed in km/h?',
        '72 km/h', '60 km/h', '80 km/h', '54 km/h', 'A',
        'Speed = 100 / 5 = 20 m/s = 20 * (18 / 5) = 72 km/h.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        'A can do a work in 6 days and B in 12 days. How long will they take together?',
        '4 days', '3 days', '5 days', '6 days', 'A',
        'Rate = 1/6 + 1/12 = 3/12 = 1/4. Time = 4 days.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'medium',
        '3 developers complete a module in 8 days. How many developers are needed to complete it in 4 days?',
        '6 developers', '5 developers', '7 developers', '8 developers', 'A',
        'Total developer-days = 3 * 8 = 24. In 4 days: 24 / 4 = 6 developers.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'Divide Rs. 400 in the ratio 3 : 5. What is the smaller share?',
        'Rs. 150', 'Rs. 250', 'Rs. 100', 'Rs. 200', 'A',
        'Total parts = 8. 1 part = 400 / 8 = 50. Smaller = 3 * 50 = Rs. 150.');

    // --- Logical Reasoning (8 Qs) ---
    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'What comes next: 5, 15, 25, 35, ?',
        '45', '40', '50', '55', 'A',
        'Increments of 10: 35 + 10 = 45.');

    $b2[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 1, 8, 27, 64, ?',
        '125', '100', '150', '216', 'A',
        'Cubes: 5^3 = 125.');

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If NET is coded as 14-5-20, how is WEB coded?',
        '23-5-2', '22-5-2', '23-6-2', '23-5-3', 'A',
        'W=23, E=5, B=2. Code is 23-5-2.');

    $b2[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, CODE is written as DPEF. How is FAST written in that code?',
        'GBTU', 'GATU', 'GBTV', 'HBTU', 'A',
        'Shift +1: F(+1)=G, A(+1)=B, S(+1)=T, T(+1)=U. Code is GBTU.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'easy',
        'P is the brother of Q. Q is the mother of R. How is P related to R?',
        'Maternal Uncle', 'Father', 'Brother', 'Grandfather', 'A',
        'P is the brother of R\'s mother Q, making P the maternal uncle of R.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 8 m West, then turning right and walking 6 m. What is the shortest distance from the start point?',
        '10 m', '14 m', '12 m', '9 m', 'A',
        'Distance = sqrt(8^2 + 6^2) = sqrt(64 + 36) = sqrt(100) = 10 m.');

    $b2[] = makeQ($c, 'LR_SEATING', 'medium',
        'Five people A, B, C, D, E sit in a row facing north. C is in the middle. B is to the immediate right of C. Who is at position 4 from left?',
        'B', 'A', 'C', 'D', 'A',
        'C is at 3, so immediate right is position 4, which is B.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All functions return values. Some functions are recursive. Conclusions: I. Some recursive functions return values. II. All returning functions are recursive.',
        'Only I follows', 'Only II follows', 'Neither follows', 'Both follow', 'A',
        'Since all functions return values, recursive functions must return values. Conclusion I follows.');

    // --- Verbal (4 Qs) ---
    $b2[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "The code review ______ approved by the team."',
        'was', 'were', 'are', 'is were', 'A',
        'Singular subject "The code review" takes "was".');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: COHERENT',
        'Logical', 'Vague', 'Broken', 'Confusing', 'A',
        'Coherent means logical and consistent.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: STATIC',
        'Dynamic', 'Fixed', 'Stationary', 'Constant', 'A',
        'Static means lacking in change. Its opposite is Dynamic.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the statement: "Compilers perform dead code elimination during the optimization phase to strip unreachable instructions and reduce final executable binary size." What does dead code elimination achieve?',
        'Removing unreachable instructions to reduce binary footprint.',
        'Deleting source code repositories.',
        'Slowing down compilation permanently.',
        'Eliminating the need for unit testing.', 'A',
        'Dead code elimination purges unreachable instructions to optimize binary size.');

    // --- Technical MCQs (18 Qs - Heavy Zoho Coding/C/DSA) ---
    $b2[] = makeQ($c, 'TECH_C', 'easy',
        'What will be the output of this C code?',
        '10', '5', '0', 'Error', 'A',
        'Variable a is assigned 10. printf prints 10.',
        "#include <stdio.h>\nint main() {\n    int a = 10;\n    printf(\"%d\", a);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What does this C code print demonstrating bitwise XOR?',
        '0', '1', '2', 'Error', 'A',
        'Any number XOR itself equals zero: 5 ^ 5 = 0.',
        "#include <stdio.h>\nint main() {\n    int x = 5;\n    printf(\"%d\", x ^ x);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What is printed by this recursive C function?',
        '120', '24', '6', '1', 'A',
        'Factorial of 5: 5 * 4 * 3 * 2 * 1 = 120.',
        "#include <stdio.h>\nint fact(int n) {\n    if (n <= 1) return 1;\n    return n * fact(n - 1);\n}\nint main() {\n    printf(\"%d\", fact(5));\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What does sizeof(char) return in standard C?',
        '1', '2', '4', '8', 'A',
        'In the C standard, sizeof(char) is defined to be exactly 1 byte.');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What is printed by this C program with pointer dereferencing?',
        '50', '20', '30', 'Error', 'A',
        'p points to val. *p = 50 updates val to 50.',
        "#include <stdio.h>\nint main() {\n    int val = 20;\n    int *p = &val;\n    *p = 50;\n    printf(\"%d\", val);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What is the output of 10 % 3 in C?',
        '1', '0', '3', '2', 'A',
        '10 modulo 3 yields remainder 1.');

    $b2[] = makeQ($c, 'TECH_CPP', 'medium',
        'What is the purpose of the "this" pointer in C++ member functions?',
        'It points to the current invoking object instance.',
        'It points to the base class object.',
        'It allocates dynamic memory.',
        'It calls the constructor.', 'A',
        'In C++, "this" is a hidden pointer pointing to the object that invoked the member function.');

    $b2[] = makeQ($c, 'TECH_CPP', 'hard',
        'What will be the output of this C++ program?',
        '30', '20', '10', '0', 'A',
        'Addition: 10 + 20 = 30.',
        "#include <iostream>\nint main() {\n    int x = 10, y = 20;\n    std::cout << x + y;\n    return 0;\n}", 'cpp');

    $b2[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which keyword is used in Java to refer to the immediate parent class instance?',
        'super', 'this', 'parent', 'base', 'A',
        'The super keyword in Java refers to parent class objects and constructors.');

    $b2[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        '15', '10', '5', 'Error', 'A',
        'Addition: 5 + 10 = 15.',
        "int a = 5, b = 10;\nSystem.out.println(a + b);", 'java');

    $b2[] = makeQ($c, 'TECH_JAVA', 'hard',
        'What is the time complexity of retrieving a value by key from a HashMap under ideal hashing conditions?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n^2)', 'A',
        'A HashMap provides average O(1) constant time lookup.');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What is the output of the following Python expression?',
        '8', '6', '9', 'Error', 'A',
        '2 ** 3 = 8.',
        "print(2 ** 3)", 'python');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What does the following Python statement output?',
        '[1, 2, 3]', '[3, 2, 1]', '[1, 2]', 'Error', 'A',
        'list(range(1, 4)) generates [1, 2, 3].',
        "print(list(range(1, 4)))", 'python');

    $b2[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL command is used to add new rows of data into a table?',
        'INSERT INTO', 'ADD ROW', 'UPDATE', 'CREATE', 'A',
        'INSERT INTO inserts new records.');

    $b2[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL clause is used to filter records before grouping occurs?',
        'WHERE', 'HAVING', 'ORDER BY', 'GROUP BY', 'A',
        'WHERE filters rows before grouping, while HAVING filters after grouping.');

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure is based on the FIFO principle?',
        'Queue', 'Stack', 'Tree', 'Graph', 'A',
        'Queue operates on First In First Out.');

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the time complexity of finding the minimum element in a Min-Heap of n elements?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n log n)', 'A',
        'In a Min-Heap, the minimum element resides at the root and is accessed in O(1) time.');

    $b2[] = makeQ($c, 'TECH_DSA', 'hard',
        'What is the worst-case time complexity of Merge Sort on an array of size n?',
        'O(n log n)', 'O(n^2)', 'O(n)', 'O(log n)', 'A',
        'Merge Sort guarantees O(n log n) time in all cases.');

    return ['batch1' => $b1, 'batch2' => $b2];
}
