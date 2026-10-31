-- Migration: Create centralized rate_limits table for shared hosting throttle protection
CREATE TABLE IF NOT EXISTS `rate_limits` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `action` VARCHAR(64) NOT NULL,
    `identifier` VARCHAR(150) NOT NULL,
    `created_at` DATETIME NOT NULL,
    INDEX `idx_action_ident_time` (`action`, `identifier`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
