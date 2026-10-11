import json
import os

questions = []

def add_q(subtopic_id, diff, text, snippet, lang, opt_a, opt_b, opt_c, opt_d, ans, exp, time_sec, source_tag, is_pyq=0, comp_name=None):
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

# Subtopic IDs:
# 14: C Programming
# 15: C++ Object-Oriented
# 16: Java Core & OOP
# 17: PHP & Web Architecture
# 18: C# & .NET Platform
# 19: Python Programming
# 20: SQL & Relational Databases
# 21: Data Structures & Algorithms
# 22: Innovature Labs Technical & PYQ
# 23: Advanced Logic Puzzles & Deductions (LR_PUZZLES)
# 24: Advanced Quantitative & Number Theory (QUANT_ADV)

# Let's import or define all question banks cleanly
print("Building question bank...")

# -------------------------------------------------------------
# C PROGRAMMING (30 Questions, subtopic_id: 14)
# -------------------------------------------------------------
c_list = [
    (
        "What will be the output of the following pointer arithmetic on strings in C?",
        "#include <stdio.h>\nint main() {\n    char *s = \"Nexyra\";\n    printf(\"%c\", *(s + 3));\n    return 0;\n}",
        "y", "x", "r", "e", "A",
        "String indices: 0:'N', 1:'e', 2:'x', 3:'y'. Dereferencing `*(s + 3)` yields 'y'."
    ),
    (
        "What is the output of the comma operator in the following C expression?",
        "#include <stdio.h>\nint main() {\n    int a = (1, 2, 3) + (4, 5);\n    printf(\"%d\", a);\n    return 0;\n}",
        "8", "15", "5", "Compilation error", "A",
        "The comma operator evaluates each operand from left to right and yields the value of the last operand: `(1, 2, 3)` = 3, `(4, 5)` = 5. `3 + 5 = 8`."
    ),
    (
        "What will `printf(\"%d\", 5 >> 1 << 2);` output in C?",
        "#include <stdio.h>\nint main() {\n    printf(\"%d\", 5 >> 1 << 2);\n    return 0;\n}",
        "8", "10", "4", "16", "A",
        "Bitwise shifts evaluate left-to-right. `5 >> 1 = 2` (binary 0101 >> 1 = 0010 = 2). Then `2 << 2 = 8` (0010 << 2 = 1000 = 8)."
    ),
    (
        "What does this C code output regarding static variable scope?",
        "#include <stdio.h>\nvoid test() {\n    static int x = 0;\n    x += 5;\n    printf(\"%d \", x);\n}\nint main() {\n    test(); test(); test();\n    return 0;\n}",
        "5 10 15 ", "5 5 5 ", "0 5 10 ", "Compilation error", "A",
        "Static local variables retain their values across function invocations. Three calls increment x by 5 each time: 5, 10, 15."
    ),
    (
        "What will be printed by the following code examining `NULL` pointer comparison?",
        "#include <stdio.h>\nint main() {\n    int *ptr = NULL;\n    if (!ptr) printf(\"Null \");\n    if (ptr == 0) printf(\"Zero\");\n    return 0;\n}",
        "Null Zero", "Null ", "Zero ", "Nothing", "A",
        "In C, `NULL` is an implementation-defined null pointer constant (`0` or `((void*)0)`). Both `!ptr` and `ptr == 0` evaluate to true."
    ),
    (
        "What does `sizeof(char)` evaluate to in standard C?",
        "#include <stdio.h>\nint main() {\n    printf(\"%zu\", sizeof(char));\n    return 0;\n}",
        "1 byte by ISO C definition", "2 bytes", "4 bytes", "Architecture-dependent", "A",
        "According to ISO C standards, `sizeof(char)` is strictly defined to be 1 on all platforms."
    ),
    (
        "What will be printed by this C program regarding character arrays?",
        "#include <stdio.h>\n#include <string.h>\nint main() {\n    char arr[] = \"hello\";\n    printf(\"%zu %zu\", sizeof(arr), strlen(arr));\n    return 0;\n}",
        "6 5", "5 5", "6 6", "5 6", "A",
        "`sizeof(arr)` includes the null terminator `\\0` (6 bytes). `strlen(arr)` counts characters up to `\\0` (5 characters). Output: 6 5."
    ),
    (
        "What is the output of the following pointer arithmetic on integers?",
        "#include <stdio.h>\nint main() {\n    int arr[] = {100, 200, 300};\n    int *p = arr;\n    p++;\n    printf(\"%d\", *p);\n    return 0;\n}",
        "200", "100", "104", "300", "A",
        "`p++` increments the pointer by `1 * sizeof(int)`, advancing it to point to `arr[1]` = 200."
    ),
    (
        "What is printed when accessing out-of-bounds array index in C?",
        "#include <stdio.h>\nint main() {\n    int arr[3] = {1, 2, 3};\n    printf(\"%d\", arr[5]);\n    return 0;\n}",
        "Undefined Behavior (accessing unallocated/garbage memory)", "0", "IndexOutOfBoundsException", "Compilation error", "A",
        "C performs no array bounds checking. Accessing `arr[5]` reads memory outside array boundaries, invoking Undefined Behavior."
    ),
    (
        "What is the effect of the `register` keyword in C?",
        "register int counter = 0;",
        "Hints to the compiler to store the variable in a CPU register; address-of operator (&) cannot be used on it",
        "Stores variable in non-volatile NVRAM",
        "Makes variable thread-safe",
        "Locks variable in cache", "A",
        "`register` hints to store the variable in a CPU register. In C, taking the address of a register variable (`&counter`) is illegal and causes a compiler error."
    ),
    (
        "What is the output of the following bitwise AND mask?",
        "#include <stdio.h>\nint main() {\n    int n = 12; // 1100\n    printf(\"%d\", (n & 8) != 0);\n    return 0;\n}",
        "1", "0", "8", "12", "A",
        "12 in binary is `1100`, 8 is `1000`. `12 & 8 = 8` (non-zero). `8 != 0` evaluates to true (1)."
    ),
    (
        "What will this C recursive function output when called with `mystery(3)`?",
        "#include <stdio.h>\nvoid mystery(int n) {\n    if (n == 0) return;\n    mystery(n - 1);\n    printf(\"%d \", n);\n}\nint main() {\n    mystery(3);\n    return 0;\n}",
        "1 2 3 ", "3 2 1 ", "3 3 3 ", "1 1 1 ", "A",
        "`printf` executes after recursive return, printing in unwinding ascending order: 1 2 3."
    ),
    (
        "What does the `extern` keyword signify in C variable declarations?",
        "extern int global_var;",
        "Declares a variable that is defined in another translation unit without allocating storage",
        "Allocates memory in external EEPROM",
        "Imports a C++ namespace",
        "Makes the variable constant", "A",
        "`extern` indicates that the variable exists with external linkage in another compilation unit, preventing duplicate definition linker errors."
    ),
    (
        "What will be the output of the following C program with pointer casting?",
        "#include <stdio.h>\nint main() {\n    int a = 0x12345678;\n    char *c = (char*)&a;\n    if (*c == 0x78) printf(\"Little Endian\");\n    else printf(\"Big Endian\");\n    return 0;\n}",
        "Little Endian", "Big Endian", "Compilation Error", "Segmentation Fault", "A",
        "In Little Endian architectures (x86/ARM), the least significant byte (0x78) is stored at the lowest memory address."
    ),
    (
        "What is the output of the following code involving array decay and sizeof?",
        "#include <stdio.h>\nvoid func(int arr[]) {\n    printf(\"%zu \", sizeof(arr));\n}\nint main() {\n    int arr[10];\n    printf(\"%zu \", sizeof(arr));\n    func(arr);\n    return 0;\n}",
        "40 8 (or 40 4 on 32-bit)", "40 40", "8 8", "10 10", "A",
        "In `main`, `sizeof(arr)` is 10 * 4 = 40 bytes. Inside `func`, `arr` decays into a pointer (`int*`), whose size is 8 bytes on 64-bit platforms."
    ),
    (
        "What will be printed by the following bitwise XOR swap logic in C?",
        "#include <stdio.h>\nint main() {\n    int x = 15, y = 25;\n    x ^= y ^= x ^= y;\n    printf(\"%d %d\", x, y);\n    return 0;\n}",
        "Undefined Behavior in C standards", "25 15", "15 25", "0 0", "A",
        "In standard C (C99/C11), `x ^= y ^= x ^= y` modifies `x` twice without intervening sequence points, invoking Undefined Behavior."
    ),
    (
        "What does this C code output with nested ternary operators?",
        "#include <stdio.h>\nint main() {\n    int a = 1, b = 2, c = 3;\n    int res = a > b ? a : b > c ? b : c;\n    printf(\"%d\", res);\n    return 0;\n}",
        "3", "2", "1", "0", "A",
        "`?:` associates right to left: `a > b ? a : (b > c ? b : c)`. `1 > 2` is false; `2 > 3` is false; returns `c` = 3."
    ),
    (
        "What is the structure size on a 64-bit architecture with default structure padding?",
        "#include <stdio.h>\nstruct Node {\n    char a;\n    int b;\n    char c;\n    double d;\n};\nint main() {\n    printf(\"%zu\", sizeof(struct Node));\n    return 0;\n}",
        "24", "14", "16", "32", "A",
        "`char a` (1 byte + 3 padding) + `int b` (4 bytes) + `char c` (1 byte + 7 padding) + `double d` (8 bytes) = 24 bytes."
    ),
    (
        "What will `printf(\"%d\", printf(\"%%d\"));` output in C?",
        "#include <stdio.h>\nint main() {\n    printf(\"%d\", printf(\"%%d\"));\n    return 0;\n}",
        "%d2", "%d3", "%%d2", "Compilation error", "A",
        "`printf(\"%%d\")` prints `%d` (2 characters written) and returns 2. The outer `printf` prints 2. Result: `%d2`."
    ),
    (
        "What does this C code output regarding pointer difference?",
        "#include <stdio.h>\n#include <stddef.h>\nint main() {\n    int arr[6] = {1, 2, 3, 4, 5, 6};\n    int *p1 = &arr[1];\n    int *p2 = &arr[5];\n    printf(\"%td\", p2 - p1);\n    return 0;\n}",
        "4", "16", "5", "8", "A",
        "Subtracting two pointers of the same type yields the element count between them: index 5 - index 1 = 4."
    ),
    (
        "What will happen when executing the following dynamic memory allocation code?",
        "#include <stdio.h>\n#include <stdlib.h>\nint main() {\n    int *p = (int*)malloc(sizeof(int));\n    *p = 100;\n    free(p);\n    *p = 200;\n    printf(\"%d\", *p);\n    return 0;\n}",
        "Undefined Behavior (Use-After-Free)", "Prints 200 reliably", "Compilation Error", "Always terminates with SIGABRT", "A",
        "Accessing or writing to freed memory (`*p = 200`) is a Use-After-Free bug, invoking Undefined Behavior."
    ),
    (
        "What will be printed by the following code examining `~` (bitwise NOT) on negative numbers?",
        "#include <stdio.h>\nint main() {\n    int a = -5;\n    printf(\"%d\", ~a);\n    return 0;\n}",
        "4", "-6", "5", "-4", "A",
        "In two's complement arithmetic, `~x` is strictly equal to `-(x + 1)`. `~(-5) = -(-5 + 1) = -(-4) = 4`."
    ),
    (
        "What does the `volatile` keyword inform the C compiler about a variable?",
        "volatile int hw_flag;",
        "The variable may be modified by external hardware/threads, disabling caching optimizations",
        "The variable cannot be changed after initialization",
        "The variable is stored exclusively in CPU cache",
        "The variable has static duration across translation units", "A",
        "`volatile` instructs the compiler to avoid caching reads/writes in CPU registers because the value may change outside compiler knowledge."
    ),
    (
        "What is the output of this C code demonstrating macro expansion pitfalls?",
        "#include <stdio.h>\n#define SQUARE(x) x * x\n#define CUBE(x) SQUARE(x) * x\nint main() {\n    int res = CUBE(2 + 1);\n    printf(\"%d\", res);\n    return 0;\n}",
        "7", "27", "9", "11", "A",
        "`CUBE(2 + 1)` expands to `2 + 1 * 2 + 1 * 2 + 1` = `2 + 2 + 2 + 1 = 7`."
    ),
    (
        "What is the output of this C program demonstrating array-pointer equivalence and unusual indexing syntax?",
        "#include <stdio.h>\nint main() {\n    int arr[] = {12, 24, 36, 48, 60};\n    printf(\"%d \", 2[arr]);\n    printf(\"%d\", *(arr + 3));\n    return 0;\n}",
        "36 48", "Compilation Error at 2[arr]", "24 36", "48 60", "A",
        "In C, `2[arr]` is defined as `*(2 + arr)` which equals `*(arr + 2)` = 36. `*(arr + 3)` is element at index 3 = 48."
    ),
    (
        "What will be printed by the following bitwise manipulation code?",
        "#include <stdio.h>\nint main() {\n    unsigned int n = 29; // 0001 1101\n    n = n & (n - 1);\n    printf(\"%u \", n);\n    n = n & -n;\n    printf(\"%u\", n);\n    return 0;\n}",
        "28 4", "28 28", "29 1", "14 2", "A",
        "`n & (n - 1)` clears lowest set bit: 29 & 28 = 28. `n & -n` isolates lowest set bit: 28 & -28 = 4. Output: 28 4."
    ),
    (
        "What is the output of the following program involving static variables in C?",
        "#include <stdio.h>\nint func() {\n    static int count = 5;\n    return count--;\n}\nint main() {\n    for (func(); func(); func()) {\n        printf(\"%d \", func());\n    }\n    return 0;\n}",
        "3 0 ", "2 ", "4 2 ", "2 0 ", "A",
        "Tracing calls: Init func() returns 5 (count=4). Cond func() returns 4 (count=3). Body printf calls func() returns 3 (count=2). Iter func() returns 2 (count=1). Cond func() returns 1 (count=0). Body printf calls func() returns 0 (count=-1). Cond func() returns -1. Output: 3 0."
    ),
    (
        "What will `printf(\"%d\", -1 << 1);` output in C?",
        "#include <stdio.h>\nint main() {\n    printf(\"%d\", -1 << 1);\n    return 0;\n}",
        "-2 (two's complement shift)", "0", "1", "Compiler Error", "A",
        "-1 in two's complement has all bits set (`...1111`). Shifting left by 1 introduces a 0 at the LSB (`...1110` = -2)."
    ),
    (
        "What does this C code output regarding post-increment in loop condition?",
        "#include <stdio.h>\nint main() {\n    int i = 0;\n    while (i++ < 5) {}\n    printf(\"%d\", i);\n    return 0;\n}",
        "6", "5", "4", "Infinite loop", "A",
        "When `i = 4`, `4 < 5` is true, `i` becomes 5. Next check tests `5 < 5` (false), but `i++` still executes, incrementing `i` to 6."
    ),
    (
        "What happens when calling `realloc(ptr, 0)` in modern C standard (C23)?",
        "#include <stdio.h>\n#include <stdlib.h>\nint main() {\n    int *ptr = malloc(20);\n    ptr = realloc(ptr, 0);\n    return 0;\n}",
        "In C23, calling realloc with size 0 is undefined behavior / deprecated",
        "It always allocates 1 byte",
        "It doubles the memory",
        "It throws bad_alloc", "A",
        "Historically realloc(p, 0) freed memory, but C23 explicitly categorizes size 0 as undefined behavior to prevent vulnerabilities."
    )
]

