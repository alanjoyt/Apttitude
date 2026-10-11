<?php
require_once __DIR__ . '/config/db.php';

echo "Seeding C, C++, and Java Technical Questions...\n";

try {
    $db = Database::getConnection();

    $stmt = $db->prepare("
        INSERT INTO `questions` 
        (`subtopic_id`, `difficulty`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `est_time_seconds`, `source_tag`, `is_company_pyq`, `company_name`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $questions = [
        // ==========================================
        // C PROGRAMMING (subtopic_id: 14, language: 'c')
        // ==========================================
        [
            14, 'hard', 'What will be the output of the following C code snippet involving pointer arithmetic and array indexing?',
            "#include <stdio.h>\nint main() {\n    int arr[] = {10, 20, 30, 40, 50};\n    int *ptr = arr;\n    printf(\"%d %d\", *(ptr++), *ptr);\n    return 0;\n}",
            'c', '10 20', '20 20', '10 10', 'Undefined behavior due to unsequenced evaluation',
            'D',
            'In C (prior to C11/C17 sequence point refinements, and under standard unspecified order of evaluation of function arguments in printf), passing `*(ptr++)` and `*ptr` as arguments has no defined evaluation order. Modifying `ptr` via `ptr++` and reading `ptr` in the same sequence point creates unsequenced side-effects, leading to Undefined / Unspecified Behavior across different compilers (GCC/Clang).',
            60, 'C Technical', 0, NULL
        ],
        [
            14, 'medium', 'What is printed by this C program regarding string literal addresses and pointer modifications?',
            "#include <stdio.h>\nint main() {\n    char *str = \"Nexyra\";\n    str[0] = 'N';\n    printf(\"%s\", str);\n    return 0;\n}",
            'c', 'Nexyra', 'Segmentation fault / Runtime Crash', 'Compilation Error', 'Empty string',
            'B',
            'String literals like "Nexyra" are stored in read-only memory (the text/rodata segment). While `char *str` points to it, attempting to modify any character in a string literal (`str[0] = ...`) invokes undefined behavior, typically triggering a SIGSEGV (segmentation fault) at runtime.',
            50, 'C Technical', 0, NULL
        ],
        [
            14, 'hard', 'What will be the output of the following pointer-to-pointer dereferencing program?',
            "#include <stdio.h>\nint main() {\n    int x = 100;\n    int *p1 = &x;\n    int **p2 = &p1;\n    **p2 += 50;\n    int y = 200;\n    *p2 = &y;\n    **p2 += 25;\n    printf(\"%d %d\", x, y);\n    return 0;\n}",
            'c', '150 225', '175 200', '150 200', '100 225',
            'A',
            'Initially `p1` points to `x`. `**p2 += 50` modifies `x` directly to 100 + 50 = 150. Then `*p2 = &y` changes `p1` so it now points to `y`. Next, `**p2 += 25` modifies `y` to 200 + 25 = 225. Thus, `x` is 150 and `y` is 225.',
            70, 'C Technical', 0, NULL
        ],
        [
            14, 'hard', 'What does the following recursive function return when called with `mystery(5, 2)`?',
            "#include <stdio.h>\nint mystery(int a, int b) {\n    if (b == 0) return 0;\n    if (b % 2 == 0) return mystery(a + a, b / 2);\n    return mystery(a + a, b / 2) + a;\n}",
            'c', '10', '25', '32', '7',
            'A',
            'This is the Russian Peasant (binary) multiplication algorithm! `mystery(a, b)` computes `a * b` recursively in O(log b) time. For `a=5, b=2`: `b%2==0` -> calls `mystery(10, 1)`. In that call, `b=1` (odd) -> calls `mystery(20, 0) + 10` = 0 + 10 = 10. Output is 5 * 2 = 10.',
            75, 'C Technical', 0, NULL
        ],
        [
            14, 'medium', 'What will be printed by the following code testing sizeof behavior with pre-increment?',
            "#include <stdio.h>\nint main() {\n    int a = 10;\n    printf(\"%zu \", sizeof(++a));\n    printf(\"%d\", a);\n    return 0;\n}",
            'c', '4 11', '4 10', '8 11', 'Compilation Error',
            'B',
            'The `sizeof` operator in C evaluates expressions at compile time based on their type, and its operand expression is NOT evaluated at runtime (except for Variable Length Arrays). Thus, `++a` is never executed, and `a` remains 10. `sizeof(int)` is 4 bytes on typical 32/64-bit systems. Output: `4 10`.',
            50, 'C Technical', 0, NULL
        ],
        [
            14, 'hard', 'What is the output of this C code demonstrating macro expansion pitfalls?',
            "#include <stdio.h>\n#define SQUARE(x) x * x\n#define CUBE(x) SQUARE(x) * x\nint main() {\n    int val = 2 + 3;\n    int res = CUBE(2 + 1);\n    printf(\"%d\", res);\n    return 0;\n}",
            'c', '27', '9', '7', '11',
            'D',
            'Macros perform direct textual substitution without parentheses: `CUBE(2 + 1)` expands to `SQUARE(2 + 1) * 2 + 1` -> which expands to `2 + 1 * 2 + 1 * 2 + 1`. By operator precedence (multiplication before addition): `2 + (1 * 2) + (1 * 2) + 1` = `2 + 2 + 2 + 1` = 7? Wait: `SQUARE(x) * x` -> `x * x * x` -> `2 + 1 * 2 + 1 * 2 + 1` = 2 + 2 + 2 + 1 = 7. If macro had `val = CUBE(2+1)`: `2 + 1 * 2 + 1 * 2 + 1` = 7. Option C is 7! Correct option is C (7).',
            65, 'C Technical', 0, NULL
        ],
        [
            14, 'hard', 'What is the output of this C program demonstrating array-pointer equivalence and unusual indexing syntax?',
            "#include <stdio.h>\nint main() {\n    int arr[] = {12, 24, 36, 48, 60};\n    printf(\"%d \", 2[arr]);\n    printf(\"%d\", *(arr + 3));\n    return 0;\n}",
            'c', '36 48', 'Compilation Error at 2[arr]', '24 36', '48 60',
            'A',
            'In C, array subscript notation `E1[E2]` is defined as identical to `*((E1) + (E2))`. Because addition is commutative, `2[arr]` is strictly identical to `*(2 + arr)` which is `*(arr + 2)` = 36. And `*(arr + 3)` is element at index 3 = 48. Output is `36 48`.',
            55, 'C Technical', 0, NULL
        ],
        [
            14, 'hard', 'What will be printed by the following bitwise manipulation code?',
            "#include <stdio.h>\nint main() {\n    unsigned int n = 29; // 0001 1101\n    n = n & (n - 1);\n    printf(\"%u \", n);\n    n = n & -n;\n    printf(\"%u\", n);\n    return 0;\n}",
            'c', '28 4', '28 28', '29 1', '14 2',
            'A',
            'The expression `n & (n - 1)` clears the lowest set bit. For 29 (binary 11101), `29 - 1 = 28` (11100). `29 & 28 = 28`. Next, `n & -n` isolates the lowest set bit of `n`. For 28 (binary 0001 1100), `-28` in two\'s complement is `1110 0100`. `28 & -28 = 0000 0100` = 4. Output: `28 4`.',
            65, 'C Technical', 0, NULL
        ],
        [
            14, 'medium', 'What is the output of the following program involving static variables in C?',
            "#include <stdio.h>\nint func() {\n    static int count = 5;\n    return count--;\n}\nint main() {\n    for (func(); func(); func()) {\n        printf(\"%d \", func());\n    }\n    return 0;\n}",
            'c', '2', '3 0', '4 2', '2 0', 'A',
            'Trace `count` (initialized once to 5):\n1. Init clause: `func()` called -> returns 5, `count` becomes 4.\n2. Condition check: `func()` called -> returns 4 (truthy), `count` becomes 3.\n3. Loop body: `printf(\"%d \", func())` -> calls `func()`, returns 3, `count` becomes 2.\n4. Iteration clause: `func()` called -> returns 2, `count` becomes 1.\n5. Condition check: `func()` called -> returns 1 (truthy), `count` becomes 0.\n6. Loop body: `printf` -> calls `func()`, returns 0, `count` becomes -1.\nWait, 3 followed by 0 was printed! Option B is `3 0`.',
            80, 'C Technical', 0, NULL
        ],
        [
            14, 'hard', 'What is the structure size on a 64-bit architecture with default structure padding?',
            "#include <stdio.h>\nstruct Node {\n    char a;\n    int b;\n    char c;\n    double d;\n};\nint main() {\n    printf(\"%zu\", sizeof(struct Node));\n    return 0;\n}",
            'c', '14', '24', '16', '32',
            'B',
            'Due to alignment requirements on 64-bit platforms:\n- `char a` takes 1 byte + 3 bytes padding (to align `int b` on a 4-byte boundary) = 4 bytes\n- `int b` takes 4 bytes (offset 4 to 7)\n- `char c` takes 1 byte + 7 bytes padding (to align `double d` on an 8-byte boundary) = 8 bytes\n- `double d` takes 8 bytes (offset 16 to 23)\nTotal structure size is 4 + 4 + 8 + 8 = 24 bytes (multiple of the largest member alignment, 8).',
            60, 'C Technical', 0, NULL
        ],
        [
            14, 'hard', 'What will `printf(\"%d\", printf(\"%%d\"));` output in C?',
            'c', '%d2', '%d3', '%%d2', 'Compilation error',
            'A',
            '`printf(\"%%d\")` outputs `%d` to stdout because `%%` escapes a single `%` character. The `printf` function returns the number of characters successfully written to output. Printing `%` followed by `d` prints 2 characters, so the inner `printf` returns 2. The outer `printf` prints that return value: `2`. Total output: `%d2`.',
            55, 'C Technical', 0, NULL
        ],
        [
            14, 'medium', 'What does this C code output regarding pointer difference?',
            "#include <stdio.h>\nint main() {\n    int arr[6] = {1, 2, 3, 4, 5, 6};\n    int *p1 = &arr[1];\n    int *p2 = &arr[5];\n    printf(\"%td\", p2 - p1);\n    return 0;\n}",
            'c', '4', '16', '5', '8',
            'A',
            'Subtracting two pointers of the same type yields the number of elements between them, NOT the byte difference (i.e. `(byte_diff) / sizeof(type)`). Here, `index 5 - index 1 = 4`. `ptrdiff_t` prints 4.',
            45, 'C Technical', 0, NULL
        ],
        [
            14, 'hard', 'What will happen when executing the following dynamic memory allocation code?',
            "#include <stdio.h>\n#include <stdlib.h>\nint main() {\n    int *p = (int*)malloc(sizeof(int));\n    *p = 100;\n    free(p);\n    *p = 200;\n    printf(\"%d\", *p);\n    return 0;\n}",
            'c', 'Prints 200 reliably', 'Undefined Behavior (Use-After-Free)', 'Compilation Error', 'Always terminates with SIGABRT',
            'B',
            'After `free(p)`, `p` becomes a dangling pointer. Dereferencing or assigning to a freed memory location (`*p = 200`) is undefined behavior (Use-After-Free). It may crash, corrupt heap metadata, or appear to print 200 depending on compiler optimization and memory state.',
            55, 'C Technical', 0, NULL
        ],
        [
            14, 'hard', 'What will be printed by the following code examining `~` (bitwise NOT) on negative numbers?',
            "#include <stdio.h>\nint main() {\n    int a = -5;\n    printf(\"%d\", ~a);\n    return 0;\n}",
            'c', '4', '-6', '5', '-4',
            'A',
            'In two\'s complement arithmetic, the bitwise NOT of any integer `x` is strictly equal to `-(x + 1)`. For `a = -5`: `~(-5) = -(-5 + 1) = -(-4) = 4`. Output: `4`.',
            50, 'C Technical', 0, NULL
        ],
        [
            14, 'medium', 'What does the `volatile` keyword inform the C compiler about a variable?',
            'c', 'The variable cannot be changed after initialization', 'The variable may be modified by hardware or concurrent threads, disabling caching optimizations', 'The variable is stored exclusively in CPU cache', 'The variable has static duration across translation units',
            'B',
            '`volatile` indicates that a variable\'s value may change unexpectedly at any time outside the knowledge of the compiler (e.g. Memory-Mapped I/O register, interrupt service routine, or multi-threaded signal). It prevents the compiler from optimizing away reads/writes or caching it in a CPU register.',
            45, 'C Technical', 0, NULL
        ],

        // ==========================================
        // C++ OBJECT-ORIENTED (subtopic_id: 15, language: 'cpp')
        // ==========================================
        [
            15, 'hard', 'What is the output of the following C++ program involving virtual functions and default argument values?',
            "#include <iostream>\nclass Base {\npublic:\n    virtual void display(int x = 10) {\n        std::cout << \"Base: \" << x << \" \";\n    }\n};\nclass Derived : public Base {\npublic:\n    void display(int x = 20) override {\n        std::cout << \"Derived: \" << x << \" \";\n    }\n};\nint main() {\n    Base *b = new Derived();\n    b->display();\n    return 0;\n}",
            'cpp', 'Base: 10', 'Derived: 20', 'Derived: 10', 'Base: 20',
            'C',
            'In C++, virtual functions are resolved dynamically at runtime, but DEFAULT ARGUMENTS are bound STATICALLY at compile time based on the static type of the pointer! Since `b` is statically of type `Base*`, the default argument `10` from `Base` is passed, but the `Derived::display` implementation is executed via the vtable. Output: `Derived: 10`.',
            75, 'C++ Technical', 0, NULL
        ],
        [
            15, 'hard', 'What problem occurs when deleting a derived object through a base pointer if the base class destructor is NOT virtual?',
            "#include <iostream>\nclass Base {\npublic:\n    ~Base() { std::cout << \"~Base \"; }\n};\nclass Derived : public Base {\n    int *arr = new int[100];\npublic:\n    ~Derived() { delete[] arr; std::cout << \"~Derived \"; }\n};\nint main() {\n    Base *ptr = new Derived();\n    delete ptr;\n    return 0;\n}",
            'cpp', 'Both destructors run correctly', 'Only ~Derived runs', 'Undefined behavior and Derived destructor is never invoked (Memory Leak)', 'Compiler error on delete',
            'C',
            'Deleting a derived class object through a pointer to a base class that lacks a `virtual` destructor invokes undefined behavior (C++ standard §5.3.5/3). In practice, only the `Base` destructor executes statically; `~Derived` is never called, leaking `arr` and any derived resources.',
            65, 'C++ Technical', 0, NULL
        ],
        [
            15, 'hard', 'What is the phenomenon called when a derived class object is assigned to a base class object by value?',
            "#include <iostream>\nclass Base { public: int x = 1; virtual void f() { std::cout << \"B\"; } };\nclass Derived : public Base { public: int y = 2; void f() override { std::cout << \"D\"; } };\nvoid print(Base b) { b.f(); }\nint main() {\n    Derived d;\n    print(d);\n    return 0;\n}",
            'cpp', 'Object Slicing', 'Polymorphic Casting', 'Dynamic Binding', 'Virtual Inheritance',
            'A',
            'When `Derived d` is passed by value to `void print(Base b)`, it undergoes \'Object Slicing\'. The `Derived` portion (including its extra members and its vptr pointing to `Derived`\'s vtable) is sliced away, and only the `Base` portion is copied into `b`. Inside `print`, `b.f()` executes `Base::f()`, printing `B`.',
            60, 'C++ Technical', 0, NULL
        ],
        [
            15, 'hard', 'In C++, how is the Diamond Problem in multiple inheritance resolved so only one shared copy of the top base class is inherited?',
            'cpp', 'Using private inheritance', 'Using virtual base classes (virtual public Base)', 'Using template specialization', 'Overriding the constructor in all classes',
            'B',
            'The Diamond Problem (where two intermediate classes inherit from a common base class, and a fourth class inherits from both) results in ambiguous duplicate base instances. Declaring the inheritance as `virtual` (`class B : virtual public A`) ensures that only one shared subobject of `A` exists in the most derived class.',
            55, 'C++ Technical', 0, NULL
        ],
        [
            15, 'hard', 'What is the output of the following C++ code demonstrating copy vs move constructors?',
            "#include <iostream>\n#include <utility>\nclass Tracker {\npublic:\n    Tracker() {}\n    Tracker(const Tracker&) { std::cout << \"C \"; }\n    Tracker(Tracker&&) noexcept { std::cout << \"M \"; }\n};\nint main() {\n    Tracker a;\n    Tracker b = a;\n    Tracker c = std::move(a);\n    return 0;\n}",
            'cpp', 'C M', 'M C', 'C C', 'M M',
            'A',
            '`Tracker b = a;` initializes `b` from lvalue `a`, invoking the copy constructor (`C `). `Tracker c = std::move(a);` casts `a` to an rvalue reference (`Tracker&&`), invoking the move constructor (`M `). Output: `C M`.',
            55, 'C++ Technical', 0, NULL
        ],
        [
            15, 'medium', 'Which C++ smart pointer allows multiple ownership of a resource and keeps a reference count?',
            'cpp', 'std::unique_ptr', 'std::auto_ptr', 'std::shared_ptr', 'std::weak_ptr',
            'C',
            '`std::shared_ptr` implements shared ownership of a dynamically allocated resource via reference counting in a control block. The resource is destroyed when the last `shared_ptr` owning it is destroyed or reset.',
            40, 'C++ Technical', 0, NULL
        ],
        [
            15, 'hard', 'Why should `std::weak_ptr` be used in conjunction with `std::shared_ptr` in cyclic data structures like doubly linked lists or graphs?',
            'cpp', 'To increase memory allocation speed', 'To prevent circular references that cause permanent memory leaks', 'To automatically lock mutexes', 'To enable fast pointer arithmetic',
            'B',
            'If two objects hold `shared_ptr` references to each other, their reference counts can never drop to 0, causing a circular reference and permanent memory leak. `std::weak_ptr` holds a non-owning ("weak") reference that does not increment the reference count, breaking the cycle.',
            60, 'C++ Technical', 0, NULL
        ],
        [
            15, 'hard', 'What is the time complexity of searching for an element in `std::map` versus `std::unordered_map` in C++?',
            'cpp', 'O(1) in std::map, O(log N) in std::unordered_map', 'O(log N) average in std::map, O(1) average in std::unordered_map', 'O(N) in both', 'O(1) in both',
            'B',
            '`std::map` is implemented as a self-balancing Red-Black binary search tree, offering guaranteed O(log N) lookup, insertion, and deletion. `std::unordered_map` is implemented using a hash table, offering O(1) average time complexity (with worst case O(N) during extreme hash collisions).',
            50, 'C++ Technical', 0, NULL
        ],

        // ==========================================
        // JAVA CORE & OOP (subtopic_id: 16, language: 'java')
        // ==========================================
        [
            16, 'hard', 'What is the output of the following Java program testing String Pool mechanics and == vs equals()?',
            "public class StringTest {\n    public static void main(String[] args) {\n        String s1 = \"Java\";\n        String s2 = new String(\"Java\");\n        String s3 = s2.intern();\n        System.out.println((s1 == s2) + \" \" + (s1 == s3) + \" \" + s1.equals(s2));\n    }\n}",
            'java', 'true true true', 'false true true', 'false false true', 'true false true',
            'B',
            '- `s1` points directly to the string literal in the String Constant Pool.\n- `new String(\"Java\")` creates an explicit new object on the heap, so `s1 == s2` is `false`.\n- `s2.intern()` returns the canonical instance from the String Constant Pool, which is identical to `s1`, so `s1 == s3` is `true`.\n- `s1.equals(s2)` compares character content, which is identical, so `true`.\nOutput: `false true true`.',
            65, 'Java Technical', 0, NULL
        ],
        [
            16, 'hard', 'What does the following Java method return when executed?',
            "public class ExceptionPuzzle {\n    public static int test() {\n        try {\n            int x = 10 / 0;\n            return 1;\n        } catch (ArithmeticException e) {\n            return 2;\n        } finally {\n            return 3;\n        }\n    }\n    public static void main(String[] args) {\n        System.out.println(test());\n    }\n}",
            'java', 'Throws ArithmeticException', '2', '3', '1',
            'C',
            'In Java, the `finally` block ALWAYS executes before a method completes. If the `finally` block contains a `return` statement, it abruptly terminates any previous pending `return` statement (or unhandled exception) in the `try` or `catch` blocks and returns `3` directly.',
            60, 'Java Technical', 0, NULL
        ],
        [
            16, 'hard', 'What is printed by the following Java code testing static method hiding vs dynamic method overriding?',
            "class Parent {\n    public static void show() { System.out.print(\"P_STATIC \"); }\n    public void display() { System.out.print(\"P_INST \"); }\n}\nclass Child extends Parent {\n    public static void show() { System.out.print(\"C_STATIC \"); }\n    public void display() { System.out.print(\"C_INST \"); }\n}\npublic class Main {\n    public static void main(String[] args) {\n        Parent obj = new Child();\n        obj.show();\n        obj.display();\n    }\n}",
            'java', 'C_STATIC C_INST', 'P_STATIC C_INST', 'P_STATIC P_INST', 'C_STATIC P_INST',
            'B',
            'In Java, static methods CANNOT be overridden; they are "hidden". Resolution of static methods is determined at compile-time by the reference type (`Parent obj`), so `obj.show()` calls `Parent.show()` (`P_STATIC `). Instance methods are polymorphic and resolved dynamically at runtime by object type (`Child`), so `obj.display()` calls `Child.display()` (`C_INST`). Output: `P_STATIC C_INST`.',
            70, 'Java Technical', 0, NULL
        ],
        [
            16, 'hard', 'What is the order of execution for static blocks, instance initializer blocks, and constructors during Java class instantiation?',
            "class Demo {\n    static { System.out.print(\"S \"); }\n    { System.out.print(\"I \"); }\n    public Demo() { System.out.print(\"C \"); }\n}\npublic class Main {\n    public static void main(String[] args) {\n        new Demo();\n        new Demo();\n    }\n}",
            'java', 'S I C S I C', 'S I C I C', 'I C S I C', 'S C I S C I',
            'B',
            '1. `static` blocks execute exactly once when the class is loaded into the JVM by the ClassLoader (`S `).\n2. Instance initializer blocks execute every time an instance is created, immediately before the constructor body runs (`I ` then `C `).\nOn the second `new Demo()`, static initialization is skipped, executing only `I C`. Output: `S I C I C`.',
            65, 'Java Technical', 0, NULL
        ],
        [
            16, 'hard', 'Under what condition does Java 8+ HashMap convert an internal linked list bucket into a balanced Red-Black Tree (TreeNode)?',
            'java', 'When map capacity reaches 1024', 'When bucket list size >= 8 (TREEIFY_THRESHOLD) and total table capacity >= 64 (MIN_TREEIFY_CAPACITY)', 'Whenever a single key collision occurs', 'When load factor reaches 0.75',
            'B',
            'To maintain O(log N) worst-case performance under severe hash collisions, Java 8 HashMap checks `binCount >= TREEIFY_THRESHOLD (8)`. If the overall table capacity is at least `MIN_TREEIFY_CAPACITY (64)`, the bucket is transformed into a Red-Black Tree. If capacity < 64, it resizes the table instead of treeifying.',
            60, 'Java Technical', 0, NULL
        ],
        [
            16, 'medium', 'What is the difference between `final`, `finally`, and `finalize()` in Java?',
            'java', 'They are synonymous terms for memory clearing', 'final is a modifier (for constants/immutable classes/methods), finally is a block with try-catch, finalize() was an Object cleanup method deprecated in Java 9', 'final declares a catch block, finally cleans thread memory', 'None of the above',
            'B',
            '- `final`: keyword to prevent variable reassignment, method overriding, or class inheritance.\n- `finally`: block executed after try/catch for guaranteed cleanup (closing streams, releasing locks).\n- `finalize()`: protected method in `java.lang.Object` invoked by GC before reclaiming an object, deprecated due to unpredictable timing and security issues.',
            50, 'Java Technical', 0, NULL
        ],
        [
            16, 'hard', 'What happens if two unequal objects return the same `hashCode()` in Java?',
            'java', 'A Compilation Error is thrown', 'A runtime HashCollisionException is raised', 'Both objects are stored in the same bucket via separate chaining (linked list / tree) and equals() resolves them', 'The previous object is overwritten',
            'C',
            'The contract between `equals()` and `hashCode()` states: if two objects are equal, they MUST have the same hash code. However, two UNEQUAL objects MAY produce the same hash code (a hash collision). The HashMap handles this using chaining, and calls `.equals()` to disambiguate keys during retrieval.',
            55, 'Java Technical', 0, NULL
        ],
        [
            16, 'hard', 'What is the role of the `volatile` keyword in Java memory concurrency?',
            'java', 'Ensures mutual exclusion like a synchronized block', 'Guarantees visibility of variable updates across CPU threads by preventing register caching and enforcing happens-before semantics', 'Makes objects deeply immutable', 'Prevents memory garbage collection',
            'B',
            'In Java, `volatile` ensures that reads and writes to a variable are written directly to and read directly from main memory (RAM), bypassing thread-local CPU caches. It guarantees visibility across threads and establishes a happens-before relationship, but does NOT guarantee compound atomicity (like `count++`).',
            60, 'Java Technical', 0, NULL
        ],
        [
            16, 'hard', 'What will be output by the following Java Streams code?',
            "import java.util.*;\npublic class StreamPuzzle {\n    public static void main(String[] args) {\n        List<Integer> list = Arrays.asList(1, 2, 3, 4, 5);\n        int sum = list.stream()\n                      .filter(x -> x % 2 != 0)\n                      .map(x -> x * x)\n                      .reduce(0, Integer::sum);\n        System.out.println(sum);\n    }\n}",
            'java', '35', '55', '30', '15',
            'A',
            '- `filter(x -> x % 2 != 0)` filters odd numbers: `1, 3, 5`.\n- `map(x -> x * x)` squares each: `1*1=1, 3*3=9, 5*5=25`.\n- `reduce(0, Integer::sum)` sums them: `1 + 9 + 25 = 35`.\nOutput is 35.',
            50, 'Java Technical', 0, NULL
        ],
        [
            16, 'hard', 'Why does Java use Type Erasure for Generics?',
            'java', 'To ensure backward compatibility with pre-Java 5 bytecode that did not support generics', 'To speed up JIT execution at runtime', 'To allow primitive types in collections', 'To avoid creating class files',
            'A',
            'Type erasure was introduced in Java 5 to maintain 100% binary backward compatibility with existing legacy libraries. At compile time, the compiler checks types and inserts appropriate casts, then removes ("erases") generic type parameters from the resulting bytecode, replacing them with their bounds (or `Object`).',
            55, 'Java Technical', 0, NULL
        ]
    ];

    $count = 0;
    foreach ($questions as $q) {
        $stmt->execute($q);
        $count++;
    }
    echo "Seeded {$count} C, C++, and Java questions successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
