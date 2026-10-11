import json

with open("data/technical_and_hard_questions.json", "r", encoding="utf-8") as f:
    questions = json.load(f)

print(f"Initial count: {len(questions)}")

def add(subtopic_id, diff, text, snippet, lang, opt_a, opt_b, opt_c, opt_d, ans, exp, time_sec, source_tag, is_pyq=0, comp_name=None):
    questions.append({
        "subtopic_id": subtopic_id,
        "difficulty": diff,
        "question_text": text,
        "code_snippet": snippet,
        "language": lang,
        "option_a": opt_a,
        "option_b": opt_b,
        "option_c": opt_c,
        "option_d": opt_d,
        "correct_option": ans,
        "explanation": exp,
        "est_time_seconds": time_sec,
        "source_tag": source_tag,
        "is_company_pyq": is_pyq,
        "company_name": comp_name
    })

# Add more C questions
c_extra = [
    (
        "What is the output of the following C program with format strings in printf?",
        "#include <stdio.h>\nint main() {\n    int x = 10;\n    printf(\"%*d\", 5, x);\n    return 0;\n}",
        "   10 (field width 5 with right-aligned 10)", "10", "5 10", "Compilation error", "A",
        "The `*` in the format specifier `%*d` specifies dynamic field width passed as an argument. Width 5 prints 3 spaces followed by 10."
    ),
    (
        "What does the macro token-pasting operator ## do in C?",
        "#define CONCAT(a, b) a ## b\nint xy = 30;\nprintf(\"%d\", CONCAT(x, y));",
        "30", "xy", "Compilation Error", "0", "A",
        "The `##` token-pasting preprocessor operator merges tokens `x` and `y` into `xy`, which accesses variable `xy` (value 30)."
    ),
    (
        "What is the output of the stringification preprocessor operator # in C?",
        "#define STR(x) #x\nprintf(\"%s\", STR(Apttitudee));",
        "Apttitudee", "#Apttitudee", "Compilation error", "null", "A",
        "The `#` operator turns preprocessor arguments into string literals wrapped in double quotes: `\"Apttitudee\"`."
    ),
    (
        "What is printed by this bitwise right shift on unsigned integers?",
        "#include <stdio.h>\nint main() {\n    unsigned int u = 16;\n    printf(\"%u\", u >> 3);\n    return 0;\n}",
        "2", "4", "8", "0", "A",
        "`16 >> 3` divides by `2^3 = 8`. `16 / 8 = 2`."
    ),
    (
        "What does `fflush(stdin)` result in according to ANSI C standards?",
        "fflush(stdin);",
        "Undefined Behavior (fflush is defined ONLY for output and update streams)",
        "Clears the keyboard input buffer cleanly on all OS",
        "Terminates standard input stream",
        "Flushes the console screen", "A",
        "Per ISO C standard §7.21.5.2, `fflush` on an input stream like `stdin` is undefined behavior. Portable code reads until `\\n`."
    )
]

for q in c_extra:
    add(14, 'hard', q[0], q[1], 'c', q[2], q[3], q[4], q[5], q[6], q[7], 60, 'C Technical')

# Add more C++ questions
cpp_extra = [
    (
        "What is printed by `typeid` in C++ when run on polymorphic objects?",
        "#include <iostream>\n#include <typeinfo>\nclass Base { virtual void f() {} };\nclass Derived : public Base {};\nint main() { Base *b = new Derived(); std::cout << (typeid(*b) == typeid(Derived)); return 0; }",
        "1 (true because Base has virtual functions, enabling RTTI type resolution)",
        "0 (false)",
        "Compilation error",
        "Runtime TypeMismatchException", "A",
        "When `typeid` is applied to a dereferenced pointer of a polymorphic class (with virtual functions), it evaluates RTTI dynamically at runtime."
    ),
    (
        "What is the difference between `std::deque` and `std::vector` internal memory allocation in C++?",
        "// std::deque vs std::vector",
        "std::vector uses a single contiguous dynamic array; std::deque uses a chunked array of fixed-size memory blocks with a map pointer",
        "std::deque is a linked list",
        "std::vector allows O(1) front insertion",
        "They have identical memory layouts", "A",
        "`std::deque` allocates multiple fixed-sized chunks indexed by a central directory, providing $O(1)$ amortized insertion at both head and tail."
    ),
    (
        "What does the `noexcept` specifier guarantee to the C++ compiler?",
        "void process() noexcept;",
        "The function will not throw exceptions; if an exception escapes, std::terminate() is called immediately",
        "The function runs in constant time",
        "The function is inlined",
        "The function disables move semantics", "A",
        "`noexcept` informs the compiler that no exception will escape, enabling optimizations like `std::vector` move reallocation."
    ),
    (
        "What is the output of the following C++ friend function declaration?",
        "class Box { int width = 10; friend int getWidth(Box b); };\nint getWidth(Box b) { return b.width; }\n// getWidth(Box())",
        "10 (friend functions have access to private and protected members)",
        "Compilation Error: width is private",
        "0",
        "Segmentation fault", "A",
        "Declaring a function as `friend` inside a class grants it full access to all private and protected members of that class."
    ),
    (
        "What is an Inline Namespace used for in C++11?",
        "inline namespace v2 { void api(); }",
        "Library versioning where symbols of the inline namespace are treated as belonging to the enclosing namespace without qualifying",
        "Inlining all functions inside",
        "Preventing symbol export",
        "Disabling name mangling", "A",
        "Inline namespaces enable seamless library ABI versioning: members can be referenced without qualification while remaining versioned."
    )
]