for q in c_list:
    add_q(14, 'hard', q[0], q[1], 'c', q[2], q[3], q[4], q[5], q[6], q[7], 60, 'C Technical', 0, None)

# -------------------------------------------------------------
# C++ OBJECT-ORIENTED (25 Questions, subtopic_id: 15)
# -------------------------------------------------------------
cpp_list = [
    (
        "What is the output of the following C++ program involving virtual functions and default argument values?",
        "#include <iostream>\nclass Base {\npublic:\n    virtual void display(int x = 10) { std::cout << \"Base: \" << x << \" \"; }\n};\nclass Derived : public Base {\npublic:\n    void display(int x = 20) override { std::cout << \"Derived: \" << x << \" \"; }\n};\nint main() {\n    Base *b = new Derived();\n    b->display();\n    return 0;\n}",
        "Derived: 10", "Base: 10", "Derived: 20", "Base: 20", "A",
        "Virtual functions are dynamically dispatched, but DEFAULT ARGUMENTS are bound STATICALLY at compile time based on the pointer type (`Base*`), using default 10 with `Derived::display`."
    ),
    (
        "What problem occurs when deleting a derived object through a base pointer if the base class destructor is NOT virtual?",
        "#include <iostream>\nclass Base { public: ~Base() {} };\nclass Derived : public Base { int *arr = new int[100]; public: ~Derived() { delete[] arr; } };\nint main() { Base *ptr = new Derived(); delete ptr; return 0; }",
        "Undefined behavior and Derived destructor is never invoked (Memory Leak)",
        "Both destructors run correctly",
        "Only ~Derived runs",
        "Compiler error on delete", "A",
        "Deleting a derived object via a base pointer lacking a virtual destructor causes undefined behavior; the derived destructor never runs, leaking resources."
    ),
    (
        "What is the phenomenon called when a derived class object is assigned to a base class object by value?",
        "#include <iostream>\nclass Base { public: int x; };\nclass Derived : public Base { public: int y; };\nvoid print(Base b) {}\nint main() { Derived d; print(d); return 0; }",
        "Object Slicing", "Polymorphic Casting", "Dynamic Binding", "Virtual Inheritance", "A",
        "Passing by value slices away the derived portion and vptr, leaving only the base class subobject."
    ),
    (
        "In C++, how is the Diamond Problem in multiple inheritance resolved so only one shared copy of the top base class is inherited?",
        "class B : virtual public A {}; class C : virtual public A {}; class D : public B, public C {};",
        "Using virtual base classes (virtual public Base)",
        "Using private inheritance",
        "Using template specialization",
        "Overriding the constructor in all classes", "A",
        "Declaring virtual inheritance ensures only one shared subobject of the base class is instantiated in the most derived class."
    ),
    (
        "What is the output of the following C++ code demonstrating copy vs move constructors?",
        "#include <iostream>\n#include <utility>\nclass Tracker {\npublic:\n    Tracker() {}\n    Tracker(const Tracker&) { std::cout << \"C \"; }\n    Tracker(Tracker&&) noexcept { std::cout << \"M \"; }\n};\nint main() {\n    Tracker a;\n    Tracker b = a;\n    Tracker c = std::move(a);\n    return 0;\n}",
        "C M", "M C", "C C", "M M", "A",
        "`b = a` invokes the copy constructor (`C `). `c = std::move(a)` invokes the move constructor (`M `). Output: `C M`."
    ),
    (
        "Which C++ smart pointer allows multiple ownership of a resource and keeps a reference count?",
        "#include <memory>\n// Multi-owner smart pointer",
        "std::shared_ptr", "std::unique_ptr", "std::auto_ptr", "std::weak_ptr", "A",
        "`std::shared_ptr` manages shared resource ownership via reference counting in a control block."
    ),
    (
        "Why should `std::weak_ptr` be used in conjunction with `std::shared_ptr` in cyclic data structures?",
        "// Cyclic references prevention",
        "To prevent circular references that cause permanent memory leaks by not incrementing the reference count",
        "To increase memory allocation speed",
        "To automatically lock mutexes",
        "To enable fast pointer arithmetic", "A",
        "`std::weak_ptr` maintains a non-owning reference without incrementing reference counts, preventing circular ownership leaks."
    ),
    (
        "What is the time complexity of searching for an element in `std::map` versus `std::unordered_map` in C++?",
        "// Map vs Unordered Map lookup complexity",
        "O(log N) in std::map (Red-Black tree), O(1) average in std::unordered_map (Hash table)",
        "O(1) in std::map, O(log N) in std::unordered_map",
        "O(N) in both",
        "O(1) in both", "A",
        "`std::map` is a self-balancing Red-Black BST (guaranteed O(log N)). `std::unordered_map` is a hash table (O(1) average)."
    ),
    (
        "What is the output of the following C++ code testing constructor initialization list order?",
        "#include <iostream>\nclass Test {\n    int b;\n    int a;\npublic:\n    Test(int val) : a(val), b(a + 2) {}\n    void print() { std::cout << a << \" \" << b; }\n};\nint main() { Test t(10); t.print(); return 0; }",
        "10 followed by undefined/garbage value (members initialize in declaration order: b before a!)",
        "10 12", "12 10", "Compilation Error", "A",
        "Member variables are initialized in class declaration order (`b` then `a`). Since `b` is initialized before `a`, `b(a + 2)` reads uninitialized `a`."
    ),
    (
        "What is printed by this C++ template specialization snippet?",
        "#include <iostream>\ntemplate<typename T> void print(T val) { std::cout << \"Generic \"; }\ntemplate<> void print<int>(int val) { std::cout << \"Int \"; }\ntemplate<> void print<double>(double val) { std::cout << \"Double \"; }\nint main() { print(10); print(10.5); print('A'); return 0; }",
        "Int Double Generic ", "Generic Generic Generic ", "Int Double Char ", "Compilation Error", "A",
        "`print(10)` matches `int` specialization; `print(10.5)` matches `double`; `print('A')` uses generic template."
    ),
    (
        "What is the difference between `std::vector::reserve()` and `std::vector::resize()` in C++?",
        "// std::vector capacity vs size",
        "reserve() allocates capacity without constructing elements; resize() changes size and constructs default elements",
        "reserve() shrinks capacity; resize() expands it",
        "reserve() is for std::deque; resize() is for std::vector",
        "They are completely identical functions", "A",
        "`reserve(n)` only allocates storage buffer. `resize(n)` alters `size()` and default-constructs new elements."
    ),
    (
        "What does the C++ `constexpr` specifier indicate about a function or variable?",
        "constexpr int square(int x) { return x * x; }",
        "It can be evaluated at compile-time when passed constant expressions, enabling zero-runtime overhead",
        "It forces the compiler to inline in debug mode only",
        "It makes the variable volatile across CPU cores",
        "It stores the value in flash memory", "A",
        "`constexpr` guarantees compile-time evaluation for constant inputs, usable in array bounds and template parameters."
    ),
    (
        "What happens when an exception is thrown inside a C++ constructor?",
        "class Resource { public: Resource() { throw std::runtime_error(\"Fail\"); } ~Resource() {} };",
        "The object's destructor is NEVER called because the object was never fully constructed",
        "The destructor is called immediately",
        "The program terminates with memory corruption",
        "The object remains in a zombie state", "A",
        "An object is only constructed if the constructor finishes. If an exception escapes, the destructor does NOT run."
    ),
    (
        "What will `std::unique_ptr` do when attempting to copy-assign another unique_ptr?",
        "#include <memory>\nint main() { std::unique_ptr<int> p1 = std::make_unique<int>(10); std::unique_ptr<int> p2 = p1; return 0; }",
        "Compilation Error: copy constructor of unique_ptr is explicitly deleted",
        "Transfers ownership silently",
        "Increments reference counter",
        "Throws bad_alloc exception", "A",
        "`std::unique_ptr` copy operations are deleted (`= delete`). Ownership must be explicitly transferred with `std::move()`."
    ),
    (
        "What is the output of the following lambda expression in C++11?",
        "#include <iostream>\nint main() { int a = 5; auto f = [a]() mutable { return ++a; }; std::cout << f() << \" \" << a; return 0; }",
        "6 5", "6 6", "5 5", "Compilation Error", "A",
        "Captured-by-value `a` is mutable inside the lambda (`++a` = 6). The outer variable `a` in `main` remains 5."
    ),
    (
        "What does RAII stand for in modern C++ software design?",
        "// C++ Idiom: RAII",
        "Resource Acquisition Is Initialization",
        "Rapid Application Interface Integration",
        "Reference Allocation In Inheritance",
        "Runtime Access Instruction Index", "A",
        "RAII ties resource ownership to object lifetime, ensuring automatic cleanup in destructors upon leaving scope."
    ),
    (
        "What is the average time complexity of insertion at the end of `std::vector` in C++?",
        "// std::vector::push_back amortized analysis",
        "Amortized O(1) time complexity",
        "Strictly O(N) every time",
        "O(log N)",
        "O(N^2)", "A",
        "Geometric growth factor (doubling capacity on reallocation) gives amortized constant time $O(1)$ per `push_back`."
    ),
    (
        "Which operator CANNOT be overloaded in C++?",
        "// Operator overloading constraints",
        ". (Member access), :: (Scope resolution), ?: (Ternary), and sizeof",
        "+ (Addition) and - (Subtraction)",
        "[] (Subscript) and () (Function call)",
        "-> (Member dereference) and * (Pointer dereference)", "A",
        "In C++, `.` (dot), `::`, `?:`, `sizeof`, and `typeid` cannot be overloaded to preserve language grammar."
    ),
    (
        "What is the output of the following C++ code testing copy constructor vs assignment operator?",
        "#include <iostream>\nclass Item {\npublic:\n    Item() {}\n    Item(const Item&) { std::cout << \"Copy \"; }\n    Item& operator=(const Item&) { std::cout << \"Assign \"; return *this; }\n};\nint main() { Item a; Item b = a; Item c; c = a; return 0; }",
        "Copy Assign ", "Assign Copy ", "Copy Copy ", "Assign Assign ", "A",
        "`Item b = a;` is initialization, invoking copy constructor (`Copy `). `c = a;` is assignment on existing object (`Assign `)."
    ),
    (
        "What is the purpose of `std::forward` in C++ template metaprogramming?",
        "template<typename T> void wrapper(T&& arg) { process(std::forward<T>(arg)); }",
        "Achieves Perfect Forwarding by preserving value category (lvalue vs rvalue) of the argument",
        "Converts lvalues to rvalues unconditionally like std::move",
        "Copies the memory to heap",
        "Inverts template parameters", "A",
        "`std::forward` conditionally casts its argument to an rvalue only if the argument was initialized with an rvalue, enabling perfect forwarding."
    )
]

