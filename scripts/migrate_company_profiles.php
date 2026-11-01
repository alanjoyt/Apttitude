<?php
require_once __DIR__ . '/../config/db.php';
$db = Database::getConnection();

echo "Creating company_profiles table...\n";

$db->exec("
    CREATE TABLE IF NOT EXISTS company_profiles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        slug VARCHAR(100) NOT NULL UNIQUE,
        category VARCHAR(50) DEFAULT 'IT Services',
        difficulty_tier ENUM('standard', 'elevated', 'premium') DEFAULT 'standard',
        description TEXT,
        rounds_json JSON,
        test_pattern TEXT,
        syllabus TEXT,
        logo_icon VARCHAR(50) DEFAULT 'building',
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

$companies = [
    [
        'name' => 'Innovature',
        'slug' => 'innovature',
        'category' => 'Software & Engineering Solutions',
        'difficulty_tier' => 'standard',
        'description' => 'Innovature campus recruitment features technical problem solving, C/Python logic, OOP concepts, and logical aptitude.',
        'rounds' => ['Online Written Assessment (Aptitude + Technical MCQ)', 'Technical Interview Round 1 (Data Structures & Core Coding)', 'Managerial & HR Interview'],
        'pattern' => 'Quantitative: 25%, Logical: 25%, Core CS/Coding MCQ: 50%. Duration: 60-75 mins.',
        'syllabus' => 'C Pointers, Strings, Arrays, Python fundamentals, DBMS normalization, SQL joins, Number series, Time & Work.',
        'icon' => 'code'
    ],
    [
        'name' => 'TCS NQT',
        'slug' => 'tcs-nqt',
        'category' => 'IT Services & Consulting',
        'difficulty_tier' => 'standard',
        'description' => 'Tata Consultancy Services National Qualifier Test (NQT) gateway for Ninja and Digital fresher hiring profiles.',
        'rounds' => ['TCS NQT Cognitive Test (Foundation Section)', 'Advanced Quantitative & Technical Section', 'Hands-on Coding Assessment', 'Technical + HR Interview'],
        'pattern' => 'Numerical Ability: 20 Qs, Reasoning Ability: 20 Qs, Verbal Ability: 25 Qs, Advanced Cognitive & Coding. Negative marking applies in select sections.',
        'syllabus' => 'Percentages, Profit & Loss, Arrangements, Syllogisms, Reading Comprehension, Pseudocode analysis, Algorithms.',
        'icon' => 'server'
    ],
    [
        'name' => 'Infosys',
        'slug' => 'infosys',
        'category' => 'IT Services & Consulting',
        'difficulty_tier' => 'standard',
        'description' => 'Infosys Specialist Programmer (SP) and Digital Specialist Engineer (DSE) & Systems Engineer recruitment drives.',
        'rounds' => ['Online Assessment (Sectional cutoff mandatory)', 'Technical Interview', 'HR Interview'],
        'pattern' => 'Mathematical Thinking (Quant): 15 Qs / 35m, Logical Ability: 15 Qs / 25m, Verbal Ability: 20 Qs / 20m, Pseudocode: 5 Qs / 10m, Puzzle Solving: 4 Qs / 10m.',
        'syllabus' => 'Cryptarithmetic, Permutations & Combinations, Data Sufficiency, Deductive Reasoning, Sentence Correction, Pseudocode tracing.',
        'icon' => 'cpu'
    ],
    [
        'name' => 'Wipro NLTH',
        'slug' => 'wipro-nlth',
        'category' => 'IT Services & Consulting',
        'difficulty_tier' => 'standard',
        'description' => 'Wipro Elite National Talent Hunt (NLTH) assessment testing aptitude, written English comprehension, and coding proficiency.',
        'rounds' => ['National Qualifier Online Test', 'Essay Writing (WET)', 'Technical Interview', 'HR Discussion'],
        'pattern' => 'Quants: 16 Qs, Logical: 14 Qs, Verbal: 22 Qs, Coding: 2 problems. Total time: 128 mins.',
        'syllabus' => 'Speed & Distance, Blood Relations, Coding-Decoding, Error Spotting, Array manipulation, String parsing.',
        'icon' => 'globe'
    ],
    [
        'name' => 'Accenture',
        'slug' => 'accenture',
        'category' => 'Technology & Consulting',
        'difficulty_tier' => 'standard',
        'description' => 'Accenture Associate Software Engineer (ASE) and Advanced ASE recruitment process.',
        'rounds' => ['Cognitive & Technical Assessment (eliminatory)', 'Coding Assessment (mandatory upon passing Stage 1)', 'Communication Assessment', 'Technical & HR Interview'],
        'pattern' => 'Cognitive: 50 Qs (Critical Thinking, English, Abstract Reasoning), Technical: 40 Qs (Pseudocode, Cloud, Networking, Security). Total: 90 Qs / 90 mins.',
        'syllabus' => 'Bitwise operations, Pseudocode loops, MS Office & Cloud basics, Network protocols, Syllogisms.',
        'icon' => 'briefcase'
    ],
    [
        'name' => 'Cognizant',
        'slug' => 'cognizant',
        'category' => 'IT Services & Consulting',
        'difficulty_tier' => 'standard',
        'description' => 'Cognizant GenC, GenC Elevate, and GenC Next assessment tracks powered by modern skill-first evaluations.',
        'rounds' => ['Communication / Aptitude Assessment', 'Technical & Domain MCQ', 'Coding Assessment', 'Combined Technical & HR Interview'],
        'pattern' => 'Numerical: 25 Qs, Logical: 25 Qs, Verbal: 20 Qs, Code debugging: 7 Qs. Duration: 100-120 mins.',
        'syllabus' => 'Number properties, Profit & Loss, Seating arrangement, Sentence completion, C/Java debugging.',
        'icon' => 'terminal'
    ],
    [
        'name' => 'Capgemini',
        'slug' => 'capgemini',
        'category' => 'Consulting & Technology',
        'difficulty_tier' => 'standard',
        'description' => 'Capgemini Exceller campus drive evaluating game-based aptitude, behavioral profiling, and technical competency.',
        'rounds' => ['Pseudocode & Technical MCQ', 'English Communication Test', 'Game-Based Aptitude Test', 'Behavioral Profiling', 'Technical & HR Interview'],
        'pattern' => 'Pseudocode: 30 Qs / 30m, English: 30 Qs / 30m, 4 Interactive Mini-games, Coding: 2 Qs / 45m.',
        'syllabus' => 'Data structures, Flowchart logic, Grammar & Vocabulary, Grid Challenge, Motion Challenge, Deductive logic.',
        'icon' => 'layers'
    ],
    [
        'name' => 'Deloitte',
        'slug' => 'deloitte',
        'category' => 'Consulting & Advisory',
        'difficulty_tier' => 'elevated',
        'description' => 'Deloitte USI Analyst hiring for engineering and consulting graduates.',
        'rounds' => ['Online Assessment (Quantitative, Logical, Verbal, Computer Fundamentals)', 'Deloitte Group Discussion / Jam Session (Select campuses)', 'Technical Interview', 'HR Interview'],
        'pattern' => 'Quants: 16 Qs, Logical: 22 Qs, Verbal: 25 Qs, Computer Fundamentals: 30 Qs. Duration: 95 mins.',
        'syllabus' => 'Logarithms, Probability, Direction sense, Statement & Assumptions, OOP concepts, Database management.',
        'icon' => 'shield'
    ],
    [
        'name' => 'Tech Mahindra',
        'slug' => 'tech-mahindra',
        'category' => 'IT Services & Telecom Solutions',
        'difficulty_tier' => 'standard',
        'description' => 'Tech Mahindra Associate Software Engineer hiring track focusing on core logic, communication, and technical acumen.',
        'rounds' => ['Online Aptitude & English Test', 'Technical & Psychometric Test', 'Conversational AI / Speaking Test', 'Technical & HR Interview'],
        'pattern' => 'Logical: 25 Qs, Quantitative: 25 Qs, English: 25 Qs, Technical: 25 Qs. Duration: 100 mins.',
        'syllabus' => 'Time & Work, Simple & Compound Interest, Coding-Decoding, Operating Systems, Computer Architecture.',
        'icon' => 'hard-drive'
    ],
    [
        'name' => 'Zoho',
        'slug' => 'zoho',
        'category' => 'Product & SaaS Software',
        'difficulty_tier' => 'elevated',
        'description' => 'Zoho Corporation Software Developer hiring, recognized for heavy emphasis on C programming, memory manipulation, and algorithms.',
        'rounds' => ['Round 1: General Aptitude & C/C++ Tracing MCQ', 'Round 2: Basic Programming (5 hands-on questions)', 'Round 3: Advanced Programming & Complex Logic (3 problems)', 'Round 4: Technical HR Interview', 'Round 5: General HR Interview'],
        'pattern' => 'Round 1: 30-40 Qs (Math Aptitude + Deep C Code Tracing, Pointers, Arrays, Bitwise operators). Strict time limit.',
        'syllabus' => 'Pointers, Dynamic Memory (malloc/free), String manipulation without library functions, Recursion, Matrix transformations.',
        'icon' => 'box'
    ],
    [
        'name' => 'LTI Mindtree',
        'slug' => 'lti-mindtree',
        'category' => 'Technology Consulting & Digital Solutions',
        'difficulty_tier' => 'standard',
        'description' => 'LTIMindtree campus assessment hiring across Specialist and Practitioner engineering bands.',
        'rounds' => ['Online Aptitude & Technical Assessment', 'Spoken English Assessment', 'Coding Test (Band-specific)', 'Technical Interview', 'HR Discussion'],
        'pattern' => 'Quantitative: 20 Qs, Logical: 20 Qs, Technical MCQ: 20 Qs, Coding: 1-2 Problems. Total time: 110 mins.',
        'syllabus' => 'Ratios, Mixtures & Alligations, Series, Syllogisms, DBMS keys, OOP principles, DSA basics.',
        'icon' => 'activity'
    ],
    [
        'name' => 'HCL',
        'slug' => 'hcl',
        'category' => 'IT Services & Enterprise Technology',
        'difficulty_tier' => 'standard',
        'description' => 'HCLTech First Careers and Graduate Engineer Trainee (GET) campus hiring drive.',
        'rounds' => ['Online Aptitude & Technical Assessment', 'Technical Interview', 'HR Interview'],
        'pattern' => 'Quantitative Ability: 15 Qs, Logical Reasoning: 15 Qs, Verbal: 15 Qs, Technical (CS/IT basics): 15 Qs. Duration: 60 mins.',
        'syllabus' => 'Averages, Profit & Loss, Seating arrangement, Networking OSI layers, SQL basic queries, C pointers.',
        'icon' => 'monitor'
    ],
    [
        'name' => 'Amazon',
        'slug' => 'amazon',
        'category' => 'Product, Cloud & Big Tech',
        'difficulty_tier' => 'premium',
        'description' => 'Amazon Software Development Engineer (SDE-1) campus and off-campus recruitment drive.',
        'rounds' => ['Online Assessment 1 (Code Debugging / Reasoning)', 'Online Assessment 2 (Coding: 2 algorithmic problems + Work Style Survey)', 'Online Assessment 3 (System Logic / Simulation)', 'Virtual Onsite Technical Interviews (3-4 Rounds: DSA + Leadership Principles)'],
        'pattern' => 'Coding: 2 questions / 70 mins (Medium/Hard LeetCode style), Work Styles: 50 items / 20 mins.',
        'syllabus' => 'Trees, Graphs, Dynamic Programming, Hash Maps, Heaps, Amazon 16 Leadership Principles, System scalability.',
        'icon' => 'cloud'
    ],
    [
        'name' => 'UST',
        'slug' => 'ust',
        'category' => 'Digital Transformation & IT Services',
        'difficulty_tier' => 'standard',
        'description' => 'UST Global campus recruitment for Associate Software Developers and Quality Engineers.',
        'rounds' => ['Aptitude & Technical Online Assessment', 'Coding Challenge', 'Technical Evaluation Round', 'Culture Fit & HR Interview'],
        'pattern' => 'Aptitude (Quants + Reasoning + Verbal): 45 Qs, Core CS (Java/Python/OOP/SQL): 30 Qs. Duration: 75 mins.',
        'syllabus' => 'Time & Distance, Percentages, Blood relations, Java Collections, SQL Normalization, Exception Handling.',
        'icon' => 'feather'
    ],
    [
        'name' => 'IBM',
        'slug' => 'ibm',
        'category' => 'Enterprise Cloud & Cognitive Solutions',
        'difficulty_tier' => 'standard',
        'description' => 'IBM Entry-Level Developer and Associate System Engineer hiring process.',
        'rounds' => ['Cognitive Ability Assessment (Cognify game-based or MCQ)', 'English Language Assessment', 'Coding Assessment (HackerRank)', 'Technical Interview', 'HR Interview'],
        'pattern' => 'Cognitive games / Quantitative: 20 Qs, Reasoning: 20 Qs, Technical: 20 Qs, Coding: 1-2 questions. Duration: 90 mins.',
        'syllabus' => 'Grid navigation, Numerical reasoning, Cloud fundamentals, Operating Systems (Linux/Unix), Data structures.',
        'icon' => 'database'
    ]
];

$stmt = $db->prepare("
    INSERT INTO company_profiles (name, slug, category, difficulty_tier, description, rounds_json, test_pattern, syllabus, logo_icon)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        category = VALUES(category),
        difficulty_tier = VALUES(difficulty_tier),
        description = VALUES(description),
        rounds_json = VALUES(rounds_json),
        test_pattern = VALUES(test_pattern),
        syllabus = VALUES(syllabus),
        logo_icon = VALUES(logo_icon)
");

foreach ($companies as $c) {
    $stmt->execute([
        $c['name'],
        $c['slug'],
        $c['category'],
        $c['difficulty_tier'],
        $c['description'],
        json_encode($c['rounds']),
        $c['pattern'],
        $c['syllabus'],
        $c['icon']
    ]);
}

$count = $db->query("SELECT COUNT(*) FROM company_profiles")->fetchColumn();
echo "Seeded $count company profiles successfully.\n";
