import json

with open("data/technical_and_hard_questions.json", "r", encoding="utf-8") as f:
    qs = json.load(f)

more_tech = [
    # C questions (14)
    (14, 'hard', 'What is the output of the following C code demonstrating enum values?', '#include <stdio.h>\nenum Day { MON=1, TUE, WED=5, THU };\nint main() {\n    printf("%d %d", TUE, THU);\n    return 0;\n}', 'c', '2 6', '2 5', '1 5', '0 6', 'A', 'Enums auto-increment by 1 from previous value. TUE = MON+1 = 2. THU = WED+1 = 6. Output: 2 6.', 50, 'C Technical', 0, None),
    (14, 'hard', 'What will this C code output regarding string comparison?', '#include <stdio.h>\n#include <string.h>\nint main() {\n    char s1[] = "abc";\n    char s2[] = "abd";\n    printf("%d", strcmp(s1, s2) < 0);\n    return 0;\n}', 'c', '1', '0', '-1', 'Compilation Error', 'A', 'strcmp compares ASCII values. At index 2, \'c\' (99) < \'d\' (100), returning negative. < 0 is true (1).', 50, 'C Technical', 0, None),
    (14, 'hard', 'What does void * pointer arithmetic evaluate to in GCC extension vs standard ISO C?', 'void *p = (void*)1000; p++;', 'c', 'In ISO C: Illegal; In GCC: treated as 1 byte increment (points to 1001)', 'Always 4 bytes', 'Always 8 bytes', 'Compilation error in GCC', 'A', 'ISO C prohibits arithmetic on void*. GCC provides an extension treating sizeof(void) as 1.', 55, 'C Technical', 0, None),
    (14, 'hard', 'What is printed by this bitwise toggle code in C?', '#include <stdio.h>\nint main() {\n    int a = 10; // 1010\n    a ^= (1 << 2);\n    printf("%d", a);\n    return 0;\n}', 'c', '14', '10', '6', '12', 'A', '10 in binary is 1010. 1 << 2 is 0100 (4). 1010 ^ 0100 = 1110 (14). Output: 14.', 50, 'C Technical', 0, None),
    (14, 'hard', 'What is the effect of declaring a function static in C?', 'static void helper() {}', 'c', 'Restricts the function scope to its translation unit (internal linkage), preventing symbol collision', 'Makes the function execute only once', 'Allocates it on stack', 'Makes it reentrant', 'A', 'static on C functions enforces internal linkage, making it invisible to the linker outside that .c file.', 50, 'C Technical', 0, None),

    # C++ (15)
    (15, 'hard', 'What does dynamic_cast return in C++ when casting an incompatible polymorphic pointer?', 'Base *b = new Base(); Derived *d = dynamic_cast<Derived*>(b);', 'cpp', 'Returns nullptr', 'Throws std::bad_cast', 'Throws NullPointerException', 'Undefined behavior', 'A', 'dynamic_cast on pointer types returns nullptr on failure. (On reference types, it throws std::bad_cast).', 55, 'C++ Technical', 0, None),
    (15, 'hard', 'What does mutable member variable allow in a C++ class?', 'class Logger { mutable int counter = 0; public: void log() const { counter++; } };', 'cpp', 'Allows the member to be modified even inside const member functions or on const objects', 'Makes the variable thread-safe', 'Forces heap allocation', 'Prevents destruction', 'A', 'mutable allows specific members (like caches or mutexes) to be altered within const member functions.', 55, 'C++ Technical', 0, None),
    (15, 'hard', 'What is the output of std::move on a const object in C++?', 'const std::string s = "hello"; std::string target = std::move(s);', 'cpp', 'Invokes the COPY constructor because rvalue references cannot bind to non-const rvalues', 'Invokes move constructor', 'Compilation error', 'Clears s to empty', 'A', 'Moving a const object silently falls back to copy construction because move constructors require non-const rvalue references (T&&).', 60, 'C++ Technical', 0, None),

    # Java (16)
    (16, 'hard', 'What will be output by System.out.println(String.valueOf(null)); in Java?', 'System.out.println(String.valueOf(null));', 'java', 'Throws NullPointerException (resolves to char[] overload)', 'Prints "null"', 'Prints ""', 'Compilation error', 'A', 'String.valueOf(char[] data) is more specific than String.valueOf(Object obj). Passing null resolves to char[], throwing a NullPointerException!', 65, 'Java Technical', 0, None),
    (16, 'hard', 'What is the time complexity of ArrayList.contains(obj) in Java?', 'List<String> list = new ArrayList<>(); list.contains("test");', 'java', 'O(N) linear scan', 'O(1)', 'O(log N)', 'O(N log N)', 'A', 'ArrayList.contains() performs a linear scan through the array using .equals(), taking O(N) time.', 45, 'Java Technical', 0, None),
    (16, 'hard', 'What does @FunctionalInterface annotation enforce in Java 8+?', '@FunctionalInterface interface Calculator { int calc(int a, int b); }', 'java', 'Ensures the interface has exactly ONE abstract method (SAM), generating a compile error if breached', 'Makes all methods default', 'Prevents lambda implementation', 'Forces serialization', 'A', '@FunctionalInterface informs the compiler that the interface has exactly one abstract method, usable for lambdas.', 50, 'Java Technical', 0, None),

    # PHP (17)
    (17, 'hard', 'What does declare(strict_types=1); enforce in PHP files?', '<?php declare(strict_types=1); ?>', 'php', 'Enforces strict type checking on function arguments and return types for calls made within that file', 'Enforces strict checking globally across all files', 'Converts all variables to typed memory', 'Disables error reporting', 'A', 'declare(strict_types=1) affects function calls originating from that file, disabling scalar type coercion.', 55, 'PHP Technical', 0, None),
    (17, 'hard', 'What is the output of is_numeric("  123.45  ") in PHP?', 'var_dump(is_numeric("  123.45  "));', 'php', 'bool(true) (whitespace is trimmed by is_numeric)', 'bool(false)', 'null', 'Notice', 'A', 'is_numeric() ignores leading and trailing whitespace when determining numeric validity, returning true.', 50, 'PHP Technical', 0, None),

    # C# (18)
    (18, 'hard', 'What is the output of string concatenation in a loop vs StringBuilder in C#?', '// String concatenation vs StringBuilder', 'csharp', 'String concatenation creates a new string object and copies characters on every iteration; StringBuilder modifies its internal buffer in-place', 'They have identical performance', 'String is always faster for 10,000 iterations', 'StringBuilder causes memory leaks', 'A', 'Because strings are immutable, s += x inside a loop creates O(N^2) memory copies. StringBuilder uses an internal buffer.', 55, 'C# Technical', 0, None),
    (18, 'hard', 'What does Task.WhenAll return in C# when one or more tasks fail with exceptions?', 'await Task.WhenAll(task1, task2);', 'csharp', 'Aggregates all exceptions into an AggregateException property, rethrowing the first exception upon await', 'Silently ignores failed tasks', 'Returns null', 'Terminates process', 'A', 'Awaiting Task.WhenAll rethrows the first thrown exception, while the Task\'s Exception property contains all aggregated faults.', 60, 'C# Technical', 0, None),

    # Python (19)
    (19, 'hard', 'What is the output of type(type) in Python?', 'print(type(type))', 'python', '<class \'type\'> (type is an instance of itself)', '<class \'object\'>', '<class \'class\'>', 'SyntaxError', 'A', 'In Python\'s object model, type is the metaclass of all types, including itself! type(type) is <class \'type\'>.', 55, 'Python Technical', 0, None),
    (19, 'hard', 'What will be output by print(set([1, 2, 2, 3, 1])) in Python?', 'print(set([1, 2, 2, 3, 1]))', 'python', '{1, 2, 3}', '[1, 2, 3]', '{1, 2, 2, 3}', 'TypeError', 'A', 'Sets in Python store unique elements only, automatically discarding duplicates: {1, 2, 3}.', 45, 'Python Technical', 0, None),

    # SQL (20)
    (20, 'hard', 'What is the output of SELECT 1 WHERE 1 = 1 AND (NULL IS NOT NULL OR 2 = 2); in SQL?', 'SELECT 1 WHERE 1 = 1 AND (NULL IS NOT NULL OR 2 = 2);', 'sql', '1 (because 2 = 2 is TRUE, making the OR clause TRUE)', '0 rows', 'NULL', 'Syntax Error', 'A', 'NULL IS NOT NULL is FALSE. FALSE OR TRUE evaluates to TRUE. TRUE AND TRUE is TRUE, returning 1.', 50, 'SQL Technical', 0, None),
    (20, 'hard', 'What is the purpose of an Index Seek versus an Index Scan in SQL?', '-- Query performance analysis', 'sql', 'Index Seek navigates the B-Tree directly to specific rows; Index Scan reads all rows in the index structure sequentially', 'Index Scan is always faster', 'Index Seek is for tables without keys', 'There is no difference', 'A', 'Index Seek traverses B-Tree root to leaf (O(log N)). Index Scan reads through the entire index (O(N)).', 55, 'SQL Technical', 0, None),

    # DSA (21)
    (21, 'hard', 'What is the height of an AVL tree with N nodes in the worst case?', '// AVL Tree worst-case height', 'dsa', 'Approximately 1.44 * log2(N)', '2 * N', 'log2(N) exactly', 'N / 2', 'A', 'In the worst case (Fibonacci trees), the height of an AVL tree is bounded by ~ 1.44 log2 N.', 55, 'DSA Technical', 0, None),
    (21, 'hard', 'What is the time complexity of searching an element in a Skip List?', '// Skip List complexity', 'dsa', 'O(log N) average time complexity using multiple linked levels', 'O(N) always', 'O(1)', 'O(N^2)', 'A', 'Skip Lists maintain layered forward pointers that skip elements, achieving O(log N) average search time.', 55, 'DSA Technical', 0, None),

    # Innovature Labs PYQs (22)
    (22, 'hard', '[Innovature Labs Technical PYQ] Innovature Labs interview: What will be the output of int x = 1; printf("%d %d %d", x, ++x, x++);?', '#include <stdio.h>\nint main() {\n    int x = 1;\n    printf("%d %d %d", x, ++x, x++);\n    return 0;\n}', 'innovature', 'Undefined Behavior (unsequenced modification of x in function arguments)', '1 2 2', '3 3 1', '3 2 1', 'A', 'Modifying and reading x multiple times in function argument list invokes Undefined Behavior in standard C.', 65, 'Innovature Labs PYQ', 1, 'Innovature Labs'),
    (22, 'hard', '[Innovature Labs Technical PYQ] Innovature Labs Java: What is the output of comparing two new Boolean objects with ==?', 'Boolean b1 = new Boolean(true); Boolean b2 = new Boolean(true); System.out.println(b1 == b2);', 'innovature', 'false (distinct heap references)', 'true', 'Compilation error', 'ClassCastException', 'A', 'new Boolean(true) allocates distinct heap objects; == compares references, returning false.', 50, 'Innovature Labs PYQ', 1, 'Innovature Labs'),
    (22, 'hard', '[Innovature Labs SQL PYQ] Innovature Labs placement: How to retrieve all departments that have NO assigned employees in SQL?', 'SELECT d.dept_name FROM Departments d ...', 'innovature', 'SELECT d.dept_name FROM Departments d LEFT JOIN Employees e ON d.id = e.dept_id WHERE e.id IS NULL;', 'SELECT d.dept_name FROM Departments d INNER JOIN Employees e ON d.id = e.dept_id;', 'SELECT d.dept_name FROM Departments d WHERE e.dept_id = 0;', 'SELECT d.dept_name FROM Departments d HAVING COUNT(e.id) = 0;', 'A', 'LEFT JOIN preserves all departments; WHERE e.id IS NULL isolates departments with zero employees.', 60, 'Innovature Labs PYQ', 1, 'Innovature Labs'),
    (22, 'hard', '[Innovature Labs Quant PYQ] Innovature Labs Aptitude: A train 240 m long passes a telegraph pole in 16 seconds. How long will it take to pass a platform 420 m long?', 'None', 'innovature', '44 seconds', '40 seconds', '48 seconds', '38 seconds', 'A', 'Train speed = 240 / 16 = 15 m/s. Total distance for platform = 240 + 420 = 660 m. Time = 660 / 15 = 44 seconds.', 65, 'Innovature Labs PYQ', 1, 'Innovature Labs')
]

for row in more_tech:
    qs.append({
        'subtopic_id': row[0],
        'difficulty': row[1],
        'question_text': row[2],
        'code_snippet': row[3],
        'language': row[4],
        'option_a': row[5],
        'option_b': row[6],
        'option_c': row[7],
        'option_d': row[8],
        'correct_option': row[9],
        'explanation': row[10],
        'est_time_seconds': row[11],
        'source_tag': row[12],
        'is_company_pyq': row[13],
        'company_name': row[14]
    })

tech_cnt = len([q for q in qs if q['subtopic_id'] in range(14, 23)])
print(f"Total questions: {len(qs)}, Technical count: {tech_cnt}")

with open("data/technical_and_hard_questions.json", "w", encoding="utf-8") as f:
    json.dump(qs, f, indent=2)
print("Updated successfully!")