for q in cpp_extra:
    add(15, 'hard', q[0], q[1], 'cpp', q[2], q[3], q[4], q[5], q[6], q[7], 60, 'C++ Technical')

# Add more Java questions
java_extra = [
    (
        "What will be printed by the following Java code testing boxing inside `equals()`?",
        "Long val = 10L;\nSystem.out.println(val.equals(10));",
        "false (because 10 is autoboxed to Integer, and Long.equals checks if obj instanceof Long)",
        "true",
        "Compilation Error",
        "Throws ClassCastException", "A",
        "`Long.equals(Object obj)` first checks `if (obj instanceof Long)`. Passing literal `10` autoboxes to `Integer`, so `instanceof Long` is false!"
    ),
    (
        "What is the difference between `Callable<T>` and `Runnable` in Java concurrency?",
        "// Callable vs Runnable in java.util.concurrent",
        "Callable can return a value of type T and throw checked exceptions; Runnable cannot return a value and cannot throw checked exceptions",
        "Runnable runs on separate CPU cores; Callable runs on main thread",
        "Callable is deprecated in Java 8",
        "Runnable requires synchronized blocks", "A",
        "`Callable<T>.call()` returns `T` and declares `throws Exception`. `Runnable.run()` returns `void` and cannot declare checked exceptions."
    ),
    (
        "What does the `finalize()` method replacement in modern Java (Java 9+) entail?",
        "// java.lang.ref.Cleaner",
        "`java.lang.ref.Cleaner` and `PhantomReference` for safer, non-blocking cleanup of native resources",
        "Explicit C++ destructors",
        "Automatic RAM wiping",
        "Thread.destroy()", "A",
        "`finalize()` was deprecated in Java 9 due to performance and security flaws, replaced by `Cleaner` and `AutoCloseable`."
    ),
    (
        "What will be output by `Arrays.binarySearch()` on an unsorted array in Java?",
        "int[] arr = { 5, 2, 8, 1 }; System.out.println(Arrays.binarySearch(arr, 2));",
        "Undefined/unpredictable result (binarySearch requires array to be sorted beforehand)",
        "1",
        "0",
        "Throws UnsortedArrayException", "A",
        "Java API documentation explicitly states: the array must be sorted prior to making this call; otherwise the results are undefined."
    ),
    (
        "What is the default initial capacity and load factor of a standard Java `HashSet`?",
        "// HashSet initial capacity and load factor",
        "Capacity 16, Load Factor 0.75",
        "Capacity 10, Load Factor 0.5",
        "Capacity 32, Load Factor 1.0",
        "Capacity 64, Load Factor 0.8", "A",
        "`HashSet` is backed internally by a `HashMap`, which defaults to an initial capacity of 16 and load factor of 0.75."
    )
]

for q in java_extra:
    add(16, 'hard', q[0], q[1], 'java', q[2], q[3], q[4], q[5], q[6], q[7], 60, 'Java Technical')

