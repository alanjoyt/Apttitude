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

