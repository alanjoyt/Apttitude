<?php
/**
 * Rate Limiter Service
 * File: app/Services/RateLimiter.php
 *
 * Database-backed rate limiter for shared hosting (InfinityFree / local WAMP)
 * without requiring Redis or Memcached.
 */

require_once __DIR__ . '/../../config/db.php';

class RateLimiter {

    /**
     * Check if the given identifier has exceeded the maximum attempts within the window
     *
     * @param string $action Action key (e.g., 'login', 'answer_submit')
     * @param string $identifier Client IP, user_id, or account identifier
     * @param int $maxAttempts Maximum allowed attempts
     * @param int $windowSeconds Rolling window duration in seconds
     * @return bool True if within limits, false if rate limited
     */
    public static function check(string $action, string $identifier, int $maxAttempts, int $windowSeconds): bool {
        $db = Database::getConnection();
        $cutoff = date('Y-m-d H:i:s', time() - $windowSeconds);

        try {
            $stmt = $db->prepare("
                SELECT COUNT(*) FROM rate_limits 
                WHERE action = ? AND identifier = ? AND created_at >= ?
            ");
            $stmt->execute([$action, $identifier, $cutoff]);
            $count = (int)$stmt->fetchColumn();

            return $count < $maxAttempts;
        } catch (\Throwable $e) {
            error_log("RateLimiter::check failed: " . $e->getMessage());
            return true; // Fail open gracefully if DB table issue
        }
    }

    /**
     * Record an attempt for the given identifier
     */
    public static function hit(string $action, string $identifier): void {
        $db = Database::getConnection();
        try {
            $stmt = $db->prepare("
                INSERT INTO rate_limits (action, identifier, created_at)
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$action, $identifier]);

            // Periodic garbage collection (1 in 50 requests)
            if (mt_rand(1, 50) === 1) {
                self::pruneOldRecords($db);
            }
        } catch (\Throwable $e) {
            error_log("RateLimiter::hit failed: " . $e->getMessage());
        }
    }

    /**
     * Reset / clear recorded attempts for an identifier (e.g., after successful login)
     */
    public static function reset(string $action, string $identifier): void {
        $db = Database::getConnection();
        try {
            $stmt = $db->prepare("DELETE FROM rate_limits WHERE action = ? AND identifier = ?");
            $stmt->execute([$action, $identifier]);
        } catch (\Throwable $e) {
            error_log("RateLimiter::reset failed: " . $e->getMessage());
        }
    }

    /**
     * Enforce rate limit across one or multiple identifiers.
     * Halts execution with HTTP 429 if any identifier exceeds limits.
     *
     * @param string $action Action key
     * @param array|string $identifiers One or more identifiers to inspect
     * @param int $maxAttempts Maximum allowed attempts
     * @param int $windowSeconds Window duration in seconds
     * @param string $message Error message
     */
    public static function enforce(
        string $action, 
        array|string $identifiers, 
        int $maxAttempts, 
        int $windowSeconds, 
        string $message = 'Too many requests. Please try again later.'
    ): void {
        $identList = is_array($identifiers) ? $identifiers : [$identifiers];

        foreach ($identList as $ident) {
            $ident = trim((string)$ident);
            if (empty($ident)) continue;

            if (!self::check($action, $ident, $maxAttempts, $windowSeconds)) {
                http_response_code(429);
                header('Retry-After: ' . $windowSeconds);

                // Auto-detect JSON request or API route
                $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                    || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false)
                    || (strpos($_SERVER['REQUEST_URI'] ?? '', 'api_') !== false)
                    || (strpos($_GET['action'] ?? '', 'api_') === 0);

                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'rate_limited',
                        'error' => 'rate_limited',
                        'message' => $message,
                        'retry_after' => $windowSeconds
                    ]);
                } else {
                    $error = $message;
                    if (file_exists(__DIR__ . '/../Views/login.php') && ($_GET['action'] ?? '') === 'login') {
                        require __DIR__ . '/../Views/login.php';
                    } else {
                        echo "<!DOCTYPE html><html><head><title>429 Too Many Requests</title></head><body style='font-family:sans-serif;padding:40px;text-align:center;'><h2>429 - Too Many Requests</h2><p>" . htmlspecialchars($message) . "</p></body></html>";
                    }
                }
                exit;
            }
        }
    }

    /**
     * Prune rate_limit rows older than 24 hours
     */
    private static function pruneOldRecords(PDO $db): void {
        try {
            $db->exec("DELETE FROM rate_limits WHERE created_at < NOW() - INTERVAL 1 DAY");
        } catch (\Throwable $e) {}
    }
}
