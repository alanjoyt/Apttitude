-- ==============================================================================
-- Migration: 20261031_company_practice_patterns_and_unlockables.sql
-- Description: Idempotent migration for 6 Company Mock Packs and practice patterns
-- Target Database: MySQL 5.7+ / 8.0+ (InfinityFree / Local WAMP)
-- Engine: InnoDB
-- ==============================================================================

-- 1. Ensure required columns exist on questions table
-- (Idempotent column check via procedural block or standard DDL)

-- 2. Insert or Update 6 Company Mock Packs in unlockables
INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'MPHASIS_MOCK_PACK', 'Mphasis Placement Challenge Pack', 'Comprehensive quantitative, logical, verbal, and pseudo code practice pack modeled on Mphasis AMCAT assessment patterns.', 'company_mock', 250, 'bi bi-cpu-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'MPHASIS_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'HEXAWARE_MOCK_PACK', 'Hexaware PGET Placement Pack', 'Aptitude, C++ STL, domain pseudo code, and problem-solving practice pack modeled on Hexaware graduate recruitment patterns.', 'company_mock', 250, 'bi bi-hdd-network-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'HEXAWARE_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'PERSISTENT_MOCK_PACK', 'Persistent Systems Tech Mock Pack', 'Advanced quantitative reasoning, core DBMS/OS fundamentals, and algorithmic challenges modeled on Persistent Systems assessment patterns.', 'company_mock', 250, 'bi bi-terminal-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'PERSISTENT_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'VIRTUSA_MOCK_PACK', 'Virtusa NeuralHack & Placement Pack', 'Core technical MCQs, C pointers, nested pseudo code loops, and quantitative aptitude practice modeled on Virtusa hiring patterns.', 'company_mock', 250, 'bi bi-code-square'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'VIRTUSA_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'ORACLE_MOCK_PACK', 'Oracle Server Technology Placement Pack', 'High-caliber practice pack covering SQL, transactions, triggers, C++ OOP, and quantitative speed aptitude modeled on Oracle test patterns.', 'company_mock', 250, 'bi bi-database-fill-gear'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'ORACLE_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'COFORGE_MOCK_PACK', 'Coforge Tech Talent Placement Pack', 'Quantitative ratios, series logic, operator precedence pseudo code, and computer science fundamentals modeled on Coforge assessment patterns.', 'company_mock', 250, 'bi bi-diagram-3-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'COFORGE_MOCK_PACK');

-- 3. Synchronize titles and descriptions for existing entries (relabeling PYQ -> Practice Pattern)
UPDATE `unlockables`
SET 
    `title` = 'Mphasis Placement Challenge Pack',
    `description` = 'Comprehensive quantitative, logical, verbal, and pseudo code practice pack modeled on Mphasis AMCAT assessment patterns.'
WHERE `code` = 'MPHASIS_MOCK_PACK';

UPDATE `unlockables`
SET 
    `title` = 'Hexaware PGET Placement Pack',
    `description` = 'Aptitude, C++ STL, domain pseudo code, and problem-solving practice pack modeled on Hexaware graduate recruitment patterns.'
WHERE `code` = 'HEXAWARE_MOCK_PACK';

UPDATE `unlockables`
SET 
    `title` = 'Persistent Systems Tech Mock Pack',
    `description` = 'Advanced quantitative reasoning, core DBMS/OS fundamentals, and algorithmic challenges modeled on Persistent Systems assessment patterns.'
WHERE `code` = 'PERSISTENT_MOCK_PACK';

UPDATE `unlockables`
SET 
    `title` = 'Virtusa NeuralHack & Placement Pack',
    `description` = 'Core technical MCQs, C pointers, nested pseudo code loops, and quantitative aptitude practice modeled on Virtusa hiring patterns.'
WHERE `code` = 'VIRTUSA_MOCK_PACK';

UPDATE `unlockables`
SET 
    `title` = 'Oracle Server Technology Placement Pack',
    `description` = 'High-caliber practice pack covering SQL, transactions, triggers, C++ OOP, and quantitative speed aptitude modeled on Oracle test patterns.'
WHERE `code` = 'ORACLE_MOCK_PACK';

UPDATE `unlockables`
SET 
    `title` = 'Coforge Tech Talent Placement Pack',
    `description` = 'Quantitative ratios, series logic, operator precedence pseudo code, and computer science fundamentals modeled on Coforge assessment patterns.'
WHERE `code` = 'COFORGE_MOCK_PACK';

-- 4. Ensure question flags and source tags conform to Practice Pattern standards
UPDATE `questions`
SET 
    `is_company_pyq` = 0,
    `verification_status` = 'practice_style',
    `source_tag` = CONCAT(`company_name`, ' Practice Pattern')
WHERE `company_name` IN ('Mphasis', 'Hexaware', 'Persistent', 'Virtusa', 'Oracle', 'Coforge');
