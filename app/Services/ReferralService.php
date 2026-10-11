<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/WalletService.php';

class ReferralService {

    /**
     * Get or generate user's referral code
     */
    public static function getReferralCode(int $userId): string {
        $user = User::findById($userId);
        if (!$user) return 'APTT-000000';

        if (!empty($user['referral_code'])) {
            return $user['referral_code'];
        }

        // Generate unique code
        $code = 'APTT-' . strtoupper(substr(md5($userId . $user['email'] . microtime()), 0, 6));
        $db = Database::getConnection();
        $db->prepare("UPDATE users SET referral_code = ? WHERE id = ?")->execute([$code, $userId]);
        return $code;
    }

    /**
     * Get referral statistics for dashboard / profile
     */
    public static function getReferralStats(int $userId): array {
        $db = Database::getConnection();
        $code = self::getReferralCode($userId);

        // Fetch referred users
        $stmt = $db->prepare("
            SELECT id, name, tier, created_at 
            FROM users 
            WHERE referred_by = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        $referredUsers = $stmt->fetchAll();

        $totalInvited = count($referredUsers);
        $coinsEarned = $totalInvited * 25; // 25 Speed Coins per referral

        return [
            'referral_code' => $code,
            'referral_link' => APP_URL . '/public/index.php?action=register&ref=' . $code,
            'total_invited' => $totalInvited,
            'coins_earned' => $coinsEarned,
            'referred_users' => $referredUsers
        ];
    }

    /**
     * Process referral link on new user registration
     */
    public static function processReferral(int $newUserId, string $referralCode): bool {
        $referralCode = strtoupper(trim($referralCode));
        if (empty($referralCode)) return false;

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, name FROM users WHERE referral_code = ? AND id != ?");
        $stmt->execute([$referralCode, $newUserId]);
        $referrer = $stmt->fetch();

        if (!$referrer) return false;

        $referrerId = (int)$referrer['id'];

        // Link referred_by on new user
        $db->prepare("UPDATE users SET referred_by = ? WHERE id = ?")->execute([$referrerId, $newUserId]);

        // Award +25 Speed Coins to Referrer
        WalletService::addCoins(
            $referrerId, 
            25, 
            'streak_reward', 
            "Referral Bonus: Invited student #" . $newUserId
        );

        // Award +25 Speed Coins Welcome Bonus to New Student
        WalletService::addCoins(
            $newUserId, 
            25, 
            'admin_grant', 
            "Referral Welcome Bonus from " . $referrer['name']
        );

        return true;
    }
}
