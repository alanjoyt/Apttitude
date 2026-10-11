<?php
// Configuration & Database PDO Singleton for Apttitudee

// Auto-detect environment: Localhost (WAMP/XAMPP) vs InfinityFree / Remote Production
$httpHost = $_SERVER['HTTP_HOST'] ?? '';
$serverName = $_SERVER['SERVER_NAME'] ?? '';
$docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';

$isInfinityFree = (
    strpos($httpHost, 'apttitudee.xo.je') !== false ||
    strpos($httpHost, 'xo.je') !== false ||
    strpos($httpHost, 'apti.gt.tc') !== false ||
    strpos($httpHost, 'apttitudeee.gt.tc') !== false ||
    strpos($httpHost, 'gt.tc') !== false ||
    strpos($httpHost, 'infinityfree') !== false ||
    strpos($httpHost, 'epizy') !== false ||
    strpos($httpHost, 'rf.gd') !== false ||
    strpos($serverName, 'xo.je') !== false ||
    strpos($serverName, 'gt.tc') !== false ||
    strpos($docRoot, 'if0_') !== false ||
    strpos($docRoot, 'epiz_') !== false
);

// Optional local custom overrides (e.g. config.local.php)
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

if (!defined('DB_HOST')) {
    if ($isInfinityFree) {
        // InfinityFree Production MySQL Details (for apttitudee.xo.je / if0_43141634)
        define('DB_HOST', getenv('DB_HOST') ?: 'sql306.infinityfree.com');
        define('DB_NAME', getenv('DB_NAME') ?: 'if0_43141634_apttitudee');
        define('DB_USER', getenv('DB_USER') ?: 'if0_43141634');
        define('DB_PASS', getenv('DB_PASS') ?: 'nexycaaa');
        define('APP_NAME', 'Apttitudee');
        define('APP_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . ($httpHost ?: 'apttitudee.xo.je'));
    } else {
        // Localhost Development (WAMP/XAMPP)
        define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
        define('DB_NAME', getenv('DB_NAME') ?: 'apttitudee');
        define('DB_USER', getenv('DB_USER') ?: 'root');
        define('DB_PASS', getenv('DB_PASS') ?: '');
        define('APP_NAME', 'Apttitudee');
        define('APP_URL', 'http://localhost/Apttitudee');
    }
}

// Replace with your real Google Cloud Console OAuth 2.0 Client ID for production
if (!defined('GOOGLE_CLIENT_ID')) {
    define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com');
}

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $candidateHosts = array_unique([DB_HOST, 'sql306.infinityfree.com', 'sql106.infinityfree.com', 'sql312.infinityfree.com']);
            $candidateUsers = array_unique([DB_USER, 'if0_43141634', 'if0_43071889', 'if0_43084648']);
            $candidateDbs = array_unique([DB_NAME, 'if0_43141634_apttitudee', 'if0_43071889_apttitudee', 'if0_43084648_apttitudee']);
            $candidatePasses = array_unique([DB_PASS, 'nexycaaa', 'nexy1024', 'nexy1@24', 'nexyra1024']);

            $lastException = null;

            // Direct attempt first
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                $lastException = $e;

                // Fallback attempt across candidates
                foreach ($candidatePasses as $tryPass) {
                    foreach ($candidateDbs as $tryDb) {
                        try {
                            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . $tryDb . ";charset=utf8mb4";
                            self::$instance = new PDO($dsn, DB_USER, $tryPass, [
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                                PDO::ATTR_EMULATE_PREPARES => false,
                            ]);
                            break 2;
                        } catch (PDOException $e2) {
                            $lastException = $e2;
                        }
                    }
                }
            }

            // If connection failed
            if (self::$instance === null) {
                // If on local development, attempt host-only connection to auto-create missing db
                if (DB_HOST === '127.0.0.1' || DB_HOST === 'localhost') {
                    try {
                        $dsnHostOnly = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
                        $pdo = new PDO($dsnHostOnly, DB_USER, DB_PASS);
                        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        $pdo->exec("USE `" . DB_NAME . "`");
                        
                        self::$instance = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                        ]);
                    } catch (PDOException $e2) {
                        die("Database connection error: " . $e2->getMessage());
                    }
                } else {
                    die("<div style='font-family:sans-serif;max-width:620px;margin:50px auto;padding:25px;border:1px solid #f5c2c7;background:#f8d7da;border-radius:12px;color:#842029;'>" .
                        "<h3 style='margin-top:0;'>⚠️ Database Connection Failed</h3>" .
                        "<p>Unable to connect to database <strong>" . htmlspecialchars(DB_NAME) . "</strong> on <strong>" . htmlspecialchars(DB_HOST) . "</strong> with user <strong>" . htmlspecialchars(DB_USER) . "</strong>.</p>" .
                        "<p><strong>Current Settings in config/db.php:</strong></p>" .
                        "<ul style='line-height:1.6;'>" .
                        "<li>Host: <code>" . htmlspecialchars(DB_HOST) . "</code></li>" .
                        "<li>User: <code>" . htmlspecialchars(DB_USER) . "</code></li>" .
                        "<li>Database: <code>" . htmlspecialchars(DB_NAME) . "</code></li>" .
                        "</ul>" .
                        "<small style='color:#666;'>MySQL Error: " . htmlspecialchars($lastException ? $lastException->getMessage() : 'Connection failed') . "</small>" .
                        "</div>");
                }
            }

            // Auto-heal & ensure all Phase 1 & Phase 2 tables and seed data exist!
            self::autoMigrateAllTables(self::$instance);
        }
        return self::$instance;
    }

    /**
     * Master Auto-Migration & Seeder Runner
     */
    public static function autoMigrateAllTables(PDO $db): void {
        static $migrated = false;
        if ($migrated) return;
        $migrated = true;

        try {
            // Speed optimization: If tables already exist, skip heavy DDL on every request
            $check = $db->query("SHOW TABLES LIKE 'topics'")->fetch();
            if ($check && !isset($_GET['force_migrate'])) {
                return;
            }

            // 1. Users Table (with all addon columns)
            $db->exec("
                CREATE TABLE IF NOT EXISTS `users` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `name` VARCHAR(100) NOT NULL,
                  `email` VARCHAR(150) NOT NULL UNIQUE,
                  `password_hash` VARCHAR(255) NOT NULL,
                  `plan` ENUM('free', 'premium') NOT NULL DEFAULT 'free',
                  `total_xp` INT NOT NULL DEFAULT 0,
                  `coins` INT NOT NULL DEFAULT 0,
                  `tier` ENUM('Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond') NOT NULL DEFAULT 'Bronze',
                  `current_streak` INT NOT NULL DEFAULT 0,
                  `last_active_date` DATE DEFAULT NULL,
                  `streak_freezes` INT NOT NULL DEFAULT 1,
                  `bio` TEXT DEFAULT NULL,
                  `avatar_icon` VARCHAR(50) DEFAULT 'bi-person-circle',
                  `referral_code` VARCHAR(20) DEFAULT NULL UNIQUE,
                  `referred_by` INT DEFAULT NULL,
                  `device_hash` VARCHAR(255) DEFAULT NULL,
                  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB;
            ");

            // Ensure columns exist if table was created earlier without them
            $cols = $db->query("SHOW COLUMNS FROM `users`")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('bio', $cols)) $db->exec("ALTER TABLE `users` ADD COLUMN `bio` TEXT DEFAULT NULL");
            if (!in_array('avatar_icon', $cols)) $db->exec("ALTER TABLE `users` ADD COLUMN `avatar_icon` VARCHAR(50) DEFAULT 'bi-person-circle'");
            if (!in_array('referral_code', $cols)) $db->exec("ALTER TABLE `users` ADD COLUMN `referral_code` VARCHAR(20) DEFAULT NULL");
            if (!in_array('referred_by', $cols)) $db->exec("ALTER TABLE `users` ADD COLUMN `referred_by` INT DEFAULT NULL");
            if (!in_array('device_hash', $cols)) $db->exec("ALTER TABLE `users` ADD COLUMN `device_hash` VARCHAR(255) DEFAULT NULL");

            // 2. Topics & Subtopics
            $db->exec("
                CREATE TABLE IF NOT EXISTS `topics` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `name` VARCHAR(100) NOT NULL,
                  `code` VARCHAR(20) NOT NULL UNIQUE,
                  `icon` VARCHAR(50) DEFAULT 'bi-journal-text',
                  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `subtopics` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `topic_id` INT NOT NULL,
                  `name` VARCHAR(100) NOT NULL,
                  `code` VARCHAR(50) NOT NULL UNIQUE,
                  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  FOREIGN KEY (`topic_id`) REFERENCES `topics`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;
            ");

            // Seed Topics & Subtopics
            $db->exec("
                INSERT INTO `topics` (`id`, `name`, `code`, `icon`) VALUES
                (1, 'Quantitative Aptitude', 'QUANT', 'bi-calculator-fill'),
                (2, 'Logical Reasoning', 'LR', 'bi-puzzle-fill'),
                (3, 'Verbal Ability', 'VERBAL', 'bi-chat-left-quote-fill'),
                (4, 'Data Interpretation', 'DI', 'bi-bar-chart-line-fill')
                ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

                INSERT INTO `subtopics` (`id`, `topic_id`, `name`, `code`) VALUES
                (1, 1, 'Speed, Distance & Time', 'QUANT_SDT'),
                (2, 1, 'Percentages & Profit/Loss', 'QUANT_PERC'),
                (3, 1, 'Ratio & Proportion', 'QUANT_RATIO'),
                (4, 2, 'Seating Arrangements', 'LR_SEATING'),
                (5, 2, 'Blood Relations', 'LR_BLOOD'),
                (6, 2, 'Number & Letter Series', 'LR_SERIES'),
                (7, 3, 'Reading Comprehension', 'VERBAL_RC'),
                (8, 3, 'Sentence Correction', 'VERBAL_SC'),
                (9, 4, 'Bar Charts & Tables', 'DI_BAR_TABLE')
                ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
            ");

            // 3. Questions & Attempts
            $db->exec("
                CREATE TABLE IF NOT EXISTS `questions` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `subtopic_id` INT NOT NULL,
                  `difficulty` ENUM('easy', 'medium', 'hard') NOT NULL DEFAULT 'medium',
                  `question_text` TEXT NOT NULL,
                  `option_a` TEXT NOT NULL,
                  `option_b` TEXT NOT NULL,
                  `option_c` TEXT NOT NULL,
                  `option_d` TEXT NOT NULL,
                  `correct_option` ENUM('A', 'B', 'C', 'D') NOT NULL,
                  `explanation` TEXT NOT NULL,
                  `est_time_seconds` INT NOT NULL DEFAULT 45,
                  `total_attempts_count` INT NOT NULL DEFAULT 0,
                  `total_correct_count` INT NOT NULL DEFAULT 0,
                  `source_tag` VARCHAR(100) DEFAULT 'General',
                  `is_company_pyq` TINYINT(1) NOT NULL DEFAULT 0,
                  `company_name` VARCHAR(50) DEFAULT NULL,
                  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;
            ");

            // Ensure columns exist on existing table
            $qCols = $db->query("SHOW COLUMNS FROM `questions`")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('is_company_pyq', $qCols)) $db->exec("ALTER TABLE `questions` ADD COLUMN `is_company_pyq` TINYINT(1) NOT NULL DEFAULT 0");
            if (!in_array('company_name', $qCols)) $db->exec("ALTER TABLE `questions` ADD COLUMN `company_name` VARCHAR(50) DEFAULT NULL");
            // Auto-flag existing PYQ questions
            $db->exec("UPDATE `questions` SET `is_company_pyq` = 1 WHERE `source_tag` LIKE '%PYQ%' OR `source_tag` LIKE '%2025%' OR `source_tag` LIKE '%NQT%' OR `source_tag` LIKE '%INFOSYS%' OR `source_tag` LIKE '%WIPRO%' OR `source_tag` LIKE '%ACCENTURE%' OR `source_tag` LIKE '%COGNIZANT%'");

            $db->exec("
                CREATE TABLE IF NOT EXISTS `attempts` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `user_id` INT DEFAULT NULL,
                  `question_id` INT NOT NULL,
                  `selected_option` ENUM('A', 'B', 'C', 'D') NOT NULL,
                  `is_correct` TINYINT(1) NOT NULL,
                  `confidence` ENUM('sure', 'not_sure', 'guessing') NOT NULL DEFAULT 'sure',
                  `time_taken_seconds` INT NOT NULL DEFAULT 0,
                  `xp_earned` INT NOT NULL DEFAULT 0,
                  `coins_earned` INT NOT NULL DEFAULT 0,
                  `mode` ENUM('practice', 'mock', 'daily_challenge', 'guest') NOT NULL DEFAULT 'practice',
                  `mistake_type` ENUM('concept_gap', 'calculation_error', 'misread_question', 'ran_out_of_time') DEFAULT NULL,
                  `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                  FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;
            ");

            $attCols = $db->query("SHOW COLUMNS FROM `attempts`")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('mistake_type', $attCols)) {
                $db->exec("ALTER TABLE `attempts` ADD COLUMN `mistake_type` ENUM('concept_gap', 'calculation_error', 'misread_question', 'ran_out_of_time') DEFAULT NULL");
            }

            // 4. Progress, Leaderboard, Badges, Store, Reports
            $db->exec("
                CREATE TABLE IF NOT EXISTS `user_progress` (
                  `user_id` INT NOT NULL,
                  `subtopic_id` INT NOT NULL,
                  `total_attempted` INT NOT NULL DEFAULT 0,
                  `total_correct` INT NOT NULL DEFAULT 0,
                  `current_mastery_level` INT NOT NULL DEFAULT 1,
                  `next_review_at` DATETIME DEFAULT NULL,
                  PRIMARY KEY (`user_id`, `subtopic_id`),
                  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                  FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `leaderboard_scores` (
                  `user_id` INT NOT NULL,
                  `period` ENUM('weekly', 'alltime') NOT NULL,
                  `tier` ENUM('Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond') NOT NULL DEFAULT 'Bronze',
                  `period_xp` INT NOT NULL DEFAULT 0,
                  `rank_position` INT DEFAULT 0,
                  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`user_id`, `period`),
                  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `badges` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `code` VARCHAR(50) NOT NULL UNIQUE,
                  `title` VARCHAR(100) NOT NULL,
                  `description` TEXT NOT NULL,
                  `icon` VARCHAR(50) NOT NULL,
                  `xp_reward` INT NOT NULL DEFAULT 50
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `user_badges` (
                  `user_id` INT NOT NULL,
                  `badge_id` INT NOT NULL,
                  `earned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`user_id`, `badge_id`),
                  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                  FOREIGN KEY (`badge_id`) REFERENCES `badges`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `coin_transactions` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `user_id` INT NOT NULL,
                  `amount` INT NOT NULL,
                  `transaction_type` ENUM('speed_bonus', 'streak_reward', 'mock_perfect', 'store_purchase', 'admin_grant') NOT NULL,
                  `reference_description` VARCHAR(255) NOT NULL,
                  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `unlockables` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `code` VARCHAR(50) NOT NULL UNIQUE,
                  `title` VARCHAR(100) NOT NULL,
                  `description` TEXT NOT NULL,
                  `item_type` ENUM('company_mock', 'streak_freeze', 'cosmetic_badge', 'hint_token') NOT NULL,
                  `coin_cost` INT NOT NULL DEFAULT 100,
                  `icon` VARCHAR(50) DEFAULT 'bi-bag-fill'
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `user_unlocks` (
                  `user_id` INT NOT NULL,
                  `unlockable_id` INT NOT NULL,
                  `unlocked_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`user_id`, `unlockable_id`),
                  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                  FOREIGN KEY (`unlockable_id`) REFERENCES `unlockables`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `question_reports` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `question_id` INT NOT NULL,
                  `user_id` INT DEFAULT NULL,
                  `reason` ENUM('wrong_answer', 'typo', 'vague_explanation', 'other') NOT NULL,
                  `comment` TEXT DEFAULT NULL,
                  `status` ENUM('pending', 'resolved', 'dismissed') NOT NULL DEFAULT 'pending',
                  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `system_settings` (
                  `setting_key` VARCHAR(50) PRIMARY KEY,
                  `setting_value` VARCHAR(255) NOT NULL,
                  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB;
            ");

            // 5. Phase 2 Feature Tables (Quests, Friends, Squads, Duels, Time Attack, Campaign)
            $db->exec("
                CREATE TABLE IF NOT EXISTS `daily_quests` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `title` VARCHAR(100) NOT NULL,
                  `description` TEXT NOT NULL,
                  `quest_type` ENUM('solve_count', 'hard_questions', 'sure_correct', 'streak_maintain', 'mock_complete') NOT NULL,
                  `target_count` INT NOT NULL DEFAULT 1,
                  `xp_reward` INT NOT NULL DEFAULT 100,
                  `coin_reward` INT NOT NULL DEFAULT 20,
                  `icon` VARCHAR(50) DEFAULT 'bi-bullseye'
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `user_daily_quests` (
                  `user_id` INT NOT NULL,
                  `quest_id` INT NOT NULL,
                  `progress_count` INT NOT NULL DEFAULT 0,
                  `is_completed` TINYINT(1) NOT NULL DEFAULT 0,
                  `is_claimed` TINYINT(1) NOT NULL DEFAULT 0,
                  `assigned_date` DATE NOT NULL,
                  PRIMARY KEY (`user_id`, `quest_id`, `assigned_date`),
                  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                  FOREIGN KEY (`quest_id`) REFERENCES `daily_quests`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `friendships` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `user_id` INT NOT NULL,
                  `friend_id` INT NOT NULL,
                  `status` ENUM('pending', 'accepted', 'blocked') NOT NULL DEFAULT 'pending',
                  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  UNIQUE KEY `user_friend_unique` (`user_id`, `friend_id`),
                  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                  FOREIGN KEY (`friend_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `guilds` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `name` VARCHAR(100) NOT NULL UNIQUE,
                  `tag` VARCHAR(10) NOT NULL UNIQUE,
                  `description` TEXT,
                  `leader_id` INT NOT NULL,
                  `weekly_xp_target` INT NOT NULL DEFAULT 5000,
                  `current_weekly_xp` INT NOT NULL DEFAULT 0,
                  `icon` VARCHAR(50) DEFAULT 'bi-shield-shaded',
                  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  FOREIGN KEY (`leader_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `guild_members` (
                  `guild_id` INT NOT NULL,
                  `user_id` INT NOT NULL,
                  `role` ENUM('leader', 'officer', 'member') NOT NULL DEFAULT 'member',
                  `contributed_xp` INT NOT NULL DEFAULT 0,
                  `joined_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`guild_id`, `user_id`),
                  FOREIGN KEY (`guild_id`) REFERENCES `guilds`(`id`) ON DELETE CASCADE,
                  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `duels` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `challenger_id` INT NOT NULL,
                  `opponent_id` INT DEFAULT NULL,
                  `status` ENUM('waiting', 'active', 'completed', 'declined') NOT NULL DEFAULT 'waiting',
                  `winner_id` INT DEFAULT NULL,
                  `wager_coins` INT NOT NULL DEFAULT 10,
                  `challenger_score` INT NOT NULL DEFAULT 0,
                  `opponent_score` INT NOT NULL DEFAULT 0,
                  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  FOREIGN KEY (`challenger_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `duel_questions` (
                  `duel_id` INT NOT NULL,
                  `question_id` INT NOT NULL,
                  `question_order` INT NOT NULL,
                  PRIMARY KEY (`duel_id`, `question_id`),
                  FOREIGN KEY (`duel_id`) REFERENCES `duels`(`id`) ON DELETE CASCADE,
                  FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `time_attack_runs` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `user_id` INT NOT NULL,
                  `score` INT NOT NULL,
                  `correct_count` INT NOT NULL,
                  `total_attempted` INT NOT NULL,
                  `max_combo` INT NOT NULL DEFAULT 0,
                  `duration_seconds` INT NOT NULL DEFAULT 60,
                  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `campaign_nodes` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `chapter_number` INT NOT NULL,
                  `node_order` INT NOT NULL,
                  `title` VARCHAR(100) NOT NULL,
                  `subtopic_id` INT NOT NULL,
                  `required_stars` INT NOT NULL DEFAULT 0,
                  `reward_xp` INT NOT NULL DEFAULT 150,
                  `reward_coins` INT NOT NULL DEFAULT 30,
                  FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;

                CREATE TABLE IF NOT EXISTS `user_campaign_progress` (
                  `user_id` INT NOT NULL,
                  `node_id` INT NOT NULL,
                  `stars_earned` INT NOT NULL DEFAULT 0,
                  `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`user_id`, `node_id`),
                  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                  FOREIGN KEY (`node_id`) REFERENCES `campaign_nodes`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB;
            ");

            // Seed Default Daily Quests & Campaign Nodes
            $db->exec("
                INSERT INTO `daily_quests` (`id`, `title`, `description`, `quest_type`, `target_count`, `xp_reward`, `coin_reward`, `icon`) VALUES
                (1, 'Daily Warmup', 'Solve 5 questions in any topic', 'solve_count', 5, 50, 10, 'bi-lightning-charge-fill'),
                (2, 'Hard Hitter', 'Answer 2 Hard difficulty questions correctly', 'hard_questions', 2, 100, 25, 'bi-fire'),
                (3, 'Master of Certainty', 'Get 3 questions correct with Sure confidence', 'sure_correct', 3, 120, 30, 'bi-shield-check')
                ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

                INSERT INTO `campaign_nodes` (`id`, `chapter_number`, `node_order`, `title`, `subtopic_id`, `required_stars`, `reward_xp`, `reward_coins`) VALUES
                (1, 1, 1, 'Velocity Outpost', 1, 0, 100, 20),
                (2, 1, 2, 'Ratio Citadel', 3, 2, 150, 25),
                (3, 1, 3, 'Percentage Peak', 2, 4, 200, 35),
                (4, 2, 1, 'Logic Labyrinth', 4, 6, 250, 40),
                (5, 2, 2, 'Bloodline Manor', 5, 9, 300, 50)
                ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

                INSERT INTO `system_settings` (`setting_key`, `setting_value`)
                VALUES ('developer_mode', '0')
                ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;
            ");

        } catch (\Throwable $e) {
            // Silently swallow errors if table auto-migration fails
        }
    }
}

