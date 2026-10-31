<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/CouponService.php';

class WalletService {

    /**
     * Get user's spendable Speed Coins balance
     */
    public static function getUserBalance(int $userId): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT coins FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $res = $stmt->fetch();
        return $res ? (int)$res['coins'] : 0;
    }

    /**
     * Credit coins to user wallet & log transaction audit row
     */
    public static function addCoins(int $userId, int $amount, string $type, string $description): bool {
        if ($amount <= 0) return false;

        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $db->prepare("UPDATE users SET coins = coins + ? WHERE id = ?")->execute([$amount, $userId]);
            $db->prepare("
                INSERT INTO coin_transactions (user_id, amount, transaction_type, reference_description)
                VALUES (?, ?, ?, ?)
            ")->execute([$userId, $amount, $type, $description]);

            $db->commit();
            User::clearCache($userId);
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    /**
     * Debit coins from user wallet & log transaction audit row
     */
    public static function spendCoins(int $userId, int $amount, string $description): bool {
        if ($amount <= 0) return false;

        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("SELECT coins, plan FROM users WHERE id = ? FOR UPDATE");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user) {
                $db->rollBack();
                return false;
            }

            if ((int)$user['coins'] < $amount) {
                $db->rollBack();
                return false;
            }

            $db->prepare("UPDATE users SET coins = coins - ? WHERE id = ?")->execute([$amount, $userId]);
            $db->prepare("
                INSERT INTO coin_transactions (user_id, amount, transaction_type, reference_description)
                VALUES (?, ?, 'store_purchase', ?)
            ")->execute([$userId, -$amount, $description]);

            $db->commit();
            User::clearCache($userId);
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    /**
     * Get list of store unlockables with unlocked state for given user
     */
    public static function getUnlockables(?int $userId = null): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM unlockables ORDER BY coin_cost ASC");
        $items = $stmt->fetchAll();

        if ($userId) {
            $unlockedStmt = $db->prepare("SELECT unlockable_id FROM user_unlocks WHERE user_id = ?");
            $unlockedStmt->execute([$userId]);
            $unlockedIds = $unlockedStmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($items as &$item) {
                $item['is_unlocked'] = in_array($item['id'], $unlockedIds);
            }
        }

        return $items;
    }

    /**
     * Purchase/unlock an item for a user (atomic with optional coupon)
     */
    public static function unlockItem(int $userId, int $unlockableId, ?string $couponCode = null, string $ip = '127.0.0.1'): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM unlockables WHERE id = ?");
        $stmt->execute([$unlockableId]);
        $item = $stmt->fetch();

        if (!$item) {
            return ['status' => 'error', 'message' => 'Store item not found'];
        }

        // Check if already unlocked (streak_freeze can be purchased multiple times up to 2 held)
        if ($item['item_type'] !== 'streak_freeze') {
            $checkStmt = $db->prepare("SELECT COUNT(*) FROM user_unlocks WHERE user_id = ? AND unlockable_id = ?");
            $checkStmt->execute([$userId, $unlockableId]);
            if ($checkStmt->fetchColumn() > 0) {
                return ['status' => 'error', 'message' => 'Item is already unlocked!'];
            }
        }

        // Begin atomic transaction for purchase + coupon redemption
        $db->beginTransaction();

        try {
            $userStmt = $db->prepare("SELECT coins, plan, streak_freezes FROM users WHERE id = ? FOR UPDATE");
            $userStmt->execute([$userId]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $db->rollBack();
                return ['status' => 'error', 'message' => 'User not found.'];
            }

            // Streak freeze max shield limit check
            if ($item['item_type'] === 'streak_freeze') {
                $currentFreezes = (int)($user['streak_freezes'] ?? 0);
                if ($currentFreezes >= 2) {
                    $db->rollBack();
                    return [
                        'status' => 'error',
                        'message' => 'You already hold the maximum limit of 2 Streak Freeze shields! Use one before refilling.'
                    ];
                }
            }

            $basePrice = (int)$item['coin_cost'];
            $finalPrice = $basePrice;
            $discountAmount = 0;

            // Redeem coupon if supplied
            if (!empty($couponCode)) {
                $couponRes = CouponService::redeemInTransaction($db, $couponCode, $item['item_type'], $basePrice, $userId, $ip);
                if ($couponRes['status'] !== 'success') {
                    $db->rollBack();
                    return ['status' => 'error', 'message' => $couponRes['message']];
                }
                $finalPrice = $couponRes['final_price'];
                $discountAmount = $couponRes['discount_amount'];
            }

            // Premium bypass
            if ($user['plan'] === 'premium') {
                $finalPrice = 0;
            }

            // Verify user has sufficient coins
            if ((int)$user['coins'] < $finalPrice) {
                $db->rollBack();
                return ['status' => 'error', 'message' => 'Insufficient Speed Coins! Earn more by solving questions quickly.'];
            }

            // Deduct coins
            if ($finalPrice > 0) {
                $db->prepare("UPDATE users SET coins = coins - ? WHERE id = ?")->execute([$finalPrice, $userId]);
            }

            // Audit transaction
            $desc = "Purchased Store Item: " . $item['title'];
            if (!empty($couponCode)) {
                $desc .= " (Coupon: " . strtoupper(trim($couponCode)) . " - Saved " . $discountAmount . " coins)";
            }
            if ($user['plan'] === 'premium') {
                $desc .= " (Premium Bypass)";
            }

            $db->prepare("
                INSERT INTO coin_transactions (user_id, amount, transaction_type, reference_description)
                VALUES (?, ?, 'store_purchase', ?)
            ")->execute([$userId, -$finalPrice, $desc]);

            // Grant item
            if ($item['item_type'] === 'streak_freeze') {
                $db->prepare("UPDATE users SET streak_freezes = streak_freezes + 1 WHERE id = ?")->execute([$userId]);
                $successMessage = 'Streak Freeze Refilled! (+1 Shield, holding ' . ((int)$user['streak_freezes'] + 1) . '/2)';
            } else {
                $db->prepare("INSERT INTO user_unlocks (user_id, unlockable_id) VALUES (?, ?)")->execute([$userId, $unlockableId]);
                $successMessage = 'Item unlocked successfully! ' . $item['title'];
            }

            $db->commit();
            User::clearCache($userId);

            return [
                'status' => 'success',
                'message' => $successMessage,
                'new_balance' => self::getUserBalance($userId),
                'discount_amount' => $discountAmount,
                'final_price' => $finalPrice
            ];
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return ['status' => 'error', 'message' => 'Purchase processing failed: ' . $e->getMessage()];
        }
    }

    /**
     * Compute total net coins for a user according to the immutable transaction ledger
     */
    public static function getLedgerSum(int $userId): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) FROM coin_transactions WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Verify whether a user's wallet balance equals their ledger sum
     */
    public static function verifyLedgerConsistency(int $userId): bool {
        return self::getUserBalance($userId) === self::getLedgerSum($userId);
    }

    /**
     * Audit all users across the system to ensure 100% wallet balance vs ledger equality
     */
    public static function getAllLedgerMismatches(): array {
        $db = Database::getConnection();
        $sql = "
            SELECT u.id, u.name, u.coins as wallet_balance, COALESCE(t.ledger_sum, 0) as ledger_sum
            FROM users u
            LEFT JOIN (
                SELECT user_id, SUM(amount) as ledger_sum
                FROM coin_transactions
                GROUP BY user_id
            ) t ON u.id = t.user_id
            WHERE u.coins != COALESCE(t.ledger_sum, 0)
        ";
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
