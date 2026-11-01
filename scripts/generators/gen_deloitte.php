<?php
require_once __DIR__ . '/helper.php';

function getDeloitteQuestions(): array {
    $b1 = [];
    $b2 = [];
    $c = 'Deloitte';

    // ========================================================
    // BATCH 1 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the compound interest on Rs. 5,000 for 2 years at 10% per annum compounded annually?',
        'Rs. 1,050', 'Rs. 1,000', 'Rs. 1,100', 'Rs. 1,150', 'A',
        'Amount = 5000 * (1 + 0.10)^2 = 5000 * 1.21 = Rs. 6,050. CI = 6050 - 5000 = Rs. 1,050.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'medium',
        'A sum of money invested at simple interest amounts to Rs. 800 in 3 years and Rs. 900 in 4 years. What is the principal sum?',
        'Rs. 500', 'Rs. 600', 'Rs. 550', 'Rs. 450', 'A',
        'Interest for 1 year = 900 - 800 = Rs. 100. Interest for 3 years = 300. Principal = 800 - 300 = Rs. 500.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'hard',
        'The average weight of 24 students in a section is 50 kg. If the teacher\'s weight is included, the average increases by 1 kg. What is the teacher\'s weight?',
        '75 kg', '70 kg', '80 kg', '65 kg', 'A',
        'Total weight of students = 24 * 50 = 1200 kg. Total weight of 25 people = 25 * 51 = 1275 kg. Teacher\'s weight = 1275 - 1200 = 75 kg.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'easy',
        'A corporate analyst calculates 18% tax on an invoice of Rs. 5,000. What is the tax amount?',
        'Rs. 900', 'Rs. 850', 'Rs. 950', 'Rs. 1,000', 'A',
        'Tax = 0.18 * 5000 = Rs. 900.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'If an item with cost price Rs. 1,500 is sold for Rs. 1,800, what is the profit percentage?',
        '20%', '15%', '25%', '18%', 'A',
        'Profit = 1800 - 1500 = 300. Profit % = (300 / 1500) * 100 = 20%.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'hard',
        'A trader gives two successive discounts of 20% and 10% on an item marked at Rs. 2,000. What is the final selling price?',
        'Rs. 1,440', 'Rs. 1,400', 'Rs. 1,500', 'Rs. 1,480', 'A',
        'After 20% discount: 2000 * 0.80 = 1600. After 10% discount: 1600 * 0.90 = Rs. 1,440.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A delivery vehicle travels 180 km in 3 hours. What is its speed in km/h?',
        '60 km/h', '55 km/h', '65 km/h', '50 km/h', 'A',
        'Speed = 180 / 3 = 60 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A train 150 m long crosses an electric post in 10 seconds. What is its speed in km/h?',
        '54 km/h', '60 km/h', '48 km/h', '72 km/h', 'A',
        'Speed in m/s = 150 / 10 = 15 m/s. Speed in km/h = 15 * (18 / 5) = 54 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'hard',
        'Two cars start at the same time from points 200 km apart and travel towards each other at 45 km/h and 55 km/h. How many hours will they take to meet?',
        '2 hours', '2.5 hours', '1.8 hours', '3 hours', 'A',
        'Relative speed = 45 + 55 = 100 km/h. Time = 200 / 100 = 2 hours.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'easy',
        'Developer A can complete a feature in 8 days and Developer B in 12 days. How many days will they take working together?',
        '4.8 days', '5.0 days', '4.5 days', '5.2 days', 'A',
        'Rate = 1/8 + 1/12 = 5/24. Time = 24 / 5 = 4.8 days.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'medium',
        'A cistern is filled by Pipe A in 15 mins and Pipe B in 20 mins. Both open for 4 mins, then Pipe A is closed. How many more minutes will Pipe B take to fill the tank?',
        '10.67 mins', '12.00 mins', '9.50 mins', '11.00 mins', 'A',
        'Rate of A+B = 1/15 + 1/20 = 7/60. In 4 mins they fill 4 * (7/60) = 28/60 = 7/15. Remaining = 8/15. Time for B = (8/15) / (1/20) = (8/15) * 20 = 160 / 15 ≈ 10.67 mins.');

    $b1[] = makeQ($c, 'DI_BAR_TABLE', 'medium',
        'Quarterly revenue table: Q1: $40M, Q2: $50M, Q3: $60M, Q4: $50M. What is the average quarterly revenue?',
        '$50M', '$45M', '$55M', '$48M', 'A',
        'Total = 40 + 50 + 60 + 50 = $200M. Average = 200 / 4 = $50M.');

    // --- Logical Reasoning (10 Qs) ---
    $b1[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next term: 8, 16, 24, 32, ?',
        '40', '36', '48', '44', 'A',
        'Multiples of 8: 32 + 8 = 40.');

    $b1[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 5, 10, 20, 40, ?',
        '80', '60', '70', '90', 'A',
        'Each term doubles: 40 * 2 = 80.');

    $b1[] = makeQ($c, 'LR_CODING', 'easy',
        'If AUDIT is coded as 1-21-4-9-20, how is RISK coded?',
        '18-9-19-11', '18-8-19-11', '19-9-19-11', '18-9-20-11', 'A',
        'R=18, I=9, S=19, K=11. Code is 18-9-19-11.');

    $b1[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, RISK is written as SJTL. How is GAIN written in that code?',
        'HBJO', 'HBJN', 'IBJO', 'HAJO', 'A',
        'Each letter shifted forward by 1: G(+1)=H, A(+1)=B, I(+1)=J, N(+1)=O. Code is HBJO.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'easy',
        'Introducing a woman, a man says: "She is the daughter of my father\'s only daughter." How is the woman related to the man?',
        'Niece', 'Daughter', 'Sister', 'Aunt', 'A',
        'Father\'s only daughter is the man\'s sister. Her daughter is his niece.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'medium',
        'A is the brother of B. C is the father of A. D is the mother of B. How is C related to D?',
        'Husband', 'Brother', 'Son', 'Father', 'A',
        'C is the father of A and B, and D is the mother of A and B. Hence, C is the husband of D.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 12 m South, then turning left and walking 5 m. What is the straight-line distance from the start point?',
        '13 m', '17 m', '15 m', '14 m', 'A',
        'Distance = sqrt(12^2 + 5^2) = sqrt(144 + 25) = sqrt(169) = 13 m.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Facing East, turning 90 degrees counter-clockwise orients one towards:',
        'North', 'South', 'West', 'North-East', 'A',
        'East + 90 deg counter-clockwise faces North.');

    $b1[] = makeQ($c, 'LR_SEATING', 'medium',
        'Four consultants P, Q, R, and S sit around a circular table facing center. P sits opposite R. Q sits to the right of P. Who sits opposite Q?',
        'S', 'R', 'P', 'None', 'A',
        'Opposite pairs are (P, R) and (Q, S). Q is opposite S.');

    $b1[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All audits are rigorous. Some rigorous processes are automated. Conclusions: I. Some audits are automated. II. All automated processes are audits.',
        'Neither I nor II follows', 'Only I follows', 'Only II follows', 'Both follow', 'A',
        'The overlap between audits and automated processes is not guaranteed. Neither conclusion follows.');

    // --- Verbal Ability (6 Qs) ---
    $b1[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "The financial report ______ published yesterday."',
        'was', 'were', 'are', 'is', 'A',
        'Singular subject "The financial report" with past time "yesterday" takes "was".');

    $b1[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the correct sentence:',
        'Neither the auditor nor the analysts were aware of the ledger discrepancy.',
        'Neither the auditor nor the analysts was aware of the ledger discrepancy.',
        'Neither the auditor or the analysts were aware of the ledger discrepancy.',
        'Neither the auditor nor the analysts have been aware on yesterday.', 'A',
        'With "neither... nor", the verb agrees with the closer plural subject "analysts", so "were" is correct.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: PRUDENT',
        'Wise', 'Reckless', 'Foolish', 'Rash', 'A',
        'Prudent means acting with or showing care and thought for the future; wise.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: COHERENT',
        'Disjointed', 'Logical', 'Clear', 'Consistent', 'A',
        'Coherent means logical and consistent. Its antonym is Disjointed.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "An immutable audit trail records every data alteration with timestamp and actor identity, ensuring regulatory compliance and non-repudiation." What does an audit trail guarantee?',
        'Regulatory compliance and non-repudiation of transactions.',
        'The deletion of user records.',
        'The elimination of all security audits.',
        'Unlimited free cloud computing.', 'A',
        'The passage notes an immutable audit log guarantees compliance and non-repudiation.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "Multi-tenant database architectures pool compute resources across multiple corporate clients while enforcing row-level security isolation to lower operational costs." How does multi-tenancy optimize costs?',
        'By sharing infrastructure resources while enforcing tenant security boundaries.',
        'By disabling encryption across corporate networks.',
        'By migrating all clients to public spreadsheets.',
        'By shutting down servers during business hours.', 'A',
        'Resource sharing across isolated tenant boundaries reduces operating expenditures.');

    // --- Technical MCQs (12 Qs) ---
    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '50', '25', '10', '0', 'A',
        'Multiplication: 5 * 10 = 50.',
        "#include <stdio.h>\nint main() {\n    int a = 5, b = 10;\n    printf(\"%d\", a * b);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print using a loop?',
        '10 20', '10 20 30', '10', 'Error', 'A',
        'Loop iterates for i = 1, 2 and prints i * 10: 10 20.',
        "#include <stdio.h>\nint main() {\n    for(int i = 1; i <= 2; i++) {\n        printf(\"%d \", i * 10);\n    }\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_CPP', 'medium',
        'What is the principle of hiding implementation details and exposing only the essential features of an object in C++?',
        'Abstraction', 'Inheritance', 'Polymorphism', 'Overloading', 'A',
        'Abstraction focuses on showing only essential features and hiding complex background details.');

    $b1[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        'Deloitte', 'D', 'None', 'Error', 'A',
        'Prints literal "Deloitte".',
        "#include <iostream>\nint main() {\n    std::cout << \"Deloitte\";\n    return 0;\n}", 'cpp');

    $b1[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which collection in Java stores key-value pairs and does not allow duplicate keys?',
        'HashMap', 'ArrayList', 'HashSet', 'LinkedList', 'A',
        'HashMap stores key-value mappings where each key is unique.');

    $b1[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        'Deloitte', 'deloitte', 'DELOITTE', 'Error', 'A',
        'Prints literal "Deloitte".',
        "String s = \"Deloitte\";\nSystem.out.println(s);", 'java');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '4', '3', '5', 'Error', 'A',
        'len("Data") evaluates to 4.',
        "print(len(\"Data\"))", 'python');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python operation?',
        '[1, 2]', '[2, 3]', '[1, 2, 3]', 'Error', 'A',
        'arr[:2] returns the first two elements [1, 2].',
        "arr = [1, 2, 3]\nprint(arr[:2])", 'python');

    $b1[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL command changes existing records in a table?',
        'UPDATE', 'ALTER', 'MODIFY', 'CHANGE', 'A',
        'The UPDATE command modifies existing data values in database tables.');

    $b1[] = makeQ($c, 'TECH_SQL', 'medium',
        'What does ACID stand for in the context of relational database transactions?',
        'Atomicity, Consistency, Isolation, Durability',
        'Accuracy, Control, Isolation, Data',
        'Access, Consistency, Indexing, Durability',
        'Atomicity, Concurrency, Integrity, Delivery', 'A',
        'ACID stands for Atomicity, Consistency, Isolation, and Durability.');

    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'In a Binary Search Tree, where are elements smaller than the root node stored?',
        'In the left subtree', 'In the right subtree', 'At leaf nodes only', 'Randomly', 'A',
        'By the BST property, all keys in the left subtree are smaller than the parent root node.');

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the time complexity of pushing an item onto a stack?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n^2)', 'A',
        'Stack push is a constant time O(1) operation.');

    // ========================================================
    // BATCH 2 (40 Questions)
    // ========================================================
    // --- Quant (12 Qs) ---
    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the value of 14^2?',
        '196', '186', '206', '176', 'A',
        '14 * 14 = 196.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'medium',
        'Find the median of the numbers: 3, 5, 7, 9, 11.',
        '7', '5', '9', '6', 'A',
        'For 5 sorted numbers, the middle (3rd) number is the median: 7.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the perimeter of a square whose area is 144 cm^2.',
        '48 cm', '44 cm', '52 cm', '36 cm', 'A',
        'Side = sqrt(144) = 12 cm. Perimeter = 4 * 12 = 48 cm.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 50% of 280?',
        '140', '130', '150', '120', 'A',
        '0.50 * 280 = 140.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'medium',
        'Selling an item for Rs. 450 results in a 10% loss. What was the cost price?',
        'Rs. 500', 'Rs. 480', 'Rs. 520', 'Rs. 490', 'A',
        'CP = 450 / 0.90 = Rs. 500.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'hard',
        'If an amount doubles at simple interest in 5 years, what is the annual rate of interest?',
        '20%', '15%', '25%', '10%', 'A',
        'SI = P => P = P * r * 5 / 100 => 5r = 100 => r = 20%.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'easy',
        'If ratio of two numbers is 3 : 4 and sum is 70, find the larger number.',
        '40', '30', '45', '35', 'A',
        '7 units = 70 => 1 unit = 10. Larger = 4 * 10 = 40.');

    $b2[] = makeQ($c, 'QUANT_RATIO', 'medium',
        'The average of four numbers is 15. If three numbers are 12, 18, and 14, what is the fourth number?',
        '16', '15', '17', '18', 'A',
        'Total = 4 * 15 = 60. Fourth = 60 - (12 + 18 + 14) = 60 - 44 = 16.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A vehicle covers 90 km in 1.5 hours. What is its speed?',
        '60 km/h', '50 km/h', '70 km/h', '55 km/h', 'A',
        'Speed = 90 / 1.5 = 60 km/h.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A person walks 20 km at 5 km/h. How many hours did he walk?',
        '4 hours', '3 hours', '5 hours', '4.5 hours', 'A',
        'Time = 20 / 5 = 4 hours.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'easy',
        '3 workers complete an analysis in 6 days. How many days will 6 workers take at the same pace?',
        '3 days', '2 days', '4 days', '5 days', 'A',
        'Workers doubled => time halved: 6 / 2 = 3 days.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A fills a tank in 10 hours and Pipe B in 15 hours. How long will both take together?',
        '6 hours', '5 hours', '7 hours', '8 hours', 'A',
        'Rate = 1/10 + 1/15 = 5/30 = 1/6. Time = 6 hours.');

    // --- Logical Reasoning (10 Qs) ---
    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next number: 15, 30, 45, 60, ?',
        '75', '70', '80', '65', 'A',
        'Multiples of 15: 60 + 15 = 75.');

    $b2[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 10, 20, 40, 80, ?',
        '160', '140', '150', '180', 'A',
        'Doubling: 80 * 2 = 160.');

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If TAX is coded as 20-1-24, how is NET coded?',
        '14-5-20', '14-6-20', '13-5-20', '14-5-21', 'A',
        'N=14, E=5, T=20. Code is 14-5-20.');

    $b2[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, SAFE is written as TBGF. How is RISK written in that code?',
        'SJTL', 'SJTK', 'RJTL', 'SKTL', 'A',
        'Shift +1: R(+1)=S, I(+1)=J, S(+1)=T, K(+1)=L. Code is SJTL.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'easy',
        'P is the sister of Q. Q is the mother of R. How is P related to R?',
        'Maternal Aunt', 'Mother', 'Sister', 'Grandmother', 'A',
        'P is the sister of R\'s mother Q, making P the maternal aunt of R.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'medium',
        'A is the father of B. B is the sister of C. How is A related to C?',
        'Father', 'Uncle', 'Brother', 'Grandfather', 'A',
        'Since B is the sister of C, B and C share the same father A.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 15 m North, then turning right and walking 20 m. What is the shortest distance from the start point?',
        '25 m', '30 m', '35 m', '20 m', 'A',
        'Distance = sqrt(15^2 + 20^2) = sqrt(225 + 400) = sqrt(625) = 25 m.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'medium',
        'Facing North, turning 90 degrees clockwise faces:',
        'East', 'West', 'South', 'North-East', 'A',
        'North + 90 deg clockwise faces East.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'medium',
        'In a row of 30 auditors, Ananya is 12th from the left. What is her position from the right?',
        '19th', '18th', '20th', '17th', 'A',
        'Position from right = 30 - 12 + 1 = 19th.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All records are verified. All verified items are authentic. Conclusions: I. All records are authentic. II. Some authentic items are records.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'All records are verified and all verified items are authentic, so all records are authentic. Also, some authentic items are records. Both follow.');

    // --- Verbal Ability (6 Qs) ---
    $b2[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "The audit team ______ completed the compliance review."',
        'has', 'have', 'are', 'is', 'A',
        '"The audit team" treated as a single entity takes singular "has".');

    $b2[] = makeQ($c, 'VERBAL_SC', 'medium',
        'Identify the grammatically correct sentence:',
        'The discrepancy in the financial statements was promptly investigated.',
        'The discrepancy in the financial statements were promptly investigated.',
        'The discrepancy in the financial statements are promptly investigated.',
        'The discrepancy in the financial statements have been promptly investigated.', 'A',
        'The singular subject "discrepancy" agrees with the singular verb "was".');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: OBJECTIVE',
        'Impartial', 'Biased', 'Prejudiced', 'Subjective', 'A',
        'Objective means not influenced by personal feelings; impartial.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: ABUNDANT',
        'Scarce', 'Plentiful', 'Ample', 'Copious', 'A',
        'Abundant means existing in large quantities. Its antonym is Scarce.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'easy',
        'Read the statement: "Data lineage tracking provides visibility into the source, transformations, and final destination of sensitive records across the enterprise." What is the primary purpose of data lineage?',
        'Tracking data origins and transformation paths across enterprise systems.',
        'Deleting duplicate historical data.',
        'Slowing down query responses.',
        'Restricting users to a single workstation.', 'A',
        'Data lineage tracks the end-to-end lifecycle and transformations of data.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the passage: "Micro-segmentation divides datacenters into secure, isolated zones to limit an adversary\'s lateral movement in the event of a perimeter breach." How does micro-segmentation enhance enterprise security?',
        'By restricting lateral movement between internal network zones.',
        'By disabling user passwords across all workstations.',
        'By eliminating all firewall devices.',
        'By making all internal servers publicly accessible.', 'A',
        'Micro-segmentation establishes perimeter isolation inside the datacenter to halt lateral movement.');

    // --- Technical MCQs (12 Qs) ---
    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '8', '6', '10', '0', 'A',
        'Addition: 4 + 4 = 8.',
        "#include <stdio.h>\nint main() {\n    int a = 4, b = 4;\n    printf(\"%d\", a + b);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print?',
        '25', '10', '5', '0', 'A',
        'Multiplication: 5 * 5 = 25.',
        "#include <stdio.h>\nint main() {\n    int a = 5;\n    printf(\"%d\", a * a);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_CPP', 'medium',
        'Which concept in C++ allows bundling data and functions together into a single unit?',
        'Encapsulation', 'Polymorphism', 'Inheritance', 'Abstraction', 'A',
        'Encapsulation wraps data and the methods operating on that data into classes.');

    $b2[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        '100', '50', '0', 'Error', 'A',
        'Prints integer 100.',
        "#include <iostream>\nint main() {\n    int x = 100;\n    std::cout << x;\n    return 0;\n}", 'cpp');

    $b2[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which keyword is used in Java to create a subclass from an existing superclass?',
        'extends', 'implements', 'inherits', 'super', 'A',
        'The extends keyword denotes inheritance in Java.');

    $b2[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        '10', '5', '15', 'Error', 'A',
        'Assignment: x = 10.',
        "int x = 10;\nSystem.out.println(x);", 'java');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '9', '6', '3', 'Error', 'A',
        'Exponentiation: 3 ** 2 = 9.',
        "print(3 ** 2)", 'python');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python operation?',
        '[10, 20]', '[20, 30]', '[10, 20, 30]', 'Error', 'A',
        'Slice items[:2] returns [10, 20].',
        "items = [10, 20, 30]\nprint(items[:2])", 'python');

    $b2[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL command removes a table schema completely from the database?',
        'DROP TABLE', 'DELETE TABLE', 'CLEAR TABLE', 'TRUNCATE TABLE', 'A',
        'DROP TABLE deletes the table definition and all rows permanently.');

    $b2[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL keyword combines the result-set of two or more SELECT statements while removing duplicate rows?',
        'UNION', 'UNION ALL', 'JOIN', 'MERGE', 'A',
        'UNION combines query results and filters out duplicate rows.');

    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'Which data structure is a FIFO (First In First Out) structure?',
        'Queue', 'Stack', 'Tree', 'Graph', 'A',
        'A Queue functions on First In First Out.');

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the average time complexity of finding a key in a balanced AVL tree?',
        'O(log n)', 'O(n)', 'O(1)', 'O(n log n)', 'A',
        'Balanced AVL trees maintain logarithmic height, ensuring O(log n) lookup.');

    return ['batch1' => $b1, 'batch2' => $b2];
}
