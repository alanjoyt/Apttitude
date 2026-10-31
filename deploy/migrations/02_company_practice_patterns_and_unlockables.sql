-- ==============================================================================
-- Migration: 02_company_practice_patterns_and_unlockables.sql
-- Description: Company Placement Practice Packs and Practice Pattern Flag Configuration
-- Target Engine: MariaDB 10.4+ / 11.x & MySQL 5.7+ / 8.0+
-- Collation: utf8mb4_unicode_ci
-- ==============================================================================

SET NAMES utf8mb4;

-- 1. Insert All Company Mock Packs (Idempotent Insertion for Full 21 Packs)
INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'TCS_MOCK_PACK', 'TCS NQT Pattern Mock Pack', 'Curated mock test matching TCS NQT placement pattern.', 'company_mock', 250, 'bi-building-fill-check'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'TCS_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'INFOSYS_MOCK_PACK', 'Infosys Placement Test Pack', 'Simulated test with Infosys reasoning & verbal difficulty.', 'company_mock', 250, 'bi-cpu-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'INFOSYS_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'WIPRO_MOCK_PACK', 'Wipro NLTH Challenge Pack', 'Targeted question set simulating Wipro National Level Talent Hunt.', 'company_mock', 250, 'bi-diagram-3-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'WIPRO_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'INNOVATURE_MOCK_PACK', 'Innovature Labs Placement Mock Pack', 'Simulated placement paper with Innovature Labs technical & reasoning questions.', 'company_mock', 250, 'bi-building-fill-gear'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'INNOVATURE_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'ACCENTURE_MOCK_PACK', 'Accenture Placement Pack', 'Simulated cognitive and technical placement practice test modeled on Accenture hiring.', 'company_mock', 250, 'bi-lightning-charge-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'ACCENTURE_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'AMAZON_MOCK_PACK', 'Amazon SDE & AWS Placement Pack', 'Curated placement mock test modeled on Amazon SDE online assessment and DSA aptitude.', 'company_mock', 250, 'bi-cloud-check-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'AMAZON_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'ZOHO_MOCK_PACK', 'Zoho Advanced Coding & Aptitude Pack', 'Simulated test modeled on Zoho problem-solving, advanced logic puzzles and Java/C output.', 'company_mock', 250, 'bi-puzzle-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'ZOHO_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'DELOITTE_MOCK_PACK', 'Deloitte National Aptitude Assessment Pack', 'Targeted set mirroring Deloitte NLA cognitive aptitude, verbal reasoning, and technical MCQs.', 'company_mock', 250, 'bi-briefcase-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'DELOITTE_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'CAPGEMINI_MOCK_PACK', 'Capgemini Exceller Placement Pack', 'Placement mock for Capgemini pseudo-code, game-based aptitude, and English comprehension.', 'company_mock', 250, 'bi-gear-wide-connected'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'CAPGEMINI_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'COGNIZANT_MOCK_PACK', 'Cognizant GenC & GenC Next Challenge Pack', 'Realistic test covering Cognizant GenC quantitative aptitude, reasoning, and automata code evaluation.', 'company_mock', 250, 'bi-terminal-split'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'COGNIZANT_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'TECH_MAHINDRA_MOCK_PACK', 'Tech Mahindra Recruitment Mock Pack', 'Comprehensive test following Tech Mahindra national assessment rounds (Aptitude + Technical MCQ).', 'company_mock', 250, 'bi-code-square'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'TECH_MAHINDRA_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'IBM_MOCK_PACK', 'IBM Cognitive & Entry-Level Developer Pack', 'Curated set simulating IBM cognitive games, numerical reasoning, cloud concepts, and data structures.', 'company_mock', 250, 'bi-database-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'IBM_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'LTI_MINDTREE_MOCK_PACK', 'LTI Mindtree Placement Mock Pack', 'Placement test set for LTI Mindtree fresher assessment (Technical MCQs, DSA, and Logical Deduction).', 'company_mock', 250, 'bi-diagram-3-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'LTI_MINDTREE_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'UST_MOCK_PACK', 'UST Global Software Developer Pack', 'Simulated placement assessment for UST Global Associate Developer and Quality Engineer profiles.', 'company_mock', 250, 'bi-laptop-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'UST_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'HCL_MOCK_PACK', 'HCL Tech First Careers Mock Pack', 'Targeted mock test simulating HCL Tech assessment in Quantitative Aptitude, Reasoning, and Core CS.', 'company_mock', 250, 'bi-hdd-network-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'HCL_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'MPHASIS_MOCK_PACK', 'Mphasis Placement Challenge Pack', 'Comprehensive quantitative, logical, verbal, and pseudo code practice pack modeled on Mphasis AMCAT assessment patterns.', 'company_mock', 250, 'bi-cpu-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'MPHASIS_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'HEXAWARE_MOCK_PACK', 'Hexaware PGET Placement Pack', 'Aptitude, C++ STL, domain pseudo code, and problem-solving practice pack modeled on Hexaware graduate recruitment patterns.', 'company_mock', 250, 'bi-hdd-network-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'HEXAWARE_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'PERSISTENT_MOCK_PACK', 'Persistent Systems Tech Mock Pack', 'Advanced quantitative reasoning, core DBMS/OS fundamentals, and algorithmic challenges modeled on Persistent Systems assessment patterns.', 'company_mock', 250, 'bi-terminal-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'PERSISTENT_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'VIRTUSA_MOCK_PACK', 'Virtusa NeuralHack & Placement Pack', 'Core technical MCQs, C pointers, nested pseudo code loops, and quantitative aptitude practice modeled on Virtusa hiring patterns.', 'company_mock', 250, 'bi-code-square'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'VIRTUSA_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'ORACLE_MOCK_PACK', 'Oracle Server Technology Placement Pack', 'High-caliber practice pack covering SQL, transactions, triggers, C++ OOP, and quantitative speed aptitude modeled on Oracle test patterns.', 'company_mock', 250, 'bi-database-fill-gear'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'ORACLE_MOCK_PACK');

INSERT INTO `unlockables` (`code`, `title`, `description`, `item_type`, `coin_cost`, `icon`)
SELECT 'COFORGE_MOCK_PACK', 'Coforge Tech Talent Placement Pack', 'Quantitative ratios, series logic, operator precedence pseudo code, and computer science fundamentals modeled on Coforge assessment patterns.', 'company_mock', 250, 'bi-diagram-3-fill'
WHERE NOT EXISTS (SELECT 1 FROM `unlockables` WHERE `code` = 'COFORGE_MOCK_PACK');

-- 2. Synchronize titles and descriptions for existing entries (Standardize on Practice Patterns)
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

-- 3. Ensure question flags and source tags conform to Practice Pattern standards
UPDATE `questions`
SET 
    `is_company_pyq` = 0,
    `verification_status` = 'practice_style',
    `source_tag` = CONCAT(`company_name`, ' Practice Pattern')
WHERE `company_name` IN ('Mphasis', 'Hexaware', 'Persistent', 'Virtusa', 'Oracle', 'Coforge');
