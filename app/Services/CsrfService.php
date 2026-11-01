<?php
/**
 * CSRF Protection Service
 * File: app/Services/CsrfService.php
 */

class CsrfService {

    /**
     * Get or initialize session CSRF token
     */
    public static function getToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token from header, POST body, or JSON payload
     * Halts execution with HTTP 403 on mismatch or absence
     */
    public static function verify(?array $input = null): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $expected = $_SESSION['csrf_token'] ?? null;
        if (!$expected) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'CSRF token missing from session'
            ]);
            exit;
        }

        // 1. Check HTTP header (handles dual/comma-separated headers if forwarded through proxies)
        $submitted = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if ($submitted) {
            $tokens = array_map('trim', explode(',', $submitted));
            foreach ($tokens as $t) {
                if ($t && hash_equals($expected, $t)) {
                    return true;
                }
            }
        }

        // 2. Check input array if provided
        if ($input && !empty($input['csrf_token']) && hash_equals($expected, $input['csrf_token'])) {
            return true;
        }

        // 3. Check $_POST
        if (!empty($_POST['csrf_token']) && hash_equals($expected, $_POST['csrf_token'])) {
            return true;
        }

        // 4. Check raw JSON php://input
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && !empty($decoded['csrf_token']) && hash_equals($expected, $decoded['csrf_token'])) {
                return true;
            }
        }

        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid or missing CSRF token'
        ]);
        exit;

        return true;
    }
}
