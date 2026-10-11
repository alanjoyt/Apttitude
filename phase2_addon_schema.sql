-- Apttitudee Addon Schema Migration: Profile, Referral & Device Locking
USE `apttitudee`;

-- Add bio, avatar_icon, referral_code, referred_by, device_hash columns safely
ALTER TABLE `users` 
ADD COLUMN `bio` VARCHAR(255) DEFAULT 'Apttitudee Scholar',
ADD COLUMN `avatar_icon` VARCHAR(50) DEFAULT 'bi-person-circle',
ADD COLUMN `referral_code` VARCHAR(20) DEFAULT NULL,
ADD COLUMN `referred_by` INT DEFAULT NULL,
ADD COLUMN `device_hash` VARCHAR(255) DEFAULT NULL;

-- Populate unique referral codes for existing users
UPDATE `users` 
SET `referral_code` = CONCAT('APTT-', UPPER(SUBSTRING(MD5(CONCAT(id, email, RAND())), 1, 6)))
WHERE `referral_code` IS NULL OR `referral_code` = '';
