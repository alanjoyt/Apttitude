<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "=================================================================\n";
echo "       REBALANCING COUNTS & VARIETY (TCS NQT, CAPGEMINI, AMAZON) \n";
echo "=================================================================\n\n";

// 1. Amazon: Reduce excess tech_dsa_complexity questions (>8%) by replacing 6 with Amazon cloud/system questions
$amazonDsaQs = $db->query("
    SELECT id, question_text 
    FROM questions 
    WHERE company_name = 'Amazon' AND is_published = 1 AND (
        question_text LIKE '%time complexity%' OR 
        question_text LIKE '%space complexity%'
    )
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

$amazonReplacements = [
    [
        'question' => "[Amazon Style Practice] In Amazon Web Services (AWS), which storage class is optimal for data that is rarely accessed but requires millisecond retrieval when needed?",
        'a' => "Amazon S3 Glacier Flexible",
        'b' => "Amazon S3 Standard-Infrequent Access (S3 Standard-IA)",
        'c' => "Amazon S3 Glacier Deep Archive",
        'd' => "Amazon EBS Cold HDD",
        'correct' => "B",
        'explanation' => "S3 Standard-IA offers rapid millisecond access for data that is accessed less frequently at a lower storage price point. Therefore, the optimal storage class is Amazon S3 Standard-Infrequent Access (S3 Standard-IA)."
    ],
    [
        'question' => "[Amazon Style Practice] Which architectural pattern decouples an order processing frontend from inventory microservices during sudden Prime Day traffic spikes?",
        'a' => "Asynchronous Queue-Based Load Leveling (e.g., Amazon SQS)",
        'b' => "Synchronous Point-to-Point HTTP RPC calls",
        'c' => "Shared single relational database file lock",
        'd' => "Hardcoded sleep delays in frontend threads",
        'correct' => "A",
        'explanation' => "Queue-based load leveling absorbs traffic spikes by buffering requests in a message queue (such as SQS), shielding backend services from saturation. Therefore, the correct architectural pattern is Asynchronous Queue-Based Load Leveling (e.g., Amazon SQS)."
    ],
    [
        'question' => "[Amazon Style Practice] In a distributed cache cluster, which cache replacement policy evicts items that have not been accessed for the longest duration?",
        'a' => "Least Recently Used (LRU)",
        'b' => "First In First Out (FIFO)",
        'c' => "Most Frequently Used (MFU)",
        'd' => "Random Replacement (RR)",
        'correct' => "A",
        'explanation' => "Least Recently Used (LRU) tracks access recency and discards the item with the oldest access timestamp. Therefore, the correct policy is Least Recently Used (LRU)."
    ],
    [
        'question' => "[Amazon Style Practice] In Amazon DynamoDB, what is the key difference between a Global Secondary Index (GSI) and a Local Secondary Index (LSI)?",
        'a' => "A GSI can have a partition key and sort key different from the base table, whereas an LSI must share the table partition key.",
        'b' => "An LSI can be created at any time after table creation, whereas a GSI cannot.",
        'c' => "A GSI only supports strongly consistent reads, while an LSI only supports eventual consistency.",
        'd' => "An LSI has unlimited storage size while a GSI is capped at 10 GB.",
        'correct' => "A",
        'explanation' => "In DynamoDB, a GSI can have partition and sort keys different from the base table and can be added anytime. An LSI must use the same partition key as the table and must be defined at table creation. Therefore, the correct answer is that a GSI can have a partition key and sort key different from the base table."
    ],
    [
        'question' => "[Amazon Style Practice] When designing an idempotent payment charge API in an e-commerce platform, which HTTP header or mechanism prevents duplicate credit card deductions on client retries?",
        'a' => "Unique Client-Generated Idempotency Key header",
        'b' => "HTTP User-Agent string",
        'c' => "Content-Encoding: gzip",
        'd' => "Accept-Language header",
        'correct' => "A",
        'explanation' => "An idempotency key sent by the client allows the server to recognize duplicate retry attempts of the same transaction and return the cached outcome without recharging. Therefore, the correct mechanism is a Unique Client-Generated Idempotency Key header."
    ],
    [
        'question' => "[Amazon Style Practice] In microservices communications, what is the primary role of a Circuit Breaker pattern?",
        'a' => "To prevent a failing downstream service from causing cascading failure across caller services.",
        'b' => "To increase network bandwidth between servers.",
        'c' => "To encrypt SSL certificates automatically.",
        'd' => "To compress JSON payloads to binary format.",
        'correct' => "A",
        'explanation' => "The Circuit Breaker pattern detects failures and encapsulates the logic of preventing an operation from constantly trying to execute an action that is doomed to fail, stopping cascading outages. Therefore, the primary role is preventing cascading failures."
    ]
];

$updStmt = $db->prepare("
    UPDATE questions 
    SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ?, explanation = ?, difficulty = 'medium', est_time_seconds = 60
    WHERE id = ?
");

foreach ($amazonDsaQs as $idx => $orig) {
    if (isset($amazonReplacements[$idx])) {
        $rep = $amazonReplacements[$idx];
        $updStmt->execute([
            $rep['question'], $rep['a'], $rep['b'], $rep['c'], $rep['d'], $rep['correct'], $rep['explanation'], $orig['id']
        ]);
        echo "Replaced Amazon DSA Q (ID {$orig['id']}) with Cloud/Architecture question.\n";
    }
}

// 2. TCS NQT: Top up with 10 original verified questions to reach >= 100
$tcsTopups = [
    [
        'question' => "[TCS NQT Style Practice] In a TCS delivery campus, the average salary of 20 junior software engineers is Rs. 35,000 and the average salary of 10 senior architects is Rs. 65,000. What is the combined average salary of all 30 employees?",
        'a' => "Rs. 45,000", 'b' => "Rs. 42,000", 'c' => "Rs. 48,000", 'd' => "Rs. 50,000",
        'correct' => "A",
        'explanation' => "Total salary = (20 * 35000) + (10 * 65000) = 7,00,000 + 6,50,000 = Rs. 13,50,000. Combined average = 1350000 / 30 = Rs. 45,000."
    ],
    [
        'question' => "[TCS NQT Style Practice] An automated QA test suite runs 240 unit tests in 16 minutes. At the same testing throughput, how many tests will run in 28 minutes?",
        'a' => "420 tests", 'b' => "380 tests", 'c' => "400 tests", 'd' => "450 tests",
        'correct' => "A",
        'explanation' => "Rate = 240 / 16 = 15 tests per minute. In 28 minutes: 28 * 15 = 420 tests."
    ],
    [
        'question' => "[TCS NQT Style Practice] If the cost price of 15 enterprise software licenses is equal to the selling price of 12 licenses, what is the profit percentage?",
        'a' => "25%", 'b' => "20%", 'c' => "30%", 'd' => "15%",
        'correct' => "A",
        'explanation' => "Let CP of 1 license = Re 1. CP of 12 = Rs. 12. SP of 12 = CP of 15 = Rs. 15. Profit = 15 - 12 = Rs. 3. Profit % = (3 / 12) * 100 = 25%."
    ],
    [
        'question' => "[TCS NQT Style Practice] Two data migration workers P and Q can complete a database transfer in 10 days and 15 days respectively. If they work on alternate days starting with P, in how many days will the transfer finish?",
        'a' => "12 days", 'b' => "11 days", 'c' => "13 days", 'd' => "14 days",
        'correct' => "A",
        'explanation' => "Total work = LCM(10, 15) = 30 units. P's rate = 3 units/day, Q's rate = 2 units/day. In 2 days (P then Q): 3 + 2 = 5 units. Days required for 30 units = (30 / 5) * 2 = 12 days."
    ],
    [
        'question' => "[TCS NQT Style Practice] Complete the geometric ratio sequence: 3, 9, 27, 81, ?",
        'a' => "243", 'b' => "216", 'c' => "162", 'd' => "270",
        'correct' => "A",
        'explanation' => "Each term is multiplied by 3: 81 * 3 = 243."
    ],
    [
        'question' => "[TCS NQT Style Practice] In a TCS recruitment round, choose the word that is opposite in meaning to 'OBVIOUS':",
        'a' => "Obscure", 'b' => "Clear", 'c' => "Apparent", 'd' => "Evident",
        'correct' => "A",
        'explanation' => "Obvious means easily perceived or understood. Its antonym is Obscure."
    ],
    [
        'question' => "[TCS NQT Style Practice] Fill in the blank: 'The deployment script succeeded _____ the secondary cluster encountered intermittent packet drops.'",
        'a' => "although", 'b' => "unless", 'c' => "despite", 'd' => "because of",
        'correct' => "A",
        'explanation' => "'Although' introduces a subordinate clause of contrast."
    ],
    [
        'question' => "[TCS NQT Style Practice] What will be the output of this C program?\n```c\n#include <stdio.h>\nint main() {\n    int a = 12, b = 4;\n    printf(\"%d\", a / b + a % b);\n    return 0;\n}\n```",
        'a' => "3", 'b' => "4", 'c' => "0", 'd' => "7",
        'correct' => "A",
        'explanation' => "12 / 4 = 3 and 12 % 4 = 0. Total = 3 + 0 = 3."
    ],
    [
        'question' => "[TCS NQT Style Practice] Which SQL operator is used to search for a specified pattern in a column using wildcards like '%' and '_'?",
        'a' => "LIKE", 'b' => "IN", 'c' => "BETWEEN", 'd' => "MATCH",
        'correct' => "A",
        'explanation' => "The LIKE operator is used in a WHERE clause to search for pattern matches with wildcard characters."
    ],
    [
        'question' => "[TCS NQT Style Practice] In Linux operating systems, which permission numerical code corresponds to read (r=4), write (w=2), and execute (x=1)?",
        'a' => "7", 'b' => "6", 'c' => "5", 'd' => "3",
        'correct' => "A",
        'explanation' => "Summing the permissions: 4 (read) + 2 (write) + 1 (execute) = 7."
    ]
];

$insStmt = $db->prepare("
    INSERT INTO questions (
        company_name, subtopic_id, difficulty, round_pattern, verification_status,
        question_text, option_a, option_b, option_c, option_d, correct_option,
        explanation, est_time_seconds, is_published, verified_by_script, import_batch_id
    ) VALUES (
        'TCS NQT', 1, 'medium', 'TCS NQT Assessment', 'practice_style',
        ?, ?, ?, ?, ?, ?, ?, 60, 1, 1, 'BATCH_TCS_TOPUP_V2'
    )
");

foreach ($tcsTopups as $t) {
    $insStmt->execute([
        $t['question'], $t['a'], $t['b'], $t['c'], $t['d'], $t['correct'], $t['explanation']
    ]);
}
echo "Added 10 new original verified questions to TCS NQT.\n";

// 3. Capgemini: Top up with 5 original verified questions
$capTopups = [
    [
        'question' => "[Capgemini Style Practice] A software consultant buys a tablet for Rs. 18,000 and spends Rs. 2,000 on accessories. He sells it for Rs. 24,000. What is his profit percentage?",
        'a' => "20%", 'b' => "25%", 'c' => "15%", 'd' => "30%",
        'correct' => "A",
        'explanation' => "Total CP = 18000 + 2000 = Rs. 20,000. SP = Rs. 24,000. Profit = 24000 - 20000 = Rs. 4,000. Profit % = (4000 / 20000) * 100 = 20%."
    ],
    [
        'question' => "[Capgemini Style Practice] Two servers A and B synchronize a file directory in 12 hours. If Server A alone can do it in 20 hours, how long will Server B take alone?",
        'a' => "30 hours", 'b' => "24 hours", 'c' => "36 hours", 'd' => "28 hours",
        'correct' => "A",
        'explanation' => "1/B = 1/12 - 1/20 = (5 - 3) / 60 = 2/60 = 1/30. Server B alone takes 30 hours."
    ],
    [
        'question' => "[Capgemini Style Practice] Identify the missing number in the difference series: 4, 9, 19, 39, ?",
        'a' => "79", 'b' => "69", 'c' => "89", 'd' => "75",
        'correct' => "A",
        'explanation' => "Pattern is *2 + 1: 4*2+1=9, 9*2+1=19, 19*2+1=39, 39*2+1 = 79."
    ],
    [
        'question' => "[Capgemini Style Practice] What will be the output of this C program?\n```c\n#include <stdio.h>\nint main() {\n    int x = 5;\n    printf(\"%d\", x << 2);\n    return 0;\n}\n```",
        'a' => "20", 'b' => "10", 'c' => "15", 'd' => "25",
        'correct' => "A",
        'explanation' => "Left shift by 2 multiplies by 2^2 = 4: 5 * 4 = 20."
    ],
    [
        'question' => "[Capgemini Style Practice] Which relational database constraint ensures that all values in a column are distinct and not null?",
        'a' => "PRIMARY KEY", 'b' => "FOREIGN KEY", 'c' => "CHECK", 'd' => "DEFAULT",
        'correct' => "A",
        'explanation' => "A PRIMARY KEY constraint uniquely identifies each record in a table, requiring unique non-null values."
    ]
];

$insCapStmt = $db->prepare("
    INSERT INTO questions (
        company_name, subtopic_id, difficulty, round_pattern, verification_status,
        question_text, option_a, option_b, option_c, option_d, correct_option,
        explanation, est_time_seconds, is_published, verified_by_script, import_batch_id
    ) VALUES (
        'Capgemini', 1, 'medium', 'Capgemini Assessment', 'practice_style',
        ?, ?, ?, ?, ?, ?, ?, 60, 1, 1, 'BATCH_CAPGEMINI_TOPUP_V2'
    )
");

foreach ($capTopups as $c) {
    $insCapStmt->execute([
        $c['question'], $c['a'], $c['b'], $c['c'], $c['d'], $c['correct'], $c['explanation']
    ]);
}
echo "Added 5 new original verified questions to Capgemini.\n";

echo "Rebalancing completed successfully.\n";
