-- ==============================================================================
-- Script: check_innodb_engines.sql
-- Description: Audit and enforce InnoDB storage engine across all application tables.
-- Target Database: MySQL 5.7+ / 8.0+ (InfinityFree / Production)
-- Rationale: Row-level locking (SELECT ... FOR UPDATE) and atomic transactions (ROLLBACK)
--            require InnoDB. MyISAM silently ignores locks and transactions, leading
--            to race conditions and double-spending vulnerabilities.
-- ==============================================================================

-- 1. Check storage engines for all tables in the current database
SELECT 
    TABLE_NAME, 
    ENGINE, 
    TABLE_ROWS, 
    CREATE_TIME,
    CASE 
        WHEN ENGINE = 'InnoDB' THEN 'OK: Supports Transactions & Row Locks'
        ELSE 'CRITICAL: Non-transactional engine detected! Must migrate to InnoDB.'
    END AS engine_status
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE'
ORDER BY ENGINE ASC, TABLE_NAME ASC;

-- 2. Idempotent Migration: Convert all application tables to InnoDB if not already set
-- (Running ALTER TABLE ... ENGINE=InnoDB on an already InnoDB table is safe and fast)
ALTER TABLE `users` ENGINE = InnoDB;
ALTER TABLE `topics` ENGINE = InnoDB;
ALTER TABLE `subtopics` ENGINE = InnoDB;
ALTER TABLE `questions` ENGINE = InnoDB;
ALTER TABLE `user_progress` ENGINE = InnoDB;
ALTER TABLE `attempts` ENGINE = InnoDB;
ALTER TABLE `coin_transactions` ENGINE = InnoDB;
ALTER TABLE `unlockables` ENGINE = InnoDB;
ALTER TABLE `user_unlocks` ENGINE = InnoDB;
ALTER TABLE `duels` ENGINE = InnoDB;

-- 3. Verify that 0 non-InnoDB tables remain
SELECT COUNT(*) AS non_innodb_table_count
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE' AND ENGINE != 'InnoDB';
