<?php
require_once __DIR__ . '/config/db.php';

echo "Seeding SQL, DSA, and Innovature Labs PYQ Questions...\n";

try {
    $db = Database::getConnection();

    $stmt = $db->prepare("
        INSERT INTO `questions` 
        (`subtopic_id`, `difficulty`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `est_time_seconds`, `source_tag`, `is_company_pyq`, `company_name`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $questions = [
        // ==========================================
        // SQL & DATABASES (subtopic_id: 20, language: 'sql')
        // ==========================================
        [
            20, 'hard', 'Which SQL query correctly finds the 2nd highest salary from an Employees table while gracefully handling duplicates and returning NULL if no second highest exists?',
            "SELECT ... FROM Employees;",
            'sql',
            'SELECT DISTINCT salary FROM Employees ORDER BY salary DESC LIMIT 1 OFFSET 1;',
            'SELECT MAX(salary) FROM Employees WHERE salary < (SELECT MAX(salary) FROM Employees);',
            'SELECT salary FROM Employees WHERE rank = 2;',
            'SELECT TOP 1 salary FROM (SELECT TOP 2 salary FROM Employees ORDER BY salary DESC) ORDER BY salary ASC;',
            'B',
            '`SELECT MAX(salary) FROM Employees WHERE salary < (SELECT MAX(salary) FROM Employees);` finds the maximum salary strictly less than the overall maximum. If all employees earn identical salaries (or only 1 employee exists), the subquery produces an empty set, and `MAX()` evaluates cleanly to `NULL` (as required by standard database benchmarks). Plain `LIMIT 1 OFFSET 1` returns an empty result set rather than a single row containing `NULL`.',
            65, 'SQL Technical', 0, NULL
        ],
        [
            20, 'hard', 'What is the crucial difference between DENSE_RANK() and RANK() window functions in SQL when dealing with tied values?',
            'sql',
            'RANK() creates contiguous integers without gaps; DENSE_RANK() leaves gaps equal to the count of ties',
            'DENSE_RANK() leaves no gaps in ranking numbers after ties; RANK() skips numbers corresponding to the number of tied positions',
            'DENSE_RANK() works only on string columns',
            'RANK() cannot be partitioned by department',
            'B',
            'When two rows tie for 1st place: `DENSE_RANK()` assigns ranks `1, 1, 2` (no gap). In contrast, `RANK()` assigns ranks `1, 1, 3` (skipping rank 2 to reflect that two entries occupied the top slot).',
            55, 'SQL Technical', 0, NULL
        ],
        [
            20, 'hard', 'What is the result of applying a WHERE condition to the right table in a LEFT OUTER JOIN?',
            "SELECT e.name, d.dept_name \nFROM Employees e \nLEFT JOIN Departments d ON e.dept_id = d.id \nWHERE d.dept_name = 'Engineering';",
            'sql',
            'It preserves all unmatched employees from the left table and populates NULL for dept_name',
            'It effectively converts the LEFT JOIN into an INNER JOIN, excluding all employees without departments or whose department is not Engineering',
            'It results in a SQL Syntax Error',
            'It creates a Cartesian Product (CROSS JOIN)',
            'B',
            'Because the `WHERE` clause is evaluated AFTER the `JOIN`, filtering on `d.dept_name = \'Engineering\'` eliminates all rows where `d.dept_name` is `NULL` (since `NULL = \'Engineering\'` evaluates to UNKNOWN/false). To preserve non-matching left rows, the condition must be placed in the `ON` clause: `ON e.dept_id = d.id AND d.dept_name = \'Engineering\'`.',
            60, 'SQL Technical', 0, NULL
        ],
        [
            20, 'hard', 'In database ACID properties, which transaction isolation level prevents Dirty Reads and Non-Repeatable Reads, but may still allow Phantom Reads?',
            'sql', 'Read Uncommitted', 'Read Committed', 'Repeatable Read', 'Serializable',
            'C',
            '- Read Uncommitted: Allows dirty reads.\n- Read Committed: Prevents dirty reads; allows non-repeatable reads.\n- Repeatable Read: Prevents dirty reads & non-repeatable reads (re-reading the same row returns identical data); phantom reads (new rows inserted by concurrent transactions matching a range) may still appear under SQL-92.\n- Serializable: Strict serial execution preventing all anomalies.',
            60, 'SQL Technical', 0, NULL
        ],
        [
            20, 'hard', 'What is the difference between WHERE and HAVING clauses in SQL?',
            'sql',
            'WHERE filters rows before group aggregations are performed; HAVING filters grouped rows after aggregation',
            'HAVING operates on individual rows; WHERE operates on groups',
            'WHERE can use aggregate functions like COUNT(); HAVING cannot',
            'They are interchangeable in all ANSI SQL engines',
            'A',
            '`WHERE` filters individual rows prior to grouping and aggregation. `HAVING` filters aggregated group records after `GROUP BY` has collapsed rows into groups. Aggregate functions like `COUNT()`, `SUM()`, `AVG()` cannot be placed directly inside a standard `WHERE` clause without subqueries.',
            45, 'SQL Technical', 0, NULL
        ],

        // ==========================================
        // DATA STRUCTURES & ALGORITHMS (subtopic_id: 21, language: 'dsa')
        // ==========================================
        [
            21, 'hard', 'How does Floyd\'s Cycle-Finding Algorithm (Tortoise and Hare) detect a loop in a Singly Linked List in O(N) time and O(1) space?',
            'dsa',
            'By reversing the linked list and comparing node memory addresses',
            'By advancing slow pointer 1 step and fast pointer 2 steps; if a cycle exists, the fast pointer will inevitably lap and meet the slow pointer inside the loop',
            'By hashing all visited node pointers into a hash set of size O(1)',
            'By counting the total number of nodes in two separate traversals',
            'B',
            'Floyd\'s cycle-finding algorithm moves `slow` pointer by 1 node and `fast` pointer by 2 nodes per iteration. If there is no loop, `fast` reaches `NULL` in O(N). If a cycle of length L exists, with each step the distance between them inside the loop decreases by 1 modulo L, guaranteeing they meet in at most L steps without using extra memory.',
            65, 'DSA Technical', 0, NULL
        ],
        [
            21, 'hard', 'In an AVL Tree, when is a Left-Right (LR) double rotation performed to restore balance factor?',
            'dsa',
            'When a node is inserted into the left subtree of the left child',
            'When a node is inserted into the right subtree of the left child (Balance Factor = +2, left child Balance Factor = -1)',
            'When a node is inserted into the right subtree of the right child',
            'When the root node has duplicate keys',
            'B',
            'An LR (Left-Right) imbalance occurs when an insertion takes place in the right subtree of the left child. This is rectified via a double rotation: first a Left rotation on the left child, followed by a Right rotation on the unbalanced parent node.',
            60, 'DSA Technical', 0, NULL
        ],
        [
            21, 'hard', 'What is the optimal data structure and time complexity to find the Next Greater Element for all elements in an array of size N?',
            'dsa',
            'Nested loops with O(N^2) time complexity',
            'Monotonic Decreasing Stack with O(N) time complexity',
            'Priority Queue (Max Heap) with O(N log N) time complexity',
            'Self-Balancing Binary Search Tree with O(N log N) time complexity',
            'B',
            'Using a Monotonic Stack (storing indices of elements in decreasing order of value), each array element is pushed and popped at most once. As we iterate through the array, while current element > stack top, we pop the top index and record current element as its Next Greater Element. Total time complexity is strictly linear O(N).',
            65, 'DSA Technical', 0, NULL
        ],
        [
            21, 'hard', 'Why is Quicksort generally preferred over Mergesort for sorting contiguous in-memory arrays, despite Mergesort having guaranteed O(N log N) worst-case?',
            'dsa',
            'Quicksort has O(1) worst-case time complexity',
            'Quicksort sorts in-place with O(log N) auxiliary stack space, exhibits superior CPU cache locality of reference, and has a smaller hidden constant factor',
            'Mergesort cannot handle duplicate values',
            'Quicksort is stable while Mergesort is unstable',
            'B',
            'Quicksort operates in-place (O(1) extra array memory vs Mergesort\'s O(N) auxiliary buffer). Its sequential memory scanning aligns with CPU cache lines and prefetching, making its real-world constant factor much smaller. (Dual-pivot Quicksort is the default primitive sorter in Java and C++).',
            60, 'DSA Technical', 0, NULL
        ],
        [
            21, 'hard', 'What is the recurrence relation and asymptotic time complexity of Karatsuba\'s fast integer multiplication algorithm?',
            'dsa',
            'T(N) = 2T(N/2) + O(N) -> O(N log N)',
            'T(N) = 3T(N/2) + O(N) -> O(N^{log_2 3}) ≈ O(N^{1.585})',
            'T(N) = 4T(N/2) + O(N) -> O(N^2)',
            'T(N) = T(N-1) + O(N) -> O(N^2)',
            'B',
            'Standard grade-school multiplication splits N-digit numbers into 4 sub-multiplications: `T(N) = 4T(N/2) + O(N) = O(N^2)`. Karatsuba cleverly computes `(a+b)(c+d) - ac - bd` to obtain the cross-term with only 3 recursive multiplications: `T(N) = 3T(N/2) + O(N)`. By Master Theorem, `T(N) = O(N^{log_2 3}) ≈ O(N^{1.585})`.',
            70, 'DSA Technical', 0, NULL
        ],

        // ==========================================
        // INNOVATURE LABS PREVIOUS YEAR QUESTIONS (subtopic_id: 22, language: 'innovature')
        // ==========================================
        [
            22, 'hard', '[Innovature Labs Technical PYQ] What will be the output of the following C program asked in Innovature Labs placement technical assessment?',
            "#include <stdio.h>\nint main() {\n    int a = 5;\n    int b = ++a + a++ + --a;\n    printf(\"a=%d b=%d\", a, b);\n    return 0;\n}",
            'innovature',
            'a=6 b=18 (or compiler dependent due to unsequenced modifications)',
            'a=5 b=15',
            'a=7 b=21',
            'Compilation Error',
            'A',
            'Innovature Labs frequently tests awareness of C sequence points and undefined behavior! In C standards, modifying variable `a` multiple times within a single expression without an intervening sequence point (`++a + a++ + --a`) invokes Undefined Behavior (UB). In typical GCC evaluation order on x86, it evaluates to `a=6 b=18` or `a=6 b=19`. Recognizing undefined behavior and compiler dependency is the hallmark of strong C candidates.',
            70, 'Innovature Labs PYQ', 1, 'Innovature Labs'
        ],
        [
            22, 'hard', '[Innovature Labs Technical PYQ] What is printed by this Innovature Labs interview question on C pointer increments?',
            "#include <stdio.h>\nint main() {\n    char str[] = \"Innovature\";\n    char *p = str;\n    printf(\"%c \", *p++);\n    printf(\"%c \", *++p);\n    printf(\"%c\", ++*p);\n    return 0;\n}",
            'innovature', 'I o p', 'I n o', 'I n p', 'I n n',
            'A',
            'Step by step pointer trace:\n1. `*p++`: post-increment. Evaluates `*p` (\'I\') then increments `p` to point to \'n\' (index 1).\n2. `*++p`: pre-increment. Increments `p` to point to \'o\' (index 2) then dereferences it, printing \'o\'.\n3. `++*p`: dereferences `p` (\'o\') and increments the character value (\'o\' + 1 = \'p\'), printing \'p\'.\nOutput: `I o p`.',
            75, 'Innovature Labs PYQ', 1, 'Innovature Labs'
        ],
        [
            22, 'hard', '[Innovature Labs Technical PYQ] In an Innovature Labs Java assessment, what is the output of this inheritance constructor chaining code?',
            "class Alpha {\n    Alpha() { System.out.print(\"A\"); }\n    Alpha(int x) { System.out.print(\"Ax\"); }\n}\nclass Beta extends Alpha {\n    Beta() { super(5); System.out.print(\"B\"); }\n    Beta(int y) { this(); System.out.print(\"By\"); }\n}\npublic class Test {\n    public static void main(String[] args) {\n        new Beta(10);\n    }\n}",
            'innovature', 'AxBBy', 'ABBy', 'AxBy', 'Compilation error',
            'A',
            'Tracing constructor execution:\n1. `new Beta(10)` invokes `Beta(int y)`.\n2. `Beta(int y)` calls `this()` via constructor delegation.\n3. `Beta()` calls `super(5)` as its explicit super constructor.\n4. `Alpha(int x)` executes -> prints \'Ax\'.\n5. Control returns to `Beta()` body -> prints \'B\'.\n6. Control returns to `Beta(int y)` body -> prints \'By\'.\nOutput: `AxBBy`.',
            70, 'Innovature Labs PYQ', 1, 'Innovature Labs'
        ],
        [
            22, 'hard', '[Innovature Labs Technical PYQ] Innovature Labs SQL Round: Which query retrieves all employees who earn more than their direct managers?',
            "SELECT e.name FROM Employee e ...",
            'innovature',
            'SELECT e.name FROM Employee e JOIN Employee m ON e.manager_id = m.id WHERE e.salary > m.salary;',
            'SELECT e.name FROM Employee e WHERE e.salary > AVG(e.salary);',
            'SELECT e.name FROM Employee e WHERE e.manager_id IS NOT NULL AND e.salary > e.salary;',
            'SELECT e.name FROM Employee e LEFT JOIN Employee m ON e.id = m.manager_id WHERE e.salary > m.salary;',
            'A',
            'A classic Innovature Labs interview problem! This requires a Self-Join where the `Employee` table `e` represents employees and `m` represents their managers connected via `e.manager_id = m.id`. The condition `WHERE e.salary > m.salary` compares the employee\'s salary against their direct manager\'s salary.',
            60, 'Innovature Labs PYQ', 1, 'Innovature Labs'
        ],
        [
            22, 'hard', '[Innovature Labs Reasoning PYQ] Innovature Labs Written Test: 8 team members (A, B, C, D, E, F, G, H) sit around a circular table. 4 face the center and 4 face outward. A sits third to the right of B, who faces inward. C sits second to the left of A and faces the center. D is not adjacent to B or C. Who sits opposite to A?',
            null,
            'innovature', 'D', 'E', 'F', 'H',
            'A',
            'Detailed circular arrangement deduction:\n- Place B facing inward at position 1.\n- A sits 3rd to right of B (counterclockwise) -> position 4.\n- C sits 2nd to left of A and faces inward. If A faced outward, 2nd left would be position 6. If A faced inward, 2nd left would be position 2. Since D cannot sit adjacent to B (pos 8, 2) or C, positioning constraints uniquely force D to occupy position 8 (opposite A at position 4).\nCorrect answer is D.',
            85, 'Innovature Labs PYQ', 1, 'Innovature Labs'
        ],
        [
            22, 'hard', '[Innovature Labs Reasoning PYQ] Innovature Labs Aptitude Test: In a certain code language used in Innovature software logs, \"743\" means \"compile clean build\", \"657\" means \"build test pass\", and \"934\" means \"clean code compile\". What code digit represents \"code\"?',
            null,
            'innovature', '9', '3', '4', '7',
            'A',
            'Common word elimination analysis:\n1. Compare \"743\" (\"compile clean build\") and \"934\" (\"clean code compile\"): Common digits are 4 and 3; common words are \"compile\" and \"clean\". Thus, {3, 4} = {\"compile\", \"clean\"}.\n2. In \"934\" (\"clean code compile\"), the remaining unique word is \"code\", and the remaining unique digit is 9!\nTherefore, 9 represents \"code\".',
            60, 'Innovature Labs PYQ', 1, 'Innovature Labs'
        ],
        [
            22, 'hard', '[Innovature Labs Quant PYQ] Innovature Labs Aptitude: A software developer takes 10 hours to code a feature. A junior developer takes 15 hours. If they work together on alternate hours with the senior developer starting first, in how many hours will the feature be completed?',
            null,
            'innovature', '12 hours', '11 hours 40 minutes', '12 hours 30 minutes', '11 hours',
            'A',
            'Work efficiency & alternating hours calculation:\n- Let total work = LCM(10, 15) = 30 units.\n- Senior\'s rate = 30 / 10 = 3 units/hour.\n- Junior\'s rate = 30 / 15 = 2 units/hour.\n- In a 2-hour cycle (Senior + Junior): work done = 3 + 2 = 5 units.\n- To complete 30 units: Number of 2-hour cycles = 30 / 5 = 6 cycles.\n- Total time = 6 cycles * 2 hours/cycle = 12 hours exactly!',
            75, 'Innovature Labs PYQ', 1, 'Innovature Labs'
        ],
        [
            22, 'hard', '[Innovature Labs Technical PYQ] What is the output of the following C recursive function called with `f(4)`?',
            "#include <stdio.h>\nvoid f(int n) {\n    if (n <= 0) return;\n    f(n - 1);\n    printf(\"%d \", n);\n    f(n - 2);\n}",
            'innovature', '1 2 3 1 4 1 2', '1 2 1 3 1 4 1 2 1', '4 3 2 1', '1 2 3 4',
            'A',
            'Tracing recursion tree:\n- `f(1)`: `f(0)` -> prints 1 -> `f(-1)` => prints \"1 \"\n- `f(2)`: `f(1)` (\"1 \") -> prints 2 -> `f(0)` => prints \"1 2 \"\n- `f(3)`: `f(2)` (\"1 2 \") -> prints 3 -> `f(1)` (\"1 \") => prints \"1 2 3 1 \"\n- `f(4)`: `f(3)` (\"1 2 3 1 \") -> prints 4 -> `f(2)` (\"1 2 \") => prints \"1 2 3 1 4 1 2 \".\nOutput: `1 2 3 1 4 1 2 `.',
            80, 'Innovature Labs PYQ', 1, 'Innovature Labs'
        ],
        [
            22, 'hard', '[Innovature Labs Technical PYQ] Innovature Labs Java: What happens when trying to compile and run this code with a generic array creation?',
            "public class GenTest<T> {\n    T[] arr = new T[10];\n}",
            'innovature',
            'Compiles and runs with array of 10 elements',
            'Compilation Error: Cannot create a generic array of T',
            'Runtime GenericSignatureException',
            'Creates an Object[] array silently',
            'B',
            'In Java, generic array creation `new T[10]` is explicitly forbidden by the compiler (\'Cannot create a generic array of T\'). Because Java generics are implemented via Type Erasure, the runtime has no reified type information for `T` and cannot perform array store type checks (`ArrayStoreException`). One must use `(T[]) new Object[10]` or `Array.newInstance()`.',
            60, 'Innovature Labs PYQ', 1, 'Innovature Labs'
        ]
    ];

    $count = 0;
    foreach ($questions as $q) {
        $stmt->execute($q);
        $count++;
    }
    echo "Seeded {$count} SQL, DSA, and Innovature Labs questions successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
