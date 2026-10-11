-- Apttitudee Database Schema
-- Compatible with MySQL 5.7+ / 8.0+

CREATE DATABASE IF NOT EXISTS `apttitudee` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `apttitudee`;

-- 1. Users Table (with XP + Speed Coins Dual-Currency)
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


-- 2. Topics Table
CREATE TABLE IF NOT EXISTS `topics` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `icon` VARCHAR(50) DEFAULT 'bi-journal-text',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Subtopics Table
CREATE TABLE IF NOT EXISTS `subtopics` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `topic_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`topic_id`) REFERENCES `topics`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Questions Table
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
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Attempts Table (with Coins Earned)
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
  `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. User Progress (Spaced Repetition & Topic Mastery)
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

-- 7. Leaderboard Scores Table
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

-- 8. Badges Table
CREATE TABLE IF NOT EXISTS `badges` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `title` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `icon` VARCHAR(50) NOT NULL,
  `xp_reward` INT NOT NULL DEFAULT 50
) ENGINE=InnoDB;

-- 9. User Badges Table
CREATE TABLE IF NOT EXISTS `user_badges` (
  `user_id` INT NOT NULL,
  `badge_id` INT NOT NULL,
  `earned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `badge_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`badge_id`) REFERENCES `badges`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. Coin Transactions Table (Audit log for speed coins)
CREATE TABLE IF NOT EXISTS `coin_transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `amount` INT NOT NULL,
  `transaction_type` ENUM('speed_bonus', 'streak_reward', 'mock_perfect', 'store_purchase', 'admin_grant') NOT NULL,
  `reference_description` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 11. Unlockables Store Catalog Table
CREATE TABLE IF NOT EXISTS `unlockables` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `title` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `item_type` ENUM('company_mock', 'streak_freeze', 'cosmetic_badge', 'hint_token') NOT NULL,
  `coin_cost` INT NOT NULL DEFAULT 100,
  `icon` VARCHAR(50) DEFAULT 'bi-bag-fill'
) ENGINE=InnoDB;

-- 12. User Unlocks Table
CREATE TABLE IF NOT EXISTS `user_unlocks` (
  `user_id` INT NOT NULL,
  `unlockable_id` INT NOT NULL,
  `unlocked_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `unlockable_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`unlockable_id`) REFERENCES `unlockables`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 13. Question Reports Table
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