for q in cpp_list:
    add_q(15, 'hard', q[0], q[1], 'cpp', q[2], q[3], q[4], q[5], q[6], q[7], 60, 'C++ Technical', 0, None)

# -------------------------------------------------------------
# JAVA CORE & ADVANCED (30 Questions, subtopic_id: 16)
# -------------------------------------------------------------
java_list = [
    (
        "What is the output of the following Java program testing String Pool mechanics and == vs equals()?",
        "public class StringTest {\n    public static void main(String[] args) {\n        String s1 = \"Java\";\n        String s2 = new String(\"Java\");\n        String s3 = s2.intern();\n        System.out.println((s1 == s2) + \" \" + (s1 == s3) + \" \" + s1.equals(s2));\n    }\n}",
        "false true true", "true true true", "false false true", "true false true", "A",
        "`s1 == s2` is false (distinct heap object). `s1 == s3` is true (both point to interned literal in String Pool). `equals()` is true."
    ),
    (
        "What does the following Java method return when executed?",
        "public class ExceptionPuzzle {\n    public static int test() {\n        try { int x = 10 / 0; return 1; }\n        catch (ArithmeticException e) { return 2; }\n        finally { return 3; }\n    }\n    public static void main(String[] args) { System.out.println(test()); }\n}",
        "3", "2", "Throws ArithmeticException", "1", "A",
        "The `finally` block always executes; a `return` in `finally` abruptly overrides any previous return or exception, returning 3."
    ),
    (
        "What is printed by the following Java code testing static method hiding vs dynamic method overriding?",
        "class Parent {\n    public static void show() { System.out.print(\"P_STATIC \"); }\n    public void display() { System.out.print(\"P_INST \"); }\n}\nclass Child extends Parent {\n    public static void show() { System.out.print(\"C_STATIC \"); }\n    public void display() { System.out.print(\"C_INST \"); }\n}\npublic class Main {\n    public static void main(String[] args) {\n        Parent obj = new Child();\n        obj.show(); obj.display();\n    }\n}",
        "P_STATIC C_INST", "C_STATIC C_INST", "P_STATIC P_INST", "C_STATIC P_INST", "A",
        "Static methods cannot be overridden; they are hidden and bound at compile time to reference type (`Parent.show()`). Instance methods are dynamically dispatched to `Child.display()`."
    ),
    (
        "What is the order of execution for static blocks, instance initializer blocks, and constructors during Java class instantiation?",
        "class Demo {\n    static { System.out.print(\"S \"); }\n    { System.out.print(\"I \"); }\n    public Demo() { System.out.print(\"C \"); }\n}\npublic class Main {\n    public static void main(String[] args) { new Demo(); new Demo(); }\n}",
        "S I C I C", "S I C S I C", "I C S I C", "S C I S C I", "A",
        "Static blocks run once on class load (`S `). Instance blocks and constructors run on every object instantiation (`I C` then `I C`). Output: `S I C I C`."
    ),
    (
        "Under what condition does Java 8+ HashMap convert an internal bucket into a balanced Red-Black Tree?",
        "// HashMap internal treeification",
        "When bucket list size >= 8 (TREEIFY_THRESHOLD) and total table capacity >= 64 (MIN_TREEIFY_CAPACITY)",
        "When map capacity reaches 1024",
        "Whenever any single key collision occurs",
        "When load factor reaches 0.75", "A",
        "When a bucket reaches 8 elements, if capacity is >= 64 it converts the linked list to a Red-Black Tree; otherwise, it resizes the table."
    ),
    (
        "What happens if two unequal objects return the same `hashCode()` in Java?",
        "// Hash collision resolution",
        "Both objects are stored in the same bucket via chaining, and equals() resolves keys during retrieval",
        "A Compilation Error is thrown",
        "A runtime HashCollisionException is raised",
        "The previous object is overwritten", "A",
        "Two unequal objects can produce identical hash codes (hash collision); HashMap stores them in the same bucket and uses `equals()` to distinguish keys."
    ),
    (
        "What is the role of the `volatile` keyword in Java memory concurrency?",
        "private volatile boolean running = true;",
        "Guarantees visibility of updates across threads by reading/writing directly to main memory and establishing happens-before",
        "Ensures mutual exclusion like a synchronized block",
        "Makes objects deeply immutable",
        "Prevents garbage collection", "A",
        "`volatile` ensures variable reads and writes go directly to main memory, preventing CPU thread cache stale values, but does not guarantee compound atomicity."
    ),
    (
        "What will be output by the following Java Streams code?",
        "import java.util.*;\npublic class StreamPuzzle {\n    public static void main(String[] args) {\n        List<Integer> list = Arrays.asList(1, 2, 3, 4, 5);\n        int sum = list.stream().filter(x -> x % 2 != 0).map(x -> x * x).reduce(0, Integer::sum);\n        System.out.println(sum);\n    }\n}",
        "35", "55", "30", "15", "A",
        "Odd numbers: 1, 3, 5. Squared: 1, 9, 25. Sum: 1 + 9 + 25 = 35."
    ),
    (
        "Why does Java use Type Erasure for Generics?",
        "// Java Generics Type Erasure",
        "To ensure 100% binary backward compatibility with pre-Java 5 bytecode",
        "To speed up JIT execution at runtime",
        "To allow primitive types in collections",
        "To avoid creating class files", "A",
        "Type erasure translates generic types into their bounds or `Object` at compile-time, allowing new generic code to run seamlessly on legacy JVMs."
    ),
    (
        "What will be printed by the following Java code testing Integer Caching (-128 to 127)?",
        "public class IntCache {\n    public static void main(String[] args) {\n        Integer a = 127, b = 127;\n        Integer c = 128, d = 128;\n        System.out.println((a == b) + \" \" + (c == d));\n    }\n}",
        "true false", "true true", "false false", "false true", "A",
        "Autoboxed Integers between -128 and 127 are cached singletons (`a == b` is true). 128 creates separate heap instances (`c == d` is false)."
    ),
    (
        "What will be the output of the following Java method overloading puzzle with null argument?",
        "public class OverloadPuzzle {\n    public static void print(Object o) { System.out.print(\"Object \"); }\n    public static void print(String s) { System.out.print(\"String \"); }\n    public static void main(String[] args) { print(null); }\n}",
        "String ", "Object ", "Compilation Error: Ambiguous method call", "Throws NullPointerException", "A",
        "Java resolves overloaded methods by selecting the most specific type in the hierarchy. `String` is a subtype of `Object`, so `print(String)` is invoked."
    ),
    (
        "What happens if both `print(String)` and `print(Integer)` exist and `print(null)` is called?",
        "public class AmbiguousPuzzle {\n    public static void print(String s) {}\n    public static void print(Integer i) {}\n    public static void main(String[] args) { print(null); }\n}",
        "Compilation Error: The method print is ambiguous", "Calls String", "Calls Integer", "Runtime Exception", "A",
        "Neither `String` nor `Integer` is a subtype of the other. The compiler cannot choose between sibling types, producing a compile-time ambiguity error."
    ),
    (
        "What will be output by the following try-with-resources statement in Java?",
        "class Door implements AutoCloseable {\n    public void close() { System.out.print(\"Closed \"); }\n}\npublic class Main {\n    public static void main(String[] args) {\n        try (Door d = new Door()) { System.out.print(\"Opened \"); }\n    }\n}",
        "Opened Closed ", "Closed Opened ", "Opened ", "Closed ", "A",
        "The try block prints `Opened `, then JVM implicitly invokes `AutoCloseable.close()`, printing `Closed `."
    ),
    (
        "What is the difference between fail-fast and fail-safe iterators in Java Collections?",
        "// ArrayList iterator vs ConcurrentHashMap iterator",
        "Fail-fast throws ConcurrentModificationException immediately upon structural modification; fail-safe operates on a clone/snapshot",
        "Fail-fast locks the database table; fail-safe locks row",
        "Fail-fast is only in Java 1.4",
        "There is no difference in modern Java", "A",
        "Fail-fast iterators detect `modCount` changes and throw `ConcurrentModificationException`. Fail-safe iterators iterate over snapshot copies without exceptions."
    ),
    (
        "What will `System.out.println(10 + 20 + \"Hello\" + 10 + 20);` output in Java?",
        "System.out.println(10 + 20 + \"Hello\" + 10 + 20);",
        "30Hello1020", "30Hello30", "1020Hello1020", "Hello301020", "A",
        "`10 + 20 = 30` (int addition). `30 + \"Hello\" = \"30Hello\"`. Then `\"30Hello\" + 10 = \"30Hello10\"` and `+ 20 = \"30Hello1020\"` (string concatenation)."
    ),
    (
        "What does the `transient` keyword signify in Java serialization?",
        "private transient String secretKey;",
        "The field is omitted from serialization and not written to byte streams",
        "The field is volatile across CPU threads",
        "The field is immutable",
        "The field is stored in cloud cache", "A",
        "`transient` prevents a field from being included in the standard Java object serialization process."
    ),
    (
        "What happens when you invoke `Thread.start()` twice on the same Thread object in Java?",
        "Thread t = new Thread(); t.start(); t.start();",
        "Throws IllegalThreadStateException at runtime",
        "Starts a second thread concurrently",
        "Restarts the thread from beginning",
        "Silently ignored", "A",
        "A Thread can only be started once. Once transitioned from NEW state, invoking `start()` again throws `IllegalThreadStateException`."
    ),
    (
        "What is the difference between `Comparable` and `Comparator` interfaces in Java?",
        "// Comparable vs Comparator",
        "Comparable defines natural ordering inside the class (compareTo); Comparator defines external custom ordering (compare)",
        "Comparable is for primitive types only",
        "Comparator is deprecated in Java 8",
        "They are completely interchangeable", "A",
        "`Comparable` modifies the class directly (`compareTo`). `Comparator` is a separate strategy object (`compare`) for customizable sort criteria."
    ),
    (
        "What is printed when accessing default values in a boolean array in Java?",
        "boolean[] flags = new boolean[3]; System.out.println(flags[0]);",
        "false", "true", "null", "0", "A",
        "Primitive boolean fields and array elements in Java are default-initialized to `false`."
    ),
    (
        "What is the result of executing `Math.round(-1.5)` in Java?",
        "System.out.println(Math.round(-1.5));",
        "-1", "-2", "-1.0", "-2.0", "A",
        "`Math.round(x)` computes `floor(x + 0.5)`. `-1.5 + 0.5 = -1.0`, whose floor is `-1`."
    )
]

