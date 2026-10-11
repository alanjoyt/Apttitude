<?php
require_once __DIR__ . '/../../config/db.php';

class DeviceLockService {

    private static ?bool $devModeCache = null;

    /**
     * Check if Developer Mode is enabled in system settings
     */
    public static function isDeveloperMode(): bool {
        if (self::$devModeCache !== null) {
            return self::$devModeCache;
        }
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'developer_mode'");
            $stmt->execute();
            $val = $stmt->fetchColumn();
            self::$devModeCache = ($val === '1');
            return self::$devModeCache;
        } catch (\Throwable $e) {
            self::$devModeCache = false;
            return false; // Default to OFF (strict security) if table doesn't exist or DB error
        }
    }

    /**
     * Update Developer Mode setting
     */
    public static function setDeveloperMode(bool $enabled): bool {
        try {
            $db = Database::getConnection();
            $db->exec("
                CREATE TABLE IF NOT EXISTS `system_settings` (
                  `setting_key` VARCHAR(50) PRIMARY KEY,
                  `setting_value` VARCHAR(255) NOT NULL,
                  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB;
            ");
            $val = $enabled ? '1' : '0';
            $stmt = $db->prepare("
                INSERT INTO system_settings (setting_key, setting_value) 
                VALUES ('developer_mode', ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            return $stmt->execute([$val, $val]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Generate unique device signature hash
     */
    public static function generateDeviceHash(): string {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UnknownDevice';
        return hash('sha256', $userAgent . '_APTT_DEVICE_LOCK_SALT_' . session_id());
    }

    /**
     * Register active device lock on login
     */
    public static function registerDeviceLock(int $userId): string {
        $hash = self::generateDeviceHash();
        $_SESSION['device_hash'] = $hash;

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE users SET device_hash = ? WHERE id = ?");
            $stmt->execute([$hash, $userId]);
        } catch (\Throwable $e) {
            // Gracefully handle if DB is unreachable
        }

        return $hash;
    }

    /**
     * Validate active device session lock on page requests
     */
    public static function validateDeviceLock(?int $userId): bool {
        // If Developer Mode is ON, skip device lock check to allow testing with multiple accounts on 1 device
        if (self::isDeveloperMode()) {
            return true;
        }

        if (!$userId || !isset($_SESSION['device_hash'])) {
            return true;
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT device_hash FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $dbHash = $stmt->fetchColumn();

            // If no device hash is set yet, register current
            if (empty($dbHash)) {
                self::registerDeviceLock($userId);
                return true;
            }

            // Compare session device_hash against database active device lock
            if ($_SESSION['device_hash'] !== $dbHash) {
                // Device Lock Mismatch! Terminate session immediately
                session_destroy();
                session_start();
                $_SESSION['device_lock_error'] = "Security Lock Triggered: Account accessed from another device. Device Lock active.";
                header('Location: index.php?action=login&device_lock=1');
                exit;
            }
        } catch (\Throwable $e) {
            // Gracefully ignore DB error during session validation
            return true;
        }

        return true;
    }
}

