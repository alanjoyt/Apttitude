-- Migration 05: 7-Day Fix Plan After Each Mock
-- Idempotent for MariaDB 10.4+ / 11.x

-- 1. Extend coin_transactions transaction_type enum to support fix_plan_reward
ALTER TABLE `coin_transactions` 
MODIFY COLUMN `transaction_type` ENUM('speed_bonus','streak_reward','mock_perfect','store_purchase','admin_grant','fix_plan_reward') NOT NULL;

-- 2. Create fix_plans table
CREATE TABLE IF NOT EXISTS `fix_plans` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `mock_session_id` VARCHAR(64) NULL,
  `plan_type` ENUM('standard', 'challenge', 'needs_data') NOT NULL DEFAULT 'standard',
  `status` ENUM('active', 'completed', 'abandoned', 'replaced') NOT NULL DEFAULT 'active',
  `weakest_subtopics` LONGTEXT NULL,
  `baseline_accuracy` DECIMAL(5,2) NULL DEFAULT 0.00,
  `retest_accuracy` DECIMAL(5,2) NULL DEFAULT NULL,
  `started_at` DATETIME NOT NULL,
  `completed_at` DATETIME NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_mock_session_id` (`mock_session_id`),
  INDEX `idx_user_status` (`user_id`, `status`),
  CONSTRAINT `fk_fix_plans_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create fix_plan_days table
CREATE TABLE IF NOT EXISTS `fix_plan_days` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `plan_id` INT NOT NULL,
  `day_no` TINYINT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `task_type` ENUM('targeted_practice', 'mistake_review', 'speed_drill', 'retest') NOT NULL,
  `subtopic_id` INT NULL,
  `target_count` INT NOT NULL DEFAULT 5,
  `progress_count` INT NOT NULL DEFAULT 0,
  `status` ENUM('locked', 'pending', 'completed') NOT NULL DEFAULT 'locked',
  `xp_reward` INT NOT NULL DEFAULT 20,
  `coin_reward` INT NOT NULL DEFAULT 5,
  `unlocked_at` DATETIME NULL DEFAULT NULL,
  `completed_at` DATETIME NULL DEFAULT NULL,
  UNIQUE KEY `uniq_plan_day` (`plan_id`, `day_no`),
  INDEX `idx_plan_day_status` (`plan_id`, `day_no`, `status`),
  CONSTRAINT `fk_fix_plan_days_plan` FOREIGN KEY (`plan_id`) REFERENCES `fix_plans`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
