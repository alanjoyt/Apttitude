-- ==============================================================================
-- Migration: 06_college_leaderboards.sql
-- Description: College Directory, Leaderboards, 30-day Change Limit, and Privacy Opt-Out
-- Target Engine: MariaDB 10.4+ / 11.x & MySQL 5.7+ / 8.0+
-- Collation: utf8mb4_unicode_ci
-- ==============================================================================

SET NAMES utf8mb4;

-- 1. Colleges Table
CREATE TABLE IF NOT EXISTS `colleges` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `state` VARCHAR(100) NOT NULL,
    `status` ENUM('active', 'pending', 'rejected') NOT NULL DEFAULT 'active',
    `requested_by` INT NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_colleges_status_name` (`status`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Alter Users Table to add college associations, 30-day throttle, and privacy flag
DROP PROCEDURE IF EXISTS `AddCollegeColumnsToUsers`;
DELIMITER //
CREATE PROCEDURE `AddCollegeColumnsToUsers`()
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'college_id'
    ) THEN
        ALTER TABLE `users` ADD COLUMN `college_id` INT NULL DEFAULT NULL AFTER `plan`;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'college_changed_at'
    ) THEN
        ALTER TABLE `users` ADD COLUMN `college_changed_at` DATETIME NULL DEFAULT NULL AFTER `college_id`;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'hide_name_on_boards'
    ) THEN
        -- hide_name_on_boards: Privacy by default (Option B). Defaults to 1 (hidden / "Anonymous Student")
        -- unless the student explicitly selects to show their name on college boards.
        ALTER TABLE `users` ADD COLUMN `hide_name_on_boards` TINYINT(1) NOT NULL DEFAULT 1 AFTER `college_changed_at`;
    END IF;

    -- Add index on college_id if not present
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.STATISTICS 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND INDEX_NAME = 'idx_user_college'
    ) THEN
        ALTER TABLE `users` ADD KEY `idx_user_college` (`college_id`);
    END IF;
END //
DELIMITER ;
CALL `AddCollegeColumnsToUsers`();
DROP PROCEDURE IF EXISTS `AddCollegeColumnsToUsers`;

-- 3. Seed Initial Directory of Curated Active Engineering & Degree Colleges
INSERT IGNORE INTO `colleges` (`code`, `name`, `city`, `state`, `status`) VALUES
('IIT_BOMBAY', 'Indian Institute of Technology Bombay', 'Mumbai', 'Maharashtra', 'active'),
('IIT_DELHI', 'Indian Institute of Technology Delhi', 'New Delhi', 'Delhi', 'active'),
('IIT_MADRAS', 'Indian Institute of Technology Madras', 'Chennai', 'Tamil Nadu', 'active'),
('IIT_KHARAGPUR', 'Indian Institute of Technology Kharagpur', 'Kharagpur', 'West Bengal', 'active'),
('BITS_PILANI', 'Birla Institute of Technology and Science Pilani', 'Pilani', 'Rajasthan', 'active'),
('NIT_TRICHY', 'National Institute of Technology Tiruchirappalli', 'Tiruchirappalli', 'Tamil Nadu', 'active'),
('NIT_SURATHKAL', 'National Institute of Technology Karnataka Surathkal', 'Mangaluru', 'Karnataka', 'active'),
('VIT_VELLORE', 'Vellore Institute of Technology', 'Vellore', 'Tamil Nadu', 'active'),
('SRM_KTR', 'SRM Institute of Science and Technology', 'Chennai', 'Tamil Nadu', 'active'),
('MANIPAL_TECH', 'Manipal Institute of Technology', 'Manipal', 'Karnataka', 'active'),
('PSG_TECH', 'PSG College of Technology', 'Coimbatore', 'Tamil Nadu', 'active'),
('COEP_PUNE', 'College of Engineering Pune', 'Pune', 'Maharashtra', 'active'),
('RVCE_BLR', 'R.V. College of Engineering', 'Bengaluru', 'Karnataka', 'active'),
('PES_BLR', 'PES University', 'Bengaluru', 'Karnataka', 'active'),
('BMS_BLR', 'BMS College of Engineering', 'Bengaluru', 'Karnataka', 'active'),
('MSRIT_BLR', 'Ramaiah Institute of Technology', 'Bengaluru', 'Karnataka', 'active'),
('SSN_CHE', 'SSN College of Engineering', 'Chennai', 'Tamil Nadu', 'active'),
('THAPAR_PATIALA', 'Thapar Institute of Engineering and Technology', 'Patiala', 'Punjab', 'active'),
('AMITY_NOIDA', 'Amity University Noida', 'Noida', 'Uttar Pradesh', 'active'),
('VJTI_MUMBAI', 'Veermata Jijabai Technological Institute', 'Mumbai', 'Maharashtra', 'active');