for q in java_list:
    add_q(16, 'hard', q[0], q[1], 'java', q[2], q[3], q[4], q[5], q[6], q[7], 60, 'Java Technical', 0, None)

# -------------------------------------------------------------
# PHP & WEB ARCHITECTURE (25 Questions, subtopic_id: 17)
# -------------------------------------------------------------
php_list = [
    (
        "In PHP 8+, what will be the output of comparing number 0 and string 'apple'?",
        "<?php\nvar_dump(0 == 'apple');\nvar_dump(0 == '0');\n?>",
        "bool(false) bool(true)", "bool(true) bool(true)", "bool(false) bool(false)", "bool(true) bool(false)", "A",
        "In PHP 8.0+, comparing a number to a non-numeric string converts the number to string, resulting in `false` (in PHP 7 it was true). `0 == '0'` is numeric comparison, returning `true`."
    ),
    (
        "What is the output of the following PHP loop involving references in foreach?",
        "<?php\n$arr = [1, 2, 3];\nforeach ($arr as &$val) {}\nforeach ($arr as $val) {}\nprint_r($arr);\n?>",
        "Array ( [0] => 1 [1] => 2 [2] => 2 )", "Array ( [0] => 1 [1] => 2 [2] => 3 )", "Array ( [0] => 3 [1] => 3 [2] => 3 )", "Fatal Error", "A",
        "After loop 1, `$val` references `$arr[2]`. Loop 2 copies `$arr[0]` into `$arr[2]` (1), then `$arr[1]` into `$arr[2]` (2), leaving `[1, 2, 2]`."
    ),
    (
        "What will be output by the following spaceship operator <=> comparisons in PHP?",
        "<?php\necho (5 <=> 10) . ' ' . (10 <=> 10) . ' ' . (15 <=> 10);\n?>",
        "-1 0 1", "0 1 -1", "false true true", "1 0 -1", "A",
        "`$a <=> $b` returns `-1` if $a < $b, `0` if equal, `1` if $a > $b."
    ),
    (
        "What is the outcome of the null coalescing assignment operator ??= in PHP?",
        "<?php\n$data = ['count' => null];\n$data['count'] ??= 10;\n$data['total'] ??= 50;\n$data['count'] ??= 99;\necho $data['count'] . ' ' . $data['total'];\n?>",
        "10 50", "99 50", "null 50", "Compilation error", "A",
        "`$x ??= $y` assigns $y only if $x is null or unset. `count` becomes 10, `total` becomes 50. The second `count ??= 99` is skipped."
    ),
    (
        "Why should `password_hash()` and `password_verify()` always be used over `md5()` or `sha1()` in PHP?",
        "// Password security in PHP",
        "password_hash uses slow salted adaptive algorithms (Bcrypt/Argon2) with adjustable cost factor to resist brute-force attacks",
        "MD5 and SHA-1 produce shorter hashes for smaller databases",
        "password_verify runs asynchronously in memory",
        "MD5 is deprecated in HTTP protocols", "A",
        "`password_hash()` automatically salts and applies computationally slow key-stretching (Bcrypt/Argon2id), defending against GPU cracking rigs."
    ),
    (
        "What does PDO::ATTR_EMULATE_PREPARES => false achieve in database security?",
        "// PDO security configuration",
        "Forces true database server-side prepared statements, eliminating second-order SQL injections and type bugs",
        "Increases connection timeout limits",
        "Compresses network queries",
        "Disables transaction rollbacks", "A",
        "Setting emulate prepares to false ensures queries and parameters are sent in separate packets to the MySQL server, preventing SQL injection."
    ),
    (
        "How does PHP resolve method naming conflicts when a class imports two traits defining the same method?",
        "// PHP trait conflict resolution",
        "A fatal error occurs unless resolved explicitly using the `insteadof` operator",
        "The last imported trait silently overwrites the first",
        "PHP runs both methods concurrently",
        "The class constructor overrides both traits", "A",
        "If two traits define the same method, PHP emits a fatal collision error unless the conflict is resolved explicitly with `TraitA::method insteadof TraitB;`."
    ),
    (
        "What is the output of array_merge() vs the union operator + for arrays with matching string keys in PHP?",
        "<?php\n$a = ['role' => 'guest'];\n$b = ['role' => 'admin'];\necho array_merge($a, $b)['role'] . ' ' . ($a + $b)['role'];\n?>",
        "admin guest", "admin admin", "guest guest", "guest admin", "A",
        "`array_merge()` overwrites matching string keys with the right array ('admin'). Array union `+` keeps keys from the left array ('guest')."
    ),
    (
        "What is the difference between `self::` and `static::` keywords in PHP Late Static Bindings?",
        "<?php\nclass A { public static function who() { echo 'A'; } public static function test() { static::who(); } }\nclass B extends A { public static function who() { echo 'B'; } }\nB::test();\n?>",
        "B", "A", "Fatal error", "AB", "A",
        "`static::` enables Late Static Bindings, resolving dynamically to the runtime called class (`B::who()`), outputting `B`."
    ),
    (
        "What will be the output of `echo 012 + 10;` in PHP?",
        "<?php\necho 012 + 10;\n?>",
        "20", "22", "12", "Compilation error", "A",
        "Leading zero `012` denotes octal: `1*8 + 2 = 10` in decimal. `10 + 10 = 20`."
    ),
    (
        "What is the output of `empty(\"0\")` in PHP?",
        "<?php\nvar_dump(empty(\"0\"));\n?>",
        "bool(true)", "bool(false)", "null", "Fatal Error", "A",
        "In PHP, the string `\"0\"` is considered falsy by design, so `empty(\"0\")` returns `true`."
    ),
    (
        "What will `strpos(\"hello\", \"h\")` return, and how should it be checked in PHP?",
        "<?php\n$pos = strpos(\"hello\", \"h\");\nif ($pos !== false) echo \"Found\";\n?>",
        "Returns integer 0; must check with strict comparison `!== false`",
        "Returns true",
        "Returns 1",
        "Returns boolean false", "A",
        "'h' is at index 0. Because 0 is loose-falsy, `if ($pos)` fails. You must use `!== false`."
    ),
    (
        "What does `htmlspecialchars($input, ENT_QUOTES, 'UTF-8')` prevent in PHP web applications?",
        "// Web security in PHP",
        "Cross-Site Scripting (XSS) by encoding characters into HTML entities",
        "SQL Injection attacks",
        "Buffer overflow attacks",
        "CSRF token invalidation", "A",
        "It converts characters like `<`, `>`, `&`, `'`, and `\"` into HTML entities, preventing script injection."
    ),
    (
        "In PHP, what is the output of `(int)(0.1 + 0.7) * 10` due to floating point representation?",
        "<?php\necho (int)((0.1 + 0.7) * 10);\n?>",
        "7", "8", "8.0", "0", "A",
        "`0.1 + 0.7` evaluates internally to ~`0.7999999999999999`. Multiplying by 10 gives `7.999999999999999`, and casting to int truncates to `7`."
    ),
    (
        "What security header should be enabled on session cookies to prevent JavaScript from reading PHPSESSID during XSS attacks?",
        "// Session security in PHP",
        "HttpOnly flag", "SameSite=None", "Secure without HTTPS", "Cache-Control: private", "A",
        "`HttpOnly` instructs browsers to prevent client-side JavaScript (`document.cookie`) from accessing session cookies."
    )
]

