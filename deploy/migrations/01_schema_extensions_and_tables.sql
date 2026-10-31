-- ==============================================================================
-- Migration: 01_schema_extensions_and_tables.sql
-- Description: Core Schema Extensions, Rate Limiter, Mock Sessions & System Settings
-- Target Engine: MariaDB 10.4+ / 11.x & MySQL 5.7+ / 8.0+
-- Collation: utf8mb4_unicode_ci (strictly compatible with MariaDB / InfinityFree)
-- ==============================================================================

SET NAMES utf8mb4;

-- 1. Rate Limits Table (Required for Login Lockout, Store & Duel Rate Limiting)
CREATE TABLE IF NOT EXISTS `rate_limits` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `action` VARCHAR(64) NOT NULL,
    `identifier` VARCHAR(150) NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_action_ident_time` (`action`, `identifier`, `created_at`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Mock Test Sessions Table (Server-Authoritative Proctoring, Timer & Scores)
CREATE TABLE IF NOT EXISTS `mock_sessions` (
    `id` VARCHAR(64) NOT NULL,
    `user_id` INT NULL,
    `pack_code` VARCHAR(50) NOT NULL DEFAULT 'GENERAL_MOCK',
    `pack_title` VARCHAR(150) NOT NULL DEFAULT 'Placement Practice Mock',
    `total_questions` INT NOT NULL DEFAULT 10,
    `duration_seconds` INT NOT NULL DEFAULT 600,
    `started_at` DATETIME NOT NULL,
    `deadline` DATETIME NOT NULL,
    `status` ENUM('active','completed','expired') NOT NULL DEFAULT 'active',
    `question_ids` TEXT NULL,
    `user_answers` TEXT NULL,
    `tab_switch_count` INT NOT NULL DEFAULT 0,
    `correct_count` INT NOT NULL DEFAULT 0,
    `wrong_count` INT NOT NULL DEFAULT 0,
    `skipped_count` INT NOT NULL DEFAULT 0,
    `score_marks` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    `total_marks` DECIMAL(6,2) NOT NULL DEFAULT 10.00,
    `accuracy_percent` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `total_time_seconds` INT NOT NULL DEFAULT 0,
    `completed_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_user_deadline` (`user_id`, `deadline`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. System Settings Table (Atomic Locks for Weekly Rollover & Configuration)
CREATE TABLE IF NOT EXISTS `system_settings` (
    `setting_key` VARCHAR(50) NOT NULL,
    `setting_value` VARCHAR(255) NOT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initialize baseline settings if absent
INSERT INTO `system_settings` (`setting_key`, `setting_value`)
SELECT 'last_weekly_rollover', '2026-W44'
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'last_weekly_rollover');

-- 4. Duels Status Enum Hardening (Include 'cancelled' for abandonment cleanup)
ALTER TABLE `duels` 
MODIFY COLUMN `status` ENUM('waiting', 'active', 'completed', 'declined', 'cancelled') NOT NULL DEFAULT 'waiting';

-- 5. Student Question Submissions Table & Compatibility View
CREATE TABLE IF NOT EXISTS `student_question_submissions` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `company_name` VARCHAR(100) NOT NULL,
    `drive_date` DATE NULL,
    `round_pattern` VARCHAR(100) DEFAULT 'Written Assessment',
    `topic_name` VARCHAR(100) NOT NULL,
    `difficulty` ENUM('easy','medium','hard') DEFAULT 'medium',
    `question_text` TEXT NOT NULL,
    `code_snippet` TEXT NULL,
    `language` VARCHAR(30) NULL,
    `option_a` TEXT NOT NULL,
    `option_b` TEXT NOT NULL,
    `option_c` TEXT NOT NULL,
    `option_d` TEXT NOT NULL,
    `correct_option` ENUM('A','B','C','D') NOT NULL,
    `explanation` TEXT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `admin_notes` TEXT NULL,
    `reviewed_by` INT NULL,
    `reviewed_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_submission_status` (`status`),
    INDEX `idx_submission_company` (`company_name`),
    INDEX `idx_submission_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE OR REPLACE VIEW `student_submissions` AS 
SELECT 
    `id`, `user_id`, `company_name`, `drive_date`, `round_pattern`, 
    `topic_name`, `difficulty`, `question_text`, `code_snippet`, 
    `language`, `option_a`, `option_b`, `option_c`, `option_d`, 
    `correct_option`, `explanation`, `status`, `admin_notes`, 
    `reviewed_by`, `reviewed_at`, `created_at` 
FROM `student_question_submissions`;

-- 6. Helper Procedure for Idempotent Column Additions
DROP PROCEDURE IF EXISTS `AddColIfMissing`;

DELIMITER $$
CREATE PROCEDURE `AddColIfMissing`(
    IN in_table VARCHAR(64),
    IN in_col VARCHAR(64),
    IN in_def VARCHAR(255)
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = in_table 
          AND COLUMN_NAME = in_col
    ) THEN
        SET @ddl = CONCAT('ALTER TABLE `', in_table, '` ADD COLUMN `', in_col, '` ', in_def);
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END $$
DELIMITER ;

-- Apply columns to `attempts` table
CALL AddColIfMissing('attempts', 'mock_session_id', 'VARCHAR(64) NULL AFTER `mode`');
CALL AddColIfMissing('attempts', 'pack_code', 'VARCHAR(50) NULL AFTER `mock_session_id`');
CALL AddColIfMissing('attempts', 'skip_reason', 'VARCHAR(50) NULL AFTER `pack_code`');
CALL AddColIfMissing('attempts', 'is_implausible_time', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER `time_taken_seconds`');

-- Apply columns to `questions` table
CALL AddColIfMissing('questions', 'is_published', 'TINYINT(1) NOT NULL DEFAULT 1');
CALL AddColIfMissing('questions', 'total_attempts_count', 'INT NOT NULL DEFAULT 0');
CALL AddColIfMissing('questions', 'total_correct_count', 'INT NOT NULL DEFAULT 0');
CALL AddColIfMissing('questions', 'is_company_pyq', 'TINYINT(1) NOT NULL DEFAULT 0');
CALL AddColIfMissing('questions', 'verification_status', 'VARCHAR(50) NOT NULL DEFAULT \'practice_style\'');
CALL AddColIfMissing('questions', 'verification_level', 'VARCHAR(50) NOT NULL DEFAULT \'verified\'');
CALL AddColIfMissing('questions', 'company_name', 'VARCHAR(100) NULL');
CALL AddColIfMissing('questions', 'source_tag', 'VARCHAR(100) NULL');
CALL AddColIfMissing('questions', 'round_pattern', 'VARCHAR(100) NULL');
CALL AddColIfMissing('questions', 'content_hash', 'VARCHAR(64) NULL');

DROP PROCEDURE IF EXISTS `AddColIfMissing`;
