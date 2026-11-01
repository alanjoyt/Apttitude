<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Services/GamificationEngine.php';
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
        $cfg = GamificationEngine::getConfig();
        $bonusPerRef = (int)($cfg['referral_bonus_coins'] ?? 15);

        // Fetch referred users
        $stmt = $db->prepare("
            SELECT u.id, u.name, u.tier, u.created_at,
                   (SELECT COUNT(*) FROM attempts WHERE user_id = u.id) as questions_answered
            FROM users u
            WHERE u.referred_by = ? 
            ORDER BY u.created_at DESC
        ");
        $stmt->execute([$userId]);
        $referredUsers = $stmt->fetchAll();

        // Calculate coins earned by checking paid transactions
        $paidStmt = $db->prepare("
            SELECT COALESCE(SUM(amount), 0) FROM coin_transactions
            WHERE user_id = ? AND transaction_type = 'admin_grant'
              AND reference_description LIKE 'Referral Bonus: Invited student #%'
        ");
        $paidStmt->execute([$userId]);
        $coinsEarned = (int)$paidStmt->fetchColumn();

        return [
            'referral_code' => $code,
            'referral_link' => APP_URL . '/public/index.php?action=register&ref=' . $code,
            'total_invited' => count($referredUsers),
            'coins_earned' => $coinsEarned,
            'referred_users' => $referredUsers
        ];
    }

    /**
     * Process referral link on new user registration (links users; rewards paid after 20 Qs)
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

        // Link referred_by on new user (reward is held in escrow until 20 questions completed)
        $db->prepare("UPDATE users SET referred_by = ? WHERE id = ?")->execute([$referrerId, $newUserId]);
        User::clearCache($newUserId);

        return true;
    }

    /**
     * Check if referred user reached the 20-question qualification threshold
     * Enforces:
     * 1. Minimum 20 questions answered by referred user
     * 2. Max 10 successful referrals paid per month per referrer
     */
    public static function checkAndAwardReferralBonus(int $newUserId): bool {
        $db = Database::getConnection();
        $user = User::findById($newUserId);
        if (!$user || empty($user['referred_by'])) return false;

        $referrerId = (int)$user['referred_by'];
        $cfg = GamificationEngine::getConfig();
        $minQuestions = (int)($cfg['referral_min_questions_required'] ?? 20);
        $maxPerMonth = (int)($cfg['referral_max_per_month'] ?? 10);
        $bonusCoins = (int)($cfg['referral_bonus_coins'] ?? 15);

        // 1. Verify user has answered at least 20 questions
        $attStmt = $db->prepare("SELECT COUNT(*) FROM attempts WHERE user_id = ?");
        $attStmt->execute([$newUserId]);
        $totalAnswers = (int)$attStmt->fetchColumn();

        if ($totalAnswers < $minQuestions) {
            return false; // Threshold not met yet
        }

        // 2. Check if referral bonus was already awarded for this pair
        $dupStmt = $db->prepare("
            SELECT COUNT(*) FROM coin_transactions
            WHERE user_id = ? AND transaction_type = 'admin_grant'
              AND reference_description LIKE ?
        ");
        $dupStmt->execute([$referrerId, "Referral Bonus: Invited student #{$newUserId}%"]);
        if ((int)$dupStmt->fetchColumn() > 0) {
            return false; // Already awarded
        }

        // 3. Check monthly cap for the referrer (max 10/month)
        $monthStmt = $db->prepare("
            SELECT COUNT(*) FROM coin_transactions
            WHERE user_id = ? AND transaction_type = 'admin_grant'
              AND reference_description LIKE 'Referral Bonus: Invited student #%'
              AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())
        ");
        $monthStmt->execute([$referrerId]);
        if ((int)$monthStmt->fetchColumn() >= $maxPerMonth) {
            return false; // Monthly cap reached for referrer
        }

        // 4. Award bonus to Referrer
        WalletService::addCoins(
            $referrerId,
            $bonusCoins,
            'admin_grant',
            "Referral Bonus: Invited student #{$newUserId} (reached {$minQuestions} questions)"
        );

        // 5. Award matching bonus to New Student
        WalletService::addCoins(
            $newUserId,
            $bonusCoins,
            'admin_grant',
            "Referral Bonus: Solved {$minQuestions} questions milestone"
        );

        return true;
    }
}
