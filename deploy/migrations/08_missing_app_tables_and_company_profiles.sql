-- ============================================================================
-- Migration 08: Missing Application Tables & Company Profiles
-- Target: MariaDB 10.4+ / MySQL 8.0+ (InfinityFree & Local WAMP)
-- Idempotent: Table creation with IF NOT EXISTS, data with INSERT IGNORE INTO
-- ============================================================================

-- 1. Company Profiles (referenced in CompanyController, AdminController)
CREATE TABLE IF NOT EXISTS `company_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'IT Services',
  `difficulty_tier` enum('standard','elevated','premium') COLLATE utf8mb4_unicode_ci DEFAULT 'standard',
  `description` text COLLATE utf8mb4_unicode_ci,
  `rounds_json` json DEFAULT NULL,
  `test_pattern` text COLLATE utf8mb4_unicode_ci,
  `syllabus` text COLLATE utf8mb4_unicode_ci,
  `logo_icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'building',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_checked` date DEFAULT '2026-10-18',
  `is_reported_pattern` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_comp_name` (`name`),
  UNIQUE KEY `uniq_comp_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `company_profiles` (`id`, `name`, `slug`, `category`, `difficulty_tier`, `description`, `rounds_json`, `test_pattern`, `syllabus`, `logo_icon`, `is_active`, `created_at`, `updated_at`, `last_checked`, `is_reported_pattern`) VALUES
('1', 'Innovature', 'innovature', 'Software & Engineering Solutions', 'standard', 'Innovature campus recruitment features technical problem solving, C/Python logic, OOP concepts, and logical aptitude.', '[\"Online Written Assessment (Aptitude + Technical MCQ)\", \"Technical Interview Round 1 (Data Structures & Core Coding)\", \"Managerial & HR Interview\"]', 'Quantitative: 25%, Logical: 25%, Core CS/Coding MCQ: 50%. Duration: 60-75 mins. (Reported pattern, please verify)', 'C Pointers, Strings, Arrays, Python fundamentals, DBMS normalization, SQL joins, Number series, Time & Work. (Reported pattern, please verify)', 'code', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('2', 'TCS NQT', 'tcs-nqt', 'IT Services & Consulting', 'standard', 'Tata Consultancy Services National Qualifier Test (NQT) gateway for Ninja and Digital fresher hiring profiles.', '[\"TCS NQT Cognitive Test (Foundation Section)\", \"Advanced Quantitative & Technical Section\", \"Hands-on Coding Assessment\", \"Technical + HR Interview\"]', 'Numerical Ability: 20 Qs, Reasoning Ability: 20 Qs, Verbal Ability: 25 Qs, Advanced Cognitive & Coding. Negative marking applies in select sections. (Reported pattern, please verify)', 'Percentages, Profit & Loss, Arrangements, Syllogisms, Reading Comprehension, Pseudocode analysis, Algorithms. (Reported pattern, please verify)', 'server', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('3', 'Infosys', 'infosys', 'IT Services & Consulting', 'standard', 'Infosys Specialist Programmer (SP) and Digital Specialist Engineer (DSE) & Systems Engineer recruitment drives.', '[\"Online Assessment (Sectional cutoff mandatory)\", \"Technical Interview\", \"HR Interview\"]', 'Mathematical Thinking (Quant): 15 Qs / 35m, Logical Ability: 15 Qs / 25m, Verbal Ability: 20 Qs / 20m, Pseudocode: 5 Qs / 10m, Puzzle Solving: 4 Qs / 10m. (Reported pattern, please verify)', 'Cryptarithmetic, Permutations & Combinations, Data Sufficiency, Deductive Reasoning, Sentence Correction, Pseudocode tracing. (Reported pattern, please verify)', 'cpu', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('4', 'Wipro NLTH', 'wipro-nlth', 'IT Services & Consulting', 'standard', 'Wipro Elite National Talent Hunt (NLTH) assessment testing aptitude, written English comprehension, and coding proficiency.', '[\"National Qualifier Online Test\", \"Essay Writing (WET)\", \"Technical Interview\", \"HR Discussion\"]', 'Quants: 16 Qs, Logical: 14 Qs, Verbal: 22 Qs, Coding: 2 problems. Total time: 128 mins. (Reported pattern, please verify)', 'Speed & Distance, Blood Relations, Coding-Decoding, Error Spotting, Array manipulation, String parsing. (Reported pattern, please verify)', 'globe', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('5', 'Accenture', 'accenture', 'Technology & Consulting', 'standard', 'Accenture Associate Software Engineer (ASE) and Advanced ASE recruitment process.', '[\"Cognitive & Technical Assessment (eliminatory)\", \"Coding Assessment (mandatory upon passing Stage 1)\", \"Communication Assessment\", \"Technical & HR Interview\"]', 'Cognitive: 50 Qs (Critical Thinking, English, Abstract Reasoning), Technical: 40 Qs (Pseudocode, Cloud, Networking, Security). Total: 90 Qs / 90 mins. (Reported pattern, please verify)', 'Bitwise operations, Pseudocode loops, MS Office & Cloud basics, Network protocols, Syllogisms. (Reported pattern, please verify)', 'briefcase', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('6', 'Cognizant', 'cognizant', 'IT Services & Consulting', 'standard', 'Cognizant GenC, GenC Elevate, and GenC Next assessment tracks powered by modern skill-first evaluations.', '[\"Communication / Aptitude Assessment\", \"Technical & Domain MCQ\", \"Coding Assessment\", \"Combined Technical & HR Interview\"]', 'Numerical: 25 Qs, Logical: 25 Qs, Verbal: 20 Qs, Code debugging: 7 Qs. Duration: 100-120 mins. (Reported pattern, please verify)', 'Number properties, Profit & Loss, Seating arrangement, Sentence completion, C/Java debugging. (Reported pattern, please verify)', 'terminal', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('7', 'Capgemini', 'capgemini', 'Consulting & Technology', 'standard', 'Capgemini Exceller campus drive evaluating game-based aptitude, behavioral profiling, and technical competency.', '[\"Pseudocode & Technical MCQ\", \"English Communication Test\", \"Game-Based Aptitude Test\", \"Behavioral Profiling\", \"Technical & HR Interview\"]', 'Pseudocode: 30 Qs / 30m, English: 30 Qs / 30m, 4 Interactive Mini-games, Coding: 2 Qs / 45m. (Reported pattern, please verify)', 'Data structures, Flowchart logic, Grammar & Vocabulary, Grid Challenge, Motion Challenge, Deductive logic. (Reported pattern, please verify)', 'layers', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('8', 'Deloitte', 'deloitte', 'Consulting & Advisory', 'elevated', 'Deloitte USI Analyst hiring for engineering and consulting graduates.', '[\"Online Assessment (Quantitative, Logical, Verbal, Computer Fundamentals)\", \"Deloitte Group Discussion / Jam Session (Select campuses)\", \"Technical Interview\", \"HR Interview\"]', 'Quants: 16 Qs, Logical: 22 Qs, Verbal: 25 Qs, Computer Fundamentals: 30 Qs. Duration: 95 mins. (Reported pattern, please verify)', 'Logarithms, Probability, Direction sense, Statement & Assumptions, OOP concepts, Database management. (Reported pattern, please verify)', 'shield', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('9', 'Tech Mahindra', 'tech-mahindra', 'IT Services & Telecom Solutions', 'standard', 'Tech Mahindra Associate Software Engineer hiring track focusing on core logic, communication, and technical acumen.', '[\"Online Aptitude & English Test\", \"Technical & Psychometric Test\", \"Conversational AI / Speaking Test\", \"Technical & HR Interview\"]', 'Logical: 25 Qs, Quantitative: 25 Qs, English: 25 Qs, Technical: 25 Qs. Duration: 100 mins. (Reported pattern, please verify)', 'Time & Work, Simple & Compound Interest, Coding-Decoding, Operating Systems, Computer Architecture. (Reported pattern, please verify)', 'hard-drive', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('10', 'Zoho', 'zoho', 'Product & SaaS Software', 'elevated', 'Zoho Corporation Software Developer hiring, recognized for heavy emphasis on C programming, memory manipulation, and algorithms.', '[\"Round 1: General Aptitude & C/C++ Tracing MCQ\", \"Round 2: Basic Programming (5 hands-on questions)\", \"Round 3: Advanced Programming & Complex Logic (3 problems)\", \"Round 4: Technical HR Interview\", \"Round 5: General HR Interview\"]', 'Round 1: 30-40 Qs (Math Aptitude + Deep C Code Tracing, Pointers, Arrays, Bitwise operators). Strict time limit. (Reported pattern, please verify)', 'Pointers, Dynamic Memory (malloc/free), String manipulation without library functions, Recursion, Matrix transformations. (Reported pattern, please verify)', 'box', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('11', 'LTI Mindtree', 'lti-mindtree', 'Technology Consulting & Digital Solutions', 'standard', 'LTIMindtree campus assessment hiring across Specialist and Practitioner engineering bands.', '[\"Online Aptitude & Technical Assessment\", \"Spoken English Assessment\", \"Coding Test (Band-specific)\", \"Technical Interview\", \"HR Discussion\"]', 'Quantitative: 20 Qs, Logical: 20 Qs, Technical MCQ: 20 Qs, Coding: 1-2 Problems. Total time: 110 mins. (Reported pattern, please verify)', 'Ratios, Mixtures & Alligations, Series, Syllogisms, DBMS keys, OOP principles, DSA basics. (Reported pattern, please verify)', 'activity', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('12', 'HCL', 'hcl', 'IT Services & Enterprise Technology', 'standard', 'HCLTech First Careers and Graduate Engineer Trainee (GET) campus hiring drive.', '[\"Online Aptitude & Technical Assessment\", \"Technical Interview\", \"HR Interview\"]', 'Quantitative Ability: 15 Qs, Logical Reasoning: 15 Qs, Verbal: 15 Qs, Technical (CS/IT basics): 15 Qs. Duration: 60 mins. (Reported pattern, please verify)', 'Averages, Profit & Loss, Seating arrangement, Networking OSI layers, SQL basic queries, C pointers. (Reported pattern, please verify)', 'monitor', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('13', 'Amazon', 'amazon', 'Product, Cloud & Big Tech', 'premium', 'Amazon Software Development Engineer (SDE-1) campus and off-campus recruitment drive.', '[\"Online Assessment 1 (Code Debugging / Reasoning)\", \"Online Assessment 2 (Coding: 2 algorithmic problems + Work Style Survey)\", \"Online Assessment 3 (System Logic / Simulation)\", \"Virtual Onsite Technical Interviews (3-4 Rounds: DSA + Leadership Principles)\"]', 'Coding: 2 questions / 70 mins (Medium/Hard LeetCode style), Work Styles: 50 items / 20 mins. (Reported pattern, please verify)', 'Trees, Graphs, Dynamic Programming, Hash Maps, Heaps, Amazon 16 Leadership Principles, System scalability. (Reported pattern, please verify)', 'cloud', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('14', 'UST', 'ust', 'Digital Transformation & IT Services', 'standard', 'UST Global campus recruitment for Associate Software Developers and Quality Engineers.', '[\"Aptitude & Technical Online Assessment\", \"Coding Challenge\", \"Technical Evaluation Round\", \"Culture Fit & HR Interview\"]', 'Aptitude (Quants + Reasoning + Verbal): 45 Qs, Core CS (Java/Python/OOP/SQL): 30 Qs. Duration: 75 mins. (Reported pattern, please verify)', 'Time & Distance, Percentages, Blood relations, Java Collections, SQL Normalization, Exception Handling. (Reported pattern, please verify)', 'feather', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('15', 'IBM', 'ibm', 'Enterprise Cloud & Cognitive Solutions', 'standard', 'IBM Entry-Level Developer and Associate System Engineer hiring process.', '[\"Cognitive Ability Assessment (Cognify game-based or MCQ)\", \"English Language Assessment\", \"Coding Assessment (HackerRank)\", \"Technical Interview\", \"HR Interview\"]', 'Cognitive games / Quantitative: 20 Qs, Reasoning: 20 Qs, Technical: 20 Qs, Coding: 1-2 questions. Duration: 90 mins. (Reported pattern, please verify)', 'Grid navigation, Numerical reasoning, Cloud fundamentals, Operating Systems (Linux/Unix), Data structures. (Reported pattern, please verify)', 'database', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1');

-- 2. Coupons (referenced in CouponService, AdminController)
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `discount_value` int(11) NOT NULL DEFAULT 0,
  `max_discount` int(11) DEFAULT NULL,
  `min_price` int(11) NOT NULL DEFAULT 0,
  `applicable_items` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `total_limit` int(11) DEFAULT NULL,
  `per_user_limit` int(11) NOT NULL DEFAULT 1,
  `allowed_users` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `times_used` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_coupon_code` (`code`),
  KEY `idx_coupon_code` (`code`),
  KEY `idx_coupon_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `max_discount`, `min_price`, `applicable_items`, `starts_at`, `expires_at`, `total_limit`, `per_user_limit`, `allowed_users`, `is_active`, `times_used`, `created_by`, `created_at`, `updated_at`) VALUES
('56', 'DIST', 'percent', '80', NULL, '0', 'company_mock', '2026-10-20 00:00:00', '2026-10-31 23:27:00', '60', '1', NULL, '1', '0', '9', '2026-10-22 23:27:57', '2026-10-22 23:40:51'),
('57', 'DIST80', 'percent', '61', NULL, '0', 'all', '2026-10-20 00:00:00', '2026-10-31 23:34:00', NULL, '1', NULL, '1', '1', '9', '2026-10-22 23:35:05', '2026-10-23 00:06:41');

-- 3. Coupon Redemptions (referenced in CouponService)
CREATE TABLE IF NOT EXISTS `coupon_redemptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_price` int(11) NOT NULL,
  `discount_amount` int(11) NOT NULL,
  `final_price` int(11) NOT NULL,
  `redeemed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cr_coupon_id` (`coupon_id`),
  KEY `idx_cr_user_id` (`user_id`),
  KEY `idx_cr_user_coupon` (`user_id`,`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Coupon Audit Log (referenced in CouponService)
CREATE TABLE IF NOT EXISTS `coupon_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempted_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_success` tinyint(1) NOT NULL DEFAULT 0,
  `failure_reason` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attempted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cal_rate_limit` (`ip_address`,`attempted_at`),
  KEY `idx_cal_user_rate_limit` (`user_id`,`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. User Practice Sessions (referenced in PracticeController, AdminController)
CREATE TABLE IF NOT EXISTS `user_practice_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'practice',
  `subtopic_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `session_data` json DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_ups_user_session` (`user_id`,`session_type`),
  KEY `idx_ups_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Admin Audit Log (referenced in AdminController)
CREATE TABLE IF NOT EXISTS `admin_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_aal_admin` (`admin_id`),
  KEY `idx_aal_action` (`action`),
  KEY `idx_aal_target` (`target_type`,`target_id`),
  KEY `idx_aal_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;