# Add more PHP questions
php_extra = [
    (
        "What will be the output of `var_dump(in_array('123abc', [123], true))` in PHP?",
        "<?php\nvar_dump(in_array('123abc', [123], true));\n?>",
        "bool(false) (strict type check prevents loose string-to-number casting)",
        "bool(true)",
        "null",
        "Fatal error", "A",
        "Passing `$strict = true` to `in_array()` performs `===` comparison, comparing string `'123abc'` to int `123`, which is `false`."
    ),
    (
        "What does the Nullsafe operator ?-> do in PHP 8?",
        "<?php\n$country = $user?->getAddress()?->country;\n?>",
        "Short-circuits and returns null without throwing an Error if any link in the chain is null",
        "Throws NullPointerException",
        "Converts null to empty string",
        "Logs an error to error_log", "A",
        "The `?->` operator stops evaluating and yields `null` if the object expression on its left evaluates to `null`."
    ),
    (
        "What does `session_regenerate_id(true)` accomplish in PHP web authentication?",
        "<?php\nsession_regenerate_id(true);\n?>",
        "Prevents Session Fixation attacks by issuing a new session ID and deleting the old session data file",
        "Logs the user out immediately",
        "Resets database connection pool",
        "Deletes all cookies in client browser", "A",
        "`session_regenerate_id(true)` generates a fresh random session ID and purges the previous session file, stopping Session Fixation."
    ),
    (
        "What is the output of the spaceship operator on arrays in PHP: `[1, 2] <=> [1, 3]`?",
        "<?php\necho [1, 2] <=> [1, 3];\n?>",
        "-1", "1", "0", "Fatal error", "A",
        "Array comparison evaluates element-by-element from index 0. At index 1, `2 < 3`, so the spaceship operator yields `-1`."
    ),
    (
        "What will be printed by `echo (true ? '1' : false ? '2' : '3');` in PHP 8?",
        "<?php\n// Nested ternary deprecated without parentheses; parenthesized evaluation:\necho (true ? '1' : (false ? '2' : '3'));\n?>",
        "1", "2", "3", "Fatal error", "A",
        "The condition evaluates to `true`, directly returning `'1'`."
    )
]

for q in php_extra:
    add(17, 'hard', q[0], q[1], 'php', q[2], q[3], q[4], q[5], q[6], q[7], 60, 'PHP Technical')

# Add more C# questions
csharp_extra = [
    (
        "What is the difference between `Task.Run` and `Task.Factory.StartNew` in C#?",
        "// Task.Run vs Task.Factory.StartNew",
        "Task.Run is shorthand for StartNew with default TaskCreationOptions.DenyChildAttach and ThreadPool scheduling",
        "Task.Run creates OS threads directly",
        "StartNew cannot return a result",
        "They use different thread pools", "A",
        "`Task.Run` provides safe defaults for offloading compute-bound work to the ThreadPool without accidental child task attachment."
    ),
    (
        "What is the purpose of the `volatile` keyword in C#?",
        "private volatile bool _isStopped;",
        "Ensures reads and writes are not reordered or cached in CPU registers across threads",
        "Locks the memory block with a mutex",
        "Makes the field immutable",
        "Forces garbage collection", "A",
        "`volatile` in C# inserts memory barriers ensuring the freshest value is read from and written to main memory across threads."
    ),
    (
        "What will be output by `Console.WriteLine(sizeof(decimal));` in C#?",
        "Console.WriteLine(sizeof(decimal));",
        "16 bytes (128-bit high precision financial representation)",
        "8 bytes",
        "4 bytes",
        "32 bytes", "A",
        "`decimal` in C# occupies 16 bytes (128 bits), designed for financial calculations with 28-29 significant digits without binary rounding errors."
    ),
    (
        "What happens when you invoke `GC.Collect()` in production .NET applications?",
        "GC.Collect();",
        "Traces and compacts the managed heap, suspending threads and causing latency spikes",
        "Clears all RAM immediately with zero latency",
        "Frees unmanaged C++ pointers",
        "Restarts the ASP.NET worker process", "A",
        "`GC.Collect()` triggers a full garbage collection cycle, pausing application threads and disrupting generational heap heuristics."
    ),
    (
        "What is the difference between `IEnumerable<T>` and `IQueryable<T>` in Entity Framework / LINQ?",
        "// IEnumerable vs IQueryable",
        "IEnumerable executes queries in-memory on client side; IQueryable translates expression trees into native SQL executed on database server",
        "IEnumerable is asynchronous; IQueryable is synchronous",
        "IQueryable is for XML files only",
        "There is no difference in execution", "A",
        "`IQueryable<T>` uses Expression Trees to translate filters (`.Where()`) into SQL on the DB server. `IEnumerable<T>` downloads rows into memory."
    )
]

