-- Migration: 20261010_ensure_user_addon_columns.sql
-- Description: Ensures addon columns exist on fresh databases without executing runtime DDL on page requests.

ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `bio` TEXT DEFAULT NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `avatar_icon` VARCHAR(50) DEFAULT 'bi-person-circle';
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `referral_code` VARCHAR(20) DEFAULT NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `referred_by` INT DEFAULT NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `device_hash` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `role` VARCHAR(20) DEFAULT 'user';
