/*M!999999\- enable the sandbox mode */ 

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;
DROP TABLE IF EXISTS `admin_audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(64) NOT NULL,
  `target_type` varchar(64) NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_aal_admin` (`admin_id`),
  KEY `idx_aal_action` (`action`),
  KEY `idx_aal_target` (`target_type`,`target_id`),
  KEY `idx_aal_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `question_id` int(11) NOT NULL,
  `selected_option` enum('A','B','C','D') NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `confidence` enum('sure','not_sure','guessing') NOT NULL DEFAULT 'sure',
  `time_taken_seconds` int(11) NOT NULL DEFAULT 0,
  `is_implausible_time` tinyint(1) NOT NULL DEFAULT 0,
  `xp_earned` int(11) NOT NULL DEFAULT 0,
  `coins_earned` int(11) NOT NULL DEFAULT 0,
  `mode` enum('practice','mock','daily_challenge','guest') NOT NULL DEFAULT 'practice',
  `mock_session_id` varchar(64) DEFAULT NULL,
  `pack_code` varchar(50) DEFAULT NULL,
  `skip_reason` varchar(50) DEFAULT NULL,
  `attempted_at` timestamp NULL DEFAULT current_timestamp(),
  `mistake_type` enum('concept_gap','calculation_error','misread_question','ran_out_of_time') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attempts_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3077 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(50) NOT NULL,
  `xp_reward` int(11) NOT NULL DEFAULT 50,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `campaign_nodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaign_nodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chapter_number` int(11) NOT NULL,
  `node_order` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `subtopic_id` int(11) NOT NULL,
  `required_stars` int(11) NOT NULL DEFAULT 0,
  `reward_xp` int(11) NOT NULL DEFAULT 150,
  `reward_coins` int(11) NOT NULL DEFAULT 30,
  PRIMARY KEY (`id`),
  KEY `subtopic_id` (`subtopic_id`),
  CONSTRAINT `campaign_nodes_ibfk_1` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coin_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coin_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `transaction_type` enum('speed_bonus','streak_reward','mock_perfect','store_purchase','admin_grant','fix_plan_reward') NOT NULL,
  `reference_description` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `coin_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=477 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `college_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `college_stats` (
  `college_id` int(11) NOT NULL,
  `total_students` int(11) NOT NULL DEFAULT 0,
  `active_members` int(11) NOT NULL DEFAULT 0,
  `weekly_xp` int(11) NOT NULL DEFAULT 0,
  `avg_xp_per_active` decimal(8,2) NOT NULL DEFAULT 0.00,
  `avg_mock_score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `last_calculated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`college_id`),
  KEY `idx_stats_ranking` (`active_members`,`avg_xp_per_active`,`weekly_xp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `colleges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `colleges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `status` enum('active','pending','rejected') NOT NULL DEFAULT 'active',
  `requested_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_colleges_status_name` (`status`,`name`),
  KEY `fk_colleges_requester` (`requested_by`),
  CONSTRAINT `fk_colleges_requester` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=629 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `company_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT 'IT Services',
  `difficulty_tier` enum('standard','elevated','premium') DEFAULT 'standard',
  `description` text DEFAULT NULL,
  `rounds_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `test_pattern` text DEFAULT NULL,
  `syllabus` text DEFAULT NULL,
  `logo_icon` varchar(50) DEFAULT 'building',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_checked` date DEFAULT '2026-10-18',
  `is_reported_pattern` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coupon_audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(64) NOT NULL,
  `attempted_code` varchar(64) NOT NULL,
  `is_success` tinyint(1) NOT NULL DEFAULT 0,
  `failure_reason` varchar(128) DEFAULT NULL,
  `attempted_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cal_rate_limit` (`ip_address`,`attempted_at`),
  KEY `idx_cal_user_rate_limit` (`user_id`,`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coupon_redemptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_redemptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_key` varchar(64) NOT NULL,
  `original_price` int(11) NOT NULL,
  `discount_amount` int(11) NOT NULL,
  `final_price` int(11) NOT NULL,
  `redeemed_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cr_coupon_id` (`coupon_id`),
  KEY `idx_cr_user_id` (`user_id`),
  KEY `idx_cr_user_coupon` (`user_id`,`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(64) NOT NULL,
  `discount_type` enum('percent','fixed') NOT NULL DEFAULT 'fixed',
  `discount_value` int(11) NOT NULL DEFAULT 0,
  `max_discount` int(11) DEFAULT NULL,
  `min_price` int(11) NOT NULL DEFAULT 0,
  `applicable_items` varchar(255) NOT NULL DEFAULT 'all',
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `total_limit` int(11) DEFAULT NULL,
  `per_user_limit` int(11) NOT NULL DEFAULT 1,
  `allowed_users` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `times_used` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_coupon_code` (`code`),
  KEY `idx_coupon_code` (`code`),
  KEY `idx_coupon_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `daily_quests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_quests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `quest_type` enum('solve_count','hard_questions','sure_correct','streak_maintain','mock_complete') NOT NULL,
  `target_count` int(11) NOT NULL DEFAULT 1,
  `xp_reward` int(11) NOT NULL DEFAULT 100,
  `coin_reward` int(11) NOT NULL DEFAULT 20,
  `icon` varchar(50) DEFAULT 'bi-bullseye',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `duel_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `duel_questions` (
  `duel_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `question_order` int(11) NOT NULL,
  PRIMARY KEY (`duel_id`,`question_id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `duel_questions_ibfk_1` FOREIGN KEY (`duel_id`) REFERENCES `duels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `duel_questions_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `duels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `duels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `challenger_id` int(11) NOT NULL,
  `opponent_id` int(11) DEFAULT NULL,
  `status` enum('waiting','active','completed','declined','cancelled') NOT NULL DEFAULT 'waiting',
  `winner_id` int(11) DEFAULT NULL,
  `wager_coins` int(11) NOT NULL DEFAULT 10,
  `challenger_score` int(11) NOT NULL DEFAULT 0,
  `opponent_score` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `is_bot` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `challenger_id` (`challenger_id`),
  CONSTRAINT `duels_ibfk_1` FOREIGN KEY (`challenger_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fix_plan_days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fix_plan_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) NOT NULL,
  `day_no` tinyint(4) NOT NULL,
  `title` varchar(150) NOT NULL,
  `task_type` enum('targeted_practice','mistake_review','speed_drill','retest') NOT NULL,
  `subtopic_id` int(11) DEFAULT NULL,
  `target_count` int(11) NOT NULL DEFAULT 5,
  `progress_count` int(11) NOT NULL DEFAULT 0,
  `status` enum('locked','pending','completed') NOT NULL DEFAULT 'locked',
  `xp_reward` int(11) NOT NULL DEFAULT 20,
  `coin_reward` int(11) NOT NULL DEFAULT 5,
  `unlocked_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_plan_day` (`plan_id`,`day_no`),
  KEY `idx_plan_day_status` (`plan_id`,`day_no`,`status`),
  CONSTRAINT `fk_fix_plan_days_plan` FOREIGN KEY (`plan_id`) REFERENCES `fix_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=582 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fix_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fix_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `mock_session_id` varchar(64) DEFAULT NULL,
  `plan_type` enum('standard','challenge','needs_data') NOT NULL DEFAULT 'standard',
  `status` enum('active','completed','abandoned','replaced') NOT NULL DEFAULT 'active',
  `weakest_subtopics` longtext DEFAULT NULL,
  `baseline_accuracy` decimal(5,2) DEFAULT 0.00,
  `retest_accuracy` decimal(5,2) DEFAULT NULL,
  `started_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_mock_session_id` (`mock_session_id`),
  KEY `idx_user_status` (`user_id`,`status`),
  CONSTRAINT `fk_fix_plans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `friendships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `friendships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `status` enum('pending','accepted','blocked') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_friend_unique` (`user_id`,`friend_id`),
  KEY `friend_id` (`friend_id`),
  CONSTRAINT `friendships_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `friendships_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `guild_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `guild_members` (
  `guild_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('leader','officer','member') NOT NULL DEFAULT 'member',
  `contributed_xp` int(11) NOT NULL DEFAULT 0,
  `joined_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`guild_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `guild_members_ibfk_1` FOREIGN KEY (`guild_id`) REFERENCES `guilds` (`id`) ON DELETE CASCADE,
  CONSTRAINT `guild_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `guilds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `guilds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `tag` varchar(10) NOT NULL,
  `description` text DEFAULT NULL,
  `leader_id` int(11) NOT NULL,
  `weekly_xp_target` int(11) NOT NULL DEFAULT 5000,
  `current_weekly_xp` int(11) NOT NULL DEFAULT 0,
  `icon` varchar(50) DEFAULT 'bi-shield-shaded',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `tag` (`tag`),
  KEY `leader_id` (`leader_id`),
  CONSTRAINT `guilds_ibfk_1` FOREIGN KEY (`leader_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leaderboard_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leaderboard_scores` (
  `user_id` int(11) NOT NULL,
  `period` enum('weekly','alltime') NOT NULL,
  `tier` enum('Bronze','Silver','Gold','Platinum','Diamond') NOT NULL DEFAULT 'Bronze',
  `period_xp` int(11) NOT NULL DEFAULT 0,
  `rank_position` int(11) DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`,`period`),
  CONSTRAINT `leaderboard_scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mock_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mock_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unlockable_id` int(11) NOT NULL,
  `mode` enum('quick','standard','full') NOT NULL,
  `question_count` int(11) NOT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `blueprint` longtext NOT NULL,
  `negative_marking` decimal(4,2) NOT NULL DEFAULT 0.25,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_unlockable_mode` (`unlockable_id`,`mode`),
  KEY `idx_unlockable_active` (`unlockable_id`,`active`),
  CONSTRAINT `fk_mock_configs_unlockable` FOREIGN KEY (`unlockable_id`) REFERENCES `unlockables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mock_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mock_sessions` (
  `id` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `pack_code` varchar(50) NOT NULL DEFAULT 'GENERAL_MOCK',
  `pack_title` varchar(150) NOT NULL DEFAULT 'Placement Practice Mock',
  `mode` enum('quick','standard','full') NOT NULL DEFAULT 'standard',
  `total_questions` int(11) NOT NULL DEFAULT 10,
  `question_count` int(11) NOT NULL DEFAULT 10,
  `duration_seconds` int(11) NOT NULL DEFAULT 600,
  `started_at` datetime NOT NULL,
  `deadline` datetime NOT NULL,
  `status` enum('active','completed','expired') NOT NULL DEFAULT 'active',
  `question_ids` text DEFAULT NULL,
  `user_answers` text DEFAULT NULL,
  `tab_switch_count` int(11) NOT NULL DEFAULT 0,
  `correct_count` int(11) NOT NULL DEFAULT 0,
  `wrong_count` int(11) NOT NULL DEFAULT 0,
  `skipped_count` int(11) NOT NULL DEFAULT 0,
  `score_marks` decimal(6,2) NOT NULL DEFAULT 0.00,
  `total_marks` decimal(6,2) NOT NULL DEFAULT 10.00,
  `accuracy_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_time_seconds` int(11) NOT NULL DEFAULT 0,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_deadline` (`user_id`,`deadline`),
  KEY `idx_status` (`status`),
  KEY `idx_pack_mode_status_score` (`pack_code`,`mode`,`status`,`score_marks`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `question_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `question_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reason` enum('wrong_answer','typo','vague_explanation','other') NOT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('pending','resolved','dismissed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `question_reports_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subtopic_id` int(11) NOT NULL,
  `difficulty` enum('easy','medium','hard') NOT NULL DEFAULT 'medium',
  `question_text` text NOT NULL,
  `code_snippet` text DEFAULT NULL,
  `language` varchar(30) DEFAULT NULL,
  `option_a` text NOT NULL,
  `option_b` text NOT NULL,
  `option_c` text NOT NULL,
  `option_d` text NOT NULL,
  `correct_option` enum('A','B','C','D') NOT NULL,
  `explanation` text NOT NULL,
  `est_time_seconds` int(11) NOT NULL DEFAULT 45,
  `total_attempts_count` int(11) NOT NULL DEFAULT 0,
  `total_correct_count` int(11) NOT NULL DEFAULT 0,
  `source_tag` varchar(100) DEFAULT 'General',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `is_company_pyq` tinyint(1) NOT NULL DEFAULT 0,
  `company_name` varchar(50) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `verification_status` varchar(50) NOT NULL DEFAULT 'practice_style',
  `verification_level` varchar(50) NOT NULL DEFAULT 'verified',
  `round_pattern` varchar(100) DEFAULT NULL,
  `content_hash` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subtopic_id` (`subtopic_id`),
  KEY `idx_questions_language` (`language`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1100351 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rate_limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rate_limits` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `action` varchar(64) NOT NULL,
  `identifier` varchar(150) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_action_ident_time` (`action`,`identifier`,`created_at`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `student_question_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_question_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `company_name` varchar(100) NOT NULL,
  `drive_date` date DEFAULT NULL,
  `round_pattern` varchar(100) DEFAULT 'Written Assessment',
  `topic_name` varchar(100) NOT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT 'medium',
  `question_text` text NOT NULL,
  `code_snippet` text DEFAULT NULL,
  `language` varchar(30) DEFAULT NULL,
  `option_a` text NOT NULL,
  `option_b` text NOT NULL,
  `option_c` text NOT NULL,
  `option_d` text NOT NULL,
  `correct_option` enum('A','B','C','D') NOT NULL,
  `explanation` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_submission_status` (`status`),
  KEY `idx_submission_company` (`company_name`),
  KEY `idx_submission_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `student_submissions`;
/*!50001 DROP VIEW IF EXISTS `student_submissions`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `student_submissions` AS SELECT
 1 AS `id`,
  1 AS `user_id`,
  1 AS `company_name`,
  1 AS `drive_date`,
  1 AS `round_pattern`,
  1 AS `topic_name`,
  1 AS `difficulty`,
  1 AS `question_text`,
  1 AS `code_snippet`,
  1 AS `language`,
  1 AS `option_a`,
  1 AS `option_b`,
  1 AS `option_c`,
  1 AS `option_d`,
  1 AS `correct_option`,
  1 AS `explanation`,
  1 AS `status`,
  1 AS `admin_notes`,
  1 AS `reviewed_by`,
  1 AS `reviewed_at`,
  1 AS `created_at` */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `subtopics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subtopics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `topic_id` (`topic_id`),
  CONSTRAINT `subtopics_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9994 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `time_attack_runs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `time_attack_runs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `correct_count` int(11) NOT NULL,
  `total_attempted` int(11) NOT NULL,
  `max_combo` int(11) NOT NULL DEFAULT 0,
  `duration_seconds` int(11) NOT NULL DEFAULT 60,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `time_attack_runs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `icon` varchar(50) DEFAULT 'bi-journal-text',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `unlockables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `unlockables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `item_type` enum('company_mock','streak_freeze','cosmetic_badge','hint_token') NOT NULL,
  `coin_cost` int(11) NOT NULL DEFAULT 100,
  `icon` varchar(50) DEFAULT 'bi-bag-fill',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_badges` (
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `earned_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`badge_id`),
  KEY `badge_id` (`badge_id`),
  CONSTRAINT `user_badges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_badges_ibfk_2` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_campaign_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_campaign_progress` (
  `user_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `stars_earned` int(11) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`node_id`),
  KEY `node_id` (`node_id`),
  CONSTRAINT `user_campaign_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_campaign_progress_ibfk_2` FOREIGN KEY (`node_id`) REFERENCES `campaign_nodes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_daily_quests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_daily_quests` (
  `user_id` int(11) NOT NULL,
  `quest_id` int(11) NOT NULL,
  `progress_count` int(11) NOT NULL DEFAULT 0,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `is_claimed` tinyint(1) NOT NULL DEFAULT 0,
  `assigned_date` date NOT NULL,
  PRIMARY KEY (`user_id`,`quest_id`,`assigned_date`),
  KEY `quest_id` (`quest_id`),
  CONSTRAINT `user_daily_quests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_daily_quests_ibfk_2` FOREIGN KEY (`quest_id`) REFERENCES `daily_quests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_practice_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_practice_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_type` varchar(32) NOT NULL DEFAULT 'practice',
  `subtopic_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `session_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`session_data`)),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_ups_user_session` (`user_id`,`session_type`),
  KEY `idx_ups_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_progress` (
  `user_id` int(11) NOT NULL,
  `subtopic_id` int(11) NOT NULL,
  `total_attempted` int(11) NOT NULL DEFAULT 0,
  `total_correct` int(11) NOT NULL DEFAULT 0,
  `current_mastery_level` int(11) NOT NULL DEFAULT 1,
  `next_review_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`,`subtopic_id`),
  KEY `subtopic_id` (`subtopic_id`),
  CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_progress_ibfk_2` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_topic_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_topic_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subtopic_id` int(11) NOT NULL,
  `reset_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_subtopic` (`user_id`,`subtopic_id`),
  KEY `subtopic_id` (`subtopic_id`),
  CONSTRAINT `user_topic_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_topic_resets_ibfk_2` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_unlocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_unlocks` (
  `user_id` int(11) NOT NULL,
  `unlockable_id` int(11) NOT NULL,
  `unlocked_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`unlockable_id`),
  KEY `unlockable_id` (`unlockable_id`),
  CONSTRAINT `user_unlocks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_unlocks_ibfk_2` FOREIGN KEY (`unlockable_id`) REFERENCES `unlockables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `plan` enum('free','premium') NOT NULL DEFAULT 'free',
  `college_id` int(11) DEFAULT NULL,
  `college_changed_at` datetime DEFAULT NULL,
  `hide_name_on_boards` tinyint(1) NOT NULL DEFAULT 1,
  `total_xp` int(11) NOT NULL DEFAULT 0,
  `coins` int(11) NOT NULL DEFAULT 0,
  `tier` enum('Bronze','Silver','Gold','Platinum','Diamond') NOT NULL DEFAULT 'Bronze',
  `current_streak` int(11) NOT NULL DEFAULT 0,
  `last_active_date` date DEFAULT NULL,
  `streak_freezes` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `onboarding_completed` tinyint(1) NOT NULL DEFAULT 0,
  `bio` text DEFAULT NULL,
  `avatar_icon` varchar(50) DEFAULT 'bi-person-circle',
  `referral_code` varchar(20) DEFAULT NULL,
  `referred_by` int(11) DEFAULT NULL,
  `device_hash` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_user_college` (`college_id`),
  CONSTRAINT `fk_users_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=40466 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50001 DROP VIEW IF EXISTS `student_submissions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `student_submissions` AS select `student_question_submissions`.`id` AS `id`,`student_question_submissions`.`user_id` AS `user_id`,`student_question_submissions`.`company_name` AS `company_name`,`student_question_submissions`.`drive_date` AS `drive_date`,`student_question_submissions`.`round_pattern` AS `round_pattern`,`student_question_submissions`.`topic_name` AS `topic_name`,`student_question_submissions`.`difficulty` AS `difficulty`,`student_question_submissions`.`question_text` AS `question_text`,`student_question_submissions`.`code_snippet` AS `code_snippet`,`student_question_submissions`.`language` AS `language`,`student_question_submissions`.`option_a` AS `option_a`,`student_question_submissions`.`option_b` AS `option_b`,`student_question_submissions`.`option_c` AS `option_c`,`student_question_submissions`.`option_d` AS `option_d`,`student_question_submissions`.`correct_option` AS `correct_option`,`student_question_submissions`.`explanation` AS `explanation`,`student_question_submissions`.`status` AS `status`,`student_question_submissions`.`admin_notes` AS `admin_notes`,`student_question_submissions`.`reviewed_by` AS `reviewed_by`,`student_question_submissions`.`reviewed_at` AS `reviewed_at`,`student_question_submissions`.`created_at` AS `created_at` from `student_question_submissions` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

