<?php
/**
 * Master Migration Script: migrate_all.php
 * Idempotent schema installer covering all changes from Phases 1, 2, 3 and UI Polish.
 * Safe to run multiple times without error or data loss.
 */

function runMasterMigration(?PDO $customDb = null): void {
    if ($customDb !== null) {
        $db = $customDb;
    } else {
        require_once __DIR__ . '/../config/db.php';
        $db = Database::getConnection();
    }

    echo "=================================================================\n";
    echo "           APTTITUDEE MASTER IDEMPOTENT MIGRATION                \n";
    echo "=================================================================\n\n";

    // Helper to check if column exists
    $hasColumn = function(PDO $db, string $table, string $column): bool {
        $stmt = $db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
        return (bool)$stmt->fetch();
    };

    // Helper to check if table exists
    $hasTable = function(PDO $db, string $table): bool {
        $stmt = $db->query("SHOW TABLES LIKE '{$table}'");
        return (bool)$stmt->fetch();
    };

    // 1. Table: company_profiles
    if (!$hasTable($db, 'company_profiles')) {
        echo "Creating table 'company_profiles'...\n";
        $db->exec("
            CREATE TABLE IF NOT EXISTS `company_profiles` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(128) NOT NULL,
                `slug` VARCHAR(128) NOT NULL UNIQUE,
                `category` VARCHAR(64) DEFAULT 'IT Services',
                `difficulty_tier` VARCHAR(32) DEFAULT 'standard',
                `test_pattern` TEXT,
                `syllabus` TEXT,
                `rounds_json` JSON,
                `is_reported_pattern` TINYINT(1) DEFAULT 1,
                `last_checked` DATE DEFAULT '2026-10-18',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        echo "  [OK] Created 'company_profiles'.\n";
    } else {
        echo "Table 'company_profiles' exists. Checking columns...\n";
        if (!$hasColumn($db, 'company_profiles', 'is_reported_pattern')) {
            $db->exec("ALTER TABLE `company_profiles` ADD COLUMN `is_reported_pattern` TINYINT(1) DEFAULT 1");
            echo "  [OK] Added 'is_reported_pattern'.\n";
        }
        if (!$hasColumn($db, 'company_profiles', 'last_checked')) {
            $db->exec("ALTER TABLE `company_profiles` ADD COLUMN `last_checked` DATE DEFAULT '2026-10-18'");
            echo "  [OK] Added 'last_checked'.\n";
        }
    }

    // 2. Table: student_question_submissions
    if (!$hasTable($db, 'student_question_submissions')) {
        echo "Creating table 'student_question_submissions'...\n";
        $db->exec("
            CREATE TABLE IF NOT EXISTS `student_question_submissions` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT NOT NULL,
                `company_name` VARCHAR(128) DEFAULT NULL,
                `subtopic_id` INT DEFAULT NULL,
                `round_pattern` VARCHAR(255) DEFAULT NULL,
                `question_text` TEXT NOT NULL,
                `option_a` VARCHAR(255) NOT NULL,
                `option_b` VARCHAR(255) NOT NULL,
                `option_c` VARCHAR(255) NOT NULL,
                `option_d` VARCHAR(255) NOT NULL,
                `correct_option` ENUM('A','B','C','D') NOT NULL,
                `explanation` TEXT DEFAULT NULL,
                `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                `admin_notes` TEXT DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (`user_id`),
                INDEX (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        echo "  [OK] Created 'student_question_submissions'.\n";
    }

    // 3. Columns on 'questions' table
    echo "Auditing 'questions' schema...\n";
    $qCols = [
        'round_pattern' => "VARCHAR(255) DEFAULT NULL",
        'verification_status' => "ENUM('practice_style', 'verified_pyq', 'student_reported') DEFAULT 'practice_style'",
        'verification_level' => "ENUM('executed', 'computed', 'reviewed', 'unchecked') DEFAULT 'unchecked'",
        'reviewed_by' => "INT DEFAULT NULL",
        'reviewed_at' => "DATETIME DEFAULT NULL",
        'import_batch_id' => "VARCHAR(64) DEFAULT NULL",
        'verification_notes' => "TEXT DEFAULT NULL",
        'content_hash' => "VARCHAR(64) DEFAULT NULL"
    ];

    foreach ($qCols as $col => $definition) {
        if (!$hasColumn($db, 'questions', $col)) {
            $db->exec("ALTER TABLE `questions` ADD COLUMN `{$col}` {$definition}");
            echo "  [OK] Added questions.{$col}\n";
        }
    }

    // Index on content_hash
    $idxCheck = $db->query("SHOW INDEX FROM `questions` WHERE Key_name = 'idx_content_hash'")->fetch();
    if (!$idxCheck) {
        $db->exec("ALTER TABLE `questions` ADD INDEX `idx_content_hash` (`content_hash`)");
        echo "  [OK] Added index idx_content_hash.\n";
    }

    // 4. Columns on 'users' table
    echo "Auditing 'users' schema...\n";
    if (!$hasColumn($db, 'users', 'highest_tier')) {
        $db->exec("ALTER TABLE `users` ADD COLUMN `highest_tier` VARCHAR(32) DEFAULT 'Bronze'");
        echo "  [OK] Added users.highest_tier\n";
    }

    // 5. Columns on 'duels' table
    if ($hasTable($db, 'duels')) {
        echo "Auditing 'duels' schema...\n";
        if (!$hasColumn($db, 'duels', 'is_bot')) {
            $db->exec("ALTER TABLE `duels` ADD COLUMN `is_bot` TINYINT(1) DEFAULT 0");
            echo "  [OK] Added duels.is_bot\n";
        }
    }

    // 6. Columns on 'attempts' table
    if ($hasTable($db, 'attempts')) {
        echo "Auditing 'attempts' schema...\n";
        if (!$hasColumn($db, 'attempts', 'is_boss')) {
            $db->exec("ALTER TABLE `attempts` ADD COLUMN `is_boss` TINYINT(1) DEFAULT 0");
            echo "  [OK] Added attempts.is_boss\n";
        }
        if (!$hasColumn($db, 'attempts', 'mistake_type')) {
            $db->exec("ALTER TABLE `attempts` ADD COLUMN `mistake_type` ENUM('concept_gap','calculation_error','misread_question','ran_out_of_time') DEFAULT NULL");
            echo "  [OK] Added attempts.mistake_type\n";
        }
    }

    // 7. Seed company profiles if table is empty
    $profileCount = (int)$db->query("SELECT COUNT(*) FROM `company_profiles`")->fetchColumn();
    if ($profileCount === 0 && file_exists(__DIR__ . '/../database/seeders/company_profiles_seed.json')) {
        echo "Seeding initial company profiles...\n";
        $seed = json_decode(file_get_contents(__DIR__ . '/../database/seeders/company_profiles_seed.json'), true);
        $ins = $db->prepare("
            INSERT INTO `company_profiles` (name, slug, category, difficulty_tier, test_pattern, syllabus, rounds_json, is_reported_pattern, last_checked)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1, '2026-10-18')
        ");
        foreach ($seed as $cp) {
            $ins->execute([
                $cp['name'], $cp['slug'], $cp['category'], $cp['difficulty_tier'],
                $cp['test_pattern'], $cp['syllabus'], json_encode($cp['rounds_json'] ?? [])
            ]);
        }
        echo "  [OK] Seeded " . count($seed) . " company profiles.\n";
    }

    // 8. Table: user_practice_sessions (Quiz auto-save & resume)
    if (!$hasTable($db, 'user_practice_sessions')) {
        echo "Creating table 'user_practice_sessions'...\n";
        $db->exec("
            CREATE TABLE IF NOT EXISTS `user_practice_sessions` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT NOT NULL,
                `session_type` VARCHAR(32) NOT NULL DEFAULT 'practice',
                `subtopic_id` INT DEFAULT NULL,
                `question_id` INT DEFAULT NULL,
                `session_data` JSON DEFAULT NULL,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY `uniq_user_session` (`user_id`, `session_type`),
                INDEX `idx_user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        echo "  [OK] Created 'user_practice_sessions'.\n";
    }

    // 9. Tables: coupons, coupon_redemptions, coupon_audit_log
    if (!$hasTable($db, 'coupons')) {
        echo "Creating table 'coupons'...\n";
        $db->exec("
            CREATE TABLE IF NOT EXISTS `coupons` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `code` VARCHAR(64) NOT NULL UNIQUE,
                `discount_type` ENUM('percent', 'fixed') NOT NULL DEFAULT 'fixed',
                `discount_value` INT NOT NULL DEFAULT 0,
                `max_discount` INT DEFAULT NULL,
                `min_price` INT NOT NULL DEFAULT 0,
                `applicable_items` VARCHAR(255) NOT NULL DEFAULT 'all',
                `starts_at` DATETIME DEFAULT NULL,
                `expires_at` DATETIME DEFAULT NULL,
                `total_limit` INT DEFAULT NULL,
                `per_user_limit` INT NOT NULL DEFAULT 1,
                `allowed_users` TEXT DEFAULT NULL,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `times_used` INT NOT NULL DEFAULT 0,
                `created_by` INT DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_code` (`code`),
                INDEX `idx_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        echo "  [OK] Created 'coupons'.\n";
    }

    if (!$hasTable($db, 'coupon_redemptions')) {
        echo "Creating table 'coupon_redemptions'...\n";
        $db->exec("
            CREATE TABLE IF NOT EXISTS `coupon_redemptions` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `coupon_id` INT NOT NULL,
                `user_id` INT NOT NULL,
                `item_key` VARCHAR(64) NOT NULL,
                `original_price` INT NOT NULL,
                `discount_amount` INT NOT NULL,
                `final_price` INT NOT NULL,
                `redeemed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_coupon_id` (`coupon_id`),
                INDEX `idx_user_id` (`user_id`),
                INDEX `idx_user_coupon` (`user_id`, `coupon_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        echo "  [OK] Created 'coupon_redemptions'.\n";
    }

    if (!$hasTable($db, 'coupon_audit_log')) {
        echo "Creating table 'coupon_audit_log'...\n";
        $db->exec("
            CREATE TABLE IF NOT EXISTS `coupon_audit_log` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT DEFAULT NULL,
                `ip_address` VARCHAR(64) NOT NULL,
                `attempted_code` VARCHAR(64) NOT NULL,
                `is_success` TINYINT(1) NOT NULL DEFAULT 0,
                `failure_reason` VARCHAR(128) DEFAULT NULL,
                `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_rate_limit` (`ip_address`, `attempted_at`),
                INDEX `idx_user_rate_limit` (`user_id`, `attempted_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        echo "  [OK] Created 'coupon_audit_log'.\n";
    }

    // 8. Table: admin_audit_log (Comprehensive Admin Activity Logging)
    if (!$hasTable($db, 'admin_audit_log')) {
        echo "Creating table 'admin_audit_log'...\n";
        $db->exec("
            CREATE TABLE IF NOT EXISTS `admin_audit_log` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `admin_id` INT NOT NULL,
                `action` VARCHAR(64) NOT NULL,
                `target_type` VARCHAR(64) NOT NULL,
                `target_id` INT DEFAULT NULL,
                `details` TEXT DEFAULT NULL,
                `ip_address` VARCHAR(64) DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_admin` (`admin_id`),
                INDEX `idx_action` (`action`),
                INDEX `idx_target` (`target_type`, `target_id`),
                INDEX `idx_created` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        echo "  [OK] Created 'admin_audit_log'.\n";
    }

    // 9. Modern Technical Subtopics: TypeScript, LLM, and AI
    if ($hasTable($db, 'subtopics')) {
        $techTopicStmt = $db->prepare("SELECT id FROM topics WHERE code = 'TECHNICAL'");
        $techTopicStmt->execute();
        $techTopicId = $techTopicStmt->fetchColumn() ?: 5;

        $newSubtopics = [
            ['name' => 'TypeScript & Modern JS', 'code' => 'TECH_TYPESCRIPT'],
            ['name' => 'Large Language Models (LLM) & RAG', 'code' => 'TECH_LLM'],
            ['name' => 'Artificial Intelligence & Generative AI', 'code' => 'TECH_AI']
        ];
        foreach ($newSubtopics as $st) {
            $check = $db->prepare("SELECT id FROM subtopics WHERE code = ?");
            $check->execute([$st['code']]);
            if (!$check->fetchColumn()) {
                $ins = $db->prepare("INSERT INTO subtopics (topic_id, name, code) VALUES (?, ?, ?)");
                $ins->execute([$techTopicId, $st['name'], $st['code']]);
                echo "  [OK] Created subtopic '{$st['name']}' ({$st['code']}).\n";
            }
        }

        // Ingest questions from database/migrations/20261030_add_typescript_llm_ai_questions.sql
        $aiTsSqlFile = __DIR__ . '/../database/migrations/20261030_add_typescript_llm_ai_questions.sql';
        if (file_exists($aiTsSqlFile)) {
            $sqlContent = file_get_contents($aiTsSqlFile);
            preg_match_all('/INSERT INTO `questions`[\s\S]+?LIMIT 1;/i', $sqlContent, $matches);
            $queries = $matches[0] ?? [];
            $inserted = 0;
            foreach ($queries as $qSql) {
                $qSql = trim($qSql);
                if (empty($qSql)) continue;
                try {
                    $affected = $db->exec($qSql);
                    if ($affected > 0) $inserted++;
                } catch (\Throwable $e) {}
            }
            if ($inserted > 0) {
                echo "  [OK] Ingested {$inserted} modern technical questions (TypeScript, LLM, AI).\n";
            }
        }
    }

    echo "\n>>> Master Migration Complete: All tables and columns are up to date! <<<\n\n";
}

/**
 * Per-Migration Rollback Helper
 */
function rollbackMasterMigration(?PDO $customDb = null, string $step = 'user_practice_sessions'): void {
    if ($customDb !== null) {
        $db = $customDb;
    } else {
        require_once __DIR__ . '/../config/db.php';
        $db = Database::getConnection();
    }

    echo "Rolling back migration step: {$step}...\n";
    if ($step === 'coupons') {
        $db->exec("DROP TABLE IF EXISTS `coupon_redemptions`");
        $db->exec("DROP TABLE IF EXISTS `coupon_audit_log`");
        $db->exec("DROP TABLE IF EXISTS `coupons`");
        echo "  [OK] Dropped tables 'coupons', 'coupon_redemptions', 'coupon_audit_log'.\n";
    } elseif ($step === 'user_practice_sessions') {
        $db->exec("DROP TABLE IF EXISTS `user_practice_sessions`");
        echo "  [OK] Dropped table 'user_practice_sessions'.\n";
    } elseif ($step === 'company_profiles') {
        $db->exec("DROP TABLE IF EXISTS `company_profiles`");
        echo "  [OK] Dropped table 'company_profiles'.\n";
    } elseif ($step === 'student_question_submissions') {
        $db->exec("DROP TABLE IF EXISTS `student_question_submissions`");
        echo "  [OK] Dropped table 'student_question_submissions'.\n";
    } elseif ($step === 'admin_audit_log') {
        $db->exec("DROP TABLE IF EXISTS `admin_audit_log`");
        echo "  [OK] Dropped table 'admin_audit_log'.\n";
    }
}

if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $action = $argv[1] ?? 'migrate';
    if ($action === 'rollback') {
        $step = $argv[2] ?? 'user_practice_sessions';
        rollbackMasterMigration(null, $step);
    } else {
        runMasterMigration();
    }
}
