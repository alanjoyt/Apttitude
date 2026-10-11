-- Apttitudee Phase 2 Database Schema Extensions
-- Developer & Brand: mr.ignite ~ Nexyra Solutions

USE `apttitudee`;

-- 1. Daily Quests Catalog
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

-- User Daily Quests Progress
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

-- 2. Friendships & Rivals
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

-- 3. Guilds / Study Squads
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

-- 4. Competitive: 1v1 Quiz Duels
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

-- 5. Content Depth: Time Attack High Scores
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

-- 6. Content Depth: Story Campaign Nodes
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

-- Seed Default Daily Quests
INSERT INTO `daily_quests` (`id`, `title`, `description`, `quest_type`, `target_count`, `xp_reward`, `coin_reward`, `icon`) VALUES
(1, 'Daily Warmup', 'Solve 5 questions in any topic', 'solve_count', 5, 50, 10, 'bi-lightning-charge-fill'),
(2, 'Hard Hitter', 'Answer 2 Hard difficulty questions correctly', 'hard_questions', 2, 100, 25, 'bi-fire'),
(3, 'Master of Certainty', 'Get 3 questions correct with Sure confidence', 'sure_correct', 3, 120, 30, 'bi-shield-check')
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- Seed Default Campaign Nodes ("Aptitude Quest")
INSERT INTO `campaign_nodes` (`id`, `chapter_number`, `node_order`, `title`, `subtopic_id`, `required_stars`, `reward_xp`, `reward_coins`) VALUES
(1, 1, 1, 'Velocity Outpost', 1, 0, 100, 20),
(2, 1, 2, 'Ratio Citadel', 3, 2, 150, 25),
(3, 1, 3, 'Percentage Peak', 2, 4, 200, 35),
(4, 2, 1, 'Logic Labyrinth', 4, 6, 250, 40),
(5, 2, 2, 'Bloodline Manor', 5, 9, 300, 50)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);
