-- ==============================================================================
-- FEATURE BATCH 1 CONSOLIDATED MIGRATIONS (Features 1 to 4)
-- Target: MariaDB 10.4+ / MySQL 8.0+ (InfinityFree phpMyAdmin)
-- Includes Migrations 04 through 09
-- ==============================================================================

-- ------------------------------------------------------------------------------
-- START: 04_configurable_mock_lengths.sql
-- ------------------------------------------------------------------------------
-- ==============================================================================
-- Migration: 04_configurable_mock_lengths.sql
-- Description: Configurable Mock Lengths (30/60/Full), Section Blueprints, Per-Pack Negative Marking & Per-Mode Proctoring
-- Target Engine: MariaDB 10.4+ / 11.x & MySQL 5.7+ / 8.0+
-- Collation: utf8mb4_unicode_ci
-- ==============================================================================

SET NAMES utf8mb4;

-- 1. Create mock_configs table
CREATE TABLE IF NOT EXISTS `mock_configs` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `unlockable_id` INT NOT NULL,
    `mode` ENUM('quick', 'standard', 'full') NOT NULL,
    `question_count` INT NOT NULL,
    `duration_minutes` INT NULL DEFAULT NULL,
    `blueprint` LONGTEXT NOT NULL,
    `negative_marking` DECIMAL(4,2) NOT NULL DEFAULT 0.25,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_unlockable_mode` (`unlockable_id`, `mode`),
    KEY `idx_unlockable_active` (`unlockable_id`, `active`),
    CONSTRAINT `fk_mock_configs_unlockable` FOREIGN KEY (`unlockable_id`) REFERENCES `unlockables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Idempotently alter mock_sessions