for q in csharp_extra:
    add(18, 'hard', q[0], q[1], 'csharp', q[2], q[3], q[4], q[5], q[6], q[7], 60, 'C# Technical')

# Add more Python questions
py_extra = [
    (
        "What is the output of `round(2.5)` versus `round(3.5)` in Python 3?",
        "print(round(2.5), round(3.5))",
        "2 4 (Banker's Rounding: rounds to nearest even integer)",
        "3 4",
        "3 3",
        "2 3", "A",
        "Python 3 uses Banker's Rounding (round half to even) to minimize statistical bias: 2.5 rounds to 2 (even), 3.5 rounds to 4 (even)."
    ),
    (
        "What will be the output of `bool('False')` in Python?",
        "print(bool('False'))",
        "True (any non-empty string is truthy)",
        "False",
        "None",
        "ValueError", "A",
        "In Python, the boolean value of any non-empty string (regardless of content) is always `True`."
    ),
    (
        "What is the difference between `@staticmethod` and `@classmethod` in Python?",
        "// Staticmethod vs Classmethod",
        "`@classmethod` receives the class object (`cls`) as its first argument; `@staticmethod` receives no implicit first argument",
        "`@staticmethod` cannot be called on instances",
        "`@classmethod` creates a new metaclass",
        "They are completely identical", "A",
        "`@classmethod` passes `cls` as the first parameter (useful for factory methods); `@staticmethod` behaves like a plain function inside class namespace."
    ),
    (
        "What is the output of `print([1, 2, 3] * 0)` in Python?",
        "print([1, 2, 3] * 0)",
        "[] (empty list)",
        "[0, 0, 0]",
        "None",
        "ValueError", "A",
        "Multiplying any sequence by a non-positive integer in Python yields an empty sequence of that type (`[]`)."
    ),
    (
        "What does the `nonlocal` keyword do in nested Python functions?",
        "def outer():\n    x = 10\n    def inner(): nonlocal x; x = 20",
        "Binds variable assignment to the nearest enclosing (non-global) scope",
        "Exports variable to global scope",
        "Makes variable thread-safe",
        "Creates a new local variable", "A",
        "`nonlocal` allows modifying a variable in an enclosing outer function scope without declaring it `global`."
    )
]

for q in py_extra:
    add(19, 'hard', q[0], q[1], 'python', q[2], q[3], q[4], q[5], q[6], q[7], 55, 'Python Technical')

# Add more SQL questions
sql_extra = [
    (
        "What is the output of `SELECT COUNT(1)` vs `SELECT COUNT(*)` in standard SQL engines?",
        "SELECT COUNT(1) FROM Employees;",
        "Both are semantically identical and optimize to the same query plan counting all matching rows",
        "COUNT(1) only counts the first column",
        "COUNT(*) is slower in all database engines",
        "COUNT(1) skips NULL primary keys", "A",
        "In modern ANSI SQL engines (MySQL, PostgreSQL, SQL Server), `COUNT(1)` and `COUNT(*)` are optimized identically by the query planner."
    ),
    (
        "What is the difference between `UNION` and `UNION ALL` in SQL?",
        "// UNION vs UNION ALL performance",
        "UNION removes duplicate rows by performing a sort/hash distinct operation; UNION ALL concatenates results directly without deduplication",
        "UNION ALL is slower because it sorts rows",
        "UNION can only combine two tables",
        "UNION ALL discards NULLs", "A",
        "`UNION ALL` combines result sets directly with zero deduplication overhead. `UNION` performs an internal sort/hash to eliminate duplicates."
    ),
    (
        "What is the difference between a Clustered and Non-Clustered index in relational databases?",
        "// Database indexing mechanics",
        "A clustered index defines the physical order of data rows on disk (only 1 per table); a non-clustered index stores a separate sorted structure pointing to table data",
        "A table can have multiple clustered indexes",
        "Non-clustered indexes are stored in RAM only",
        "Clustered indexes cannot be created on numeric columns", "A",
        "Because table rows can only be physically sorted one way on disk, a table can have only ONE clustered index. Non-clustered indexes maintain auxiliary B-Trees."
    ),
    (
        "What does the SQL command `EXPLAIN SELECT ...` provide to database administrators?",
        "EXPLAIN SELECT * FROM Orders WHERE total > 1000;",
        "The query execution plan showing index usage, join algorithms, rows examined, and table scan costs",
        "The execution time in nanoseconds",
        "A syntax error check without schema validation",
        "A list of database table locks", "A",
        "`EXPLAIN` reveals the query planner's execution roadmap, detailing whether indexes were used, full table scans occurred (`ALL`), and row estimates."
    ),
    (
        "What is the purpose of the Foreign Key constraint `ON DELETE CASCADE`?",
        "FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE",
        "Automatically deletes child rows when the referenced parent row is deleted",
        "Prevents deleting parent rows",
        "Sets the foreign key column to NULL",
        "Creates a backup of deleted rows", "A",
        "`ON DELETE CASCADE` guarantees referential integrity by automatically propagating deletions from the parent table to all dependent child records."
    )
]

