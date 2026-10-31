-- Authoritative Mock Test Sessions Table
CREATE TABLE IF NOT EXISTS `mock_sessions` (
  `id` VARCHAR(64) NOT NULL,
  `user_id` INT NULL,
  `pack_code` VARCHAR(50) NOT NULL DEFAULT 'GENERAL_MOCK',
  `pack_title` VARCHAR(150) NOT NULL DEFAULT 'Placement Practice Mock',
  `total_questions` INT NOT NULL DEFAULT 10,
  `duration_seconds` INT NOT NULL DEFAULT 600,
  `started_at` DATETIME NOT NULL,
  `deadline` DATETIME NOT NULL,
  `status` ENUM('active', 'completed', 'expired') NOT NULL DEFAULT 'active',
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
  KEY `idx_mock_user_status` (`user_id`, `status`),
  KEY `idx_mock_deadline` (`deadline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `attempts` 
  ADD COLUMN IF NOT EXISTS `mock_session_id` VARCHAR(64) NULL AFTER `mode`,
  ADD COLUMN IF NOT EXISTS `pack_code` VARCHAR(50) NULL AFTER `mock_session_id`,
  ADD COLUMN IF NOT EXISTS `skip_reason` VARCHAR(50) NULL AFTER `pack_code`;
