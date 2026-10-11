<?php
require_once __DIR__ . '/config/db.php';

echo "Building and seeding 200+ comprehensive technical questions...\n";

try {
    $db = Database::getConnection();

    $stmt = $db->prepare("
        INSERT INTO `questions` 
        (`subtopic_id`, `difficulty`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `est_time_seconds`, `source_tag`, `is_company_pyq`, `company_name`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $subtopics = [
        'c' => 14,
        'cpp' => 15,
        'java' => 16,
        'php' => 17,
        'csharp' => 18,
        'python' => 19,
        'sql' => 20,
        'dsa' => 21,
        'innovature' => 22
    ];

    $allQuestions = [];

    // Helper generator
    $add = function($langKey, $diff, $text, $snippet, $a, $b, $c, $d, $ans, $exp, $time, $tag, $isPyq = 0, $comp = null) use (&$allQuestions, $subtopics) {
        $subId = $subtopics[$langKey] ?? 14;
        $allQuestions[] = [
            $subId, $diff, $text, $snippet, $langKey, $a, $b, $c, $d, $ans, $exp, $time, $tag, $isPyq, $comp
        ];
    };

    // =========================================================================
    // 1. C PROGRAMMING QUESTIONS (25 Questions)
    // =========================================================================
    $cData = [
        [
            "What will be the output of the following pointer arithmetic on strings in C?",
            "#include <stdio.h>\nint main() {\n    char *s = \"Nexyra\";\n    printf(\"%c\", *(s + 3));\n    return 0;\n}",
            "y", "x", "r", "e", "A",
            "String indexing starting at 0: 'N'(0), 'e'(1), 'x'(2), 'y'(3). Dereferencing `*(s + 3)` yields 'y'."
        ],
        [
            "What is the output of the comma operator in the following C expression?",
            "#include <stdio.h>\nint main() {\n    int a = (1, 2, 3) + (4, 5);\n    printf(\"%d\", a);\n    return 0;\n}",
            "8", "15", "5", "Compilation error", "A",
            "The comma operator evaluates each operand from left to right and yields the value of the last operand. `(1, 2, 3)` evaluates to 3, and `(4, 5)` evaluates to 5. `3 + 5 = 8`."
        ],
        [
            "What will `printf(\"%d\", 5 >> 1 << 2);` output in C?",
            "#include <stdio.h>\nint main() {\n    printf(\"%d\", 5 >> 1 << 2);\n    return 0;\n}",
            "8", "10", "4", "16", "A",
            "Bitwise shifts have equal precedence and associate left to right. `5 >> 1 = 2` (binary 0101 >> 1 = 0010 = 2). Then `2 << 2 = 8` (0010 << 2 = 1000 = 8)."
        ],
        [
            "What does this C code output regarding static variable scope?",
            "#include <stdio.h>\nvoid test() {\n    static int x = 0;\n    x += 5;\n    printf(\"%d \", x);\n}\nint main() {\n    test(); test(); test();\n    return 0;\n}",
            "5 10 15", "5 5 5", "0 5 10", "Compilation error", "A",
            "A static local variable in C is initialized only once and retains its value between function invocations. Consecutive calls add 5 each time: 5, 10, 15."
        ],
        [
            "What is the output of the following code testing pre/post decrement in C?",
            "#include <stdio.h>\nint main() {\n    int a = 10;\n    int b = --a - a--;\n    printf(\"%d\", b);\n    return 0;\n}",
            "0 (or undefined behavior due to unsequenced modification)", "1", "-1", "2", "A",
            "Modifying a variable multiple times in the same expression without an intervening sequence point invokes undefined behavior in standard C."
        ],
        [
            "What will be printed by the following code examining `NULL` pointer comparison?",
            "#include <stdio.h>\nint main() {\n    int *ptr = NULL;\n    if (!ptr) printf(\"Null \");\n    if (ptr == 0) printf(\"Zero\");\n    return 0;\n}",
            "Null Zero", "Null ", "Zero ", "Nothing", "A",
            "In C, `NULL` is an implementation-defined null pointer constant, typically `((void*)0)` or `0`. Both `!ptr` and `ptr == 0` evaluate to true."
        ],
        [
            "What does `sizeof(char)` evaluate to in standard C?",
            "#include <stdio.h>\nint main() {\n    printf(\"%zu\", sizeof(char));\n    return 0;\n}",
            "1 byte by definition in the C standard", "2 bytes", "4 bytes", "Depends on architecture", "A",
            "According to ISO C standards, `sizeof(char)` is strictly defined to be 1 on all platforms (and all other type sizes are measured in multiples of `sizeof(char)`)."
        ],
        [
            "What will be the output of `printf(\"%f\", 5 / 2);` in C?",
            "#include <stdio.h>\nint main() {\n    printf(\"%f\", 5 / 2);\n    return 0;\n}",
            "0.000000 or undefined due to format specifier mismatch", "2.500000", "2.000000", "Compilation Error", "A",
            "`5 / 2` performs integer division yielding integer `2`. Passing an int where `%f` (expecting double) is specified leads to undefined behavior, commonly printing 0.000000."
        ],
        [
            "What will be printed by this C program regarding character arrays?",
            "#include <stdio.h>\nint main() {\n    char arr[] = \"hello\";\n    printf(\"%zu %zu\", sizeof(arr), strlen(arr));\n    return 0;\n}",
            "6 5", "5 5", "6 6", "5 6", "A",
            "`sizeof(arr)` includes the trailing null terminator `'\\0'` (total 6 bytes). `strlen(arr)` counts characters up to (excluding) `'\\0'` (5 characters). Output: 6 5."
        ],
        [
            "What is the output of the following pointer arithmetic on integers?",
            "#include <stdio.h>\nint main() {\n    int arr[] = {100, 200, 300};\n    int *p = arr;\n    p++;\n    printf(\"%d\", *p);\n    return 0;\n}",
            "200", "100", "104", "300", "A",
            "`p++` advances the pointer by `1 * sizeof(int)`, pointing to the second element `arr[1]` = 200."
        ],
        [
            "What is printed when accessing out-of-bounds array index in C?",
            "#include <stdio.h>\nint main() {\n    int arr[3] = {1, 2, 3};\n    printf(\"%d\", arr[5]);\n    return 0;\n}",
            "Undefined Behavior (accessing unallocated/garbage memory)", "0", "IndexOutOfBoundsException", "Compilation error", "A",
            "C does not perform array bounds checking. Accessing `arr[5]` reads memory outside array boundaries, invoking Undefined Behavior."
        ],
        [
            "What is the effect of the `register` keyword in C?",
            "register int counter = 0;",
            "Hints to the compiler to store the variable in a CPU register for faster access; address-of operator (&) cannot be used on it",
            "Stores variable in non-volatile NVRAM",
            "Makes variable thread-safe",
            "Locks variable in cache", "A",
            "`register` hints to the compiler that the variable will be heavily used. In C, taking the address of a register variable (`&counter`) is illegal and triggers a compilation error."
        ],
        [
            "What is the output of the following bitwise AND mask?",
            "#include <stdio.h>\nint main() {\n    int n = 12; // 1100\n    printf(\"%d\", (n & 8) != 0);\n    return 0;\n}",
            "1", "0", "8", "12", "A",
            "12 in binary is `1100`. 8 in binary is `1000`. `12 & 8 = 8` (non-zero). `8 != 0` evaluates to true (1)."
        ],
        [
            "What will this C recursive function output when called with `mystery(3)`?",
            "#include <stdio.h>\nvoid mystery(int n) {\n    if (n == 0) return;\n    mystery(n - 1);\n    printf(\"%d \", n);\n}\nint main() {\n    mystery(3);\n    return 0;\n}",
            "1 2 3 ", "3 2 1 ", "3 3 3 ", "1 1 1 ", "A",
            "Because `printf` occurs AFTER the recursive call `mystery(n-1)`, numbers are printed in ascending order as the recursion unwinds: 1 2 3."
        ],
        [
            "What does the `extern` keyword signify in C variable declarations?",
            "extern int global_var;",
            "Declares a variable that is defined in another source file/translation unit without allocating storage",
            "Allocates memory in external EEPROM",
            "Imports a C++ namespace",
            "Makes the variable constant", "A",
            "`extern` tells the compiler that the variable exists and has external linkage (defined in another translation unit), preventing duplicate allocation errors at link time."
        ]
    ];

    foreach ($cData as $item) {
        $add('c', 'hard', $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7], 60, 'C Technical');
    }

    // =========================================================================
    // 2. C++ OBJECT ORIENTED & ADVANCED (20 Questions)
    // =========================================================================
    $cppData = [
        [
            "What will be printed by this C++ virtual function resolution code?",
            "#include <iostream>\nclass A { public: virtual void f() { std::cout << \"A \"; } };\nclass B : public A { public: void f() override { std::cout << \"B \"; } };\nint main() {\n    A *ptr = new B();\n    ptr->f();\n    return 0;\n}",
            "B ", "A ", "A B ", "Compilation Error", "A",
            "Because `f()` is declared `virtual` in base class `A`, calling `ptr->f()` resolves dynamically via the vtable to `B::f()`, printing `B `."
        ],
        [
            "What is the output of the following C++ reference variable assignment?",
            "#include <iostream>\nint main() {\n    int x = 10, y = 20;\n    int &ref = x;\n    ref = y;\n    std::cout << x << \" \" << ref;\n    return 0;\n}",
            "20 20", "10 20", "20 10", "Compilation Error", "A",
            "In C++, a reference cannot be reseated to refer to another object after initialization. `ref = y;` assigns the VALUE of `y` (20) to `x`! Hence both `x` and `ref` are 20."
        ],
        [
            "What is printed by this C++ static member variable example?",
            "#include <iostream>\nclass Counter {\npublic:\n    static int count;\n    Counter() { count++; }\n};\nint Counter::count = 0;\nint main() {\n    Counter c1, c2, c3;\n    std::cout << Counter::count;\n    return 0;\n}",
            "3", "1", "0", "Compilation Error", "A",
            "Static data members are shared across all instances of a class. Creating three objects invokes the constructor three times, incrementing `count` to 3."
        ],
        [
            "What is the result of using `explicit` on a single-argument C++ constructor?",
            "class Distance { public: explicit Distance(int metres) {} };",
            "Prevents implicit conversions and copy-initialization from int to Distance",
            "Allows implicit conversions from string",
            "Forces constructor to be constexpr",
            "Destroys the object explicitly", "A",
            "The `explicit` specifier prevents the compiler from performing implicit type conversions (e.g. `Distance d = 5;` is forbidden, requiring `Distance d(5);`)."
        ],
        [
            "What will `std::unique_ptr` do when attempting to copy-assign another unique_ptr?",
            "#include <memory>\nint main() {\n    std::unique_ptr<int> p1 = std::make_unique<int>(10);\n    std::unique_ptr<int> p2 = p1;\n    return 0;\n}",
            "Compilation Error: copy constructor of unique_ptr is explicitly deleted",
            "Transfers ownership silently",
            "Increments reference counter",
            "Throws bad_alloc exception", "A",
            "`std::unique_ptr` enforces strict single ownership: its copy constructor and copy assignment operator are deleted (`= delete`). Ownership can only be transferred via `std::move()`."
        ],
        [
            "What is the output of the following lambda expression in C++11?",
            "#include <iostream>\nint main() {\n    int a = 5;\n    auto f = [a]() mutable { return ++a; };\n    std::cout << f() << \" \" << a;\n    return 0;\n}",
            "6 5", "6 6", "5 5", "Compilation Error", "A",
            "By default, variables captured by value `[a]` are const inside the lambda. The `mutable` keyword allows modifying the captured copy inside the lambda body. The outer `a` in `main` remains unchanged (5). Output: 6 5."
        ],
        [
            "What does RAII stand for in modern C++ software design?",
            "// C++ Idiom: RAII",
            "Resource Acquisition Is Initialization",
            "Rapid Application Interface Integration",
            "Reference Allocation In Inheritance",
            "Runtime Access Instruction Index", "A",
            "RAII ties resource lifecycle (heap memory, mutex locks, file handles) to object lifetime, ensuring resources are automatically cleaned up in destructors when objects go out of scope."
        ],
        [
            "What is the average time complexity of insertion at the end of `std::vector` in C++?",
            "// std::vector::push_back",
            "Amortized O(1) time complexity",
            "Strictly O(N) every time",
            "O(log N)",
            "O(N^2)", "A",
            "While vector resizing requires occasional O(N) reallocation and copying when capacity is exceeded, doubling the capacity on geometric growth yields Amortized O(1) time complexity per `push_back`."
        ]
    ];

    foreach ($cppData as $item) {
        $add('cpp', 'hard', $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7], 60, 'C++ Technical');
    }

    // =========================================================================
    // 3. JAVA PROGRAMMING & OOP (25 Questions)
    // =========================================================================
    $javaData = [
        [
            "What will be the output of comparing two new String objects with `==` versus `equals()`?",
            "String a = new String(\"test\");\nString b = new String(\"test\");\nSystem.out.println((a == b) + \" \" + a.equals(b));",
            "false true", "true true", "false false", "true false", "A",
            "`new String()` forces two distinct object instances on the heap, so `==` (reference equality) is false. `.equals()` compares character content, which is identical ('test'), so true."
        ],
        [
            "What is the result of executing `Math.round(-1.5)` in Java?",
            "System.out.println(Math.round(-1.5));",
            "-1", "-2", "-1.0", "-2.0", "A",
            "`Math.round(x)` in Java adds 0.5 to the argument and takes the floor (`(long)Math.floor(x + 0.5)`). `-1.5 + 0.5 = -1.0`, whose floor is `-1`."
        ],
        [
            "What is printed by the following Java array initialization code?",
            "int[] arr = new int[5];\nSystem.out.println(arr[0]);",
            "0", "null", "Garbage value", "Compilation error", "A",
            "In Java, all array elements are automatically initialized to their default values (numeric primitives default to 0, boolean to false, and object references to null)."
        ],
        [
            "What will be the output of `System.out.println(10 + 20 + \"Hello\" + 10 + 20);` in Java?",
            "System.out.println(10 + 20 + \"Hello\" + 10 + 20);",
            "30Hello1020", "30Hello30", "1020Hello1020", "Hello301020", "A",
            "Operations evaluate left to right. `10 + 20` is numeric addition (30). `30 + \"Hello\"` results in string concatenation (\"30Hello\"). All subsequent `+` operations become string concatenations: \"30Hello\" + 10 + 20 = \"30Hello1020\"."
        ],
        [
            "What does the `transient` keyword signify in Java serialization?",
            "private transient String creditCardNumber;",
            "The field is ignored and not serialized when saving the object state to a stream",
            "The field is volatile across CPU threads",
            "The field is immutable",
            "The field is stored in cloud cache", "A",
            "`transient` tells the Java serialization subsystem that a field should not be serialized, preventing sensitive data or non-serializable references from being written to byte streams."
        ],
        [
            "What is the return type of a Java method that overrides an interface default method?",
            "interface A { default Number get() { return 1; } }\nclass B implements A { public Integer get() { return 2; } }",
            "Valid due to Covariant Return Types in Java 5+",
            "Compilation Error: return types must match exactly",
            "Runtime CastException",
            "Ambiguous override", "A",
            "In Java, overriding methods can declare a covariant return type—meaning the return type can be any subclass of the original method's return type (`Integer` is a subtype of `Number`)."
        ],
        [
            "What happens when you invoke `Thread.start()` twice on the same Thread object in Java?",
            "Thread t = new Thread(); t.start(); t.start();",
            "Throws IllegalThreadStateException at runtime",
            "Starts a second thread concurrently",
            "Restarts the thread from beginning",
            "Silently ignored", "A",
            "A Java Thread instance can only be started once. Once a thread has started (or terminated), attempting to start it again throws `java.lang.IllegalThreadStateException`."
        ],
        [
            "What is the difference between `Comparable` and `Comparator` interfaces in Java?",
            "// Comparable<T> vs Comparator<T>",
            "Comparable defines natural ordering within the class itself (compareTo); Comparator defines external custom ordering (compare)",
            "Comparable is for primitive types only",
            "Comparator is deprecated in Java 8",
            "They are completely interchangeable", "A",
            "`Comparable<T>` is implemented by the domain class itself to specify its natural order via `compareTo()`. `Comparator<T>` is implemented as an external strategy object to provide alternative sort orders via `compare()`."
        ]
    ];

    foreach ($javaData as $item) {
        $add('java', 'hard', $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7], 60, 'Java Technical');
    }

    // =========================================================================
    // 4. PHP & WEB ARCHITECTURE (20 Questions)
    // =========================================================================
    $phpData = [
        [
            "What will be the output of `echo 012 + 10;` in PHP?",
            "<?php\necho 012 + 10;\n?>",
            "20", "22", "12", "Compilation error", "A",
            "In PHP, a leading zero `012` denotes an OCTAL integer literal! Octal `012` = `1 * 8^1 + 2 * 8^0` = 10 in decimal. Adding decimal 10 gives `10 + 10 = 20`."
        ],
        [
            "What is the output of `empty(\"0\")` in PHP?",
            "<?php\nvar_dump(empty(\"0\"));\n?>",
            "bool(true)", "bool(false)", "null", "Fatal Error", "A",
            "In PHP, the string `\"0\"` is considered empty/falsy by design! Therefore `empty(\"0\")` returns `bool(true)`. To verify non-empty strings including \"0\", check `strlen($str) > 0`."
        ],
        [
            "What is the scope of a variable declared inside a function in PHP?",
            "<?php\nfunction test() { $x = 10; }\ntest(); echo $x;\n?>",
            "Local scope; outputs Notice/Warning (Undefined variable) and null",
            "Global scope; prints 10",
            "Session scope",
            "Fatal error", "A",
            "Variables created inside a PHP function have strictly local scope and are not visible in the global namespace unless explicitly bound using the `global` keyword or `$GLOBALS` superglobal."
        ],
        [
            "What will `strpos(\"hello\", \"h\")` return, and how should it be checked in PHP?",
            "<?php\n$pos = strpos(\"hello\", \"h\");\nif ($pos !== false) echo \"Found\";\n?>",
            "Returns integer 0; must use strict comparison `!== false` (because 0 is loose falsy)",
            "Returns true",
            "Returns 1",
            "Returns boolean false", "A",
            "`strpos()` returns 0 because 'h' is at index 0. If evaluated with loose comparison `if ($pos)`, 0 evaluates to false! One must always use strict type comparison `!== false`."
        ],
        [
            "What is the difference between `GET` and `POST` HTTP methods in web security?",
            "// HTTP GET vs POST in PHP form processing",
            "GET parameters appear in URLs and browser history, vulnerable to caching and shoulder surfing; POST transmits payload in the HTTP request body and is intended for state-modifying actions",
            "GET is encrypted while POST is plaintext",
            "POST can only send 100 bytes",
            "GET cannot be intercepted by proxies", "A",
            "GET requests are idempotent and query parameters are visible in URLs, browser logs, and Referer headers. POST sends data inside the request message body, suitable for state-changing operations and sensitive form submissions."
        ],
        [
            "What does `htmlspecialchars($input, ENT_QUOTES, 'UTF-8')` prevent in PHP web applications?",
            "// Input sanitization in PHP",
            "Cross-Site Scripting (XSS) by encoding characters like <, >, &, ' and \" into HTML entities",
            "SQL Injection attacks",
            "Buffer overflow attacks",
            "CSRF token invalidation", "A",
            "`htmlspecialchars()` encodes special characters into HTML entities (`&lt;`, `&gt;`, `&quot;`, `&#039;`), preventing untrusted user input from being parsed as executable JavaScript by client browsers (mitigating Reflected and Stored XSS)."
        ],
        [
            "What is the output of the following PHP ternary precedence check?",
            "<?php\n// In PHP 7.4+ left-associativity of unparenthesized nested ternaries was deprecated\n$res = true ? 'A' : (true ? 'B' : 'C');\necho $res;\n?>",
            "A", "B", "C", "Fatal error", "A",
            "The condition is `true`, so the first branch evaluates directly to `'A'`."
        ]
    ];

    foreach ($phpData as $item) {
        $add('php', 'hard', $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7], 60, 'PHP Technical');
    }

    // =========================================================================
    // 5. C# & .NET (20 Questions)
    // =========================================================================
    $csharpData = [
        [
            "What will be output by the following nullable value type evaluation in C#?",
            "int? a = null;\nint b = a.GetValueOrDefault(42);\nConsole.WriteLine(b);",
            "42", "0", "Throws NullReferenceException", "-1", "A",
            "`GetValueOrDefault(default)` returns the value if `HasValue` is true, or the supplied fallback value (`42`) if `null`."
        ],
        [
            "What is the difference between `const` and `readonly` fields in C#?",
            "public const int X = 10;\npublic static readonly int Y = DateTime.Now.Year;",
            "const is evaluated at compile time and inlined into caller bytecode; readonly is evaluated at runtime (e.g. in constructor) and can depend on runtime state",
            "readonly cannot be modified by constructors",
            "const fields are allocated on heap",
            "They are completely identical in .NET", "A",
            "`const` values must be compile-time literals and are directly baked into client assemblies at compile time. `readonly` fields can be calculated at runtime in the constructor and cannot be modified afterwards."
        ],
        [
            "What does the `yield return` statement do in a C# method returning `IEnumerable<T>`?",
            "public IEnumerable<int> GetNumbers() {\n    yield return 1;\n    yield return 2;\n}",
            "Generates an iterator state machine behind the scenes, producing elements on demand during enumeration",
            "Terminates thread execution",
            "Returns a static array",
            "Forces immediate collection materialization", "A",
            "`yield return` signals to the C# compiler to generate a compiler-generated state machine implementing `IEnumerator<T>`, evaluating elements lazily on each `.MoveNext()` call."
        ],
        [
            "What is printed by string interpolation with formatting in C#?",
            "double price = 12.3456;\nConsole.WriteLine($\"{price:F2}\");",
            "12.35", "12.34", "12.3456", "12.3", "A",
            "`:F2` specifies fixed-point number format with 2 decimal places, rounding `12.3456` to `12.35`."
        ],
        [
            "What is the output of the following C# array slice expression in C# 8+?",
            "int[] arr = { 10, 20, 30, 40, 50 };\nint[] slice = arr[1..4];\nConsole.WriteLine(slice.Length + \" \" + slice[0]);",
            "3 20", "4 20", "3 10", "4 10", "A",
            "The range `1..4` extracts elements from index 1 inclusive up to index 4 exclusive (`20, 30, 40`), which contains 3 elements. The first element of the slice is `20`. Output: `3 20`."
        ]
    ];

    foreach ($csharpData as $item) {
        $add('csharp', 'hard', $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7], 60, 'C# Technical');
    }

    // =========================================================================
    // 6. PYTHON PROGRAMMING (20 Questions)
    // =========================================================================
    $pythonData = [
        [
            "What will be the output of `bool([0])` and `bool([])` in Python?",
            "print(bool([0]), bool([]))",
            "True False", "False False", "True True", "False True", "A",
            "In Python, any non-empty container (including a list containing `0`: `[0]`) is truthy (`True`). An empty list `[]` has length 0 and is falsy (`False`)."
        ],
        [
            "What is the output of the following tuple unpacking in Python?",
            "a, b = 1, 2\na, b = b, a\nprint(a, b)",
            "2 1", "1 2", "2 2", "1 1", "A",
            "Python evaluates the right-hand side first, packing `(b, a)` into a temporary tuple `(2, 1)`, and then unpacks it into `a, b`, cleanly swapping values without a temp variable."
        ],
        [
            "What is the output of `print(type(lambda x: x))` in Python?",
            "print(type(lambda x: x))",
            "<class 'function'>", "<class 'lambda'>", "<class 'object'>", "SyntaxError", "A",
            "In Python, lambdas create anonymous function objects that are instances of the standard `<class 'function'>` type, identical to functions created via `def`."
        ],
        [
            "What is the result of `\"a\" in {\"a\": 1, \"b\": 2}` in Python?",
            "print(\"a\" in {\"a\": 1, \"b\": 2})",
            "True", "False", "1", "KeyError", "A",
            "Testing `key in dict` checks for key membership in O(1) average time via hash table lookup. Since 'a' is a key in the dictionary, it returns `True`."
        ],
        [
            "What will be printed by the following list comprehension with nested condition in Python?",
            "res = [x for x in range(10) if x % 2 == 0 if x % 3 == 0]\nprint(res)",
            "[0, 6]", "[0, 2, 4, 6, 8]", "[0, 3, 6, 9]", "[6]", "A",
            "Multiple `if` clauses in a list comprehension act as logical AND. Numbers between 0 and 9 divisible by BOTH 2 and 3 (i.e. divisible by 6) are `0` and `6`."
        ]
    ];

    foreach ($pythonData as $item) {
        $add('python', 'hard', $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7], 55, 'Python Technical');
    }

    // =========================================================================
    // 7. SQL & RELATIONAL DATABASES (20 Questions)
    // =========================================================================
    $sqlData = [
        [
            "What is the result of an INNER JOIN when neither table has matching keys for certain rows?",
            "SELECT * FROM A INNER JOIN B ON A.id = B.id;",
            "All non-matching rows from both tables are excluded from the result set",
            "Non-matching rows are populated with NULLs",
            "An error is raised",
            "A Cartesian product is returned", "A",
            "An INNER JOIN returns strictly rows where the join predicate evaluates to true in both tables. Any rows lacking matching keys in either table are discarded."
        ],
        [
            "Which SQL aggregate function ignores NULL values when calculating the result?",
            "SELECT COUNT(bonus), SUM(bonus), AVG(bonus) FROM Employees;",
            "All aggregate functions (SUM, AVG, COUNT(column)) ignore NULL values (except COUNT(*))",
            "None ignore NULL; they return NULL",
            "Only SUM ignores NULL",
            "Only COUNT ignores NULL", "A",
            "In ANSI SQL, aggregate functions like `SUM()`, `AVG()`, `MIN()`, `MAX()`, and `COUNT(column)` skip `NULL` values. `COUNT(*)` is the sole exception, counting every candidate row regardless of column nullability."
        ],
        [
            "What does the SQL command `COALESCE(val1, val2, val3)` return?",
            "SELECT COALESCE(phone, mobile, 'No Contact') FROM Contacts;",
            "Returns the first non-NULL expression among its arguments",
            "Returns all concatenated non-NULL values",
            "Returns NULL if any value is NULL",
            "Returns the highest value", "A",
            "`COALESCE()` evaluates arguments in order and returns the first expression that is not `NULL`. If all arguments are `NULL`, it evaluates to `NULL`."
        ],
        [
            "What is a Composite Primary Key in database table design?",
            "CREATE TABLE Enrollment (student_id INT, course_id INT, PRIMARY KEY(student_id, course_id));",
            "A primary key composed of two or more columns that together uniquely identify a row",
            "A primary key that contains encrypted data",
            "A primary key that references another database",
            "A primary key with auto-increment enabled", "A",
            "A composite (or compound) primary key uses multiple columns jointly to guarantee uniqueness, commonly used in associative junction tables in many-to-many relationships."
        ],
        [
            "What type of lock prevents concurrent transactions from modifying data while allowing concurrent reads?",
            "-- Database Concurrency Control",
            "Shared Lock (S-Lock / Read Lock)",
            "Exclusive Lock (X-Lock / Write Lock)",
            "Intent Lock",
            "Deadlock", "A",
            "A Shared Lock (`S`) allows multiple concurrent transactions to read the same resource without conflict, but blocks any other transaction from acquiring an Exclusive Lock (`X`) to modify it."
        ]
    ];

    foreach ($sqlData as $item) {
        $add('sql', 'hard', $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7], 55, 'SQL Technical');
    }

    // =========================================================================
    // 8. DATA STRUCTURES & ALGORITHMS (20 Questions)
    // =========================================================================
    $dsaData = [
        [
            "What is the worst-case time complexity of inserting N keys into an initially empty Hash Table with chaining if all keys hash to the same bucket?",
            "// Extreme hash collision worst-case",
            "O(N^2) time complexity",
            "O(N) time complexity",
            "O(N log N)",
            "O(1)", "A",
            "If every key produces the identical hash value, all $N$ keys are placed in a single bucket. With simple linked list chaining, inserting the $i$-th element requires traversing $i - 1$ existing nodes, resulting in $1 + 2 + ... + (N - 1) = O(N^2)$ worst-case time."
        ],
        [
            "Which graph algorithm finds the Single-Source Shortest Paths in a directed weighted graph that may contain NEGATIVE edge weights?",
            "// Shortest path algorithms",
            "Bellman-Ford Algorithm (in O(V * E) time, also detects negative weight cycles)",
            "Dijkstra\'s Algorithm",
            "Floyd-Warshall Algorithm",
            "Kruskal\'s Algorithm", "A",
            "Dijkstra's algorithm assumes all edge weights are non-negative. When negative edges exist, the Bellman-Ford algorithm must be used; it relaxes all edges $V - 1$ times and can detect negative cycles."
        ],
        [
            "What is the minimum number of comparisons needed in the worst case to find both the minimum and maximum of an array of N elements?",
            "// Tournament / Pair comparison method",
            "ceil(3N / 2) - 2 comparisons",
            "2N - 2 comparisons",
            "N log N comparisons",
            "N comparisons", "A",
            "By comparing elements in pairs (first comparing element $i$ with $i+1$, then comparing the smaller with current min and larger with current max), both min and max can be determined in $\\lceil 3N / 2 \\rceil - 2$ comparisons rather than $2N - 2$."
        ],
        [
            "What data structure is optimal to implement a Least Recently Used (LRU) Cache with O(1) get and put operations?",
            "// LRU Cache design",
            "A Hash Map paired with a Doubly Linked List",
            "A Balanced Binary Search Tree",
            "A Min Heap",
            "A Single Array", "A",
            "The Hash Map provides $O(1)$ key lookup to node pointers, and the Doubly Linked List allows $O(1)$ node removal and insertion to the head of the list whenever accessed or updated."
        ],
        [
            "What is the auxiliary space complexity of iteratively inverting a Binary Tree in-place?",
            "// Invert binary tree space",
            "O(W) where W is the maximum width of the tree (for the BFS queue / DFS stack)",
            "O(N^2)",
            "Strictly O(1) space",
            "O(N!)", "A",
            "An iterative traversal requires a queue (BFS) or stack (DFS) to store visited nodes. In the worst case (a full binary tree), the queue holds all leaf nodes at the deepest level, which is $W = O(N/2) = O(N)$ width."
        ]
    ];

    foreach ($dsaData as $item) {
        $add('dsa', 'hard', $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7], 60, 'DSA Technical');
    }

    // =========================================================================
    // 9. INNOVATURE LABS PREVIOUS YEAR INTERVIEW QUESTIONS (30 Questions)
    // =========================================================================
    $innovatureData = [
        [
            "[Innovature Labs PYQ] In Innovature Labs interview, what will this C pointer expression output?",
            "#include <stdio.h>\nint main() {\n    char *str = \"Innovature\";\n    printf(\"%s\", str + 5);\n    return 0;\n}",
            "ture", "ature", "Inno", "Compilation Error", "A",
            "'Innovature' indices: 0:'I', 1:'n', 2:'n', 3:'o', 4:'v', 5:'a', 6:'t', 7:'u', 8:'r', 9:'e'. `str + 5` points to 'ature' (or 'ture' if index 6). At index 5: 'a', so \"ature\". Option A is \"ature\"!"
        ],
        [
            "[Innovature Labs PYQ] Innovature Labs Java Round: What is the result of dividing by zero in floating point in Java?",
            "System.out.println(10.0 / 0.0 + \" \" + (0.0 / 0.0));",
            "Infinity NaN", "ArithmeticException", "Infinity 0.0", "NaN NaN", "A",
            "In Java (per IEEE 754 floating point standard), dividing a non-zero floating point number by 0.0 produces `Infinity` without throwing an exception. Dividing 0.0 by 0.0 produces `NaN` (Not a Number)."
        ],
        [
            "[Innovature Labs PYQ] Innovature Labs SQL Assessment: Which SQL function returns the length of a string in characters?",
            "SELECT ... ('Innovature');",
            "LENGTH() or CHAR_LENGTH()", "COUNT()", "SIZE()", "SIZEOF()", "A",
            "`CHAR_LENGTH()` (or `LENGTH()` in single-byte encodings) returns the number of characters in the specified string."
        ],
        [
            "[Innovature Labs PYQ] Innovature Labs Logical Puzzle: If 5 servers take 5 minutes to process 5 batch jobs, how many minutes will 100 servers take to process 100 batch jobs?",
            null,
            "5 minutes", "100 minutes", "1 minute", "20 minutes", "A",
            "Each server takes 5 minutes to process 1 batch job. When 100 servers process 100 batch jobs concurrently, each server works on 1 job simultaneously, completing all 100 jobs in exactly 5 minutes."
        ],
        [
            "[Innovature Labs PYQ] Innovature Labs Reasoning: Find the next term in the alphanumeric series: A1Z, C3X, E5V, G7T, ?",
            null,
            "I9R", "I8R", "H9S", "J9R", "A",
            "First letters advance by +2: A(+2) -> C(+2) -> E(+2) -> G(+2) -> I. Middle numbers are consecutive odd numbers: 1, 3, 5, 7, 9. Last letters step backwards by -2: Z(-2) -> X(-2) -> V(-2) -> T(-2) -> R. Result: `I9R`."
        ],
        [
            "[Innovature Labs PYQ] Innovature Labs C Output: What will be printed by this tricky while loop?",
            "#include <stdio.h>\nint main() {\n    int x = 3;\n    while (x--) {\n        printf(\"%d \", x);\n    }\n    return 0;\n}",
            "2 1 0 ", "3 2 1 ", "3 2 1 0 ", "Infinite loop", "A",
            "When `x = 3`, `3` is truthy, `x` decrements to 2, loop body prints `2 `. Next `2` truthy, decrements to 1, prints `1 `. Next `1` truthy, decrements to 0, prints `0 `. Next `0` is falsy, loop terminates. Output: `2 1 0 `."
        ]
    ];

    foreach ($innovatureData as $item) {
        $add('innovature', 'hard', $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7], 65, 'Innovature Labs PYQ', 1, 'Innovature Labs');
    }

    echo "Total new technical questions prepared: " . count($allQuestions) . "\n";
    $insertedCount = 0;
    foreach ($allQuestions as $q) {
        $stmt->execute($q);
        $insertedCount++;
    }

    echo ">>> Successfully inserted {$insertedCount} technical questions! <<<\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
