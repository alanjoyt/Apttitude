<?php
require_once __DIR__ . '/../../config/db.php';

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

            // Premium users bypass coin cost
            if ($user['plan'] === 'premium') {
                $db->prepare("
                    INSERT INTO coin_transactions (user_id, amount, transaction_type, reference_description)
                    VALUES (?, 0, 'store_purchase', ?)
                ")->execute([$userId, $description . ' (Premium Bypass)']);
                $db->commit();
                User::clearCache($userId);
                return true;
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
     * Purchase/unlock an item for a user
     */
    public static function unlockItem(int $userId, int $unlockableId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM unlockables WHERE id = ?");
        $stmt->execute([$unlockableId]);
        $item = $stmt->fetch();

        if (!$item) {
            return ['status' => 'error', 'message' => 'Store item not found'];
        }

        // Check if already unlocked
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM user_unlocks WHERE user_id = ? AND unlockable_id = ?");
        $checkStmt->execute([$userId, $unlockableId]);
        if ($checkStmt->fetchColumn() > 0) {
            return ['status' => 'error', 'message' => 'Item is already unlocked!'];
        }

        // Handle specific item types (e.g. Streak Freeze)
        if ($item['item_type'] === 'streak_freeze') {
            if (!self::spendCoins($userId, $item['coin_cost'], "Purchased: " . $item['title'])) {
                return ['status' => 'error', 'message' => 'Insufficient Speed Coins! Earn more by solving questions quickly.'];
            }
            $db->prepare("UPDATE users SET streak_freezes = streak_freezes + 1 WHERE id = ?")->execute([$userId]);
            User::clearCache($userId);
            return ['status' => 'success', 'message' => 'Streak Freeze Refilled! (+1 Shield)', 'new_balance' => self::getUserBalance($userId)];
        }

        // Attempt coin purchase
        if (!self::spendCoins($userId, $item['coin_cost'], "Unlocked Store Item: " . $item['title'])) {
            return ['status' => 'error', 'message' => 'Insufficient Speed Coins! Earn more by solving questions quickly.'];
        }

        // Record unlock
        $db->prepare("INSERT INTO user_unlocks (user_id, unlockable_id) VALUES (?, ?)")
           ->execute([$userId, $unlockableId]);

        return [
            'status' => 'success',
            'message' => 'Item unlocked successfully! ' . $item['title'],
            'new_balance' => self::getUserBalance($userId)
        ];
    }
}
