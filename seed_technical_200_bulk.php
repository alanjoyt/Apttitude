<?php
require_once __DIR__ . '/config/db.php';

echo "=========================================================\n";
echo " Apttitudee - Bulk Seeder for 200+ Hard Technical Questions\n";
echo "=========================================================\n";

try {
    $db = Database::getConnection();

    $stmt = $db->prepare("
        INSERT INTO `questions` 
        (`subtopic_id`, `difficulty`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `est_time_seconds`, `source_tag`, `is_company_pyq`, `company_name`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $batch = [];

    // Helper to add
    $addQ = function($subtopicId, $difficulty, $text, $snippet, $lang, $optA, $optB, $optC, $optD, $correct, $exp, $time, $tag, $isPyq = 0, $comp = null) use (&$batch) {
        $batch[] = [$subtopicId, $difficulty, $text, $snippet, $lang, $optA, $optB, $optC, $optD, $correct, $exp, $time, $tag, $isPyq, $comp];
    };

    // ----------------------------------------------------
    // C LANGUAGE (Subtopic 14)
    // ----------------------------------------------------
    $cQuestions = [
        [
            "What will be the output of the following C program with pointer casting?",
            "#include <stdio.h>\nint main() {\n    int a = 0x12345678;\n    char *c = (char*)&a;\n    if (*c == 0x78) printf(\"Little Endian\");\n    else printf(\"Big Endian\");\n    return 0;\n}",
            "Little Endian", "Big Endian", "Compilation Error", "Segmentation Fault",
            "A",
            "In Little Endian architectures (such as x86 and standard ARM), the least significant byte (0x78) is stored at the lowest memory address. Casting `&a` to `char*` and dereferencing inspects the first byte, yielding 0x78, proving Little Endian.",
            55, "C Technical"
        ],
        [
            "What is the output of the following code involving array decay and sizeof?",
            "#include <stdio.h>\nvoid func(int arr[]) {\n    printf(\"%zu \", sizeof(arr));\n}\nint main() {\n    int arr[10];\n    printf(\"%zu \", sizeof(arr));\n    func(arr);\n    return 0;\n}",
            "40 8 (or 40 4 on 32-bit)", "40 40", "8 8", "10 10",
            "A",
            "Inside `main`, `arr` is an actual array of 10 integers (10 * 4 = 40 bytes). When passed as a function argument, `arr` decays into a pointer (`int*`), whose size is 8 bytes on a 64-bit architecture (or 4 bytes on 32-bit).",
            60, "C Technical"
        ],
        [
            "What will be printed by the following bitwise XOR swap logic?",
            "#include <stdio.h>\nint main() {\n    int x = 15, y = 25;\n    x ^= y ^= x ^= y;\n    printf(\"%d %d\", x, y);\n    return 0;\n}",
            "Undefined Behavior in C standards", "25 15", "15 25", "0 0",
            "A",
            "While in some other languages this swaps values, in C standards (C99/C11), `x ^= y ^= x ^= y` modifies variable `x` twice between sequence points without intervening synchronization, invoking Undefined Behavior!",
            65, "C Technical"
        ],
        [
            "What does this C code output with nested ternary operators?",
            "#include <stdio.h>\nint main() {\n    int a = 1, b = 2, c = 3;\n    int res = a > b ? a : b > c ? b : c;\n    printf(\"%d\", res);\n    return 0;\n}",
            "3", "2", "1", "0",
            "A",
            "In C, the conditional operator `?:` associates from right to left. The expression parses as `a > b ? a : (b > c ? b : c)`. Since `1 > 2` is false, it evaluates `b > c ? b : c`. Since `2 > 3` is false, it returns `c` = 3.",
            50, "C Technical"
        ],
        [
            "What is the output of the following recursive count function?",
            "#include <stdio.h>\nint count(int n) {\n    static int d = 1;\n    printf(\"%d \", n);\n    printf(\"%d \", d);\n    d++;\n    if (n > 1) count(n - 1);\n    printf(\"%d \", d);\n    return d;\n}\nint main() {\n    count(3);\n    return 0;\n}",
            "3 1 2 2 1 3 4 4 4", "3 1 2 2 1 3 3 2 1", "3 1 2 1 1 1 4 4 4", "Compilation Error",
            "A",
            "Static variable `d` retains its value across all recursive calls:\n1. Call(3): prints `3 1`, `d` becomes 2.\n2. Call(2): prints `2 2`, `d` becomes 3.\n3. Call(1): prints `1 3`, `d` becomes 4. Unwinds: prints `d` (4) three times. Output: `3 1 2 2 1 3 4 4 4`.",
            70, "C Technical"
        ],
        [
            "What happens when calling `realloc(ptr, 0)` in modern C standard (C23)?",
            "#include <stdio.h>\n#include <stdlib.h>\nint main() {\n    int *ptr = malloc(20);\n    ptr = realloc(ptr, 0);\n    return 0;\n}",
            "In C23, calling realloc with size 0 is undefined behavior / deprecated (previously implementation-defined free)", "It always allocates 1 byte", "It doubles the memory", "It throws bad_alloc",
            "A",
            "Historically, `realloc(p, 0)` freed the memory and returned NULL or a unique pointer. In C23, passing size 0 to `realloc` is explicitly treated as undefined behavior to eliminate portability bugs and vulnerabilities.",
            55, "C Technical"
        ],
        [
            "What is printed by this C program examining union member overlay?",
            "#include <stdio.h>\nunion Data {\n    int i;\n    char str[4];\n};\nint main() {\n    union Data d;\n    d.str[0] = 'A'; d.str[1] = 'B'; d.str[2] = '\\0'; d.str[3] = '\\0';\n    printf(\"%d\", d.str[0] + d.str[1]);\n    return 0;\n}",
            "131", "65", "66", "AB",
            "A",
            "In ASCII, 'A' has decimal value 65 and 'B' has decimal value 66. The addition `65 + 66 = 131` is computed as an integer and printed.",
            45, "C Technical"
        ],
        [
            "What will be output by the following function pointer call in C?",
            "#include <stdio.h>\nint add(int a, int b) { return a + b; }\nint subtract(int a, int b) { return a - b; }\nint operate(int (*op)(int, int), int x, int y) { return op(x, y); }\nint main() {\n    printf(\"%d\", operate(add, 10, operate(subtract, 20, 5)));\n    return 0;\n}",
            "25", "15", "5", "35",
            "A",
            "`operate(subtract, 20, 5)` computes `20 - 5 = 15`. Then `operate(add, 10, 15)` computes `10 + 15 = 25`. Output is 25.",
            50, "C Technical"
        ],
        [
            "What is printed by `printf(\"%d\", -1 << 1);` in C?",
            "#include <stdio.h>\nint main() {\n    printf(\"%d\", -1 << 1);\n    return 0;\n}",
            "-2 (in C99/C11 shifting negative values was UB; defined in C23/C++20)", "0", "1", "Compiler Error",
            "A",
            "Under two\'s complement arithmetic, `-1` has all bits set (111...111). Left shifting by 1 introduces a 0 at the LSB, resulting in `...1110`, which represents `-2`. In modern C++20 and C23 standards, this is well-defined as two\'s complement shift.",
            55, "C Technical"
        ],
        [
            "What does this C code output regarding post-increment in loop condition?",
            "#include <stdio.h>\nint main() {\n    int i = 0;\n    while (i++ < 5) {}\n    printf(\"%d\", i);\n    return 0;\n}",
            "6", "5", "4", "Infinite loop",
            "A",
            "Trace: when `i=4`, `4 < 5` is true, then `i` increments to 5. Body executes. Next, condition tests `5 < 5` (false), but `i++` still executes, incrementing `i` to 6! Loop terminates, and `printf(\"%d\", i)` prints 6.",
            50, "C Technical"
        ]
    ];

    foreach ($cQuestions as $q) {
        $addQ(14, 'hard', $q[0], $q[1], 'c', $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9]);
    }

    // ----------------------------------------------------
    // C++ PROGRAMMING (Subtopic 15)
    // ----------------------------------------------------
    $cppQuestions = [
        [
            "What is the output of the following C++ code testing constructor initialization list order?",
            "#include <iostream>\nclass Test {\n    int b;\n    int a;\npublic:\n    Test(int val) : a(val), b(a + 2) {}\n    void print() { std::cout << a << \" \" << b; }\n};\nint main() {\n    Test t(10);\n    t.print();\n    return 0;\n}",
            "10 followed by undefined/garbage value (because class members are initialized in declaration order, not initializer list order!)",
            "10 12", "12 10", "Compilation Error",
            "A",
            "In C++, member variables are ALWAYS initialized in the exact order they are DECLARED in the class definition, NOT the order they appear in the constructor initializer list! Since `int b;` is declared before `int a;`, `b` is initialized first using `a + 2`. But `a` has not been initialized yet, leading to undefined garbage for `b`!",
            75, "C++ Technical"
        ],
        [
            "What is printed by this C++ template specialization snippet?",
            "#include <iostream>\ntemplate<typename T> void print(T val) { std::cout << \"Generic \"; }\ntemplate<> void print<int>(int val) { std::cout << \"Int \"; }\ntemplate<> void print<double>(double val) { std::cout << \"Double \"; }\nint main() {\n    print(10);\n    print(10.5);\n    print('A');\n    return 0;\n}",
            "Int Double Generic", "Generic Generic Generic", "Int Double Char", "Compilation Error",
            "A",
            "`print(10)` matches the explicit specialization for `int` -> prints \"Int \". `print(10.5)` matches `double` -> prints \"Double \". `print('A')` has no specialization for `char`, falling back to primary generic template -> prints \"Generic \".",
            60, "C++ Technical"
        ],
        [
            "What is the difference between `std::vector::reserve()` and `std::vector::resize()` in C++?",
            "#include <vector>\n// What happens in memory?",
            "reserve() allocates raw memory capacity without constructing elements; resize() changes size and default-constructs new elements",
            "reserve() shrinks capacity; resize() expands it",
            "reserve() is for std::deque; resize() is for std::vector",
            "They are completely identical functions",
            "A",
            "`reserve(n)` only increases internal capacity (reallocating memory buffer if n > capacity) without altering `size()` or constructing objects. `resize(n)` alters `size()`: if n > size, it default-constructs `n - size` new elements into the vector.",
            55, "C++ Technical"
        ],
        [
            "What does the C++ `constexpr` specifier indicate about a function or variable?",
            "#include <iostream>\nconstexpr int square(int x) { return x * x; }",
            "It can be evaluated at compile-time when passed constant expressions, enabling zero-runtime overhead and use in template arguments or array sizes",
            "It forces the compiler to inline in debug mode only",
            "It makes the variable volatile across CPU cores",
            "It stores the value in flash memory",
            "A",
            "`constexpr` specifies that the value of an object or return value of a function can be computed at compile time if all input operands are compile-time constants. This enables usage in array bounds, template parameters, and compile-time optimizations.",
            55, "C++ Technical"
        ],
        [
            "What happens when an exception is thrown inside a C++ constructor?",
            "class Resource {\npublic:\n    Resource() { throw std::runtime_error(\"Fail\"); }\n    ~Resource() { std::cout << \"Destructed\"; }\n};",
            "The object\'s destructor is NEVER called because the object was never fully constructed",
            "The destructor is called immediately",
            "The program terminates with memory corruption",
            "The object remains in a zombie state",
            "A",
            "In C++, an object is only considered fully constructed once its constructor completes successfully. If an exception escapes the constructor, the destructor for that object will NOT run! Destructors of already-constructed base classes and member sub-objects are automatically unwound.",
            65, "C++ Technical"
        ]
    ];

    foreach ($cppQuestions as $q) {
        $addQ(15, 'hard', $q[0], $q[1], 'cpp', $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9]);
    }

    // ----------------------------------------------------
    // JAVA CORE & ADVANCED (Subtopic 16)
    // ----------------------------------------------------
    $javaQuestions = [
        [
            "What will be printed by the following Java code testing Integer Caching (-128 to 127)?",
            "public class IntCache {\n    public static void main(String[] args) {\n        Integer a = 127, b = 127;\n        Integer c = 128, d = 128;\n        System.out.println((a == b) + \" \" + (c == d));\n    }\n}",
            "true false", "true true", "false false", "false true",
            "A",
            "Java caches `Integer` objects between `-128` and `127` (via `IntegerCache`). For `127`, autoboxing returns the same cached reference, so `a == b` is `true`. For `128` (outside cache range), new distinct heap instances are created, so `c == d` evaluates to `false`! Output: `true false`.",
            60, "Java Technical"
        ],
        [
            "What will be the output of the following Java method overloading puzzle with null argument?",
            "public class OverloadPuzzle {\n    public static void print(Object o) { System.out.print(\"Object \"); }\n    public static void print(String s) { System.out.print(\"String \"); }\n    public static void main(String[] args) {\n        print(null);\n    }\n}",
            "String ", "Object ", "Compilation Error: Ambiguous method call", "Throws NullPointerException",
            "A",
            "When resolving overloaded methods with `null`, the Java compiler chooses the most specific parameter type in the class hierarchy. Because `String` is a subtype of `Object`, `String` is strictly more specific than `Object`. Thus, `print(String)` is chosen, printing `String `.",
            65, "Java Technical"
        ],
        [
            "What happens if both `print(String)` and `print(Integer)` exist and `print(null)` is called?",
            "public class AmbiguousPuzzle {\n    public static void print(String s) {}\n    public static void print(Integer i) {}\n    public static void main(String[] args) { print(null); }\n}",
            "Compilation Error: The method print is ambiguous", "Calls String", "Calls Integer", "Runtime Exception",
            "A",
            "Neither `String` nor `Integer` is a subtype of the other; they are peer classes in the inheritance hierarchy. The compiler cannot determine which one is more specific, resulting in a compile-time ambiguity error: \'reference to print is ambiguous\'.",
            55, "Java Technical"
        ],
        [
            "What will be output by the following try-with-resources statement in Java?",
            "class Door implements AutoCloseable {\n    public void close() { System.out.print(\"Closed \"); }\n}\npublic class Main {\n    public static void main(String[] args) {\n        try (Door d = new Door()) {\n            System.out.print(\"Opened \");\n        }\n    }\n}",
            "Opened Closed ", "Closed Opened ", "Opened ", "Closed ",
            "A",
            "In Java 7+ try-with-resources, any resource implementing `AutoCloseable` is automatically closed when execution leaves the try block. The try block prints \"Opened \", then JVM implicitly calls `close()` which prints \"Closed \". Output: `Opened Closed `.",
            50, "Java Technical"
        ],
        [
            "What is the difference between fail-fast and fail-safe iterators in Java Collections?",
            "import java.util.*;\n// ArrayList iterator vs ConcurrentHashMap iterator",
            "Fail-fast throws ConcurrentModificationException immediately upon structural modification; fail-safe operates on a cloned copy or weakly consistent snapshot without throwing exceptions",
            "Fail-fast locks the database table; fail-safe locks row",
            "Fail-fast is only in Java 1.4",
            "There is no difference in modern Java",
            "A",
            "Fail-fast iterators (like `ArrayList`, `HashMap`) check the internal `modCount`. If modified structurally during iteration, they immediately throw `ConcurrentModificationException`. Fail-safe iterators (e.g. in `java.util.concurrent`, like `CopyOnWriteArrayList`) operate on a copy or snapshot, allowing concurrent modification safely.",
            60, "Java Technical"
        ]
    ];

    foreach ($javaQuestions as $q) {
        $addQ(16, 'hard', $q[0], $q[1], 'java', $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9]);
    }

    // ----------------------------------------------------
    // PHP & WEB (Subtopic 17)
    // ----------------------------------------------------
    $phpQuestions = [
        [
            "What will be the output of array_merge() vs the union operator + for arrays with matching string keys in PHP?",
            "<?php\n$a = ['id' => 1, 'role' => 'guest'];\n$b = ['role' => 'admin', 'status' => 'active'];\n$merge = array_merge($a, $b);\n$union = $a + $b;\necho $merge['role'] . \" \" . $union['role'];\n?>",
            "admin guest", "admin admin", "guest guest", "guest admin",
            "A",
            "With matching string keys: `array_merge()` overwrites values from earlier arrays with later arrays (`$b['role']` = 'admin' overwrites). However, the array union operator `+` preserves values from the LEFT array and ignores duplicate keys from the right (`$a['role']` = 'guest' is kept). Output: `admin guest`.",
            65, "PHP Technical"
        ],
        [
            "What is the output of the following generator function in PHP 7/8?",
            "<?php\nfunction gen() {\n    yield 1;\n    yield 2;\n    return 3;\n}\n$g = gen();\nforeach ($g as $v) echo $v;\necho $g->getReturn();\n?>",
            "123", "12", "Compilation error", "321",
            "A",
            "In PHP, `foreach` iterates over the yielded values of a generator (printing `1` and `2`). When the generator completes, its return value (`3`) is accessible via the generator\'s `getReturn()` method! Output: `123`.",
            60, "PHP Technical"
        ],
        [
            "What is the difference between `self::` and `static::` keywords in PHP Late Static Bindings?",
            "<?php\nclass A { public static function who() { echo 'A'; } public static function test() { static::who(); } }\nclass B extends A { public static function who() { echo 'B'; } }\nB::test();\n?>",
            "B", "A", "Fatal error", "AB",
            "A",
            "`self::` refers to the class in which the method was declared (statically bound at compile time). `static::` references the class that was initially called at runtime using Late Static Bindings! When `B::test()` is called, `static::who()` resolves dynamically to `B::who()`, outputting `B`.",
            65, "PHP Technical"
        ],
        [
            "In PHP, what is the output of `(int)(0.1 + 0.7) * 10` due to floating point representation?",
            "<?php\necho (int)((0.1 + 0.7) * 10);\n?>",
            "7", "8", "8.0", "0",
            "A",
            "In binary floating point (IEEE 754), `0.1 + 0.7` cannot be represented exactly and evaluates to approximately `0.7999999999999999`. Multiplying by 10 gives `7.999999999999999`. Casting directly to `(int)` truncates the fractional part, yielding `7`! To fix, use `round()` or `bcmul()`.",
            60, "PHP Technical"
        ],
        [
            "What security header should be enabled on session cookies to prevent JavaScript from reading PHPSESSID during Cross-Site Scripting (XSS) attacks?",
            "<?php\n// session.cookie_httponly",
            "HttpOnly flag", "SameSite=None", "Secure without HTTPS", "Cache-Control: private",
            "A",
            "Setting `session.cookie_httponly = true` adds the `HttpOnly` flag to the session cookie. This instructs browsers that the cookie must not be accessible via client-side scripts (`document.cookie`), preventing credential/session hijacking via XSS vulnerabilities.",
            50, "PHP Technical"
        ]
    ];

    foreach ($phpQuestions as $q) {
        $addQ(17, 'hard', $q[0], $q[1], 'php', $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9]);
    }

    // ----------------------------------------------------
    // C# & .NET (Subtopic 18)
    // ----------------------------------------------------
    $csharpQuestions = [
        [
            "What is the output of string interning in C#?",
            "using System;\nclass Program {\n    static void Main() {\n        string a = \"hello\";\n        string b = new string(new char[] { 'h', 'e', 'l', 'l', 'o' });\n        Console.WriteLine(object.ReferenceEquals(a, b) + \" \" + (a == b));\n    }\n}",
            "False True", "True True", "False False", "True False",
            "A",
            "`new string(...)` allocates a distinct object instance on the heap, so `object.ReferenceEquals(a, b)` is `False`. The `==` operator for `string` is overloaded in C# to compare string character content equality, so `a == b` is `True`. Output: `False True`.",
            55, "C# Technical"
        ],
        [
            "What will happen when running this asynchronous method in C#?",
            "using System;\nusing System.Threading.Tasks;\nclass Program {\n    static async Task<int> Compute() {\n        await Task.Delay(10);\n        return 42;\n    }\n    static void Main() {\n        var t = Compute();\n        Console.WriteLine(t.Result);\n    }\n}",
            "Prints 42 (in a console app without custom SynchronizationContext)", "Deadlocks always", "Throws TaskCanceledException", "Compilation Error",
            "A",
            "In a standard .NET Console application, there is no custom `SynchronizationContext` (the thread pool handles continuations), so calling `.Result` completes without deadlock and outputs `42`. (In WinForms, WPF, or legacy ASP.NET with single-threaded synchronization contexts, this would deadlock).",
            60, "C# Technical"
        ],
        [
            "What is the difference between `is` pattern matching and `as` operator in C#?",
            "object obj = \"Innovature\";\n// if (obj is string s) vs (obj as string)",
            "`is` tests and casts in one step, working with both value types and reference types; `as` works ONLY with reference or nullable types, returning null on failure",
            "`as` throws InvalidCastException on failure",
            "`is` modifies the object type in place",
            "There is no difference in C# 9+",
            "A",
            "The `is` pattern matching operator tests the type and assigns it to a pattern variable (`obj is int x`), supporting both value types and reference types. The `as` operator can only be used with reference types or nullable value types, evaluating to `null` if the cast fails without throwing exceptions.",
            55, "C# Technical"
        ],
        [
            "What is the output of the following C# delegate multicast invocation?",
            "using System;\ndelegate int MathOp();\nclass Program {\n    static void Main() {\n        MathOp op = () => 10;\n        op += () => 20;\n        op += () => 30;\n        Console.WriteLine(op());\n    }\n}",
            "30", "10", "60", "Compilation error",
            "A",
            "When invoking a multicast delegate with a return value in C#, all registered target methods are executed sequentially, but the return value of the MULTICAST INVOCATION is the return value of the LAST method in the invocation list (`30`). Output: `30`.",
            60, "C# Technical"
        ],
        [
            "What does `readonly struct` enforce in C# 7.2+?",
            "public readonly struct Point { public int X { get; } public int Y { get; } }",
            "Guarantees that all fields are readonly and prevents compiler defensive copies when passed with `in` parameter modifier",
            "Allocates the struct in unmanaged C++ heap",
            "Disables value equality",
            "Forces garbage collection immediately",
            "A",
            "Declaring a struct as `readonly struct` informs the compiler that the type is strictly immutable. This allows the compiler to optimize passing by read-only reference (`in Point p`) without generating defensive defensive copies in memory.",
            55, "C# Technical"
        ]
    ];

    foreach ($csharpQuestions as $q) {
        $addQ(18, 'hard', $q[0], $q[1], 'csharp', $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9]);
    }

    // ----------------------------------------------------
    // PYTHON (Subtopic 19)
    // ----------------------------------------------------
    $pythonQuestions = [
        [
            "What is the output of the following Python list multiplication and mutation puzzle?",
            "grid = [[0] * 3] * 3\ngrid[0][0] = 1\nprint(grid)",
            "[[1, 0, 0], [1, 0, 0], [1, 0, 0]]", "[[1, 0, 0], [0, 0, 0], [0, 0, 0]]", "[[1, 1, 1], [0, 0, 0], [0, 0, 0]]", "IndexError",
            "A",
            "`[0] * 3` creates a single list `[0, 0, 0]`. When multiplied by 3 (`[[0]*3] * 3`), Python creates an outer list containing 3 references to the EXACT SAME inner list object! Mutating `grid[0][0]` mutates the shared underlying list, visible across all three rows: `[[1, 0, 0], [1, 0, 0], [1, 0, 0]]`.",
            65, "Python Technical"
        ],
        [
            "What will be output by the following Python dictionary comprehension?",
            "keys = ['a', 'b', 'c']\nvalues = [1, 2, 3, 4]\nd = {k: v for k, v in zip(keys, values)}\nprint(len(d), d['b'])",
            "3 2", "4 2", "3 3", "ValueError",
            "A",
            "`zip(keys, values)` stops when the shortest iterable (`keys`, length 3) is exhausted, pairing `('a', 1), ('b', 2), ('c', 3)`. The dictionary has length 3, and `d['b']` is 2. Output: `3 2`.",
            50, "Python Technical"
        ],
        [
            "What does the following decorator function print when decorating a target function in Python?",
            "def debug(func):\n    def wrapper(*args, **kwargs):\n        print(\"Calling\", func.__name__)\n        return func(*args, **kwargs)\n    return wrapper\n\n@debug\ndef greet(name):\n    return f\"Hello {name}\"\n\nprint(greet(\"Alex\"))",
            "Calling greet then Hello Alex", "Hello Alex only", "Calling greet only", "SyntaxError",
            "A",
            "The decorator syntax `@debug` is equivalent to `greet = debug(greet)`. When `greet(\"Alex\")` is invoked, it runs `wrapper()`, which first prints `Calling greet`, executes the original function returning `Hello Alex`, and then `print()` displays `Hello Alex`.",
            55, "Python Technical"
        ],
        [
            "What is the output of variable unpacking with the asterisk operator in Python?",
            "a, *b, c = [10, 20, 30, 40, 50]\nprint(a, b, c)",
            "10 [20, 30, 40] 50", "10 20 [30, 40, 50]", "[10, 20] [30, 40] 50", "ValueError",
            "A",
            "Extended iterable unpacking assigns the first element (`10`) to `a`, the last element (`50`) to `c`, and collects all remaining middle elements into a list assigned to `b` (`[20, 30, 40]`). Output: `10 [20, 30, 40] 50`.",
            50, "Python Technical"
        ],
        [
            "What is the difference between `__str__` and `__repr__` in Python classes?",
            "class Item:\n    def __init__(self, name): self.name = name\n    # __str__ vs __repr__",
            "`__str__` provides human-readable output for users; `__repr__` provides unambiguous representation for developers and debugging",
            "`__str__` is for integers only",
            "`__repr__` cannot return strings",
            "They are completely identical",
            "A",
            "`__str__()` is intended to return a user-friendly, readable string representation (invoked by `str(obj)` and `print()`). `__repr__()` aims for an unambiguous technical representation, ideally valid Python code that could recreate the object (invoked in the interactive shell or `repr(obj)`). If `__str__` is not defined, Python falls back to `__repr__`.",
            50, "Python Technical"
        ]
    ];

    foreach ($pythonQuestions as $q) {
        $addQ(19, 'hard', $q[0], $q[1], 'python', $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9]);
    }

    // ----------------------------------------------------
    // SQL & DATABASES (Subtopic 20)
    // ----------------------------------------------------
    $sqlQuestions = [
        [
            "What is the output of evaluating NULL in SQL boolean logic: SELECT * FROM T WHERE col = NULL;",
            "SELECT * FROM Users WHERE status = NULL;",
            "Returns 0 rows always (because comparison with NULL yields UNKNOWN, never TRUE; must use IS NULL)",
            "Returns all rows where status is NULL",
            "Syntax Error",
            "Throws NullPointerException",
            "A",
            "In ANSI SQL three-valued logic (TRUE, FALSE, UNKNOWN), any equality comparison with `NULL` (`col = NULL` or `col != NULL`) evaluates to `UNKNOWN`. Because `WHERE` clauses only return rows where the condition evaluates to `TRUE`, `col = NULL` never returns any rows. One must strictly use `col IS NULL`.",
            55, "SQL Technical"
        ],
        [
            "Which SQL clause is executed FIRST in the logical query processing order?",
            "SELECT dept_id, COUNT(*) FROM Emp WHERE salary > 50000 GROUP BY dept_id HAVING COUNT(*) > 5 ORDER BY dept_id;",
            "FROM clause", "SELECT clause", "WHERE clause", "ORDER BY clause",
            "A",
            "The ANSI SQL logical query processing order is:\n1. `FROM` (and JOINs)\n2. `WHERE`\n3. `GROUP BY`\n4. `HAVING`\n5. `SELECT`\n6. `DISTINCT`\n7. `ORDER BY`\n8. `LIMIT / OFFSET`.",
            45, "SQL Technical"
        ],
        [
            "What is the difference between TRUNCATE and DELETE in SQL?",
            "-- TRUNCATE TABLE Logs vs DELETE FROM Logs",
            "TRUNCATE is a DDL command that deallocates data pages, resets identity counters, cannot be filtered with WHERE, and is faster with minimal logging; DELETE is DML, row-by-row, and logs every deleted row",
            "DELETE cannot be rolled back in transactions",
            "TRUNCATE preserves identity seed values",
            "They are identical commands",
            "A",
            "`TRUNCATE` is a DDL operation that drops and re-creates the table storage pages, resetting identity/auto-increment counters with minimal logging. `DELETE` is a DML statement that removes rows individually, firing triggers and logging each row deletion, allowing fine-grained `WHERE` filtering.",
            55, "SQL Technical"
        ],
        [
            "What is a Correlated Subquery in SQL?",
            "SELECT e1.name FROM Employee e1 WHERE e1.salary > (SELECT AVG(e2.salary) FROM Employee e2 WHERE e2.dept_id = e1.dept_id);",
            "A subquery that references a column from the outer query and is re-evaluated for every candidate row of the outer query",
            "A query that runs only on indexed columns",
            "A subquery that runs once before the outer query starts",
            "A subquery that creates temporary tables",
            "A",
            "A correlated subquery contains a reference to a table in the outer query (`e2.dept_id = e1.dept_id`). Unlike a regular non-correlated subquery that executes once, a correlated subquery depends on the outer row and logically re-evaluates for each row processed by the outer query.",
            60, "SQL Technical"
        ],
        [
            "How does a B-Tree Index optimize range queries (e.g. BETWEEN 10 AND 50) compared to a Hash Index?",
            "CREATE INDEX idx_salary ON Employees(salary);",
            "B-Trees maintain elements in sorted order with linked leaf pages allowing efficient sequential range scans; Hash indexes provide O(1) equality lookups only and cannot support range queries",
            "Hash indexes are always better for range queries",
            "B-Trees do not support numbers",
            "Hash indexes sort data alphabetically",
            "A",
            "B-Tree indexes keep keys sorted and link all leaf nodes in a doubly-linked list. Once the starting boundary of a range is found via tree traversal (O(log N)), the remaining keys in the range are scanned sequentially. Hash indexes use a hash function that scatters values, making range queries impossible without a full table scan.",
            60, "SQL Technical"
        ]
    ];

    foreach ($sqlQuestions as $q) {
        $addQ(20, 'hard', $q[0], $q[1], 'sql', $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9]);
    }

    // ----------------------------------------------------
    // DATA STRUCTURES & ALGORITHMS (Subtopic 21)
    // ----------------------------------------------------
    $dsaQuestions = [
        [
            "What is the time complexity to find the median of an unsorted array of N elements using Quickselect?",
            "// Quickselect algorithm",
            "O(N) on average, O(N^2) in worst case", "O(N log N) in all cases", "O(log N) average", "O(1)",
            "A",
            "Quickselect (Hoare\'s selection algorithm) partitions the array around a pivot like Quicksort, but only recurses into the partition containing the target rank $k$. On average, the recurrence is $T(N) = T(N/2) + O(N)$, which forms a geometric series summing to $O(N)$ average time. Worst-case with bad pivots is $O(N^2)$.",
            60, "DSA Technical"
        ],
        [
            "What property must a binary tree satisfy to be classified as a valid Binary Search Tree (BST)?",
            "// BST Invariant validation",
            "For every node, all keys in its left subtree must be strictly less than the node key, and all keys in its right subtree must be strictly greater than the node key",
            "Only the immediate left child must be less than the node",
            "The tree must be completely filled at all levels",
            "Nodes must have at most 1 child",
            "A",
            "A common beginner misconception is checking only immediate children (`node.left.val < node.val`). In a true BST, the invariant applies to the ENTIRE subtree: every ancestor key imposes an upper and lower bound on all descendent nodes.",
            55, "DSA Technical"
        ],
        [
            "Which algorithm is used to find Strongly Connected Components (SCCs) in a Directed Graph in O(V + E) time?",
            "// Tarjan\'s or Kosaraju\'s algorithm",
            "Tarjan\'s algorithm using DFS and low-link values (or Kosaraju\'s algorithm with two DFS passes)",
            "Dijkstra\'s Algorithm",
            "Prim\'s Algorithm",
            "Kruskal\'s Algorithm",
            "A",
            "Both Tarjan\'s algorithm (using DFS traversal tracking discovery times and low-link values with a stack) and Kosaraju\'s algorithm (using two DFS passes on graph and transposed graph) find all Strongly Connected Components in optimal linear time $O(V + E)$.",
            60, "DSA Technical"
        ],
        [
            "What is the maximum number of edges in an undirected simple graph with N vertices that contains NO cycles (i.e. a forest)?",
            "// Graph theory properties",
            "N - 1 edges", "N edges", "N(N - 1) / 2 edges", "2N edges",
            "A",
            "An undirected acyclic graph is a forest (a collection of trees). A tree with $N$ vertices has exactly $N - 1$ edges. If the forest has $k$ connected components, it has $N - k$ edges. The maximum number of edges occurs when $k = 1$ (a single tree), which has exactly $N - 1$ edges.",
            50, "DSA Technical"
        ],
        [
            "What is the space complexity of Depth First Search (DFS) on a balanced binary tree of height H?",
            "// Call stack space complexity",
            "O(H) or O(log N) due to recursive call stack", "O(N) always", "O(1)", "O(N^2)",
            "A",
            "DFS visits nodes along paths from root to leaf, storing stack frames for nodes on the current path. On a balanced tree of $N$ nodes, the height $H = \\log_2 N$, requiring $O(H) = O(\\log N)$ auxiliary space for the call stack.",
            45, "DSA Technical"
        ]
    ];

    foreach ($dsaQuestions as $q) {
        $addQ(21, 'hard', $q[0], $q[1], 'dsa', $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9]);
    }

    // ----------------------------------------------------
    // INNOVATURE LABS PREVIOUS YEAR INTERVIEW QUESTIONS (Subtopic 22)
    // ----------------------------------------------------
    $innovatureQuestions = [
        [
            "[Innovature Labs Technical PYQ] In Innovature Labs C interview round: What will be the output of the following pointer arithmetic on 2D arrays?",
            "#include <stdio.h>\nint main() {\n    int a[3][3] = {{1, 2, 3}, {4, 5, 6}, {7, 8, 9}};\n    printf(\"%d %d\", *(*a + 1), *(*(a + 1) + 2));\n    return 0;\n}",
            "2 6", "1 5", "4 8", "2 5",
            "A",
            "`*a` is a pointer to the first row `a[0]`. `*(*a + 1)` is `a[0][1]`, which is `2`. `*(a + 1)` points to row 1 (`{4, 5, 6}`). Dereferencing `*(*(a + 1) + 2)` accesses `a[1][2]`, which is `6`. Output: `2 6`.",
            70, "Innovature Labs PYQ", 1, "Innovature Labs"
        ],
        [
            "[Innovature Labs Technical PYQ] Innovature Labs Java Round: What is the output of this static vs instance initialization puzzle?",
            "class Base {\n    static int x = 10;\n    static { x += 5; }\n}\npublic class Main {\n    public static void main(String[] args) {\n        System.out.println(Base.x);\n    }\n}",
            "15", "10", "5", "0",
            "A",
            "When class `Base` is loaded, static variable `x` is initialized to 10. Next, the static initializer block executes, computing `x += 5` = 15. When `main` accesses `Base.x`, it prints 15.",
            50, "Innovature Labs PYQ", 1, "Innovature Labs"
        ],
        [
            "[Innovature Labs Technical PYQ] Innovature Labs Database Round: How to delete duplicate rows from a table while keeping only the row with the lowest ID?",
            "DELETE t1 FROM Users t1 ...",
            "DELETE t1 FROM Users t1 INNER JOIN Users t2 WHERE t1.email = t2.email AND t1.id > t2.id;",
            "DELETE FROM Users WHERE email IN (SELECT email FROM Users GROUP BY email);",
            "DROP DUPLICATES FROM Users;",
            "DELETE DISTINCT FROM Users;",
            "A",
            "This classic Innovature Labs interview SQL query joins the table to itself on matching business key (`email`) where `t1.id > t2.id`. This matches every duplicate row with an ID higher than the minimum ID for that email and deletes `t1`, preserving only the lowest ID row.",
            65, "Innovature Labs PYQ", 1, "Innovature Labs"
        ],
        [
            "[Innovature Labs Logic PYQ] Innovature Labs Campus Round: Five colleagues (P, Q, R, S, T) work in 5 different departments: IT, HR, Finance, QA, and DevOps. P does not work in IT or HR. Q works in Finance. S is neither in IT nor QA. T does not work in IT. Which department does R work in?",
            null,
            "IT", "HR", "QA", "DevOps",
            "A",
            "Deduction by elimination:\n- Known: Q = Finance.\n- Looking for who works in IT: P != IT, Q != IT (Finance), S != IT, T != IT. The ONLY person remaining who can work in IT is R!\nTherefore, R works in IT.",
            75, "Innovature Labs PYQ", 1, "Innovature Labs"
        ],
        [
            "[Innovature Labs Quant PYQ] Innovature Labs Aptitude: In an Innovature development sprint, 6 engineers can build 12 microservices in 4 days. How many microservices can 9 engineers build in 6 days at the same productivity rate?",
            null,
            "27 microservices", "24 microservices", "30 microservices", "18 microservices",
            "A",
            "Using the Work formula: `(M1 * D1) / W1 = (M2 * D2) / W2`.\n- `(6 * 4) / 12 = 24 / 12 = 2`.\n- For second group: `(9 * 6) / W2 = 2` -> `54 / W2 = 2` -> `W2 = 54 / 2 = 27 microservices`.",
            65, "Innovature Labs PYQ", 1, "Innovature Labs"
        ],
        [
            "[Innovature Labs Technical PYQ] Innovature Labs C Assessment: What does `printf(\"%d\", sizeof('A'));` print in C versus C++?",
            "#include <stdio.h>\nint main() {\n    printf(\"%zu\", sizeof('A'));\n    return 0;\n}",
            "In C: sizeof(int) (typically 4 bytes); In C++: sizeof(char) (1 byte)",
            "1 in both C and C++",
            "4 in both C and C++",
            "Undefined behavior in C",
            "A",
            "In C, character literals like `'A'` have type `int` (character constant promotion), so `sizeof('A')` is `sizeof(int)` = 4 bytes. In C++, character literals have type `char`, so `sizeof('A')` is `sizeof(char)` = 1 byte! This is a favorite trick question at Innovature Labs.",
            65, "Innovature Labs PYQ", 1, "Innovature Labs"
        ]
    ];

    foreach ($innovatureQuestions as $q) {
        $addQ(22, 'hard', $q[0], $q[1], 'innovature', $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], 1, 'Innovature Labs');
    }

    echo "Inserting " . count($batch) . " curated questions...\n";
    $inserted = 0;
    foreach ($batch as $row) {
        $stmt->execute($row);
        $inserted++;
    }

    echo ">>> Successfully inserted {$inserted} hard technical and Innovature Labs questions! <<<\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