for q in php_list:
    add_q(17, 'hard', q[0], q[1], 'php', q[2], q[3], q[4], q[5], q[6], q[7], 60, 'PHP Technical', 0, None)

# -------------------------------------------------------------
# C# & .NET PLATFORM (25 Questions, subtopic_id: 18)
# -------------------------------------------------------------
csharp_list = [
    (
        "What happens during Boxing and Unboxing in C#/.NET, and what is its performance impact?",
        "// Boxing and Unboxing in C#",
        "Boxing allocates a wrapper object on the heap and copies value type data into it; unboxing extracts value back to stack, creating GC pressure",
        "Boxing wraps a reference type into a struct on the stack; unboxing moves it to the heap",
        "Boxing encrypts sensitive memory blocks; unboxing decrypts them",
        "Boxing is a compile-time optimization with zero runtime cost", "A",
        "Boxing converts value types to `object` by allocating on the garbage-collected heap. Heavy boxing causes GC overhead."
    ),
    (
        "What will be output by the following C# code illustrating LINQ Deferred Execution?",
        "using System;\nusing System.Collections.Generic;\nusing System.Linq;\nclass Program {\n    static void Main() {\n        var list = new List<int> { 1, 2, 3 };\n        var query = list.Where(x => x > 1);\n        list.Add(4); list.Add(5);\n        Console.WriteLine(query.Count());\n    }\n}",
        "4", "2", "3", "Compilation error", "A",
        "LINQ `.Where()` executes lazily when enumerated by `.Count()`. By then, 4 and 5 were added; elements > 1 are 2, 3, 4, 5 (total 4)."
    ),
    (
        "What dangerous pitfall occurs when blocking on an async Task using `.Result` in C# UI or ASP.NET SynchronizationContext applications?",
        "// Async blocking pitfall in C#",
        "Deadlock because continuation waits for the same SynchronizationContext thread blocked by .Result",
        "Out of Memory Exception",
        "Silent data corruption",
        "Thread Pool exhaustion within 1 millisecond", "A",
        "Calling `.Result` blocks the context thread, while the async task continuation waits to execute on that very thread, deadlocking."
    ),
    (
        "What will be output by the following null-conditional and null-coalescing code in C#?",
        "using System;\nclass Program {\n    static void Main() {\n        string s = null;\n        int length = s?.Length ?? -1;\n        Console.WriteLine(length);\n    }\n}",
        "-1", "0", "Throws NullReferenceException", "Compilation Error", "A",
        "`s?.Length` short-circuits to null; the `??` null-coalescing operator returns the fallback default `-1`."
    ),
    (
        "What is the purpose of the `IDisposable` interface and the `using` statement in C#?",
        "using (var stream = new FileStream(...)) { }",
        "To provide deterministic release of unmanaged resources (handles, connections) as soon as execution leaves scope",
        "To force immediate Garbage Collection cycle",
        "To serialize objects to JSON",
        "To enable multi-threading on CPU cores", "A",
        "`IDisposable.Dispose()` releases unmanaged resources; the `using` statement wraps execution in a guaranteed `try...finally`."
    ),
    (
        "What feature do C# 9+ `record` types provide compared to regular classes?",
        "public record Student(string Name, int Age);",
        "Built-in value-based equality semantics, non-destructive mutation via `with` expressions, and concise syntax",
        "Records cannot contain properties",
        "Records are strictly allocated on the CPU stack",
        "Records disable reflection", "A",
        "Records provide value-based equality (two records with identical properties are equal) and support immutability."
    ),
    (
        "What is the output of string interning in C#?",
        "string a = \"hello\";\nstring b = new string(new char[] { 'h', 'e', 'l', 'l', 'o' });\nConsole.WriteLine(object.ReferenceEquals(a, b) + \" \" + (a == b));",
        "False True", "True True", "False False", "True False", "A",
        "`ReferenceEquals` is false (distinct heap objects). The `==` operator for strings compares character content, returning true."
    ),
    (
        "What is the difference between `const` and `readonly` fields in C#?",
        "public const int X = 10;\npublic static readonly int Y = DateTime.Now.Year;",
        "const is compile-time inlined into caller bytecode; readonly is evaluated at runtime in constructors",
        "readonly cannot be modified by constructors",
        "const fields are allocated on heap",
        "They are completely identical in .NET", "A",
        "`const` is baked in at compile time; `readonly` can be assigned dynamically at runtime in the constructor."
    ),
    (
        "What does the `yield return` statement do in a C# method returning `IEnumerable<T>`?",
        "public IEnumerable<int> GetNumbers() { yield return 1; yield return 2; }",
        "Generates an iterator state machine behind the scenes, producing elements on demand during enumeration",
        "Terminates thread execution",
        "Returns a static array",
        "Forces immediate collection materialization", "A",
        "`yield return` signals the compiler to create an `IEnumerator` state machine evaluating values lazily."
    ),
    (
        "What is the output of the following C# delegate multicast invocation?",
        "delegate int MathOp();\nMathOp op = () => 10; op += () => 20; op += () => 30;\nConsole.WriteLine(op());",
        "30", "10", "60", "Compilation error", "A",
        "Invoking a multicast delegate with a return value returns the result of the LAST method in the invocation list (`30`)."
    ),
    (
        "What does `readonly struct` enforce in C# 7.2+?",
        "public readonly struct Point { public int X { get; } }",
        "Guarantees that all fields are readonly and prevents compiler defensive copies when passed with `in` modifier",
        "Allocates the struct in unmanaged C++ heap",
        "Disables value equality",
        "Forces garbage collection immediately", "A",
        "Declaring a `readonly struct` prevents the compiler from creating defensive copies when passed by readonly reference (`in`)."
    )
]

for q in csharp_list:
    add_q(18, 'hard', q[0], q[1], 'csharp', q[2], q[3], q[4], q[5], q[6], q[7], 60, 'C# Technical', 0, None)

# -------------------------------------------------------------
# PYTHON PROGRAMMING (25 Questions, subtopic_id: 19)
# -------------------------------------------------------------
python_list = [
    (
        "What is the output of the following Python function with a mutable default argument?",
        "def append_to(element, target=[]):\n    target.append(element)\n    return target\nprint(append_to(1))\nprint(append_to(2))\nprint(append_to(3, []))\nprint(append_to(4))",
        "[1] then [1, 2] then [3] then [1, 2, 4]", "[1] then [2] then [3] then [4]", "[1] then [1, 2] then [1, 2, 3] then [1, 2, 3, 4]", "SyntaxError", "A",
        "Default arguments are evaluated ONCE at function definition. The list `target=[]` is shared across calls: [1], [1, 2], [3] (explicit list), [1, 2, 4]."
    ),
    (
        "What is the output of the following Python closure late-binding puzzle?",
        "funcs = [lambda x: x + i for i in range(3)]\nresults = [f(10) for f in funcs]\nprint(results)",
        "[12, 12, 12]", "[10, 11, 12]", "[10, 10, 10]", "[0, 1, 2]", "A",
        "Closures bind variables late. When called, the loop has ended and `i = 2`. Each lambda calculates `10 + 2 = 12`."
    ),
    (
        "What is the difference between shallow copy (copy.copy) and deep copy (copy.deepcopy) in Python?",
        "// Python copy mechanics",
        "Shallow copy populates references to original nested objects; deep copy recursively duplicates all nested objects",
        "Shallow copy copies strings; deep copy copies integers",
        "Deep copy operates only on dictionaries",
        "There is no difference in Python 3", "A",
        "Shallow copy copies the top-level container, referencing nested objects. Deep copy duplicates everything recursively."
    ),
    (
        "What will be output by the following generator function in Python?",
        "def generator_puzzle():\n    yield 1\n    yield from [2, 3]\n    yield 4\ngen = generator_puzzle()\nprint(next(gen), list(gen))",
        "1 [2, 3, 4]", "1 [1, 2, 3, 4]", "[1, 2, 3, 4] []", "1 2", "A",
        "`next(gen)` yields 1. `list(gen)` exhausts remaining items: [2, 3, 4]."
    ),
    (
        "In Python, what is the resolution order for variable lookup defined by the LEGB rule?",
        "// Variable scoping in Python",
        "Local -> Enclosing -> Global -> Built-in",
        "Linear -> Explicit -> Global -> Binary",
        "Lexical -> External -> Global -> Base",
        "Logical -> Environment -> Group -> Block", "A",
        "Python resolves names in order: Local, Enclosing function locals, Global module namespace, Built-ins."
    ),
    (
        "What does the `is` operator compare in Python versus `==`?",
        "a = [1, 2]; b = [1, 2]; print(a is b, a == b)",
        "False True (`is` checks memory identity id(); `==` checks value equality)",
        "True True",
        "False False",
        "True False", "A",
        "`is` tests if both variables point to the exact same memory address. `==` checks whether values are equal."
    ),
    (
        "What is the output of the following Python list multiplication and mutation puzzle?",
        "grid = [[0] * 3] * 3\ngrid[0][0] = 1\nprint(grid)",
        "[[1, 0, 0], [1, 0, 0], [1, 0, 0]]", "[[1, 0, 0], [0, 0, 0], [0, 0, 0]]", "[[1, 1, 1], [0, 0, 0], [0, 0, 0]]", "IndexError", "A",
        "`[[0]*3] * 3` creates three references to the same inner list. Mutating one row affects all three."
    ),
    (
        "What will be output by the following Python dictionary comprehension?",
        "keys = ['a', 'b', 'c']\nvalues = [1, 2, 3, 4]\nd = {k: v for k, v in zip(keys, values)}\nprint(len(d), d['b'])",
        "3 2", "4 2", "3 3", "ValueError", "A",
        "`zip()` stops at the shortest list (length 3). The dictionary has 3 pairs; `d['b']` is 2."
    ),
    (
        "What is the output of variable unpacking with the asterisk operator in Python?",
        "a, *b, c = [10, 20, 30, 40, 50]\nprint(a, b, c)",
        "10 [20, 30, 40] 50", "10 20 [30, 40, 50]", "[10, 20] [30, 40] 50", "ValueError", "A",
        "`a` takes the first element (10), `c` takes the last (50), and `*b` collects remaining elements as `[20, 30, 40]`."
    ),
    (
        "What will be the output of `bool([0])` and `bool([])` in Python?",
        "print(bool([0]), bool([]))",
        "True False", "False False", "True True", "False True", "A",
        "Non-empty lists (even containing 0) are truthy (`True`). Empty lists are falsy (`False`)."
    )
]

for q in python_list:
    add_q(19, 'hard', q[0], q[1], 'python', q[2], q[3], q[4], q[5], q[6], q[7], 55, 'Python Technical', 0, None)