for q in sql_extra:
    add(20, 'hard', q[0], q[1], 'sql', q[2], q[3], q[4], q[5], q[6], q[7], 55, 'SQL Technical')

# Add more DSA questions
dsa_extra = [
    (
        "What is the time complexity of building a Binary Heap from an arbitrary array of N elements using Heapify?",
        "// Build heap complexity",
        "O(N) linear time (by summing heights of all nodes)",
        "O(N log N)",
        "O(N^2)",
        "O(log N)", "A",
        "Bottom-up heap construction runs in linear time $O(N)$ because the number of nodes at height $h$ is $N / 2^{h+1}$, forming a converging geometric series $\\sum (h / 2^h) = O(N)$."
    ),
    (
        "What is the difference between Dijkstra's Algorithm and the A* Search Algorithm?",
        "// Shortest path heuristics",
        "A* uses a heuristic function h(n) estimating distance to target to guide exploration; Dijkstra is equivalent to A* with h(n) = 0",
        "Dijkstra handles negative edge weights; A* does not",
        "A* works only on trees",
        "Dijkstra is faster in all grid maps", "A",
        "A* incorporates an admissible heuristic $f(n) = g(n) + h(n)$ to bias search toward the goal node, significantly pruning explored states."
    ),
    (
        "What is the time and space complexity of Kadane's Algorithm for Maximum Subarray Sum?",
        "// Kadane's algorithm",
        "O(N) time complexity and O(1) auxiliary space",
        "O(N log N) time and O(N) space",
        "O(N^2) time and O(1) space",
        "O(N) time and O(N) space", "A",
        "Kadane's algorithm iterates through the array once, updating `current_sum = max(arr[i], current_sum + arr[i])` in strictly linear time and constant memory."
    ),
    (
        "What is the optimal algorithm to detect whether an undirected graph is Bipartite?",
        "// Bipartite graph verification",
        "Breadth First Search (BFS) or DFS with 2-coloring",
        "Prim's Minimum Spanning Tree algorithm",
        "Floyd-Warshall algorithm",
        "Dijkstra's algorithm", "A",
        "A graph is bipartite if and only if it contains no odd-length cycles, verified in $O(V + E)$ by attempting to color vertices with 2 alternating colors."
    ),
    (
        "What is the amortized time complexity of Union-Find (Disjoint Set Union) operations with Path Compression and Union by Rank?",
        "// Disjoint Set Union complexity",
        "O(α(N)) where α is the Inverse Ackermann function (practically <= 4 for all realistic inputs)",
        "Strictly O(1)",
        "O(log N)",
        "O(N)", "A",
        "Path compression and union by rank combined yield an amortized time per operation of $O(\\alpha(N))$, where $\\alpha$ grows so slowly that $\\alpha(10^{80}) < 5$."
    )
]

for q in dsa_extra:
    add(21, 'hard', q[0], q[1], 'dsa', q[2], q[3], q[4], q[5], q[6], q[7], 60, 'DSA Technical')

