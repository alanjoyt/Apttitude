<?php
require_once __DIR__ . '/config/db.php';

echo "Seeding PHP, C#, and Python Technical Questions...\n";

try {
    $db = Database::getConnection();

    $stmt = $db->prepare("
        INSERT INTO `questions` 
        (`subtopic_id`, `difficulty`, `question_text`, `code_snippet`, `language`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `explanation`, `est_time_seconds`, `source_tag`, `is_company_pyq`, `company_name`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $questions = [
        // ==========================================
        // PHP (subtopic_id: 17, language: 'php')
        // ==========================================
        [
            17, 'hard', 'In PHP 8+, what will be the output of the following comparison between number 0 and string "apple"?',
            "<?php\nvar_dump(0 == 'apple');\nvar_dump(0 == '0');\nvar_dump(0 === '0');\n?>",
            'php', 'bool(false) bool(true) bool(false)', 'bool(true) bool(true) bool(false)', 'bool(false) bool(false) bool(false)', 'bool(true) bool(false) bool(false)',
            'A',
            'In PHP 8.0+, type comparison semantics were overhauled! Comparing a number to a non-numeric string (`0 == \"apple\"`) now converts the number to string and compares string values (or checks if the string is numeric), resulting in `false` (in PHP 7 it was notoriously `true`!). `0 == \"0\"` compares numeric string \"0\" with 0, yielding `true`. `0 === \"0\"` checks type (int vs string), yielding `false`. Output: `bool(false) bool(true) bool(false)`.',
            65, 'PHP Technical', 0, NULL
        ],
        [
            17, 'hard', 'What is the output of the following PHP loop involving references in foreach?',
            "<?php\n$arr = [1, 2, 3];\nforeach ($arr as &$val) {}\nforeach ($arr as $val) {}\nprint_r($arr);\n?>",
            'php', 'Array ( [0] => 1 [1] => 2 [2] => 2 )', 'Array ( [0] => 1 [1] => 2 [2] => 3 )', 'Array ( [0] => 3 [1] => 3 [2] => 3 )', 'Fatal Error',
            'A',
            'A classic PHP reference trap! After the first loop, `$val` remains a reference to the last element `$arr[2]`. In the second loop:\n1. `$arr[0]` (1) is assigned to `$val` -> modifies `$arr[2]` to 1.\n2. `$arr[1]` (2) is assigned to `$val` -> modifies `$arr[2]` to 2.\n3. `$arr[2]` (now 2) is assigned to `$val` -> keeps `$arr[2]` as 2.\nHence, `$arr` becomes `[1, 2, 2]`. Best practice is to `unset($val)` immediately after reference foreach loops.',
            70, 'PHP Technical', 0, NULL
        ],
        [
            17, 'hard', 'What will be output by the following spaceship operator <=> comparisons in PHP?',
            "<?php\necho (5 <=> 10) . \" \";\necho (10 <=> 10) . \" \";\necho (15 <=> 10);\n?>",
            'php', '-1 0 1', '0 1 -1', 'false true true', '1 0 -1',
            'A',
            'The spaceship operator `$a <=> $b` returns:\n- `-1` if `$a < $b`\n- `0` if `$a == $b`\n- `1` if `$a > $b`\nFor `5 <=> 10` -> `-1`. For `10 <=> 10` -> `0`. For `15 <=> 10` -> `1`. Output: `-1 0 1`.',
            45, 'PHP Technical', 0, NULL
        ],
        [
            17, 'hard', 'What is the outcome of the null coalescing assignment operator ??= in PHP?',
            "<?php\n$data = ['count' => null];\n$data['count'] ??= 10;\n$data['total'] ??= 50;\n$data['count'] ??= 99;\necho $data['count'] . \" \" . $data['total'];\n?>",
            'php', '10 50', '99 50', 'null 50', 'Compilation error',
            'A',
            'The null coalescing assignment operator `$x ??= $y` assigns `$y` to `$x` only if `$x` is unset or `null`. Since `$data[\'count\']` is initially `null`, it is assigned 10. `$data[\'total\']` does not exist, so it is created and assigned 50. The second `$data[\'count\'] ??= 99` is ignored because count is now 10 (not null). Output: `10 50`.',
            50, 'PHP Technical', 0, NULL
        ],
        [
            17, 'hard', 'Why should `password_hash()` and `password_verify()` always be used over `md5()` or `sha1()` in PHP user authentication?',
            'php', 'MD5 and SHA-1 produce shorter hashes for smaller databases', 'password_hash uses adaptive algorithms (Bcrypt/Argon2) with automatic salting and configurable work factor (cost) to resist brute-force/GPU cracking', 'password_verify runs asynchronously in memory', 'MD5 is deprecated in HTTP protocols',
            'B',
            '`md5()` and `sha1()` are general-purpose, high-speed digest algorithms without salts, making them trivially crackable via rainbow tables and high-speed GPU rigs. `password_hash()` implements slow, key-stretched, salted hashing algorithms (such as Bcrypt and Argon2id) where the computational cost can be scaled to defend against modern brute-force hardware.',
            55, 'PHP Technical', 0, NULL
        ],
        [
            17, 'medium', 'What does PDO::ATTR_EMULATE_PREPARES => false achieve in database security?',
            'php', 'Increases connection timeout limits', 'Forces the database engine to perform true native prepared statement compilation, preventing second-order SQL injection and type casting bugs', 'Compresses network queries', 'Disables transaction rollbacks',
            'B',
            'When emulated prepares are enabled (default in some configurations), PDO simply replaces placeholders with escaped strings client-side. Setting `PDO::ATTR_EMULATE_PREPARES => false` forces MySQL/PostgreSQL to compile the SQL query template and parameters in two completely separate network phases, rendering SQL injection impossible.',
            50, 'PHP Technical', 0, NULL
        ],
        [
            17, 'hard', 'How does PHP resolve method naming conflicts when a class imports two traits that define the exact same method signature?',
            'php', 'The last imported trait silently overwrites the first', 'A fatal error occurs unless resolved explicitly using the `insteadof` operator', 'PHP runs both methods concurrently', 'The class constructor overrides both traits',
            'B',
            'If two traits define a method with the same name, a Fatal error is produced: \'Trait method has not been applied, because there is a collision with...\'. The developer must explicitly declare which trait takes precedence using `TraitA::method insteadof TraitB;` and optionally create an alias for the second with `as`.',
            60, 'PHP Technical', 0, NULL
        ],
        [
            17, 'medium', 'What is the primary difference between `include` and `require` in PHP?',
            'php', 'include is for HTML while require is for PHP classes', 'If the specified file is missing, include emits an E_WARNING and script continues; require emits an E_COMPILE_ERROR and script terminates immediately', 'require cannot take relative paths', 'include runs in a separate process thread',
            'B',
            '`include` generates an `E_WARNING` if the target file cannot be found, allowing code execution to proceed. `require` raises a fatal compile-time error (`E_COMPILE_ERROR`), terminating script execution immediately because the dependency is deemed essential.',
            40, 'PHP Technical', 0, NULL
        ],

        // ==========================================
        // C# & .NET (subtopic_id: 18, language: 'csharp')
        // ==========================================
        [
            18, 'hard', 'What happens during "Boxing" and "Unboxing" in C#/.NET, and what is its performance impact?',
            'csharp', 'Boxing wraps a reference type into a struct on the stack; unboxing moves it to the heap', 'Boxing converts a value type to type object/interface by allocating a wrapper object on the managed heap; unboxing extracts the value type from the heap back to the stack', 'Boxing encrypts sensitive memory blocks; unboxing decrypts them', 'Boxing is a compile-time optimization with zero runtime cost',
            'B',
            'In C#, Boxing converts a value type (like `int`, `struct`) to `object` or an interface by allocating memory on the managed garbage-collected heap and copying the value into it. Unboxing explicitly casts the object back to a value type. Frequent boxing/unboxing causes heap allocation pressure and increases Garbage Collection pauses.',
            60, 'C# Technical', 0, NULL
        ],
        [
            18, 'hard', 'What will be output by the following C# code illustrating LINQ Deferred Execution (Lazy Evaluation)?',
            "using System;\nusing System.Collections.Generic;\nusing System.Linq;\nclass Program {\n    static void Main() {\n        var list = new List<int> { 1, 2, 3 };\n        var query = list.Where(x => x > 1);\n        list.Add(4);\n        list.Add(5);\n        Console.WriteLine(query.Count());\n    }\n}",
            'csharp', '2', '4', '3', 'Compilation error',
            'B',
            'LINQ queries (like `.Where()`) use Deferred Execution: the query definition does NOT iterate the collection when declared. It is only evaluated when a terminal operation (like `.Count()`, `.ToList()`, or `foreach`) is invoked. Since `4` and `5` were added before `.Count()` was called, the sequence evaluated is `[1, 2, 3, 4, 5]`. Elements `> 1` are `2, 3, 4, 5` (total of 4 elements). Output: `4`.',
            65, 'C# Technical', 0, NULL
        ],
        [
            18, 'hard', 'What dangerous pitfall occurs when blocking on an asynchronous method using `.Result` or `.Wait()` in C# UI or ASP.NET SynchronizationContext applications?',
            'csharp', 'Out of Memory Exception', 'Deadlock because the async continuation waits to acquire the same SynchronizationContext thread blocked by .Result', 'Silent data corruption', 'Thread Pool exhaustion within 1 millisecond',
            'B',
            'Calling `.Result` or `.Wait()` synchronously blocks the current thread (often the UI thread or ASP.NET request thread). When the asynchronous operation finishes, it attempts to marshal its continuation back onto the captured `SynchronizationContext`. Because the context thread is already blocked waiting for the Task to complete, neither can proceed, causing a classic Deadlock!',
            65, 'C# Technical', 0, NULL
        ],
        [
            18, 'hard', 'In C#, what is the difference between a `struct` (Value Type) and a `class` (Reference Type) regarding assignment?',
            'csharp', 'A class assignment copies the memory bit-by-bit; a struct assignment copies only the reference pointer', 'A struct assignment performs a field-by-field value copy; a class assignment copies only the reference pointer pointing to the same heap instance', 'Structs can inherit from classes, but classes cannot inherit from structs', 'Structs are allocated exclusively in L1 cache',
            'B',
            'Structs are value types (typically allocated inline on the stack or within containing objects). Assigning `structB = structA` creates an independent copy of all field values. Classes are reference types (allocated on the managed heap); assigning `classB = classA` merely copies the memory pointer, leaving both variables referencing the same underlying heap object.',
            55, 'C# Technical', 0, NULL
        ],
        [
            18, 'hard', 'What will be output by the following null-conditional and null-coalescing code in C#?',
            "using System;\nclass Program {\n    static void Main() {\n        string s = null;\n        int length = s?.Length ?? -1;\n        Console.WriteLine(length);\n    }\n}",
            'csharp', 'Throws NullReferenceException', '0', '-1', 'Compilation Error',
            'C',
            'The null-conditional operator `?.` short-circuits to `null` if the target `s` is `null` (evaluating `s?.Length` as `int?` with value `null`). Then the null-coalescing operator `??` provides the fallback default value `-1`. Output: `-1`.',
            45, 'C# Technical', 0, NULL
        ],
        [
            18, 'medium', 'What is the purpose of the `IDisposable` interface and the `using` statement in C#?',
            'csharp', 'To force immediate Garbage Collection cycle', 'To provide deterministic release of unmanaged resources (file handles, database connections, sockets) as soon as execution exits the scope', 'To serialize objects to JSON', 'To enable multi-threading on CPU cores',
            'B',
            '`IDisposable` defines the `Dispose()` method for releasing unmanaged system resources deterministically. The `using` statement (or declaration) is syntactic sugar that wraps the block in a `try...finally`, guaranteeing `Dispose()` is invoked even if an exception is thrown.',
            50, 'C# Technical', 0, NULL
        ],
        [
            18, 'hard', 'What feature do C# 9+ `record` types provide compared to regular classes?',
            'csharp', 'Built-in value-based equality semantics, non-destructive mutation via `with` expressions, and concise primary constructor syntax', 'Records cannot contain properties', 'Records are strictly allocated on the CPU stack', 'Records disable reflection',
            'A',
            'A `record` (or `record class`) is a reference type with built-in value-based equality (two records are equal if their properties are equal), auto-generated `GetHashCode()`, `ToString()`, and support for non-destructive mutation via the `with` keyword.',
            55, 'C# Technical', 0, NULL
        ],

        // ==========================================
        // PYTHON (subtopic_id: 19, language: 'python')
        // ==========================================
        [
            19, 'hard', 'What is the output of the following Python function with a mutable default argument?',
            "def append_to(element, target=[]):\n    target.append(element)\n    return target\n\nprint(append_to(1))\nprint(append_to(2))\nprint(append_to(3, []))\nprint(append_to(4))",
            'python', '[1] then [2] then [3] then [4]', '[1] then [1, 2] then [3] then [1, 2, 4]', '[1] then [1, 2] then [1, 2, 3] then [1, 2, 3, 4]', 'SyntaxError',
            'B',
            'In Python, default arguments are evaluated ONCE at function definition time, NOT at every call! The mutable list `target=[]` is shared across all invocations where the default is used:\n- `append_to(1)` -> `[1]`\n- `append_to(2)` -> modifies same list -> `[1, 2]`\n- `append_to(3, [])` -> uses explicit new list -> `[3]`\n- `append_to(4)` -> uses original default list -> `[1, 2, 4]`. Output: `[1]`, `[1, 2]`, `[3]`, `[1, 2, 4]`.',
            70, 'Python Technical', 0, NULL
        ],
        [
            19, 'hard', 'What is the output of the following Python closure late-binding puzzle?',
            "funcs = [lambda x: x + i for i in range(3)]\nresults = [f(10) for f in funcs]\nprint(results)",
            'python', '[10, 11, 12]', '[12, 12, 12]', '[10, 10, 10]', '[0, 1, 2]',
            'B',
            'Python closures use "late binding": the variable `i` inside the lambda is looked up when the lambda is CALLED, not when it is created. When the list comprehension finishes, `i` has the final value `2`. When each `f(10)` is executed, `10 + 2 = 12` for all three lambdas! To avoid this, use a default argument: `lambda x, i=i: x + i`. Output: `[12, 12, 12]`.',
            65, 'Python Technical', 0, NULL
        ],
        [
            19, 'hard', 'What is the difference between shallow copy (`copy.copy`) and deep copy (`copy.deepcopy`) in Python?',
            'python', 'Shallow copy creates a new compound object and inserts references to the original nested objects; deep copy recursively copies all nested objects', 'Shallow copy copies strings; deep copy copies integers', 'Deep copy operates only on dictionaries', 'There is no difference in Python 3',
            'A',
            'A shallow copy constructs a new collection object, but populates it with references to the child objects contained in the original. Modifying a mutable nested object in the shallow copy affects the original. A deep copy recursively duplicates every object found, creating completely independent nested copies.',
            55, 'Python Technical', 0, NULL
        ],
        [
            19, 'hard', 'What will be output by the following generator function in Python?',
            "def generator_puzzle():\n    yield 1\n    yield from [2, 3]\n    yield 4\n\ngen = generator_puzzle()\nprint(next(gen), list(gen))",
            'python', '1 [2, 3, 4]', '1 [1, 2, 3, 4]', '[1, 2, 3, 4] []', '1 2',
            'A',
            '`next(gen)` advances the generator to its first yield point, producing `1`. Then `list(gen)` exhausts the remainder of the generator, expanding `yield from [2, 3]` and `yield 4` into `[2, 3, 4]`. Output: `1 [2, 3, 4]`.',
            50, 'Python Technical', 0, NULL
        ],
        [
            19, 'hard', 'In Python, what is the resolution order for variable lookup defined by the LEGB rule?',
            'python', 'Linear -> Explicit -> Global -> Binary', 'Local -> Enclosing -> Global -> Built-in', 'Lexical -> External -> Global -> Base', 'Logical -> Environment -> Group -> Block',
            'B',
            'Python searches for names following the LEGB rule:\n1. **Local (L)**: Defined inside the current function/lambda.\n2. **Enclosing (E)**: Defined in enclosing/outer nested functions.\n3. **Global (G)**: Defined at top-level of module or declared with `global`.\n4. **Built-in (B)**: Pre-assigned built-ins (`print`, `len`, `range`).',
            45, 'Python Technical', 0, NULL
        ],
        [
            19, 'medium', 'What does the `is` operator compare in Python versus `==`?',
            'python', '`is` checks value equality; `==` checks identity', '`is` checks memory identity (`id(a) == id(b)`); `==` checks value equality via `__eq__`', '`is` checks type only; `==` checks memory address', 'Both operators are identical',
            'B',
            'The `is` operator evaluates whether two variables point to the exact same memory address in RAM (`id(a) == id(b)`). The `==` operator evaluates whether two objects hold equivalent values by invoking the object\'s `__eq__()` method.',
            40, 'Python Technical', 0, NULL
        ]
    ];

    $count = 0;
    foreach ($questions as $q) {
        $stmt->execute($q);
        $count++;
    }
    echo "Seeded {$count} PHP, C#, and Python questions successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