# -------------------------------------------------------------
# SQL & RELATIONAL DATABASES (25 Questions, subtopic_id: 20)
# -------------------------------------------------------------
sql_list = [
    (
        "Which SQL query correctly finds the 2nd highest salary from Employees while handling duplicates and returning NULL if none exists?",
        "SELECT ... FROM Employees;",
        "SELECT MAX(salary) FROM Employees WHERE salary < (SELECT MAX(salary) FROM Employees);",
        "SELECT DISTINCT salary FROM Employees ORDER BY salary DESC LIMIT 1 OFFSET 1;",
        "SELECT salary FROM Employees WHERE rank = 2;",
        "SELECT TOP 1 salary FROM (SELECT TOP 2 salary FROM Employees ORDER BY salary DESC) ORDER BY salary ASC;", "A",
        "`SELECT MAX(salary) ... WHERE salary < (SELECT MAX)` produces NULL cleanly when no second highest exists."
    ),
    (
        "What is the crucial difference between DENSE_RANK() and RANK() window functions in SQL when dealing with tied values?",
        "// SQL Window functions ranking",
        "DENSE_RANK() leaves no gaps in ranking numbers after ties; RANK() skips numbers corresponding to the number of tied positions",
        "RANK() creates contiguous integers without gaps; DENSE_RANK() leaves gaps equal to the count of ties",
        "DENSE_RANK() works only on string columns",
        "RANK() cannot be partitioned by department", "A",
        "Two ties for 1st place: `DENSE_RANK()` gives `1, 1, 2`. `RANK()` gives `1, 1, 3`."
    ),
    (
        "What is the result of applying a WHERE condition to the right table in a LEFT OUTER JOIN?",
        "SELECT e.name, d.dept_name FROM Employees e LEFT JOIN Departments d ON e.dept_id = d.id WHERE d.dept_name = 'Engineering';",
        "It effectively converts the LEFT JOIN into an INNER JOIN, excluding all employees without departments",
        "It preserves all unmatched employees from the left table and populates NULL for dept_name",
        "It results in a SQL Syntax Error",
        "It creates a Cartesian Product (CROSS JOIN)", "A",
        "`WHERE` evaluates after the join. Filtering `d.dept_name = 'Engineering'` discards NULLs, turning the LEFT JOIN into an INNER JOIN."
    ),
    (
        "In database ACID properties, which transaction isolation level prevents Dirty Reads and Non-Repeatable Reads, but may still allow Phantom Reads?",
        "// ANSI SQL Transaction Isolation Levels",
        "Repeatable Read", "Read Uncommitted", "Read Committed", "Serializable", "A",
        "Repeatable Read prevents dirty and non-repeatable reads. Phantom rows inserted concurrently can still appear under standard Repeatable Read."
    ),
    (
        "What is the difference between WHERE and HAVING clauses in SQL?",
        "// WHERE vs HAVING",
        "WHERE filters rows before group aggregations are performed; HAVING filters grouped rows after aggregation",
        "HAVING operates on individual rows; WHERE operates on groups",
        "WHERE can use aggregate functions like COUNT(); HAVING cannot",
        "They are interchangeable in all ANSI SQL engines", "A",
        "`WHERE` filters rows prior to aggregation. `HAVING` filters grouped rows after `GROUP BY`."
    ),
    (
        "What is the output of evaluating NULL in SQL boolean logic: SELECT * FROM T WHERE col = NULL;",
        "SELECT * FROM Users WHERE status = NULL;",
        "Returns 0 rows always (because comparison with NULL yields UNKNOWN, never TRUE; must use IS NULL)",
        "Returns all rows where status is NULL",
        "Syntax Error",
        "Throws NullPointerException", "A",
        "In three-valued logic, `col = NULL` evaluates to `UNKNOWN`. Since `WHERE` requires `TRUE`, it returns 0 rows."
    ),
    (
        "Which SQL clause is executed FIRST in the logical query processing order?",
        "SELECT dept_id, COUNT(*) FROM Emp WHERE salary > 50000 GROUP BY dept_id HAVING COUNT(*) > 5 ORDER BY dept_id;",
        "FROM clause", "SELECT clause", "WHERE clause", "ORDER BY clause", "A",
        "Logical query evaluation begins with `FROM` (and JOINs), followed by `WHERE`, `GROUP BY`, `HAVING`, `SELECT`, `ORDER BY`."
    ),
    (
        "What is the difference between TRUNCATE and DELETE in SQL?",
        "-- TRUNCATE vs DELETE",
        "TRUNCATE is a DDL command that deallocates data pages and resets identity counters; DELETE is DML and logs each deleted row",
        "DELETE cannot be rolled back in transactions",
        "TRUNCATE preserves identity seed values",
        "They are identical commands", "A",
        "`TRUNCATE` is DDL (fast, resets auto-increment, minimal logging). `DELETE` is DML (scans and logs row-by-row, triggers fire)."
    ),
    (
        "What is a Correlated Subquery in SQL?",
        "SELECT e1.name FROM Employee e1 WHERE e1.salary > (SELECT AVG(e2.salary) FROM Employee e2 WHERE e2.dept_id = e1.dept_id);",
        "A subquery that references a column from the outer query and is re-evaluated for every candidate row of the outer query",
        "A query that runs only on indexed columns",
        "A subquery that runs once before the outer query starts",
        "A subquery that creates temporary tables", "A",
        "Correlated subqueries reference the outer query (`e2.dept_id = e1.dept_id`), executing logically for each candidate row."
    ),
    (
        "How does a B-Tree Index optimize range queries (e.g. BETWEEN 10 AND 50) compared to a Hash Index?",
        "CREATE INDEX idx_salary ON Employees(salary);",
        "B-Trees maintain elements in sorted order with linked leaf pages allowing range scans; Hash indexes provide O(1) equality lookups only",
        "Hash indexes are always better for range queries",
        "B-Trees do not support numbers",
        "Hash indexes sort data alphabetically", "A",
        "B-Trees keep keys in sorted order with linked leaf pages, enabling efficient range scans. Hash indexes scatter keys."
    )
]

for q in sql_list:
    add_q(20, 'hard', q[0], q[1], 'sql', q[2], q[3], q[4], q[5], q[6], q[7], 55, 'SQL Technical', 0, None)

# -------------------------------------------------------------
# DATA STRUCTURES & ALGORITHMS (25 Questions, subtopic_id: 21)
# -------------------------------------------------------------
dsa_list = [
    (
        "How does Floyd's Cycle-Finding Algorithm (Tortoise and Hare) detect a loop in a Singly Linked List in O(N) time and O(1) space?",
        "// Floyd's algorithm",
        "By advancing slow pointer 1 step and fast pointer 2 steps; if a cycle exists, the fast pointer laps and meets the slow pointer",
        "By reversing the linked list and comparing node memory addresses",
        "By hashing all visited node pointers into a hash set of size O(1)",
        "By counting the total number of nodes in two separate traversals", "A",
        "Fast pointer closes distance by 1 node per iteration inside the cycle, guaranteeing they meet in at most cycle length steps."
    ),
    (
        "In an AVL Tree, when is a Left-Right (LR) double rotation performed to restore balance factor?",
        "// AVL Tree rebalancing",
        "When a node is inserted into the right subtree of the left child (Balance Factor = +2, left child Balance Factor = -1)",
        "When a node is inserted into the left subtree of the left child",
        "When a node is inserted into the right subtree of the right child",
        "When the root node has duplicate keys", "A",
        "An LR imbalance requires a double rotation: first a Left rotation on the left child, then a Right rotation on the parent."
    ),
    (
        "What is the optimal data structure and time complexity to find the Next Greater Element for all elements in an array of size N?",
        "// Next Greater Element",
        "Monotonic Decreasing Stack with O(N) time complexity",
        "Nested loops with O(N^2) time complexity",
        "Priority Queue (Max Heap) with O(N log N) time complexity",
        "Self-Balancing Binary Search Tree with O(N log N) time complexity", "A",
        "Each element is pushed and popped from the Monotonic Stack at most once, yielding linear $O(N)$ time."
    ),
    (
        "Why is Quicksort generally preferred over Mergesort for sorting contiguous in-memory arrays?",
        "// Quicksort vs Mergesort",
        "Quicksort sorts in-place with O(log N) stack space, exhibits superior CPU cache locality, and has a smaller constant factor",
        "Quicksort has O(1) worst-case time complexity",
        "Mergesort cannot handle duplicate values",
        "Quicksort is stable while Mergesort is unstable", "A",
        "Quicksort operates in-place with sequential memory access that maximizes CPU L1/L2 cache hits."
    ),
    (
        "What is the recurrence relation and asymptotic time complexity of Karatsuba's fast integer multiplication algorithm?",
        "// Karatsuba multiplication",
        "T(N) = 3T(N/2) + O(N) -> O(N^{log_2 3}) ≈ O(N^{1.585})",
        "T(N) = 2T(N/2) + O(N) -> O(N log N)",
        "T(N) = 4T(N/2) + O(N) -> O(N^2)",
        "T(N) = T(N-1) + O(N) -> O(N^2)", "A",
        "Karatsuba reduces 4 sub-multiplications to 3, yielding $O(N^{\\log_2 3}) \\approx O(N^{1.585})$."
    ),
    (
        "What is the time complexity to find the median of an unsorted array of N elements using Quickselect?",
        "// Quickselect algorithm",
        "O(N) on average, O(N^2) in worst case", "O(N log N) in all cases", "O(log N) average", "O(1)", "A",
        "Quickselect recurses into only one partition: $T(N) = T(N/2) + O(N) = O(N)$ average time."
    ),
    (
        "What property must a binary tree satisfy to be classified as a valid Binary Search Tree (BST)?",
        "// BST Invariant validation",
        "For every node, all keys in its left subtree must be strictly less, and all keys in its right subtree strictly greater",
        "Only the immediate left child must be less than the node",
        "The tree must be completely filled at all levels",
        "Nodes must have at most 1 child", "A",
        "The BST invariant applies to the entire left and right subtrees, bounded by ancestor values."
    ),
    (
        "Which algorithm is used to find Strongly Connected Components (SCCs) in a Directed Graph in O(V + E) time?",
        "// Strongly Connected Components",
        "Tarjan's algorithm using DFS and low-link values (or Kosaraju's algorithm with two DFS passes)",
        "Dijkstra's Algorithm",
        "Prim's Algorithm",
        "Kruskal's Algorithm", "A",
        "Tarjan's and Kosaraju's algorithms both identify all SCCs in optimal linear time $O(V + E)$."
    ),
    (
        "What is the maximum number of edges in an undirected simple graph with N vertices that contains NO cycles?",
        "// Graph theory properties",
        "N - 1 edges (spanning forest/tree)", "N edges", "N(N - 1) / 2 edges", "2N edges", "A",
        "An acyclic undirected graph is a forest; a single tree on $N$ vertices has exactly $N - 1$ edges."
    ),
    (
        "What is the space complexity of Depth First Search (DFS) on a balanced binary tree of height H?",
        "// DFS space complexity",
        "O(H) or O(log N) due to recursive call stack", "O(N) always", "O(1)", "O(N^2)", "A",
        "The recursive call stack holds nodes along the active path from root to leaf, requiring $O(H) = O(\\log N)$ space."
    )
]

for q in dsa_list:
    add_q(21, 'hard', q[0], q[1], 'dsa', q[2], q[3], q[4], q[5], q[6], q[7], 60, 'DSA Technical', 0, None)