# Add more Innovature Labs PYQs
innovature_extra = [
    (
        "[Innovature Labs Technical PYQ] Innovature Labs C interview: What is the output of bitwise masking to check if a number is a power of 2?",
        "#include <stdio.h>\nint main() {\n    int n = 16;\n    if ((n > 0) && (n & (n - 1)) == 0) printf(\"Power of 2\");\n    else printf(\"Not power of 2\");\n    return 0;\n}",
        "Power of 2", "Not power of 2", "Compilation error", "Infinite loop", "A",
        "A power of 2 has exactly 1 set bit in binary. `n & (n - 1)` clears that single bit, leaving 0. Output: `Power of 2`."
    ),
    (
        "[Innovature Labs Technical PYQ] Innovature Labs Java Round: What does `String.join('-', '2026', '10', '10')` return?",
        "String s = String.join(\"-\", \"2026\", \"10\", \"10\");",
        "2026-10-10", "20261010-", "-2026-10-10", "Compilation error", "A",
        "`String.join(delimiter, elements)` joins elements using the specified delimiter string: `2026-10-10`."
    ),
    (
        "[Innovature Labs Reasoning PYQ] Innovature Labs Aptitude: In an Innovature software company, 70% of engineers code in Java, 60% code in Python, and 50% code in both. What percentage of engineers code in NEITHER?",
        None,
        "20%", "10%", "30%", "15%", "A",
        "By inclusion-exclusion: Total coding in at least one = P(Java) + P(Python) - P(Both) = 70% + 60% - 50% = 80%. Percentage coding in neither = 100% - 80% = 20%."
    ),
    (
        "[Innovature Labs Technical PYQ] Innovature Labs SQL Round: How do you select the top 3 projects with the highest budget in SQL?",
        "SELECT project_name FROM Projects ...",
        "SELECT project_name FROM Projects ORDER BY budget DESC LIMIT 3;",
        "SELECT project_name FROM Projects WHERE budget > 3;",
        "SELECT project_name FROM Projects GROUP BY budget LIMIT 3;",
        "SELECT MAX(budget, 3) FROM Projects;", "A",
        "Sorting by `budget DESC` and restricting with `LIMIT 3` retrieves the 3 highest budget projects."
    ),
    (
        "[Innovature Labs Logic PYQ] Innovature Labs Written Test: What is the angle between the clock hands at 4:20?",
        None,
        "10 degrees", "0 degrees", "20 degrees", "15 degrees", "A",
        "Angle = |30(4) - 5.5(20)| = |120 - 110| = 10 degrees."
    )
]

for q in innovature_extra:
    add(22, 'hard', q[0], q[1], 'innovature', q[2], q[3], q[4], q[5], q[6], q[7], 65, 'Innovature Labs PYQ', 1, 'Innovature Labs')

# Add more Hard Logic Puzzles (subtopic 23)
lr_extra = [
    (
        "Circular Facing Puzzle: Six friends (A, B, C, D, E, F) sit around a circular table. Three face inward towards the center and three face outward. A sits opposite B and both face inward. C sits to the immediate right of A and faces outward. Who sits to the immediate left of B?",
        None,
        "D or E (deducible from remaining constraints)", "C", "A", "B", "A",
        "Constraint resolution positions C to right of A. A and B are opposite facing inward, with remaining outward facers placed symmetrically."
    ),
    (
        "Cryptarithm: If TWO + TWO = FOUR, and O = 4, R = 8, what digit is F?",
        None,
        "1 (since the carry from the hundred's column into thousands must be 1)",
        "2", "3", "4", "A",
        "In any addition of two 3-digit numbers yielding a 4-digit number, the leading carry digit F must be 1."
    ),
    (
        "Floor Arrangement: 7 people (P, Q, R, S, T, U, V) live on floors 1 to 7. P lives on an odd floor above floor 3. There are three floors between P and T. Q lives immediately below T. On which floor does Q live?",
        None,
        "Floor 2 (if P is on Floor 5, T is on Floor 1, but Q is below T, so P is on Floor 7, T is on Floor 3, Q is on Floor 2)",
        "Floor 4", "Floor 6", "Floor 1", "A",
        "P is on odd floor above 3 (5 or 7). If P is on 7, 3 floors between P and T puts T on floor 3. Q is immediately below T -> Q lives on Floor 2!"
    )
]

for q in lr_extra:
    add(23, 'hard', q[0], q[1], 'lr', q[2], q[3], q[4], q[5], q[6], q[7], 75, 'Hard Logic Puzzle', 0, None)

print(f"Final total questions count: {len(questions)}")
with open("data/technical_and_hard_questions.json", "w", encoding="utf-8") as f:
    json.dump(questions, f, indent=2)

print("Updated data/technical_and_hard_questions.json!")
