<?php
require_once __DIR__ . '/helper.php';

function getHclQuestions(): array {
    $b1 = [];
    $b2 = [];
    $c = 'HCL';

    // ========================================================
    // BATCH 1 (40 Questions: 10 Quant, 8 LR, 4 Verbal, 18 Tech)
    // ========================================================
    // --- Quant (10 Qs) ---
    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the greatest common factor of 24 and 36?',
        '12', '6', '18', '24', 'A',
        'Factors of 24 and 36 share 12 as their greatest common divisor.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'medium',
        'Find the smallest number that when divided by 6, 8, and 12 leaves a remainder of 2 in each case.',
        '26', '24', '28', '30', 'A',
        'LCM(6, 8, 12) = 24. Required number = 24 + 2 = 26.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the sum of all natural numbers from 1 to 40.',
        '820', '800', '840', '780', 'A',
        'Sum = 40 * 41 / 2 = 20 * 41 = 820.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 15% of 600?',
        '90', '80', '100', '85', 'A',
        '0.15 * 600 = 90.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'An item bought for Rs. 500 is sold for Rs. 600. What is the profit percentage?',
        '20%', '25%', '15%', '18%', 'A',
        'Profit = 100. Profit % = (100 / 500) * 100 = 20%.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A vehicle covers 180 km in 3 hours. What is its speed?',
        '60 km/h', '50 km/h', '70 km/h', '55 km/h', 'A',
        'Speed = 180 / 3 = 60 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A train 150 m long crosses an electric post in 10 seconds. What is its speed in km/h?',
        '54 km/h', '60 km/h', '48 km/h', '72 km/h', 'A',
        'Speed = 150 / 10 = 15 m/s = 15 * (18 / 5) = 54 km/h.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'easy',
        'A and B finish a job in 6 days. A alone takes 10 days. How long will B take alone?',
        '15 days', '12 days', '18 days', '20 days', 'A',
        'Rate = 1/6 - 1/10 = 2/30 = 1/15. B takes 15 days.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A fills a tank in 4 hours and Pipe B in 6 hours. How long together?',
        '2.4 hours', '2.0 hours', '3.0 hours', '2.5 hours', 'A',
        'Rate = 1/4 + 1/6 = 5/12. Time = 12 / 5 = 2.4 hours.');

    $b1[] = makeQ($c, 'DI_BAR_TABLE', 'medium',
        'Server uptime table: Server 1: 99%, Server 2: 98%, Server 3: 100%. What is the average uptime percentage across the three servers?',
        '99%', '98.5%', '99.5%', '98%', 'A',
        'Average = (99 + 98 + 100) / 3 = 297 / 3 = 99%.');

    // --- Logical Reasoning (8 Qs) ---
    $b1[] = makeQ($c, 'LR_SERIES', 'easy',
        'What is the next number: 8, 16, 24, 32, ?',
        '40', '36', '48', '44', 'A',
        'Multiples of 8: 32 + 8 = 40.');

    $b1[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 3, 9, 27, 81, ?',
        '243', '216', '162', '256', 'A',
        'Powers of 3: 81 * 3 = 243.');

    $b1[] = makeQ($c, 'LR_CODING', 'easy',
        'If NET is coded as 14-5-20, how is HUB coded?',
        '8-21-2', '8-20-2', '7-21-2', '8-21-3', 'A',
        'H=8, U=21, B=2. Code is 8-21-2.');

    $b1[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, CODE is written as DPEF. How is DATA written in that code?',
        'EBUB', 'EBVA', 'EBUA', 'EAVB', 'A',
        'Shift +1: D(+1)=E, A(+1)=B, T(+1)=U, A(+1)=B. Code is EBUB.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'easy',
        'A is the brother of B. B is the mother of C. How is A related to C?',
        'Maternal Uncle', 'Father', 'Brother', 'Grandfather', 'A',
        'A is the brother of C\'s mother B, so A is the maternal uncle of C.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 12 m North, then turning right and walking 5 m. What is the shortest distance from the start point?',
        '13 m', '17 m', '15 m', '14 m', 'A',
        'Distance = sqrt(12^2 + 5^2) = sqrt(144 + 25) = sqrt(169) = 13 m.');

    $b1[] = makeQ($c, 'LR_SEATING', 'medium',
        'Four engineers sit around a circular table facing center: A, B, C, D. A is opposite C. B is to the right of A. Who is opposite B?',
        'D', 'C', 'A', 'None', 'A',
        'Opposite pairs are (A, C) and (B, D). B is opposite D.');

    $b1[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All packets are routed. All routed items are secure. Conclusions: I. All packets are secure. II. Some secure items are packets.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'All packets are routed, and all routed items are secure => all packets are secure. Also some secure items are packets. Both follow.');

    // --- Verbal (4 Qs) ---
    $b1[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "The network switch ______ configured yesterday."',
        'was', 'were', 'are', 'is were', 'A',
        'Singular subject "The network switch" takes "was".');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: COGNIZANT',
        'Aware', 'Ignorant', 'Unconscious', 'Blind', 'A',
        'Cognizant means having knowledge or being aware.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: OBTUSE',
        'Sharp', 'Dull', 'Dense', 'Slow', 'A',
        'Obtuse means dull or slow. Its opposite is Sharp.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the statement: "TCP provides reliable, ordered byte-stream delivery with congestion control, while UDP prioritizes low latency over guaranteed delivery." What differentiates TCP from UDP?',
        'TCP guarantees reliable ordered delivery while UDP prioritizes speed and low latency.',
        'TCP cannot run on the internet.',
        'UDP requires physical optical cables exclusively.',
        'TCP deletes packets during congestion.', 'A',
        'TCP provides reliable sequenced transport while UDP offers lower latency without ordering guarantees.');

    // --- Technical MCQs (18 Qs - Leaning Systems, OS, C, Networks) ---
    $b1[] = makeQ($c, 'TECH_C', 'easy',
        'What is the size of a pointer on a 64-bit architecture in C?',
        '8 bytes', '4 bytes', '2 bytes', '16 bytes', 'A',
        'On a 64-bit system, memory addresses are 64 bits wide, occupying 8 bytes.');

    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program demonstrating pointer values?',
        '25', '10', '0', 'Error', 'A',
        'Pointer p points to num. *p assigns 25 to num. printf prints 25.',
        "#include <stdio.h>\nint main() {\n    int num = 10;\n    int *p = &num;\n    *p = 25;\n    printf(\"%d\", num);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code output?',
        '10 20', '20 10', '10', 'Error', 'A',
        'a[0] is 10 and a[1] is 20. printf prints 10 20.',
        "#include <stdio.h>\nint main() {\n    int a[2] = {10, 20};\n    printf(\"%d %d\", a[0], a[1]);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What will be printed by this C code?',
        '8', '4', '16', '0', 'A',
        'Left shift: 1 << 3 shifts 1 left by 3 bits, giving 2^3 = 8.',
        "#include <stdio.h>\nint main() {\n    int x = 1 << 3;\n    printf(\"%d\", x);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_CPP', 'medium',
        'Which header in C++ is used for standard input and output streams?',
        '<iostream>', '<stdio.h>', '<conio.h>', '<stdlib.h>', 'A',
        'The <iostream> header defines standard stream objects like std::cin and std::cout.');

    $b1[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        'HCLTech', 'HCL', 'Tech', 'Error', 'A',
        'Prints literal "HCLTech".',
        "#include <iostream>\nint main() {\n    std::cout << \"HCLTech\";\n    return 0;\n}", 'cpp');

    $b1[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which layer of the OSI model does the HTTP protocol operate on?',
        'Application Layer (Layer 7)', 'Transport Layer (Layer 4)', 'Network Layer (Layer 3)', 'Data Link Layer (Layer 2)', 'A',
        'HTTP is an application-layer protocol operating at Layer 7 of the OSI model.');

    $b1[] = makeQ($c, 'TECH_JAVA', 'medium',
        'In operating systems, what condition occurs when multiple processes are blocked each holding a resource the other needs?',
        'Deadlock', 'Starvation', 'Race Condition', 'Thrashing', 'A',
        'A Deadlock occurs when processes are unable to proceed because each holds resources needed by others in a circular chain.');

    $b1[] = makeQ($c, 'TECH_JAVA', 'hard',
        'What will the following Java code print?',
        '100', '50', '200', 'Error', 'A',
        'Multiplication: 10 * 10 = 100.',
        "int x = 10 * 10;\nSystem.out.println(x);", 'java');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '4', '2', '8', 'Error', 'A',
        'Integer division: 16 // 4 = 4.',
        "print(16 // 4)", 'python');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of this Python slice operation?',
        '[2, 3]', '[1, 2]', '[3, 4]', 'Error', 'A',
        'Slice items[1:3] returns indices 1 and 2: [2, 3].',
        "items = [1, 2, 3, 4]\nprint(items[1:3])", 'python');

    $b1[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL keyword is used to retrieve data from a database?',
        'SELECT', 'FETCH', 'RETRIEVE', 'GET', 'A',
        'SELECT retrieves records from tables.');

    $b1[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which index type in relational databases is organized as a balanced multi-way search tree?',
        'B-Tree Index', 'Bitmap Index', 'Hash Index', 'Spatial Index', 'A',
        'B-Tree indexes maintain sorted balanced tree structures optimal for range queries and equality lookups.');

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure is used by the operating system for function call stack frames?',
        'Stack', 'Queue', 'Array', 'Heap', 'A',
        'The call stack is implemented as a Stack (LIFO).');

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the time complexity of enqueuing an item in a linked-list implementation of a Queue with a tail pointer?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n^2)', 'A',
        'With a tail pointer, appending to the end takes O(1) constant time.');

    $b1[] = makeQ($c, 'TECH_DSA', 'hard',
        'In virtual memory management, what is the term for excessive paging activity where the system spends more time swapping pages than executing code?',
        'Thrashing', 'Segmentation', 'Fragmentation', 'Paging fault', 'A',
        'Thrashing occurs when the working set of active processes exceeds physical RAM capacity.');

    $b1[] = makeQ($c, 'TECH_DSA', 'hard',
        'Which CPU scheduling algorithm gives each process a small unit of CPU time (time quantum) in cyclic order?',
        'Round Robin (RR)', 'First-Come, First-Served', 'Shortest Job First', 'Priority Scheduling', 'A',
        'Round Robin scheduling assigns a fixed time quantum per process in a circular queue.');

    $b1[] = makeQ($c, 'TECH_DSA', 'hard',
        'What is the default subnet mask for a Class C IPv4 network address?',
        '255.255.255.0', '255.0.0.0', '255.255.0.0', '255.255.255.255', 'A',
        'Class C networks have 24 network bits and 8 host bits, yielding mask 255.255.255.0 (/24).');

    // ========================================================
    // BATCH 2 (40 Questions: 10 Quant, 8 LR, 4 Verbal, 18 Tech)
    // ========================================================
    // --- Quant (10 Qs) ---
    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the value of 15^2?',
        '225', '215', '235', '205', 'A',
        '15 * 15 = 225.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'medium',
        'Find the perimeter of a rectangle with length 18 m and breadth 12 m.',
        '60 m', '50 m', '55 m', '65 m', 'A',
        'Perimeter = 2 * (18 + 12) = 2 * 30 = 60 m.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the area of a right-angled triangle with base 8 cm and height 15 cm.',
        '60 cm^2', '120 cm^2', '50 cm^2', '75 cm^2', 'A',
        'Area = (1/2) * 8 * 15 = 4 * 15 = 60 cm^2.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 25% of 800?',
        '200', '180', '220', '190', 'A',
        '0.25 * 800 = 200.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'medium',
        'An item bought for Rs. 800 is sold for Rs. 1,000. Find the profit percentage.',
        '25%', '20%', '30%', '15%', 'A',
        'Profit = 200. Profit % = (200 / 800) * 100 = 25%.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A vehicle covers 240 km in 4 hours. What is its speed?',
        '60 km/h', '50 km/h', '70 km/h', '55 km/h', 'A',
        'Speed = 240 / 4 = 60 km/h.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A person walks 16 km at 4 km/h. How many hours did he walk?',
        '4 hours', '3 hours', '5 hours', '4.5 hours', 'A',
        'Time = 16 / 4 = 4 hours.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        'A can do a work in 5 days and B in 20 days. How long will they take together?',
        '4 days', '3 days', '5 days', '6 days', 'A',
        'Rate = 1/5 + 1/20 = 5/20 = 1/4. Time = 4 days.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'medium',
        '5 engineers build a network module in 12 days. How many engineers are needed to complete it in 6 days?',
        '10 engineers', '8 engineers', '12 engineers', '9 engineers', 'A',
        'Total = 5 * 12 = 60 engineer-days. In 6 days: 60 / 6 = 10 engineers.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'Divide Rs. 500 in the ratio 2 : 3. What is the smaller share?',
        'Rs. 200', 'Rs. 300', 'Rs. 150', 'Rs. 250', 'A',
        'Total parts = 5. 1 part = 100. Smaller = 2 * 100 = Rs. 200.');

    // --- Logical Reasoning (8 Qs) ---
    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next number: 12, 24, 36, 48, ?',
        '60', '54', '64', '58', 'A',
        'Multiples of 12: 48 + 12 = 60.');

    $b2[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 2, 4, 8, 16, ?',
        '32', '24', '30', '36', 'A',
        'Doubling: 16 * 2 = 32.');

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If LOG is coded as 12-15-7, how is BOX coded?',
        '2-15-24', '2-14-24', '3-15-24', '2-15-23', 'A',
        'B=2, O=15, X=24. Code is 2-15-24.');

    $b2[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, FILE is written as GJMF. How is DISK written in that code?',
        'EJTL', 'EITL', 'EJTK', 'FJTL', 'A',
        'Shift +1: D(+1)=E, I(+1)=J, S(+1)=T, K(+1)=L. Code is EJTL.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'easy',
        'X is the brother of Y. Y is the mother of Z. How is X related to Z?',
        'Maternal Uncle', 'Father', 'Brother', 'Grandfather', 'A',
        'X is the brother of Z\'s mother Y, so X is the maternal uncle of Z.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 8 m South, then turning left and walking 6 m. What is the shortest distance from the start point?',
        '10 m', '14 m', '12 m', '9 m', 'A',
        'Distance = sqrt(8^2 + 6^2) = sqrt(64 + 36) = sqrt(100) = 10 m.');

    $b2[] = makeQ($c, 'LR_SEATING', 'medium',
        'Four engineers A, B, C, D sit around a circular table facing center. A is opposite C. B is to the right of A. Who is opposite B?',
        'D', 'C', 'A', 'None', 'A',
        'Opposite pairs are (A, C) and (B, D). B is opposite D.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All threads share memory. All shared memory items require synchronization. Conclusions: I. All threads require synchronization. II. Some synchronization items are threads.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'Threads share memory, and shared memory items require synchronization, so all threads require synchronization. Both follow.');

    // --- Verbal (4 Qs) ---
    $b2[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "The firewall configuration ______ verified by the security team."',
        'was', 'were', 'are', 'is were', 'A',
        'Singular subject "The firewall configuration" takes "was".');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: SECURE',
        'Protected', 'Exposed', 'Dangerous', 'Weak', 'A',
        'Secure means safe and protected.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: COARSE',
        'Fine', 'Rough', 'Harsh', 'Crude', 'A',
        'Coarse means rough. Its antonym is Fine.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the statement: "Zero-Trust Architecture enforces strict continuous identity verification and least-privilege access for all users, regardless of whether they connect from within or outside the corporate network." What is the fundamental principle of Zero Trust?',
        'Continuous verification and least-privilege access regardless of network location.',
        'Granting full admin rights to all internal employees.',
        'Disabling firewalls inside datacenters.',
        'Eliminating passwords entirely.', 'A',
        'Zero Trust assumes breach and continuously authenticates identity independent of network perimeter.');

    // --- Technical MCQs (18 Qs - Leaning Systems, OS, C, Networks) ---
    $b2[] = makeQ($c, 'TECH_C', 'easy',
        'What will be the output of this C program?',
        '50', '25', '10', '0', 'A',
        'Multiplication: 10 * 5 = 50.',
        "#include <stdio.h>\nint main() {\n    int a = 10, b = 5;\n    printf(\"%d\", a * b);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What does this C code output?',
        '15', '10', '5', '0', 'A',
        'Addition: 10 + 5 = 15.',
        "#include <stdio.h>\nint main() {\n    int a = 10, b = 5;\n    printf(\"%d\", a + b);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What is printed by this C program with bitwise AND?',
        '1', '0', '3', '2', 'A',
        'Bitwise AND: 5 (binary 101) & 1 (binary 001) = 1.',
        "#include <stdio.h>\nint main() {\n    int x = 5 & 1;\n    printf(\"%d\", x);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What is the size of an int pointer on a 32-bit architecture in C?',
        '4 bytes', '8 bytes', '2 bytes', '1 byte', 'A',
        'On a 32-bit system, memory addresses are 32 bits wide, occupying 4 bytes.');

    $b2[] = makeQ($c, 'TECH_CPP', 'medium',
        'In C++, which keyword is used to inherit a class publicly?',
        'public', 'protected', 'private', 'extends', 'A',
        'In C++, public inheritance is declared as: class Derived : public Base.');

    $b2[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        '16', '8', '4', '0', 'A',
        'Multiplication: 4 * 4 = 16.',
        "#include <iostream>\nint main() {\n    int x = 4;\n    std::cout << x * x;\n    return 0;\n}", 'cpp');

    $b2[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which protocol translates human-readable domain names into IP addresses?',
        'DNS', 'DHCP', 'HTTP', 'FTP', 'A',
        'Domain Name System (DNS) maps human-readable domain names to IP addresses.');

    $b2[] = makeQ($c, 'TECH_JAVA', 'medium',
        'In operating systems, which state transition occurs when a running process requests I/O operation?',
        'Running to Waiting (Blocked)', 'Running to Ready', 'Waiting to Ready', 'Ready to Running', 'A',
        'When a process issues a blocking I/O request, the OS transitions it from Running to Waiting.');

    $b2[] = makeQ($c, 'TECH_JAVA', 'hard',
        'What will the following Java code print?',
        '25', '20', '30', 'Error', 'A',
        'Addition: 20 + 5 = 25.',
        "int x = 20, y = 5;\nSystem.out.println(x + y);", 'java');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '10', '5', '15', 'Error', 'A',
        'Multiplication: 2 * 5 = 10.',
        "print(2 * 5)", 'python');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python operation?',
        '[1, 2]', '[2, 3]', '[1, 2, 3]', 'Error', 'A',
        'Slice items[:2] returns [1, 2].',
        "items = [1, 2, 3]\nprint(items[:2])", 'python');

    $b2[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL clause is used to filter query results?',
        'WHERE', 'ORDER BY', 'GROUP BY', 'HAVING', 'A',
        'The WHERE clause filters rows.');

    $b2[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL keyword counts the total number of matching rows?',
        'COUNT()', 'SUM()', 'TOTAL()', 'NUMBER()', 'A',
        'COUNT() returns row tally.');

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure is based on the FIFO principle?',
        'Queue', 'Stack', 'Tree', 'Graph', 'A',
        'Queue operates on First In First Out.');

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the time complexity of searching an item in an unsorted array of size n?',
        'O(n)', 'O(1)', 'O(log n)', 'O(n^2)', 'A',
        'Unsorted array linear search takes O(n) worst case.');

    $b2[] = makeQ($c, 'TECH_DSA', 'hard',
        'In networking, what is the well-known port number used for HTTPS encrypted web traffic?',
        '443', '80', '22', '25', 'A',
        'HTTPS communicates over standard TCP port 443.');

    $b2[] = makeQ($c, 'TECH_DSA', 'hard',
        'In an operating system, what is the component responsible for switching the CPU from one process context to another?',
        'Dispatcher / Context Switcher', 'File System', 'Compiler', 'Linker', 'A',
        'The dispatcher saves state of the old process and loads state of the newly scheduled process.');

    $b2[] = makeQ($c, 'TECH_DSA', 'hard',
        'Which IP protocol is connectionless and does not guarantee packet delivery?',
        'UDP', 'TCP', 'SCTP', 'FTP', 'A',
        'User Datagram Protocol (UDP) is a connectionless transport protocol.');

    return ['batch1' => $b1, 'batch2' => $b2];
}