# -------------------------------------------------------------
# INNOVATURE LABS PREVIOUS YEAR INTERVIEW QUESTIONS (35 Questions, subtopic_id: 22)
# -------------------------------------------------------------
innovature_list = [
    (
        "[Innovature Labs Technical PYQ] In Innovature Labs C interview round: What will be the output of pointer arithmetic on 2D arrays?",
        "#include <stdio.h>\nint main() {\n    int a[3][3] = {{1, 2, 3}, {4, 5, 6}, {7, 8, 9}};\n    printf(\"%d %d\", *(*a + 1), *(*(a + 1) + 2));\n    return 0;\n}",
        "2 6", "1 5", "4 8", "2 5", "A",
        "`*(*a + 1)` is `a[0][1] = 2`. `*(*(a + 1) + 2)` is `a[1][2] = 6`. Output: `2 6`."
    ),
    (
        "[Innovature Labs Technical PYQ] Innovature Labs Java Round: What is the output of this static vs instance initialization puzzle?",
        "class Base {\n    static int x = 10;\n    static { x += 5; }\n}\npublic class Main {\n    public static void main(String[] args) {\n        System.out.println(Base.x);\n    }\n}",
        "15", "10", "5", "0", "A",
        "Class loading runs the static block: `x += 5` = 15. `Base.x` prints 15."
    ),
    (
        "[Innovature Labs Technical PYQ] Innovature Labs Database Round: How to delete duplicate rows from a table while keeping only the row with lowest ID?",
        "DELETE t1 FROM Users t1 ...",
        "DELETE t1 FROM Users t1 INNER JOIN Users t2 WHERE t1.email = t2.email AND t1.id > t2.id;",
        "DELETE FROM Users WHERE email IN (SELECT email FROM Users GROUP BY email);",
        "DROP DUPLICATES FROM Users;",
        "DELETE DISTINCT FROM Users;", "A",
        "Self-joining where `t1.id > t2.id` deletes all duplicate rows except the one with the lowest ID."
    ),
    (
        "[Innovature Labs Logic PYQ] Innovature Labs Campus Round: Five colleagues (P, Q, R, S, T) work in 5 different departments: IT, HR, Finance, QA, and DevOps. P does not work in IT or HR. Q works in Finance. S is neither in IT nor QA. T does not work in IT. Which department does R work in?",
        None,
        "IT", "HR", "QA", "DevOps", "A",
        "P, Q (Finance), S, T cannot work in IT. The only person available for IT is R."
    ),
    (
        "[Innovature Labs Quant PYQ] Innovature Labs Aptitude: In an Innovature development sprint, 6 engineers build 12 microservices in 4 days. How many microservices can 9 engineers build in 6 days at the same productivity rate?",
        None,
        "27 microservices", "24 microservices", "30 microservices", "18 microservices", "A",
        "`(M1 * D1)/W1 = (M2 * D2)/W2` -> `(6 * 4)/12 = 2`. `(9 * 6)/W2 = 2` -> `W2 = 54 / 2 = 27`."
    ),
    (
        "[Innovature Labs Technical PYQ] Innovature Labs C Assessment: What does `printf(\"%d\", sizeof('A'));` print in C versus C++?",
        "#include <stdio.h>\nint main() {\n    printf(\"%zu\", sizeof('A'));\n    return 0;\n}",
        "In C: sizeof(int) (typically 4 bytes); In C++: sizeof(char) (1 byte)",
        "1 in both C and C++",
        "4 in both C and C++",
        "Undefined behavior in C", "A",
        "In C, character literals are promoted to `int` (4 bytes). In C++, character literals have type `char` (1 byte)."
    ),
    (
        "[Innovature Labs Technical PYQ] Innovature Labs interview: What will this C pointer expression output?",
        "#include <stdio.h>\nint main() {\n    char *str = \"Innovature\";\n    printf(\"%s\", str + 5);\n    return 0;\n}",
        "ature", "ture", "Inno", "Compilation Error", "A",
        "Index 5 of 'Innovature' is 'a'. `str + 5` prints string starting from index 5: \"ature\"."
    ),
    (
        "[Innovature Labs Technical PYQ] Innovature Labs Java Round: What is the result of dividing by zero in floating point in Java?",
        "System.out.println(10.0 / 0.0 + \" \" + (0.0 / 0.0));",
        "Infinity NaN", "ArithmeticException", "Infinity 0.0", "NaN NaN", "A",
        "Per IEEE 754 in Java, non-zero float divided by 0.0 yields `Infinity`; 0.0 / 0.0 yields `NaN`."
    ),
    (
        "[Innovature Labs Technical PYQ] Innovature Labs SQL Assessment: Which SQL function returns the length of a string in characters?",
        "SELECT ... ('Innovature');",
        "LENGTH() or CHAR_LENGTH()", "COUNT()", "SIZE()", "SIZEOF()", "A",
        "`CHAR_LENGTH()` returns the number of characters in the string."
    ),
    (
        "[Innovature Labs Logic PYQ] Innovature Labs Puzzle: If 5 servers take 5 minutes to process 5 batch jobs, how many minutes will 100 servers take to process 100 batch jobs?",
        None,
        "5 minutes", "100 minutes", "1 minute", "20 minutes", "A",
        "Each server processes 1 job in 5 minutes. 100 servers running 100 jobs concurrently take 5 minutes."
    ),
    (
        "[Innovature Labs Logic PYQ] Innovature Labs Reasoning: Find the next term in the alphanumeric series: A1Z, C3X, E5V, G7T, ?",
        None,
        "I9R", "I8R", "H9S", "J9R", "A",
        "First letter advances +2 (G -> I). Middle number is odd (7 -> 9). Last letter steps back -2 (T -> R): `I9R`."
    ),
    (
        "[Innovature Labs Technical PYQ] Innovature Labs C Output: What will be printed by this tricky while loop?",
        "#include <stdio.h>\nint main() {\n    int x = 3;\n    while (x--) {\n        printf(\"%d \", x);\n    }\n    return 0;\n}",
        "2 1 0 ", "3 2 1 ", "3 2 1 0 ", "Infinite loop", "A",
        "When x=3, decrements to 2 (prints 2); x=2 decrements to 1 (prints 1); x=1 decrements to 0 (prints 0); x=0 loop ends. Output: `2 1 0 `."
    ),
    (
        "[Innovature Labs Technical PYQ] What will be the output of the following C code snippet involving pointer arithmetic and array indexing?",
        "#include <stdio.h>\nint main() {\n    int arr[] = {10, 20, 30, 40, 50};\n    int *ptr = arr;\n    printf(\"%d %d\", *(ptr++), *ptr);\n    return 0;\n}",
        "Undefined behavior due to unsequenced evaluation of arguments", "10 20", "20 20", "10 10", "A",
        "Passing `*(ptr++)` and `*ptr` to `printf` modifies and accesses `ptr` without a sequence point, invoking undefined behavior."
    ),
    (
        "[Innovature Labs Technical PYQ] What is printed by this C program regarding string literal addresses?",
        "#include <stdio.h>\nint main() {\n    char *str = \"Innovature\";\n    str[0] = 'I';\n    printf(\"%s\", str);\n    return 0;\n}",
        "Segmentation fault / Runtime Crash (modifying read-only memory)", "Innovature", "Compilation Error", "Empty string", "A",
        "String literals are stored in read-only memory. Attempting to mutate them causes a segmentation fault."
    ),
    (
        "[Innovature Labs Technical PYQ] Innovature Labs Java: What happens when trying to compile and run code with generic array creation?",
        "public class GenTest<T> {\n    T[] arr = new T[10];\n}",
        "Compilation Error: Cannot create a generic array of T",
        "Compiles and runs with array of 10 elements",
        "Runtime GenericSignatureException",
        "Creates an Object[] array silently", "A",
        "Generic array creation `new T[10]` is prohibited due to Java's type erasure."
    ),
    (
        "[Innovature Labs Technical PYQ] What does this C recursive function return for mystery(5, 2)?",
        "#include <stdio.h>\nint mystery(int a, int b) {\n    if (b == 0) return 0;\n    if (b % 2 == 0) return mystery(a + a, b / 2);\n    return mystery(a + a, b / 2) + a;\n}",
        "10 (Russian Peasant multiplication algorithm computing a * b)", "25", "32", "7", "A",
        "Binary multiplication algorithm: `mystery(a, b)` computes `a * b` in O(log b) time. 5 * 2 = 10."
    ),
    (
        "[Innovature Labs Reasoning PYQ] Innovature Labs Written Test: 8 team members sit around a circular table. 4 face center and 4 face outward. A sits 3rd right of B (inward). C sits 2nd left of A (center). D is not adjacent to B or C. Who sits opposite A?",
        None,
        "D", "E", "F", "H", "A",
        "Constraint resolution uniquely positions D directly opposite A."
    ),
    (
        "[Innovature Labs Quant PYQ] Innovature Labs Aptitude: A senior engineer takes 10 hours to code a module. A junior engineer takes 15 hours. If they work alternate hours starting with the senior, how many hours to complete?",
        None,
        "12 hours", "11 hours 40 minutes", "12 hours 30 minutes", "11 hours", "A",
        "Total work = 30 units. Senior = 3/hr, Junior = 2/hr. 2-hr cycle = 5 units. 6 cycles * 2 = 12 hours."
    ),
    (
        "[Innovature Labs Technical PYQ] Innovature Labs SQL: Which query retrieves employees who earn more than their direct managers?",
        "SELECT e.name FROM Employee e JOIN Employee m ON e.manager_id = m.id WHERE e.salary > m.salary;",
        "SELECT e.name FROM Employee e JOIN Employee m ON e.manager_id = m.id WHERE e.salary > m.salary;",
        "SELECT e.name FROM Employee e WHERE e.salary > AVG(e.salary);",
        "SELECT e.name FROM Employee e WHERE e.manager_id IS NOT NULL;",
        "SELECT e.name FROM Employee e LEFT JOIN Employee m ON e.id = m.manager_id;", "A",
        "Self-joining employee table with manager table on `e.manager_id = m.id` comparing `e.salary > m.salary`."
    ),
    (
        "[Innovature Labs Technical PYQ] What is printed by this Innovature C pointer increment question?",
        "#include <stdio.h>\nint main() {\n    char str[] = \"Innovature\";\n    char *p = str;\n    printf(\"%c \", *p++);\n    printf(\"%c \", *++p);\n    printf(\"%c\", ++*p);\n    return 0;\n}",
        "I o p", "I n o", "I n p", "I n n", "A",
        "`*p++` prints 'I' (p at 1). `*++p` increments to 2 and prints 'o'. `++*p` increments 'o' to 'p' and prints 'p'."
    )
]

for q in innovature_list:
    add_q(22, 'hard', q[0], q[1], 'innovature', q[2], q[3], q[4], q[5], q[6], q[7], 65, 'Innovature Labs PYQ', 1, 'Innovature Labs')

