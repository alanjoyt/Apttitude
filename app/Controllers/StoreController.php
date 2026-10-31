<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Services/WalletService.php';
require_once __DIR__ . '/../Services/CouponService.php';
require_once __DIR__ . '/../Services/RateLimiter.php';

class StoreController {

    private static function verifyCsrf(array $input): void {
        $expected = $_SESSION['csrf_token'] ?? null;
        if (!$expected) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'CSRF token missing from session']);
            exit;
        }

        // 1. Check HTTP Header (handles dual/comma-separated headers if forwarded through proxies)
        $headerToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if ($headerToken) {
            $tokens = array_map('trim', explode(',', $headerToken));
            foreach ($tokens as $t) {
                if ($t && hash_equals($expected, $t)) {
                    return;
                }
            }
        }

        // 2. Check JSON payload / POST field fallback
        $bodyToken = $input['csrf_token'] ?? ($_POST['csrf_token'] ?? null);
        if ($bodyToken && hash_equals($expected, $bodyToken)) {
            return;
        }

        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing CSRF security token']);
        exit;
    }

    /**
     * Server-side Validation & Discount Price Preview Endpoint
     */
    public static function previewCouponJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Please log in to apply coupons.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        self::verifyCsrf($input);

        $code = trim($input['coupon_code'] ?? '');
        $unlockableId = (int)($input['unlockable_id'] ?? 0);

        if (!$unlockableId) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid store item selected.']);
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM unlockables WHERE id = ?");
        $stmt->execute([$unlockableId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Store item not found.']);
            exit;
        }

        $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $res = CouponService::validateAndPreview($code, $item['item_type'], (int)$item['coin_cost'], (int)$userId, $clientIp);

        if (isset($res['http_code'])) {
            http_response_code($res['http_code']);
        }

        echo json_encode($res);
        exit;
    }

    /**
     * AJAX endpoint to unlock an item using Speed Coins (supports optional coupon)
     */
    public static function unlockItemJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Please log in to unlock store items!']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        self::verifyCsrf($input);

        $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        RateLimiter::enforce(
            'store_purchase', 
            [$clientIp, 'user:' . $userId], 
            10, 
            60, 
            'Too many purchase requests. Please wait a moment.'
        );
        RateLimiter::hit('store_purchase', $clientIp);
        RateLimiter::hit('store_purchase', 'user:' . $userId);

        $rawUnlockableId = $input['unlockable_id'] ?? null;
        if ($rawUnlockableId === null || !is_numeric($rawUnlockableId) || is_array($rawUnlockableId)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid store item selected']);
            exit;
        }

        $unlockableId = (int)$rawUnlockableId;
        if ((float)$rawUnlockableId != $unlockableId || $unlockableId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid store item selected']);
            exit;
        }

        // Strict rejection if request attempts client-side price tampering
        if (isset($input['price']) || isset($input['cost']) || isset($input['amount']) || isset($input['coin_cost'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error', 
                'message' => 'Price tampering detected: item prices are authoritative and managed exclusively by server database.'
            ]);
            exit;
        }

        $couponCode = !empty($input['coupon_code']) ? trim((string)$input['coupon_code']) : null;

        $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $result = WalletService::unlockItem((int)$userId, $unlockableId, $couponCode, $clientIp);

        if ($result['status'] === 'error') {
            http_response_code(400);
        }

        echo json_encode($result);
        exit;
    }
}