DROP PROCEDURE IF EXISTS `AddColIfMissing04`;
DELIMITER $$
CREATE PROCEDURE `AddColIfMissing04`(
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

CALL AddColIfMissing04('mock_sessions', 'mode', "ENUM('quick','standard','full') NOT NULL DEFAULT 'standard' AFTER `pack_title`");
CALL AddColIfMissing04('mock_sessions', 'question_count', "INT NOT NULL DEFAULT 10 AFTER `total_questions`");

DROP PROCEDURE IF EXISTS `AddIdxIfMissing04`;
DELIMITER $$
CREATE PROCEDURE `AddIdxIfMissing04`(
    IN in_table VARCHAR(64),
    IN in_idx VARCHAR(64),
    IN in_cols VARCHAR(255)
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.STATISTICS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = in_table 
          AND INDEX_NAME = in_idx
    ) THEN
        SET @ddl = CONCAT('ALTER TABLE `', in_table, '` ADD INDEX `', in_idx, '` (', in_cols, ')');
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END $$
DELIMITER ;

CALL AddIdxIfMissing04('mock_sessions', 'idx_pack_mode_status_score', '`pack_code`, `mode`, `status`, `score_marks`');

DROP PROCEDURE IF EXISTS `AddColIfMissing04`;
DROP PROCEDURE IF EXISTS `AddIdxIfMissing04`;

-- 3. Seed Default Blueprints for all Company Mock Packs
INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (1, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.00, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (1, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.00, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (1, 'full', 80, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":26,\"weight\":1,\"difficulty_mix\":{\"easy\":8,\"medium\":12,\"hard\":6}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":24,\"weight\":1,\"difficulty_mix\":{\"easy\":8,\"medium\":12,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":24,\"weight\":1,\"difficulty_mix\":{\"easy\":8,\"medium\":12,\"hard\":4}},{\"section_name\":\"Data Interpretation\",\"topic_code\":\"DI\",\"count\":6,\"weight\":1,\"difficulty_mix\":{\"easy\":2,\"medium\":3,\"hard\":1}}]}', 0.00, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (2, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (2, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (2, 'full', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (3, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (3, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (3, 'full', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (9, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (9, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (9, 'full', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (10, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (10, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (10, 'full', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (11, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (11, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (11, 'full', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (12, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (12, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (12, 'full', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (13, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (13, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (13, 'full', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (14, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (14, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (14, 'full', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (15, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (15, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (15, 'full', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (16, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (16, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (16, 'full', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (17, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (17, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (17, 'full', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (18, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (18, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (18, 'full', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (19, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (19, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (19, 'full', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (20, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":10,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":4,\"hard\":2}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (20, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (20, 'full', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":20,\"weight\":1,\"difficulty_mix\":{\"easy\":6,\"medium\":10,\"hard\":4}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (21, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":6,\"weight\":1,\"difficulty_mix\":{\"easy\":2,\"medium\":3,\"hard\":1}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (21, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":12,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":6,\"hard\":2}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (21, 'full', 100, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":30,\"weight\":1,\"difficulty_mix\":{\"easy\":9,\"medium\":15,\"hard\":6}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":30,\"weight\":1,\"difficulty_mix\":{\"easy\":9,\"medium\":15,\"hard\":6}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":15,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":2}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":25,\"weight\":1,\"difficulty_mix\":{\"easy\":8,\"medium\":12,\"hard\":5}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (22, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":6,\"weight\":1,\"difficulty_mix\":{\"easy\":2,\"medium\":3,\"hard\":1}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (22, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":12,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":6,\"hard\":2}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (22, 'full', 100, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":30,\"weight\":1,\"difficulty_mix\":{\"easy\":9,\"medium\":15,\"hard\":6}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":30,\"weight\":1,\"difficulty_mix\":{\"easy\":9,\"medium\":15,\"hard\":6}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":15,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":2}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":25,\"weight\":1,\"difficulty_mix\":{\"easy\":8,\"medium\":12,\"hard\":5}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (23, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":6,\"weight\":1,\"difficulty_mix\":{\"easy\":2,\"medium\":3,\"hard\":1}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (23, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":12,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":6,\"hard\":2}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (23, 'full', 100, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":30,\"weight\":1,\"difficulty_mix\":{\"easy\":9,\"medium\":15,\"hard\":6}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":30,\"weight\":1,\"difficulty_mix\":{\"easy\":9,\"medium\":15,\"hard\":6}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":15,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":2}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":25,\"weight\":1,\"difficulty_mix\":{\"easy\":8,\"medium\":12,\"hard\":5}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (24, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":6,\"weight\":1,\"difficulty_mix\":{\"easy\":2,\"medium\":3,\"hard\":1}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (24, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":12,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":6,\"hard\":2}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (24, 'full', 100, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":30,\"weight\":1,\"difficulty_mix\":{\"easy\":9,\"medium\":15,\"hard\":6}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":30,\"weight\":1,\"difficulty_mix\":{\"easy\":9,\"medium\":15,\"hard\":6}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":15,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":2}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":25,\"weight\":1,\"difficulty_mix\":{\"easy\":8,\"medium\":12,\"hard\":5}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (25, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":6,\"weight\":1,\"difficulty_mix\":{\"easy\":2,\"medium\":3,\"hard\":1}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (25, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":12,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":6,\"hard\":2}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (25, 'full', 100, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":30,\"weight\":1,\"difficulty_mix\":{\"easy\":9,\"medium\":15,\"hard\":6}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":30,\"weight\":1,\"difficulty_mix\":{\"easy\":9,\"medium\":15,\"hard\":6}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":15,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":2}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":25,\"weight\":1,\"difficulty_mix\":{\"easy\":8,\"medium\":12,\"hard\":5}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (26, 'quick', 30, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":6,\"weight\":1,\"difficulty_mix\":{\"easy\":2,\"medium\":3,\"hard\":1}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":8,\"weight\":1,\"difficulty_mix\":{\"easy\":3,\"medium\":4,\"hard\":1}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (26, 'standard', 60, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":12,\"weight\":1,\"difficulty_mix\":{\"easy\":4,\"medium\":6,\"hard\":2}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":16,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":3}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);

INSERT INTO `mock_configs` (`unlockable_id`, `mode`, `question_count`, `duration_minutes`, `blueprint`, `negative_marking`, `active`)
VALUES (26, 'full', 100, NULL, '{\"sections\":[{\"section_name\":\"Quantitative Aptitude\",\"topic_code\":\"QUANT\",\"count\":30,\"weight\":1,\"difficulty_mix\":{\"easy\":9,\"medium\":15,\"hard\":6}},{\"section_name\":\"Logical Reasoning\",\"topic_code\":\"LR\",\"count\":30,\"weight\":1,\"difficulty_mix\":{\"easy\":9,\"medium\":15,\"hard\":6}},{\"section_name\":\"Verbal Ability\",\"topic_code\":\"VERBAL\",\"count\":15,\"weight\":1,\"difficulty_mix\":{\"easy\":5,\"medium\":8,\"hard\":2}},{\"section_name\":\"Technical Assessment\",\"topic_code\":\"TECHNICAL\",\"count\":25,\"weight\":1,\"difficulty_mix\":{\"easy\":8,\"medium\":12,\"hard\":5}}]}', 0.25, 1)
ON DUPLICATE KEY UPDATE `question_count` = VALUES(`question_count`), `blueprint` = VALUES(`blueprint`), `negative_marking` = VALUES(`negative_marking`);



-- ------------------------------------------------------------------------------
-- START: 05_seven_day_fix_plan.sql
-- ------------------------------------------------------------------------------
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


-- ------------------------------------------------------------------------------
-- START: 06_college_leaderboards.sql
-- ------------------------------------------------------------------------------
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


-- ------------------------------------------------------------------------------
-- START: 07_college_stats_and_kerala_colleges.sql
-- ------------------------------------------------------------------------------
-- Migration 07: College Stats Aggregate Caching, Foreign Keys, and Kerala Colleges Seed
-- MariaDB-compatible, idempotent migration

-- 1. Create college_stats aggregate caching table
CREATE TABLE IF NOT EXISTS college_stats (
    college_id INT(11) NOT NULL PRIMARY KEY,
    total_students INT(11) NOT NULL DEFAULT 0,
    active_members INT(11) NOT NULL DEFAULT 0,
    weekly_xp INT(11) NOT NULL DEFAULT 0,
    avg_xp_per_active DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    avg_mock_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    last_calculated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_stats_ranking (active_members, avg_xp_per_active, weekly_xp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Foreign Key constraints for referential integrity
-- users.college_id -> colleges.id ON DELETE SET NULL
SET @exist_fk_users = (
    SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'users' 
      AND CONSTRAINT_NAME = 'fk_users_college'
);
SET @sql_fk_users = IF(@exist_fk_users = 0, 
    'ALTER TABLE users ADD CONSTRAINT fk_users_college FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE SET NULL', 
    'SELECT "fk_users_college already exists"'
);
PREPARE stmt_fk_users FROM @sql_fk_users;
EXECUTE stmt_fk_users;
DEALLOCATE PREPARE stmt_fk_users;

-- colleges.requested_by -> users.id ON DELETE SET NULL
SET @exist_fk_req = (
    SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'colleges' 
      AND CONSTRAINT_NAME = 'fk_colleges_requester'
);
SET @sql_fk_req = IF(@exist_fk_req = 0, 
    'ALTER TABLE colleges ADD CONSTRAINT fk_colleges_requester FOREIGN KEY (requested_by) REFERENCES users(id) ON DELETE SET NULL', 
    'SELECT "fk_colleges_requester already exists"'
);
PREPARE stmt_fk_req FROM @sql_fk_req;
EXECUTE stmt_fk_req;
DEALLOCATE PREPARE stmt_fk_req;

-- 3. Seed Top 20 Kerala Engineering Colleges
INSERT INTO colleges (code, name, city, state, status, created_at)
VALUES
    ('CET_TVM', 'College of Engineering, Trivandrum (CET)', 'Thiruvananthapuram', 'Kerala', 'active', NOW()),
    ('MEC_KOCHI', 'Govt Model Engineering College (MEC)', 'Ernakulam', 'Kerala', 'active', NOW()),
    ('GEC_THRISSUR', 'Government Engineering College, Thrissur', 'Thrissur', 'Kerala', 'active', NOW()),
    ('TKMCE_KOLLAM', 'TKM College of Engineering', 'Kollam', 'Kerala', 'active', NOW()),
    ('RIT_KOTTAYAM', 'Rajiv Gandhi Institute of Technology (RIT)', 'Kottayam', 'Kerala', 'active', NOW()),
    ('GECB_TVM', 'Government Engineering College, Barton Hill', 'Thiruvananthapuram', 'Kerala', 'active', NOW()),
    ('GCEK_KANNUR', 'Government College of Engineering, Kannur', 'Kannur', 'Kerala', 'active', NOW()),
    ('MACE_KOTHAMANGALAM', 'Mar Athanasius College of Engineering (MACE)', 'Kothamangalam', 'Kerala', 'active', NOW()),
    ('RSET_KOCHI', 'Rajagiri School of Engineering & Technology (RSET)', 'Kochi', 'Kerala', 'active', NOW()),
    ('FISAT_ANGAMALY', 'Federal Institute of Science and Technology (FISAT)', 'Angamaly', 'Kerala', 'active', NOW()),
    ('SSET_ERNAKULAM', 'SCMS School of Engineering and Technology', 'Ernakulam', 'Kerala', 'active', NOW()),
    ('MITS_VARIKOLI', 'Muthoot Institute of Technology and Science (MITS)', 'Ernakulam', 'Kerala', 'active', NOW()),
    ('SAINTGITS_KTM', 'Saintgits College of Engineering', 'Kottayam', 'Kerala', 'active', NOW()),
    ('AJCE_KANJIRAPPALLY', 'Amal Jyothi College of Engineering', 'Kanjirappally', 'Kerala', 'active', NOW()),
    ('NSSCE_PALAKKAD', 'NSS College of Engineering', 'Palakkad', 'Kerala', 'active', NOW()),
    ('LBSITW_TVM', 'LBS Institute of Technology for Women', 'Thiruvananthapuram', 'Kerala', 'active', NOW()),
    ('VAST_THRISSUR', 'Vidya Academy of Science and Technology', 'Thrissur', 'Kerala', 'active', NOW()),
    ('MEC_TVM', 'Marian Engineering College', 'Thiruvananthapuram', 'Kerala', 'active', NOW()),
    ('SCTCE_TVM', 'Sree Chitra Thirunal College of Engineering (SCTCE)', 'Thiruvananthapuram', 'Kerala', 'active', NOW()),
    ('CUSAT_SOE', 'Cochin University School of Engineering (CUSAT SOE)', 'Kochi', 'Kerala', 'active', NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name), city = VALUES(city), state = VALUES(state);


-- ------------------------------------------------------------------------------
-- START: 08_missing_app_tables_and_company_profiles.sql
-- ------------------------------------------------------------------------------
-- ============================================================================
-- Migration 08: Missing Application Tables & Company Profiles
-- Target: MariaDB 10.4+ / MySQL 8.0+ (InfinityFree & Local WAMP)
-- Idempotent: Table creation with IF NOT EXISTS, data with INSERT IGNORE INTO
-- ============================================================================

-- 1. Company Profiles (referenced in CompanyController, AdminController)
CREATE TABLE IF NOT EXISTS `company_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'IT Services',
  `difficulty_tier` enum('standard','elevated','premium') COLLATE utf8mb4_unicode_ci DEFAULT 'standard',
  `description` text COLLATE utf8mb4_unicode_ci,
  `rounds_json` json DEFAULT NULL,
  `test_pattern` text COLLATE utf8mb4_unicode_ci,
  `syllabus` text COLLATE utf8mb4_unicode_ci,
  `logo_icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'building',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_checked` date DEFAULT '2026-10-18',
  `is_reported_pattern` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_comp_name` (`name`),
  UNIQUE KEY `uniq_comp_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `company_profiles` (`id`, `name`, `slug`, `category`, `difficulty_tier`, `description`, `rounds_json`, `test_pattern`, `syllabus`, `logo_icon`, `is_active`, `created_at`, `updated_at`, `last_checked`, `is_reported_pattern`) VALUES
('1', 'Innovature', 'innovature', 'Software & Engineering Solutions', 'standard', 'Innovature campus recruitment features technical problem solving, C/Python logic, OOP concepts, and logical aptitude.', '[\"Online Written Assessment (Aptitude + Technical MCQ)\", \"Technical Interview Round 1 (Data Structures & Core Coding)\", \"Managerial & HR Interview\"]', 'Quantitative: 25%, Logical: 25%, Core CS/Coding MCQ: 50%. Duration: 60-75 mins. (Reported pattern, please verify)', 'C Pointers, Strings, Arrays, Python fundamentals, DBMS normalization, SQL joins, Number series, Time & Work. (Reported pattern, please verify)', 'code', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('2', 'TCS NQT', 'tcs-nqt', 'IT Services & Consulting', 'standard', 'Tata Consultancy Services National Qualifier Test (NQT) gateway for Ninja and Digital fresher hiring profiles.', '[\"TCS NQT Cognitive Test (Foundation Section)\", \"Advanced Quantitative & Technical Section\", \"Hands-on Coding Assessment\", \"Technical + HR Interview\"]', 'Numerical Ability: 20 Qs, Reasoning Ability: 20 Qs, Verbal Ability: 25 Qs, Advanced Cognitive & Coding. Negative marking applies in select sections. (Reported pattern, please verify)', 'Percentages, Profit & Loss, Arrangements, Syllogisms, Reading Comprehension, Pseudocode analysis, Algorithms. (Reported pattern, please verify)', 'server', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('3', 'Infosys', 'infosys', 'IT Services & Consulting', 'standard', 'Infosys Specialist Programmer (SP) and Digital Specialist Engineer (DSE) & Systems Engineer recruitment drives.', '[\"Online Assessment (Sectional cutoff mandatory)\", \"Technical Interview\", \"HR Interview\"]', 'Mathematical Thinking (Quant): 15 Qs / 35m, Logical Ability: 15 Qs / 25m, Verbal Ability: 20 Qs / 20m, Pseudocode: 5 Qs / 10m, Puzzle Solving: 4 Qs / 10m. (Reported pattern, please verify)', 'Cryptarithmetic, Permutations & Combinations, Data Sufficiency, Deductive Reasoning, Sentence Correction, Pseudocode tracing. (Reported pattern, please verify)', 'cpu', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('4', 'Wipro NLTH', 'wipro-nlth', 'IT Services & Consulting', 'standard', 'Wipro Elite National Talent Hunt (NLTH) assessment testing aptitude, written English comprehension, and coding proficiency.', '[\"National Qualifier Online Test\", \"Essay Writing (WET)\", \"Technical Interview\", \"HR Discussion\"]', 'Quants: 16 Qs, Logical: 14 Qs, Verbal: 22 Qs, Coding: 2 problems. Total time: 128 mins. (Reported pattern, please verify)', 'Speed & Distance, Blood Relations, Coding-Decoding, Error Spotting, Array manipulation, String parsing. (Reported pattern, please verify)', 'globe', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('5', 'Accenture', 'accenture', 'Technology & Consulting', 'standard', 'Accenture Associate Software Engineer (ASE) and Advanced ASE recruitment process.', '[\"Cognitive & Technical Assessment (eliminatory)\", \"Coding Assessment (mandatory upon passing Stage 1)\", \"Communication Assessment\", \"Technical & HR Interview\"]', 'Cognitive: 50 Qs (Critical Thinking, English, Abstract Reasoning), Technical: 40 Qs (Pseudocode, Cloud, Networking, Security). Total: 90 Qs / 90 mins. (Reported pattern, please verify)', 'Bitwise operations, Pseudocode loops, MS Office & Cloud basics, Network protocols, Syllogisms. (Reported pattern, please verify)', 'briefcase', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('6', 'Cognizant', 'cognizant', 'IT Services & Consulting', 'standard', 'Cognizant GenC, GenC Elevate, and GenC Next assessment tracks powered by modern skill-first evaluations.', '[\"Communication / Aptitude Assessment\", \"Technical & Domain MCQ\", \"Coding Assessment\", \"Combined Technical & HR Interview\"]', 'Numerical: 25 Qs, Logical: 25 Qs, Verbal: 20 Qs, Code debugging: 7 Qs. Duration: 100-120 mins. (Reported pattern, please verify)', 'Number properties, Profit & Loss, Seating arrangement, Sentence completion, C/Java debugging. (Reported pattern, please verify)', 'terminal', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('7', 'Capgemini', 'capgemini', 'Consulting & Technology', 'standard', 'Capgemini Exceller campus drive evaluating game-based aptitude, behavioral profiling, and technical competency.', '[\"Pseudocode & Technical MCQ\", \"English Communication Test\", \"Game-Based Aptitude Test\", \"Behavioral Profiling\", \"Technical & HR Interview\"]', 'Pseudocode: 30 Qs / 30m, English: 30 Qs / 30m, 4 Interactive Mini-games, Coding: 2 Qs / 45m. (Reported pattern, please verify)', 'Data structures, Flowchart logic, Grammar & Vocabulary, Grid Challenge, Motion Challenge, Deductive logic. (Reported pattern, please verify)', 'layers', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('8', 'Deloitte', 'deloitte', 'Consulting & Advisory', 'elevated', 'Deloitte USI Analyst hiring for engineering and consulting graduates.', '[\"Online Assessment (Quantitative, Logical, Verbal, Computer Fundamentals)\", \"Deloitte Group Discussion / Jam Session (Select campuses)\", \"Technical Interview\", \"HR Interview\"]', 'Quants: 16 Qs, Logical: 22 Qs, Verbal: 25 Qs, Computer Fundamentals: 30 Qs. Duration: 95 mins. (Reported pattern, please verify)', 'Logarithms, Probability, Direction sense, Statement & Assumptions, OOP concepts, Database management. (Reported pattern, please verify)', 'shield', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('9', 'Tech Mahindra', 'tech-mahindra', 'IT Services & Telecom Solutions', 'standard', 'Tech Mahindra Associate Software Engineer hiring track focusing on core logic, communication, and technical acumen.', '[\"Online Aptitude & English Test\", \"Technical & Psychometric Test\", \"Conversational AI / Speaking Test\", \"Technical & HR Interview\"]', 'Logical: 25 Qs, Quantitative: 25 Qs, English: 25 Qs, Technical: 25 Qs. Duration: 100 mins. (Reported pattern, please verify)', 'Time & Work, Simple & Compound Interest, Coding-Decoding, Operating Systems, Computer Architecture. (Reported pattern, please verify)', 'hard-drive', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('10', 'Zoho', 'zoho', 'Product & SaaS Software', 'elevated', 'Zoho Corporation Software Developer hiring, recognized for heavy emphasis on C programming, memory manipulation, and algorithms.', '[\"Round 1: General Aptitude & C/C++ Tracing MCQ\", \"Round 2: Basic Programming (5 hands-on questions)\", \"Round 3: Advanced Programming & Complex Logic (3 problems)\", \"Round 4: Technical HR Interview\", \"Round 5: General HR Interview\"]', 'Round 1: 30-40 Qs (Math Aptitude + Deep C Code Tracing, Pointers, Arrays, Bitwise operators). Strict time limit. (Reported pattern, please verify)', 'Pointers, Dynamic Memory (malloc/free), String manipulation without library functions, Recursion, Matrix transformations. (Reported pattern, please verify)', 'box', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('11', 'LTI Mindtree', 'lti-mindtree', 'Technology Consulting & Digital Solutions', 'standard', 'LTIMindtree campus assessment hiring across Specialist and Practitioner engineering bands.', '[\"Online Aptitude & Technical Assessment\", \"Spoken English Assessment\", \"Coding Test (Band-specific)\", \"Technical Interview\", \"HR Discussion\"]', 'Quantitative: 20 Qs, Logical: 20 Qs, Technical MCQ: 20 Qs, Coding: 1-2 Problems. Total time: 110 mins. (Reported pattern, please verify)', 'Ratios, Mixtures & Alligations, Series, Syllogisms, DBMS keys, OOP principles, DSA basics. (Reported pattern, please verify)', 'activity', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('12', 'HCL', 'hcl', 'IT Services & Enterprise Technology', 'standard', 'HCLTech First Careers and Graduate Engineer Trainee (GET) campus hiring drive.', '[\"Online Aptitude & Technical Assessment\", \"Technical Interview\", \"HR Interview\"]', 'Quantitative Ability: 15 Qs, Logical Reasoning: 15 Qs, Verbal: 15 Qs, Technical (CS/IT basics): 15 Qs. Duration: 60 mins. (Reported pattern, please verify)', 'Averages, Profit & Loss, Seating arrangement, Networking OSI layers, SQL basic queries, C pointers. (Reported pattern, please verify)', 'monitor', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('13', 'Amazon', 'amazon', 'Product, Cloud & Big Tech', 'premium', 'Amazon Software Development Engineer (SDE-1) campus and off-campus recruitment drive.', '[\"Online Assessment 1 (Code Debugging / Reasoning)\", \"Online Assessment 2 (Coding: 2 algorithmic problems + Work Style Survey)\", \"Online Assessment 3 (System Logic / Simulation)\", \"Virtual Onsite Technical Interviews (3-4 Rounds: DSA + Leadership Principles)\"]', 'Coding: 2 questions / 70 mins (Medium/Hard LeetCode style), Work Styles: 50 items / 20 mins. (Reported pattern, please verify)', 'Trees, Graphs, Dynamic Programming, Hash Maps, Heaps, Amazon 16 Leadership Principles, System scalability. (Reported pattern, please verify)', 'cloud', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('14', 'UST', 'ust', 'Digital Transformation & IT Services', 'standard', 'UST Global campus recruitment for Associate Software Developers and Quality Engineers.', '[\"Aptitude & Technical Online Assessment\", \"Coding Challenge\", \"Technical Evaluation Round\", \"Culture Fit & HR Interview\"]', 'Aptitude (Quants + Reasoning + Verbal): 45 Qs, Core CS (Java/Python/OOP/SQL): 30 Qs. Duration: 75 mins. (Reported pattern, please verify)', 'Time & Distance, Percentages, Blood relations, Java Collections, SQL Normalization, Exception Handling. (Reported pattern, please verify)', 'feather', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1'),
('15', 'IBM', 'ibm', 'Enterprise Cloud & Cognitive Solutions', 'standard', 'IBM Entry-Level Developer and Associate System Engineer hiring process.', '[\"Cognitive Ability Assessment (Cognify game-based or MCQ)\", \"English Language Assessment\", \"Coding Assessment (HackerRank)\", \"Technical Interview\", \"HR Interview\"]', 'Cognitive games / Quantitative: 20 Qs, Reasoning: 20 Qs, Technical: 20 Qs, Coding: 1-2 questions. Duration: 90 mins. (Reported pattern, please verify)', 'Grid navigation, Numerical reasoning, Cloud fundamentals, Operating Systems (Linux/Unix), Data structures. (Reported pattern, please verify)', 'database', '1', '2026-10-18 21:28:45', '2026-10-19 00:19:25', '2026-10-18', '1');

-- 2. Coupons (referenced in CouponService, AdminController)
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `discount_value` int(11) NOT NULL DEFAULT 0,
  `max_discount` int(11) DEFAULT NULL,
  `min_price` int(11) NOT NULL DEFAULT 0,
  `applicable_items` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `total_limit` int(11) DEFAULT NULL,
  `per_user_limit` int(11) NOT NULL DEFAULT 1,
  `allowed_users` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `times_used` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_coupon_code` (`code`),
  KEY `idx_coupon_code` (`code`),
  KEY `idx_coupon_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Coupon Redemptions (referenced in CouponService)
CREATE TABLE IF NOT EXISTS `coupon_redemptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_price` int(11) NOT NULL,
  `discount_amount` int(11) NOT NULL,
  `final_price` int(11) NOT NULL,
  `redeemed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cr_coupon_id` (`coupon_id`),
  KEY `idx_cr_user_id` (`user_id`),
  KEY `idx_cr_user_coupon` (`user_id`,`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Coupon Audit Log (referenced in CouponService)
CREATE TABLE IF NOT EXISTS `coupon_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempted_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_success` tinyint(1) NOT NULL DEFAULT 0,
  `failure_reason` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attempted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cal_rate_limit` (`ip_address`,`attempted_at`),
  KEY `idx_cal_user_rate_limit` (`user_id`,`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. User Practice Sessions (referenced in PracticeController, AdminController)
CREATE TABLE IF NOT EXISTS `user_practice_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'practice',
  `subtopic_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `session_data` json DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_ups_user_session` (`user_id`,`session_type`),
  KEY `idx_ups_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Admin Audit Log (referenced in AdminController)
CREATE TABLE IF NOT EXISTS `admin_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_aal_admin` (`admin_id`),
  KEY `idx_aal_action` (`action`),
  KEY `idx_aal_target` (`target_type`,`target_id`),
  KEY `idx_aal_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- START: 09_anti_inspect_devtools_tracking.sql
-- ------------------------------------------------------------------------------
-- ============================================================================
-- Migration 09: Anti-Inspect DevTools Tracking
-- Target: MariaDB 10.4+ / MySQL 8.0+
-- Idempotent: Tracks count of developer tool opening detections per mock session
-- ============================================================================

ALTER TABLE `mock_sessions` 
    ADD COLUMN IF NOT EXISTS `devtools_count` int(11) NOT NULL DEFAULT 0 AFTER `tab_switch_count`;