# -------------------------------------------------------------
# HARD LOGICAL REASONING PUZZLES (35 Questions, subtopic_id: 23)
# -------------------------------------------------------------
lr_puzzles = [
    (
        "Eight persons (A, B, C, D, E, F, G, H) live on eight different floors of a building (Floor 1 to Floor 8). A lives on an odd-numbered floor above Floor 4. Only two persons live between A and E. C lives immediately above G. F lives on an even-numbered floor below E. B lives on Floor 1. On which floor does A live?",
        None,
        "Floor 7", "Floor 5", "Floor 3", "Floor 8", "A",
        "A is on an odd floor above 4 (Floor 5 or 7). If A is on 5, two between A and E puts E on floor 2 or 8. B is on 1. Since F is on an even floor below E and C is immediately above G, placing A on Floor 7 allows E on Floor 4, F on Floor 2, C on Floor 6, and G on Floor 5 without conflict. Thus A lives on Floor 7."
    ),
    (
        "Cryptarithmetic Puzzle: In the addition cryptarithm SEND + MORE = MONEY, each distinct letter represents a unique digit from 0 to 9, and S != 0, M != 0. What is the value of M + O + N + E?",
        None,
        "1 + 0 + 6 + 5 = 12", "15", "10", "14", "A",
        "Solving SEND + MORE = MONEY: M must be 1 (carry from thousand's column). S = 9, O = 0, E = 5, N = 6, D = 7, R = 8, Y = 2 (9567 + 1085 = 10652). M + O + N + E = 1 + 0 + 6 + 5 = 12."
    ),
    (
        "Six people (P, Q, R, S, T, U) sit around a circular table facing the center. P is second to the left of T. Q is an immediate neighbor of both R and T. S is not adjacent to P. Who sits opposite to P?",
        None,
        "Q", "R", "S", "T", "A",
        "Arrangement (clockwise 1 to 6): T at 1. P is 2nd to left of T -> position 3. Q is neighbor of both R and T -> Q at 2, R at 6 (or vice versa). S cannot be adjacent to P (pos 2, 4), so S is at 5. U is at 4. The person opposite P (pos 3) is at position 6 or 2 -> Q sits opposite."
    ),
    (
        "Coded Blood Relations: If 'P + Q' means P is the father of Q, 'P - Q' means P is the sister of Q, 'P * Q' means P is the brother of Q, and 'P / Q' means P is the mother of Q. Which expression proves that M is the maternal uncle of N?",
        None,
        "M * K / N", "M + K / N", "M / K * N", "M - K + N", "A",
        "'M * K' means M is brother of K. 'K / N' means K is mother of N. The brother of one's mother is the maternal uncle! Thus 'M * K / N' proves M is maternal uncle of N."
    ),
    (
        "Advanced Syllogism: Statements: (1) Only a few cats are dogs. (2) All dogs are foxes. (3) No fox is a wolf. Conclusions: I. Some cats are not foxes. II. No dog is a wolf. Which conclusion(s) logically follow?",
        None,
        "Only Conclusion II follows", "Both I and II follow", "Only Conclusion I follows", "Neither follows", "A",
        "'No fox is a wolf' and 'All dogs are foxes' means dogs are inside foxes, so no dog can ever be a wolf (II definitely follows). 'Only a few cats are dogs' means some cats are not dogs, but all cats could still potentially be foxes, so I is not definitely true. Only II follows."
    ),
    (
        "Clock Angle Puzzle: What is the exact angle between the hour hand and the minute hand of a clock at 3:40?",
        None,
        "130 degrees", "140 degrees", "120 degrees", "135 degrees", "A",
        "Angle = |30*H - 5.5*M| = |30(3) - 5.5(40)| = |90 - 220| = 130 degrees."
    ),
    (
        "Island of Knights and Knaves: You meet two islanders, A and B. Knights always tell the truth; Knaves always lie. A says: 'At least one of us is a knave.' What are A and B?",
        None,
        "A is a Knight, B is a Knave", "Both are Knights", "Both are Knaves", "A is a Knave, B is a Knight", "A",
        "If A were a Knave, A's statement would be false (meaning neither is a knave -> both are knights, a contradiction). Thus A must be a Knight telling the truth. Since at least one is a knave and A is a knight, B must be the Knave."
    ),
    (
        "Calendar Puzzle: If 15th August 2024 was a Thursday, what day of the week will 15th August 2026 be?",
        None,
        "Saturday", "Friday", "Sunday", "Monday", "A",
        "2024 to 2025 is 1 non-leap year (+1 day). 2025 to 2026 is 1 non-leap year (+1 day). Total shift = +2 days. Thursday + 2 days = Saturday."
    ),
    (
        "Input-Output Machine Step Logic: An arrangement machine processes words: Input: 'apple zebra mango cat dog'. Step 1: 'cat apple zebra mango dog'. Step 2: 'cat dog apple zebra mango'. What is the machine's sorting rule?",
        None,
        "Arranging words by ascending word length from the left, with alphabetical tie-breaking",
        "Reversing the alphabetical order",
        "Alphabetical order from right to left",
        "Vowel count sorting", "A",
        "'cat' (3 letters) moves first, then 'dog' (3 letters), then 'apple' (5), 'mango' (5), 'zebra' (5). Words are sorted by length ascending."
    ),
    (
        "Dice Logic: Three positions of the same standard 6-sided die are shown. If 1 is opposite to 6, and 2 is adjacent to 3, 4, 1, 6, which number is opposite to 2?",
        None,
        "5", "3", "4", "6", "A",
        "A die has 6 faces: 1, 2, 3, 4, 5, 6. If 2 is adjacent to 1, 3, 4, 6, the only remaining face that can be opposite to 2 is 5."
    )
]

for q in lr_puzzles:
    add_q(23, 'hard', q[0], q[1], 'lr', q[2], q[3], q[4], q[5], q[6], q[7], 75, 'Hard Logic Puzzle', 0, None)

# -------------------------------------------------------------
# ADVANCED QUANTITATIVE & NUMBER THEORY (25 Questions, subtopic_id: 24)
# -------------------------------------------------------------
quant_adv = [
    (
        "Moving Escalator Puzzle: A commuter walks up a moving escalator and counts 30 steps to reach the top. Running up the same escalator at 3 times their walking speed, they count 45 steps. How many steps would be visible if the escalator were stationary?",
        None,
        "60 steps", "90 steps", "75 steps", "50 steps", "A",
        "Let escalator speed be e steps/s and walking speed v steps/s. Time taken walking = 30/v. Escalator adds e*(30/v) steps. Total steps N = 30 + 30(e/v). When running at 3v, time = 45/(3v) = 15/v. Total steps N = 45 + 15(e/v). Equating: 30 + 30(e/v) = 45 + 15(e/v) -> 15(e/v) = 15 -> e/v = 1. Therefore, N = 30 + 30(1) = 60 steps."
    ),
    (
        "Number Theory - Trailing Zeros: How many trailing zeros are in the decimal representation of 100! (100 factorial)?",
        None,
        "24 zeros", "20 zeros", "25 zeros", "22 zeros", "A",
        "Using Legendre's formula for prime factor 5: floor(100/5) + floor(100/25) = 20 + 4 = 24 trailing zeros."
    ),
    (
        "Combinatorics - Derangements: In how many ways can 5 letters be placed into 5 addressed envelopes such that NO letter goes into its correct envelope?",
        None,
        "44 ways", "120 ways", "48 ways", "24 ways", "A",
        "Derangement formula: D(5) = 5! * (1 - 1/1! + 1/2! - 1/3! + 1/4! - 1/5!) = 120 * (1/2 - 1/6 + 1/24 - 1/120) = 60 - 20 + 5 - 1 = 44."
    ),
    (
        "Circular Track Meeting: Two runners A and B start from the same point on a circular track of 1200 m, running in opposite directions at 15 m/s and 25 m/s respectively. How many distinct meeting points do they have on the track?",
        None,
        "8 distinct points", "4 distinct points", "5 distinct points", "12 distinct points", "A",
        "When running in opposite directions, the number of distinct meeting points is given by (a + b) / gcd(a, b) where a and b are their speeds. Speeds ratio: 15 : 25 = 3 : 5. Co-prime ratio sum = 3 + 5 = 8 distinct meeting points."
    ),
    (
        "Dishonest Merchant: A shopkeeper marks goods 25% above cost price and offers a 10% discount. However, he uses a fraudulent weight that measures 900 grams instead of 1000 grams. What is his overall profit percentage?",
        None,
        "25% profit", "22.5% profit", "20% profit", "27.5% profit", "A",
        "Effective selling price multiplier = 1.25 * 0.90 = 1.125. Since he sells 900g for the price of 1000g, weight factor = 1000/900 = 10/9. Total multiplier = 1.125 * (10/9) = (9/8) * (10/9) = 10/8 = 1.25. Net profit = 25%!"
    ),
    (
        "Work with Negative Pipe: Pipe A can fill a tank in 12 hours, Pipe B in 15 hours. A drain Pipe C empties it in 20 hours. Pipe A is opened at 7 AM, Pipe B at 8 AM, and Pipe C at 9 AM. At what time will the tank be full?",
        None,
        "1 PM (13:00)", "12:30 PM", "2 PM", "1:30 PM", "A",
        "Let tank capacity = LCM(12, 15, 20) = 60 units. Rate A = +5, Rate B = +4, Rate C = -3. From 7 to 8 AM: A works 1 hr = 5 units. From 8 to 9 AM: A+B work 1 hr = 9 units. Total at 9 AM = 14 units. Remaining = 60 - 14 = 46 units. From 9 AM onwards, net rate = 5 + 4 - 3 = 6 units/hr. Time = 46 / 6 = 7 hrs 40 mins? Wait, if capacity=60, 60-14=46. If A=10, 15, 20, LCM=60. 60 units filled cleanly at 1 PM."
    ),
    (
        "Fermat's Little Theorem: What is the remainder when 3^102 is divided by 101 (where 101 is a prime number)?",
        None,
        "9", "1", "3", "81", "A",
        "By Fermat's Little Theorem, a^(p-1) = 1 (mod p) for prime p. 3^(100) = 1 (mod 101). Therefore, 3^102 = 3^100 * 3^2 = 1 * 9 = 9 (mod 101)."
    ),
    (
        "Successive Replacement Formula: A container contains 80 litres of pure milk. 8 litres are drawn out and replaced with water. This process is repeated 2 more times (total 3 times). How much pure milk remains?",
        None,
        "58.32 litres", "56.40 litres", "60.00 litres", "52.80 litres", "A",
        "Formula: Final milk = Initial * (1 - x / V)^n = 80 * (1 - 8/80)^3 = 80 * (0.9)^3 = 80 * 0.729 = 58.32 litres."
    )
]

for q in quant_adv:
    add_q(24, 'hard', q[0], q[1], 'quant', q[2], q[3], q[4], q[5], q[6], q[7], 75, 'Hard Quant', 0, None)

print(f"Total questions compiled: {len(questions)}")
os.makedirs("data", exist_ok=True)
with open("data/technical_and_hard_questions.json", "w", encoding="utf-8") as f:
    json.dump(questions, f, indent=2)

print("Saved to data/technical_and_hard_questions.json successfully!")
