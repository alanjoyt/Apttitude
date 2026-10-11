-- Migration: 20261010_mock_test_sessions_and_time.sql
-- Purpose: Add mock test session tracking, pack references, and performance indexes for Progress Reports.
-- Note: Do NOT execute automatically. Run manually when updating production schema.

-- 1. Create dedicated mock_sessions table for fast scorecard retrieval & profile mock history
CREATE TABLE IF NOT EXISTS `mock_sessions` (
  `id` VARCHAR(36) NOT NULL,
  `user_id` INT NULL,
  `pack_code` VARCHAR(50) NOT NULL DEFAULT 'GENERAL_MOCK',
  `pack_title` VARCHAR(150) NOT NULL DEFAULT 'Placement Practice Mock',
  `total_questions` INT NOT NULL DEFAULT 10,
  `correct_count` INT NOT NULL DEFAULT 0,
  `wrong_count` INT NOT NULL DEFAULT 0,
  `skipped_count` INT NOT NULL DEFAULT 0,
  `score_marks` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `total_marks` DECIMAL(6,2) NOT NULL DEFAULT 10.00,
  `accuracy_percent` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `total_time_seconds` INT NOT NULL DEFAULT 0,
  `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mock_sessions_user_date` (`user_id`, `completed_at`),
  KEY `idx_mock_sessions_pack` (`pack_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Add mock session identifier & pack code to individual question attempts
-- Use safe conditional execution if supported or run directly on migration execution:
ALTER TABLE `attempts` 
  ADD COLUMN IF NOT EXISTS `mock_session_id` VARCHAR(36) NULL AFTER `mode`,
  ADD COLUMN IF NOT EXISTS `pack_code` VARCHAR(50) NULL AFTER `mock_session_id`;

-- 3. Add performance indexes for Profile Progress Report date-range aggregations & time analysis
ALTER TABLE `attempts`
  ADD INDEX IF NOT EXISTS `idx_attempts_user_mode_date` (`user_id`, `mode`, `attempted_at`),
  ADD INDEX IF NOT EXISTS `idx_attempts_mock_session` (`mock_session_id`);
