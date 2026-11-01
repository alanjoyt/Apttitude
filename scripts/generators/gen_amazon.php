<?php
require_once __DIR__ . '/helper.php';

function getAmazonQuestions(): array {
    $b1 = [];
    $b2 = [];
    $c = 'Amazon';

    // ========================================================
    // BATCH 1 (40 Questions: 8 Quant, 8 LR, 4 Verbal, 20 Tech)
    // ========================================================
    // --- Quant (8 Qs) ---
    $b1[] = makeQ($c, 'QUANT_ADV', 'easy',
        'Find the remainder when 3^21 is divided by 10.',
        '3', '9', '7', '1', 'A',
        'Powers of 3 modulo 10 cycle with period 4: 3^1=3, 3^2=9, 3^3=7, 3^4=1. 21 mod 4 = 1. Therefore 3^21 ≡ 3^1 ≡ 3 (mod 10).');

    $b1[] = makeQ($c, 'QUANT_ADV', 'medium',
        'In how many ways can 6 packages be loaded into 3 delivery vans if each van must carry at least one package?',
        '540', '720', '360', '180', 'A',
        'Number of onto functions from a set of 6 elements to 3 bins: 3^6 - 3C1*2^6 + 3C2*1^6 = 729 - 3*64 + 3*1 = 729 - 192 + 3 = 540.');

    $b1[] = makeQ($c, 'QUANT_ADV', 'hard',
        'What is the probability of drawing 2 aces in succession from a well-shuffled standard deck of 52 cards without replacement?',
        '1/221', '1/169', '1/26', '4/663', 'A',
        'Probability of first ace = 4/52 = 1/13. Probability of second ace = 3/51 = 1/17. Combined = (1/13) * (1/17) = 1/221.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'easy',
        'A fulfillment center ships 1,200 orders on day 1 and 1,500 orders on day 2. What is the percentage increase in order volume?',
        '25%', '20%', '30%', '15%', 'A',
        'Increase = 1500 - 1200 = 300. Percentage = (300 / 1200) * 100 = 25%.');

    $b1[] = makeQ($c, 'QUANT_PERC', 'medium',
        'A seller marks an item 50% above cost price and offers a 20% discount. What is the net profit percentage?',
        '20%', '25%', '15%', '30%', 'A',
        'Let CP = 100. MP = 150. SP = 150 * 0.80 = 120. Profit % = 20%.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A delivery drone covers 60 km in 45 minutes. What is its speed in km/h?',
        '80 km/h', '75 km/h', '85 km/h', '90 km/h', 'A',
        'Time = 45 / 60 = 0.75 hours. Speed = 60 / 0.75 = 80 km/h.');

    $b1[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A delivery truck travels 120 km at 40 km/h and another 120 km at 60 km/h. What is its average speed for the full 240 km trip?',
        '48 km/h', '50 km/h', '45 km/h', '52 km/h', 'A',
        'Total time = 120/40 + 120/60 = 3 + 2 = 5 hours. Average speed = 240 / 5 = 48 km/h.');

    $b1[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Robot A packs 100 boxes in 4 hours and Robot B packs 100 boxes in 6 hours. Working together, in how many hours will they pack 100 boxes?',
        '2.4 hours', '2.0 hours', '3.0 hours', '2.5 hours', 'A',
        'Combined rate = 1/4 + 1/6 = 5/12. Time = 12 / 5 = 2.4 hours.');

    // --- Logical Reasoning (8 Qs) ---
    $b1[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next number in the series: 2, 4, 8, 16, 32, ?',
        '64', '48', '56', '72', 'A',
        'Each term doubles: 32 * 2 = 64.');

    $b1[] = makeQ($c, 'LR_SERIES', 'medium',
        'Identify the missing number: 1, 4, 10, 22, 46, ?',
        '94', '92', '96', '90', 'A',
        'Pattern is * 2 + 2: 1*2+2=4, 4*2+2=10, 10*2+2=22, 22*2+2=46, 46*2+2=94.');

    $b1[] = makeQ($c, 'LR_CODING', 'easy',
        'If CLOUD is coded as 3-12-15-21-4, how is AWS coded?',
        '1-23-19', '1-22-19', '2-23-19', '1-23-20', 'A',
        'A=1, W=23, S=19. Code is 1-23-19.');

    $b1[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, PRIME is written as QSJNF. How is FAST written in that code?',
        'GBTU', 'GATU', 'GBTV', 'HBTU', 'A',
        'Each letter shifted forward by 1: F(+1)=G, A(+1)=B, S(+1)=T, T(+1)=U. Code is GBTU.');

    $b1[] = makeQ($c, 'LR_BLOOD', 'easy',
        'A is the sister of B. B is the father of C. How is A related to C?',
        'Paternal Aunt', 'Mother', 'Sister', 'Grandmother', 'A',
        'A is the sister of C\'s father B, so A is the paternal aunt of C.');

    $b1[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 12 m North, then turning right and walking 9 m. What is the shortest straight-line distance from the start point?',
        '15 m', '21 m', '18 m', '13 m', 'A',
        'Distance = sqrt(12^2 + 9^2) = sqrt(144 + 81) = sqrt(225) = 15 m.');

    $b1[] = makeQ($c, 'LR_SEATING', 'medium',
        'Six team members sit around a circular table facing the center: A, B, C, D, E, F. A is opposite D. B is to the immediate right of A. C is between A and E. Who is opposite B?',
        'E', 'F', 'D', 'C', 'A',
        'Arranging 6 clockwise: A at 1. Opposite is 4 (D). Immediate right is 2 (B). C is between A and E, so C is at 6 and E is at 5. That leaves position 3 for F. Opposite B (pos 2) is pos 5 (E).');

    $b1[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All algorithms terminate. All terminating procedures are deterministic. Conclusions: I. All algorithms are deterministic. II. Some deterministic procedures are algorithms.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'All algorithms terminate and all terminating procedures are deterministic, so all algorithms are deterministic. Also some deterministic procedures are algorithms. Both follow.');

    // --- Verbal (4 Qs) ---
    $b1[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "The latency metrics ______ significantly improved after deploying the cache layer."',
        'have', 'has', 'is', 'was', 'A',
        'Plural subject "The latency metrics" takes "have".');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: SCALABLE',
        'Expandable', 'Rigid', 'Fragile', 'Limited', 'A',
        'Scalable means able to be expanded or upgraded on demand.');

    $b1[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: COARSE',
        'Refined', 'Rough', 'Crude', 'Harsh', 'A',
        'Coarse means rough or crude. Its antonym is Refined.');

    $b1[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the statement: "Eventual consistency allows distributed database replicas to accept asynchronous updates and converge over time, maximizing system availability over immediate strict consistency." What trade-off does eventual consistency make?',
        'Prioritizing high availability and write throughput over immediate consistency.',
        'Deleting all data upon replica mismatch.',
        'Prohibiting writes to database clusters.',
        'Requiring identical hardware on all cloud servers.', 'A',
        'Eventual consistency sacrifices immediate consistency to maximize availability and throughput.');

    // --- Technical MCQs (20 Qs - Heavy DSA & Algorithms focus for Amazon) ---
    $b1[] = makeQ($c, 'TECH_DSA', 'easy',
        'What is the time complexity of looking up a key in a Hash Map with a good hash function and no collisions?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n log n)', 'A',
        'Under uniform hashing with no collisions, hash map lookup is O(1) constant time.');

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the time complexity of building a Binary Heap of n elements using the bottom-up Heapify approach?',
        'O(n)', 'O(n log n)', 'O(log n)', 'O(n^2)', 'A',
        'Bottom-up heap construction (Floyd\'s algorithm) runs in linear time O(n).');

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'Which traversal of a Binary Search Tree (BST) visits nodes in strictly ascending sorted order?',
        'In-order traversal', 'Pre-order traversal', 'Post-order traversal', 'Level-order traversal', 'A',
        'In-order traversal visits left subtree, root, then right subtree, producing sorted output for a BST.');

    $b1[] = makeQ($c, 'TECH_DSA', 'hard',
        'What is the minimum number of comparisons required to find both the minimum and maximum of an array of n elements?',
        '3n/2 - 2', '2n - 2', 'n - 1', 'n log n', 'A',
        'By comparing elements in pairs first, the min and max can be found simultaneously in 3n/2 - 2 comparisons.');

    $b1[] = makeQ($c, 'TECH_DSA', 'hard',
        'Which algorithm is used to find the shortest path between a source vertex and all other vertices in a weighted graph with non-negative edge weights?',
        'Dijkstra\'s Algorithm', 'Bellman-Ford Algorithm', 'Floyd-Warshall Algorithm', 'Kruskal\'s Algorithm', 'A',
        'Dijkstra\'s algorithm uses a priority queue to compute single-source shortest paths in non-negative weighted graphs.');

    $b1[] = makeQ($c, 'TECH_DSA', 'hard',
        'What is the worst-case time complexity of QuickSelect to find the kth smallest element in an array?',
        'O(n^2)', 'O(n)', 'O(n log n)', 'O(log n)', 'A',
        'With poor pivot choices (such as already sorted array with extreme pivot), QuickSelect degrades to O(n^2), although average time is O(n).');

    $b1[] = makeQ($c, 'TECH_DSA', 'hard',
        'Which data structure is optimal for implementing an auto-complete search prefix suggestion system?',
        'Trie (Prefix Tree)', 'Binary Search Tree', 'Hash Map', 'Priority Queue', 'A',
        'A Trie organizes characters along paths, allowing O(k) prefix matching where k is prefix length.');

    $b1[] = makeQ($c, 'TECH_C', 'medium',
        'What is the output of the following C code demonstrating pointer increment?',
        '20', '10', '30', 'Error', 'A',
        'ptr is initialized to &arr[0]. ptr++ advances pointer to arr[1], which is 20.',
        "#include <stdio.h>\nint main() {\n    int arr[] = {10, 20, 30};\n    int *ptr = arr;\n    ptr++;\n    printf(\"%d\", *ptr);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_C', 'hard',
        'What will be printed by this C program with bitwise operations?',
        '16', '8', '32', '0', 'A',
        'Left shift: 2 << 3 = 2 * 2^3 = 2 * 8 = 16.',
        "#include <stdio.h>\nint main() {\n    int x = 2 << 3;\n    printf(\"%d\", x);\n    return 0;\n}", 'c');

    $b1[] = makeQ($c, 'TECH_CPP', 'medium',
        'What is the difference between std::vector and standard C-style array in C++?',
        'std::vector manages dynamic heap memory automatically and can resize dynamically.',
        'std::vector is slower and stored on CPU registers.',
        'C-style arrays can resize at runtime.',
        'std::vector cannot hold objects.', 'A',
        'std::vector is a dynamic container that automatically reallocates memory as elements grow.');

    $b1[] = makeQ($c, 'TECH_CPP', 'hard',
        'What will be the output of this C++ program?',
        '50', '25', '10', '0', 'A',
        'Multiplication: 10 * 5 = 50.',
        "#include <iostream>\nint main() {\n    int a = 10, b = 5;\n    std::cout << a * b;\n    return 0;\n}", 'cpp');

    $b1[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which interface in Java is implemented by PriorityQueue to support queue operations?',
        'Queue', 'List', 'Set', 'Map', 'A',
        'PriorityQueue implements the java.util.Queue interface.');

    $b1[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What is the time complexity of adding an element to a PriorityQueue of size n in Java?',
        'O(log n)', 'O(1)', 'O(n)', 'O(n log n)', 'A',
        'Insertion into a binary heap requires percolating up, taking O(log n) time.');

    $b1[] = makeQ($c, 'TECH_JAVA', 'hard',
        'In Java concurrency, what does the "volatile" keyword guarantee for a variable across threads?',
        'Visibility of writes across all threads and prevention of instruction reordering.',
        'Mutual exclusion lock on the object.',
        'Automatic atomicity of compound operations like count++.',
        'Persistence to database storage.', 'A',
        'Volatile guarantees that reads and writes go directly to main memory and prevents instruction reordering.');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '[1, 2, 3]', '[3, 2, 1]', '[1, 2]', 'Error', 'A',
        'The sorted() built-in returns a new sorted list: [1, 2, 3].',
        "print(sorted([3, 1, 2]))", 'python');

    $b1[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of the following Python dictionary operation?',
        '2', '1', 'None', 'KeyError', 'A',
        "d['b'] accesses the value corresponding to key 'b', which is 2.",
        "d = {'a': 1, 'b': 2}\nprint(d['b'])", 'python');

    $b1[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL keyword combines rows from two or more tables based on a related column between them?',
        'JOIN', 'MERGE', 'GROUP', 'UNION', 'A',
        'The JOIN clause combines rows based on a related attribute.');

    $b1[] = makeQ($c, 'TECH_SQL', 'medium',
        'What is the primary trade-off of adding a secondary index to a SQL table?',
        'It speeds up SELECT queries with WHERE filters on that column but slows down INSERT and UPDATE operations.',
        'It reduces table storage requirements on disk.',
        'It prevents table drop operations.',
        'It eliminates the need for primary keys.', 'A',
        'Secondary indexes accelerate queries but incur maintenance overhead on write operations.');

    $b1[] = makeQ($c, 'TECH_DSA', 'medium',
        'What is the space complexity of an iterative Breadth-First Search on a balanced binary tree with n nodes at maximum breadth?',
        'O(n)', 'O(log n)', 'O(1)', 'O(n^2)', 'A',
        'The maximum number of leaf nodes at the deepest level of a binary tree is approximately n/2, requiring O(n) queue space.');

    $b1[] = makeQ($c, 'TECH_DSA', 'hard',
        'Which algorithm is used to detect cycles in a directed graph using Depth-First Search?',
        'DFS with three color states (White, Gray, Black)',
        'Kruskal\'s Algorithm',
        'Dijkstra\'s Algorithm',
        'Binary Search', 'A',
        'Detecting back-edges using vertex visitation states (White=unvisited, Gray=in recursion stack, Black=completed) identifies cycles in directed graphs.');

    // ========================================================
    // BATCH 2 (40 Questions: 8 Quant, 8 LR, 4 Verbal, 20 Tech)
    // ========================================================
    // --- Quant (8 Qs) ---
    $b2[] = makeQ($c, 'QUANT_ADV', 'easy',
        'What is the value of 16^2?',
        '256', '246', '266', '236', 'A',
        '16 * 16 = 256.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'medium',
        'If 5 fair coins are tossed simultaneously, what is the probability of getting exactly 3 heads?',
        '5/16', '1/2', '3/16', '1/4', 'A',
        'Total outcomes = 2^5 = 32. Favorable outcomes = 5C3 = 10. Probability = 10 / 32 = 5/16.');

    $b2[] = makeQ($c, 'QUANT_ADV', 'hard',
        'Find the sum of all prime numbers between 10 and 25.',
        '83', '81', '85', '79', 'A',
        'Primes: 11, 13, 17, 19, 23. Sum = 11 + 13 + 17 + 19 + 23 = 83.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'easy',
        'What is 20% of 900?',
        '180', '160', '200', '170', 'A',
        '0.20 * 900 = 180.');

    $b2[] = makeQ($c, 'QUANT_PERC', 'medium',
        'An item bought for Rs. 600 is sold for Rs. 750. What is the profit percentage?',
        '25%', '20%', '30%', '15%', 'A',
        'Profit = 150. Profit % = (150 / 600) * 100 = 25%.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'easy',
        'A vehicle covers 210 km in 3 hours. What is its speed?',
        '70 km/h', '65 km/h', '75 km/h', '80 km/h', 'A',
        'Speed = 210 / 3 = 70 km/h.');

    $b2[] = makeQ($c, 'QUANT_SDT', 'medium',
        'A person walks 18 km at 6 km/h. How many hours did he walk?',
        '3 hours', '2.5 hours', '4 hours', '3.5 hours', 'A',
        'Time = 18 / 6 = 3 hours.');

    $b2[] = makeQ($c, 'QUANT_WORK', 'medium',
        'Pipe A fills a reservoir in 6 hours and Pipe B in 12 hours. How long together?',
        '4 hours', '3 hours', '5 hours', '6 hours', 'A',
        'Rate = 1/6 + 1/12 = 3/12 = 1/4. Time = 4 hours.');

    // --- Logical Reasoning (8 Qs) ---
    $b2[] = makeQ($c, 'LR_SERIES', 'easy',
        'Find the next number: 10, 20, 30, 40, ?',
        '50', '60', '45', '55', 'A',
        'Increments of 10: 40 + 10 = 50.');

    $b2[] = makeQ($c, 'LR_SERIES', 'medium',
        'Find the missing term: 1, 2, 4, 8, 16, ?',
        '32', '24', '30', '36', 'A',
        'Doubling: 16 * 2 = 32.');

    $b2[] = makeQ($c, 'LR_CODING', 'easy',
        'If KEY is coded as 11-5-25, how is AWS coded?',
        '1-23-19', '1-22-19', '2-23-19', '1-23-20', 'A',
        'A=1, W=23, S=19. Code is 1-23-19.');

    $b2[] = makeQ($c, 'LR_CODING', 'medium',
        'In a code, CODE is written as DPEF. How is RUN written in that code?',
        'SVO', 'SWO', 'TVO', 'SUN', 'A',
        'Shift +1: R(+1)=S, U(+1)=V, N(+1)=O. Code is SVO.');

    $b2[] = makeQ($c, 'LR_BLOOD', 'easy',
        'A is the daughter of B. B is the mother of C. How is A related to C?',
        'Sister', 'Mother', 'Aunt', 'Niece', 'A',
        'A and C share mother B, so A is the sister of C.');

    $b2[] = makeQ($c, 'LR_DIRECTION', 'easy',
        'Walking 10 m East, then turning right and walking 10 m. What direction is the final position from the starting point?',
        'South-East', 'North-East', 'South-West', 'North-West', 'A',
        'Displacement is East and South: South-East.');

    $b2[] = makeQ($c, 'LR_SEATING', 'medium',
        'Four engineers A, B, C, D sit around a circular table facing center. A is opposite C. B is to the right of A. Who is opposite B?',
        'D', 'C', 'A', 'None', 'A',
        'Opposite pairs are (A, C) and (B, D). B is opposite D.');

    $b2[] = makeQ($c, 'LR_PUZZLES', 'hard',
        'Statements: All binary heaps are trees. All trees are graphs. Conclusions: I. All binary heaps are graphs. II. Some graphs are binary heaps.',
        'Both I and II follow', 'Only I follows', 'Only II follows', 'Neither follows', 'A',
        'Binary heaps are trees and trees are graphs, so all binary heaps are graphs. Also some graphs are binary heaps. Both follow.');

    // --- Verbal (4 Qs) ---
    $b2[] = makeQ($c, 'VERBAL_SC', 'easy',
        'Choose the correct verb: "The service request ______ processed without error."',
        'was', 'were', 'are', 'is were', 'A',
        'Singular subject "The service request" takes "was".');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'easy',
        'Select the synonym for the word: RESILIENT',
        'Robust', 'Fragile', 'Weak', 'Soft', 'A',
        'Resilient means able to withstand adversity; robust.');

    $b2[] = makeQ($c, 'VERBAL_VOCAB', 'medium',
        'Select the antonym for the word: OPAQUE',
        'Transparent', 'Cloudy', 'Murky', 'Dark', 'A',
        'Opaque means non-transparent. Its antonym is Transparent.');

    $b2[] = makeQ($c, 'VERBAL_RC', 'medium',
        'Read the statement: "Read replicas offload analytical and read-heavy queries from the primary database instance, preserving primary compute capacity for low-latency write transactions." How do read replicas improve database architecture?',
        'By directing read traffic away from the primary instance to preserve write capacity.',
        'By deleting old transaction records.',
        'By replacing SQL with machine language.',
        'By shutting down network routers.', 'A',
        'Read replicas distribute query volume to maintain write responsiveness on the primary.');

    // --- Technical MCQs (20 Qs - Heavy Amazon DSA/Systems) ---
    $b2[] = makeQ($c, 'TECH_DSA', 'easy',
        'What is the worst-case time complexity of searching in an unsorted array of size n?',
        'O(n)', 'O(1)', 'O(log n)', 'O(n^2)', 'A',
        'Linear search checks each element up to n times: O(n).');

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'In a Min-Heap with n elements, what is the time complexity of deleting the minimum element?',
        'O(log n)', 'O(1)', 'O(n)', 'O(n log n)', 'A',
        'Removing root and heapifying down takes O(log n) time.');

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'Which algorithmic technique solves optimization problems by breaking them into overlapping subproblems and memoizing results?',
        'Dynamic Programming', 'Divide and Conquer', 'Greedy Method', 'Backtracking', 'A',
        'Dynamic programming memoizes overlapping subproblems to avoid redundant computation.');

    $b2[] = makeQ($c, 'TECH_DSA', 'hard',
        'What is the time complexity of finding strongly connected components in a directed graph using Tarjan\'s or Kosaraju\'s algorithm?',
        'O(V + E)', 'O(V * E)', 'O(V^2)', 'O(E log V)', 'A',
        'Both Tarjan\'s and Kosaraju\'s SCC algorithms run in linear time O(V + E) using depth-first search.');

    $b2[] = makeQ($c, 'TECH_DSA', 'hard',
        'What is the tightest upper bound time complexity of the Floyd-Warshall all-pairs shortest paths algorithm?',
        'O(V^3)', 'O(V^2)', 'O(V * E)', 'O(E log V)', 'A',
        'Floyd-Warshall consists of three nested loops over the V vertices, running in O(V^3) time.');

    $b2[] = makeQ($c, 'TECH_DSA', 'hard',
        'In a Bloom Filter, what type of false outcome is possible?',
        'False positive (reporting an element is in the set when it is not)',
        'False negative (reporting an element is not in the set when it is)',
        'Both false positives and false negatives',
        'Neither false positives nor false negatives', 'A',
        'Bloom filters may yield false positives due to hash collisions, but never false negatives.');

    $b2[] = makeQ($c, 'TECH_DSA', 'hard',
        'What is the amortized time complexity of inserting an element into an expanding dynamic array (such as std::vector or ArrayList)?',
        'O(1)', 'O(n)', 'O(log n)', 'O(n^2)', 'A',
        'Although resizing takes O(n), geometric array doubling ensures an amortized insertion time of O(1).');

    $b2[] = makeQ($c, 'TECH_C', 'medium',
        'What will be the output of this C program?',
        '25', '20', '15', 'Error', 'A',
        'Addition: 20 + 5 = 25.',
        "#include <stdio.h>\nint main() {\n    int a = 20, b = 5;\n    printf(\"%d\", a + b);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_C', 'hard',
        'What does this C code print?',
        '30', '15', '0', 'Error', 'A',
        'Multiplication: 5 * 6 = 30.',
        "#include <stdio.h>\nint main() {\n    int a = 5, b = 6;\n    printf(\"%d\", a * b);\n    return 0;\n}", 'c');

    $b2[] = makeQ($c, 'TECH_CPP', 'medium',
        'Which C++ container provides associative key-value mapping backed by a balanced Red-Black Tree with O(log n) operations?',
        'std::map', 'std::unordered_map', 'std::vector', 'std::list', 'A',
        'std::map in C++ standard library is implemented as a self-balancing Red-Black BST.');

    $b2[] = makeQ($c, 'TECH_CPP', 'hard',
        'What is printed by this C++ program?',
        'Amazon', 'AWS', 'None', 'Error', 'A',
        'Prints literal "Amazon".',
        "#include <iostream>\nint main() {\n    std::cout << \"Amazon\";\n    return 0;\n}", 'cpp');

    $b2[] = makeQ($c, 'TECH_JAVA', 'easy',
        'Which Java collection does not allow duplicate elements and does not guarantee insertion order?',
        'HashSet', 'ArrayList', 'LinkedList', 'LinkedHashSet', 'A',
        'HashSet enforces uniqueness backed by a hash table with non-guaranteed ordering.');

    $b2[] = makeQ($c, 'TECH_JAVA', 'medium',
        'What will the following Java code print?',
        '20', '15', '25', 'Error', 'A',
        'Addition: 15 + 5 = 20.',
        "int x = 15, y = 5;\nSystem.out.println(x + y);", 'java');

    $b2[] = makeQ($c, 'TECH_JAVA', 'hard',
        'What is the purpose of the ConcurrentHashMap class in Java?',
        'To allow thread-safe concurrent reads and bucket-level concurrent writes without locking the entire map.',
        'To prevent all multithreaded access.',
        'To store data in sorted order by key.',
        'To eliminate garbage collection.', 'A',
        'ConcurrentHashMap partitions table buckets to enable concurrent thread-safe access with minimal lock contention.');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'easy',
        'What does the following Python statement output?',
        '10', '5', '15', 'Error', 'A',
        'Multiplication: 2 * 5 = 10.',
        "print(2 * 5)", 'python');

    $b2[] = makeQ($c, 'TECH_PYTHON', 'medium',
        'What is the output of this Python slice operation?',
        '[1, 2]', '[2, 3]', '[1, 2, 3]', 'Error', 'A',
        'Slice items[:2] returns [1, 2].',
        "items = [1, 2, 3]\nprint(items[:2])", 'python');

    $b2[] = makeQ($c, 'TECH_SQL', 'easy',
        'Which SQL keyword combines query results while preserving duplicate rows?',
        'UNION ALL', 'UNION', 'JOIN', 'MERGE', 'A',
        'UNION ALL combines sets without filtering duplicate records.');

    $b2[] = makeQ($c, 'TECH_SQL', 'medium',
        'Which SQL isolation level prevents dirty reads, non-repeatable reads, and phantom reads completely?',
        'SERIALIZABLE', 'REPEATABLE READ', 'READ COMMITTED', 'READ UNCOMMITTED', 'A',
        'SERIALIZABLE is the highest isolation level, preventing all dirty reads, non-repeatable reads, and phantom reads.');

    $b2[] = makeQ($c, 'TECH_DSA', 'medium',
        'In a doubly linked list, how many pointers does each node contain?',
        'Two (next and previous)', 'One', 'Three', 'None', 'A',
        'Doubly linked list nodes maintain two pointers: one pointing forward to next and one backward to previous.');

    $b2[] = makeQ($c, 'TECH_DSA', 'hard',
        'Which data structure is used to implement Disjoint Set Union (DSU) with union by rank and path compression?',
        'Forest of Trees represented in arrays',
        'Doubly Linked List',
        'Binary Heap',
        'Trie', 'A',
        'Disjoint Set Union represents sets as trees within parent arrays, achieving near O(1) amortized inverse Ackermann time.');

    return ['batch1' => $b1, 'batch2' => $b2];
}
