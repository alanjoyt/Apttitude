<?php
/**
 * CouponService.php
 * Handles coupon creation, bulk generation, validation, price calculations,
 * atomic redemptions, and rate-limiting.
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/User.php';

class CouponService {
    // Rate limit: 5 failed attempts per 15 minutes per IP/user
    const MAX_FAILED_ATTEMPTS = 5;
    const RATE_LIMIT_WINDOW_MINUTES = 15;

    // Readable alphabet excluding ambiguous characters (0, O, 1, I, L)
    const CHARSET = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    /**
     * Generate a secure random coupon code
     */
    public static function generateCode(string $prefix = 'APT-'): string {
        $prefix = strtoupper(trim($prefix));
        $bytes = random_bytes(8);
        $code = '';
        $charLen = strlen(self::CHARSET);
        for ($i = 0; $i < 8; $i++) {
            $code .= self::CHARSET[ord($bytes[$i]) % $charLen];
            if ($i === 3) $code .= '-';
        }
        return $prefix . $code;
    }

    /**
     * Rate-limiting check: returns true if allowed, false if blocked
     */
    public static function checkRateLimit(string $ip, ?int $userId): bool {
        $db = Database::getConnection();
        $cutoff = date('Y-m-d H:i:s', strtotime('-' . self::RATE_LIMIT_WINDOW_MINUTES . ' minutes'));

        if ($userId) {
            $stmt = $db->prepare("
                SELECT COUNT(*) FROM coupon_audit_log 
                WHERE (ip_address = ? OR user_id = ?) 
                  AND is_success = 0 
                  AND attempted_at >= ?
            ");
            $stmt->execute([$ip, $userId, $cutoff]);
        } else {
            $stmt = $db->prepare("
                SELECT COUNT(*) FROM coupon_audit_log 
                WHERE ip_address = ? 
                  AND is_success = 0 
                  AND attempted_at >= ?
            ");
            $stmt->execute([$ip, $cutoff]);
        }

        return ((int)$stmt->fetchColumn()) < self::MAX_FAILED_ATTEMPTS;
    }

    /**
     * Log a coupon validation / redemption attempt
     */
    public static function logAttempt(string $ip, ?int $userId, string $code, bool $isSuccess, ?string $reason = null): void {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO coupon_audit_log (user_id, ip_address, attempted_code, is_success, failure_reason)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $ip, substr(trim($code), 0, 64), $isSuccess ? 1 : 0, $reason]);
        } catch (\Throwable $e) {}
    }

    /**
     * Create a single coupon (Admin only)
     */
    public static function createCoupon(array $data, int $adminId): array {
        $db = Database::getConnection();

        $code = strtoupper(trim($data['code'] ?? ''));
        // Strip spaces
        $code = str_replace(' ', '', $code);
        if (empty($code)) {
            $prefix = !empty($data['prefix']) ? strtoupper(trim($data['prefix'])) : 'APT-';
            $code = self::generateCode($prefix);
        }

        // Validate code format: 3 to 20 chars, alphanumeric + dashes/underscores
        if (!preg_match('/^[A-Z0-9_\-]{3,20}$/', $code)) {
            return ['status' => 'error', 'message' => 'Coupon code must be 3-20 alphanumeric characters, dashes, or underscores.'];
        }

        // Duplicate check
        $dupStmt = $db->prepare("SELECT id FROM coupons WHERE code = ?");
        $dupStmt->execute([$code]);
        if ($dupStmt->fetch()) {
            return ['status' => 'error', 'message' => 'Coupon code already exists. Please choose a unique code.'];
        }

        $discountType = ($data['discount_type'] ?? 'fixed') === 'percent' ? 'percent' : 'fixed';
        $discountValue = max(1, (int)($data['discount_value'] ?? 0));
        if ($discountType === 'percent' && $discountValue > 100) {
            $discountValue = 100;
        }

        $maxDiscount = !empty($data['max_discount']) ? max(1, (int)$data['max_discount']) : null;
        $minPrice = max(0, (int)($data['min_price'] ?? 0));
        $applicableItems = !empty($data['applicable_items']) ? trim($data['applicable_items']) : 'all';
        $startsAt = !empty($data['starts_at']) && strtotime($data['starts_at']) ? date('Y-m-d H:i:s', strtotime($data['starts_at'])) : null;
        $expiresAt = !empty($data['expires_at']) && strtotime($data['expires_at']) ? date('Y-m-d H:i:s', strtotime($data['expires_at'])) : null;
        $totalLimit = !empty($data['total_limit']) ? max(1, (int)$data['total_limit']) : null;
        $perUserLimit = max(1, (int)($data['per_user_limit'] ?? 1));
        $allowedUsers = !empty($data['allowed_users']) ? trim($data['allowed_users']) : null;
        $isActive = isset($data['is_active']) ? (int)(bool)$data['is_active'] : 1;

        $stmt = $db->prepare("
            INSERT INTO coupons (
                code, discount_type, discount_value, max_discount, min_price, 
                applicable_items, starts_at, expires_at, total_limit, per_user_limit, 
                allowed_users, is_active, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $code, $discountType, $discountValue, $maxDiscount, $minPrice,
            $applicableItems, $startsAt, $expiresAt, $totalLimit, $perUserLimit,
            $allowedUsers, $isActive, $adminId
        ]);

        return [
            'status' => 'success',
            'message' => "Coupon '{$code}' created successfully!",
            'coupon_id' => $db->lastInsertId(),
            'code' => $code
        ];
    }

    /**
     * Bulk generate N coupons with identical rules
     */
    public static function bulkGenerate(array $data, int $quantity, int $adminId): array {
        $quantity = min(500, max(1, $quantity));
        $prefix = !empty($data['prefix']) ? strtoupper(trim($data['prefix'])) : 'APT-';
        $createdCodes = [];

        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            for ($i = 0; $i < $quantity; $i++) {
                $uniqueFound = false;
                $code = '';
                for ($attempt = 0; $attempt < 10; $attempt++) {
                    $candidate = self::generateCode($prefix);
                    $check = $db->prepare("SELECT id FROM coupons WHERE code = ?");
                    $check->execute([$candidate]);
                    if (!$check->fetch()) {
                        $code = $candidate;
                        $uniqueFound = true;
                        break;
                    }
                }

                if (!$uniqueFound) continue;

                $dataCopy = $data;
                $dataCopy['code'] = $code;
                $res = self::createCoupon($dataCopy, $adminId);
                if ($res['status'] === 'success') {
                    $createdCodes[] = $code;
                }
            }
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            return ['status' => 'error', 'message' => 'Bulk generation failed: ' . $e->getMessage()];
        }

        return [
            'status' => 'success',
            'count' => count($createdCodes),
            'codes' => $createdCodes
        ];
    }

    /**
     * List all coupons with filtering & search
     */
    public static function listCoupons(array $filters = []): array {
        try {
            $db = Database::getConnection();
            $sql = "SELECT c.*, u.name as creator_name FROM coupons c LEFT JOIN users u ON c.created_by = u.id WHERE 1=1";
            $params = [];

            if (!empty($filters['search'])) {
                $sql .= " AND c.code LIKE ?";
                $params[] = '%' . trim($filters['search']) . '%';
            }

            if (isset($filters['status'])) {
                $now = date('Y-m-d H:i:s');
                if ($filters['status'] === 'active') {
                    $sql .= " AND c.is_active = 1 AND (c.expires_at IS NULL OR c.expires_at > ?) AND (c.total_limit IS NULL OR c.times_used < c.total_limit)";
                    $params[] = $now;
                } elseif ($filters['status'] === 'expired') {
                    $sql .= " AND (c.expires_at IS NOT NULL AND c.expires_at <= ?)";
                    $params[] = $now;
                } elseif ($filters['status'] === 'exhausted') {
                    $sql .= " AND (c.total_limit IS NOT NULL AND c.times_used >= c.total_limit)";
                } elseif ($filters['status'] === 'inactive') {
                    $sql .= " AND c.is_active = 0";
                }
            }

            $sql .= " ORDER BY c.id DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            error_log("CouponService::listCoupons warning: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Toggle coupon active state
     */
    public static function toggleActive(int $couponId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE coupons SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?");
        return $stmt->execute([$couponId]);
    }

    /**
     * Get redemption history for a specific coupon
     */
    public static function getRedemptionHistory(int $couponId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.*, u.name as user_name, u.email as user_email
            FROM coupon_redemptions r
            JOIN users u ON r.user_id = u.id
            WHERE r.coupon_id = ?
            ORDER BY r.id DESC
        ");
        $stmt->execute([$couponId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Server-side Validation & Discount Price Preview
     * Returns non-leaky errors to students.
     */
    public static function validateAndPreview(string $code, string $itemKey, int $itemBasePrice, int $userId, string $ip): array {
        $code = strtoupper(trim($code));

        // 1. Rate Limit Guard
        if (!self::checkRateLimit($ip, $userId)) {
            return [
                'status' => 'rate_limited',
                'http_code' => 429,
                'message' => 'Too many failed coupon attempts. Please try again after 15 minutes.'
            ];
        }

        if (empty($code)) {
            self::logAttempt($ip, $userId, $code, false, 'Empty code');
            return ['status' => 'error', 'http_code' => 400, 'message' => 'Please enter a coupon code.'];
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM coupons WHERE UPPER(code) = ? LIMIT 1");
        $stmt->execute([$code]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        $now = date('Y-m-d H:i:s');
        $graceStart = date('Y-m-d H:i:s', time() + 60); // 60s tolerance for immediate usage/clock sync
        $genericError = 'Invalid or ineligible coupon code.';

        // Non-leaky checks: verify all rules without disclosing internal details
        if (!$coupon || (int)$coupon['is_active'] !== 1) {
            self::logAttempt($ip, $userId, $code, false, 'Nonexistent or inactive');
            return ['status' => 'error', 'http_code' => 400, 'message' => $genericError];
        }

        if ($coupon['starts_at'] && $coupon['starts_at'] > $graceStart) {
            self::logAttempt($ip, $userId, $code, false, 'Not yet started');
            return ['status' => 'error', 'http_code' => 400, 'message' => $genericError];
        }

        if ($coupon['expires_at'] && $coupon['expires_at'] < $now) {
            self::logAttempt($ip, $userId, $code, false, 'Expired');
            return ['status' => 'error', 'http_code' => 400, 'message' => $genericError];
        }

        if ($coupon['total_limit'] !== null && (int)$coupon['times_used'] >= (int)$coupon['total_limit']) {
            self::logAttempt($ip, $userId, $code, false, 'Total limit exhausted');
            return ['status' => 'error', 'http_code' => 400, 'message' => $genericError];
        }

        // Check applicable items
        if ($coupon['applicable_items'] !== 'all') {
            $allowedItems = array_map('trim', explode(',', strtolower($coupon['applicable_items'])));
            if (!in_array(strtolower($itemKey), $allowedItems, true)) {
                self::logAttempt($ip, $userId, $code, false, 'Item not applicable');
                return ['status' => 'error', 'http_code' => 400, 'message' => $genericError];
            }
        }

        // Check min item price
        if ($itemBasePrice < (int)$coupon['min_price']) {
            self::logAttempt($ip, $userId, $code, false, 'Min price not met');
            return ['status' => 'error', 'http_code' => 400, 'message' => $genericError];
        }

        // Check allowed users (by username or email)
        if (!empty($coupon['allowed_users'])) {
            $user = User::findById($userId);
            if (!$user) {
                return ['status' => 'error', 'http_code' => 401, 'message' => 'Authentication required.'];
            }
            $allowedList = array_map('trim', array_map('strtolower', explode(',', $coupon['allowed_users'])));
            $uName = strtolower($user['name'] ?? '');
            $uEmail = strtolower($user['email'] ?? '');
            if (!in_array($uName, $allowedList, true) && !in_array($uEmail, $allowedList, true)) {
                self::logAttempt($ip, $userId, $code, false, 'User not in allowed_users list');
                return ['status' => 'error', 'http_code' => 400, 'message' => $genericError];
            }
        }

        // Check per-user redemption limit
        $userRedeemedStmt = $db->prepare("SELECT COUNT(*) FROM coupon_redemptions WHERE coupon_id = ? AND user_id = ?");
        $userRedeemedStmt->execute([$coupon['id'], $userId]);
        $timesRedeemedByUser = (int)$userRedeemedStmt->fetchColumn();
        if ($timesRedeemedByUser >= (int)$coupon['per_user_limit']) {
            self::logAttempt($ip, $userId, $code, false, 'Per-user limit reached');
            return ['status' => 'error', 'http_code' => 400, 'message' => $genericError];
        }

        // Calculate discount
        $discountAmount = 0;
        if ($coupon['discount_type'] === 'percent') {
            $pct = (int)$coupon['discount_value'];
            $calculated = (int)floor(($itemBasePrice * $pct) / 100);
            if ($coupon['max_discount'] !== null) {
                $calculated = min($calculated, (int)$coupon['max_discount']);
            }
            $discountAmount = $calculated;
        } else {
            $discountAmount = (int)$coupon['discount_value'];
        }

        $discountAmount = min($discountAmount, $itemBasePrice);
        $finalPrice = max(0, $itemBasePrice - $discountAmount);

        // Phase 2 Economy Rule: Streak Freeze floor at 150 coins
        if (strtolower($itemKey) === 'streak_freeze') {
            if ($finalPrice < 150) {
                $finalPrice = 150;
                $discountAmount = max(0, $itemBasePrice - 150);
            }
        }

        self::logAttempt($ip, $userId, $code, true);

        return [
            'status' => 'success',
            'coupon_id' => $coupon['id'],
            'code' => $coupon['code'],
            'discount_type' => $coupon['discount_type'],
            'discount_value' => $coupon['discount_value'],
            'original_price' => $itemBasePrice,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice
        ];
    }

    /**
     * Atomic In-Transaction Redemption
     * Runs with row lock (SELECT FOR UPDATE) on the coupon row.
     */
    public static function redeemInTransaction(PDO $db, string $code, string $itemKey, int $itemBasePrice, int $userId, string $ip): array {
        $code = strtoupper(trim($code));

        // Re-check rate limiting
        if (!self::checkRateLimit($ip, $userId)) {
            return ['status' => 'error', 'message' => 'Too many failed attempts.'];
        }

        // Lock the coupon row for concurrent update safety
        $stmt = $db->prepare("SELECT * FROM coupons WHERE UPPER(code) = ? FOR UPDATE");
        $stmt->execute([$code]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coupon || (int)$coupon['is_active'] !== 1) {
            return ['status' => 'error', 'message' => 'Invalid or ineligible coupon code.'];
        }

        $now = date('Y-m-d H:i:s');
        $graceStart = date('Y-m-d H:i:s', time() + 60);
        if ($coupon['starts_at'] && $coupon['starts_at'] > $graceStart) {
            return ['status' => 'error', 'message' => 'Invalid or ineligible coupon code.'];
        }
        if ($coupon['expires_at'] && $coupon['expires_at'] < $now) {
            return ['status' => 'error', 'message' => 'Invalid or ineligible coupon code.'];
        }
        if ($coupon['total_limit'] !== null && (int)$coupon['times_used'] >= (int)$coupon['total_limit']) {
            return ['status' => 'error', 'message' => 'Coupon total usage limit reached.'];
        }

        // Check per-user limit
        $chkUser = $db->prepare("SELECT COUNT(*) FROM coupon_redemptions WHERE coupon_id = ? AND user_id = ?");
        $chkUser->execute([$coupon['id'], $userId]);
        if ((int)$chkUser->fetchColumn() >= (int)$coupon['per_user_limit']) {
            return ['status' => 'error', 'message' => 'You have already used this coupon the maximum allowed times.'];
        }

        // Calculate discount
        $discountAmount = 0;
        if ($coupon['discount_type'] === 'percent') {
            $pct = (int)$coupon['discount_value'];
            $calculated = (int)floor(($itemBasePrice * $pct) / 100);
            if ($coupon['max_discount'] !== null) {
                $calculated = min($calculated, (int)$coupon['max_discount']);
            }
            $discountAmount = $calculated;
        } else {
            $discountAmount = (int)$coupon['discount_value'];
        }

        $discountAmount = min($discountAmount, $itemBasePrice);
        $finalPrice = max(0, $itemBasePrice - $discountAmount);

        // Phase 2 Streak Freeze price floor
        if (strtolower($itemKey) === 'streak_freeze') {
            if ($finalPrice < 150) {
                $finalPrice = 150;
                $discountAmount = max(0, $itemBasePrice - 150);
            }
        }

        // Increment usage
        $upd = $db->prepare("UPDATE coupons SET times_used = times_used + 1 WHERE id = ?");
        $upd->execute([$coupon['id']]);

        // Record redemption
        $red = $db->prepare("
            INSERT INTO coupon_redemptions (coupon_id, user_id, item_key, original_price, discount_amount, final_price)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $red->execute([$coupon['id'], $userId, $itemKey, $itemBasePrice, $discountAmount, $finalPrice]);

        return [
            'status' => 'success',
            'coupon_id' => $coupon['id'],
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice
        ];
    }
}
