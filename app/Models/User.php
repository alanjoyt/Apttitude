<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Services/GamificationEngine.php';

class User {

    private static array $idCache = [];
    private static array $emailCache = [];

    /**
     * Self-healing helper: ensure bio, avatar_icon, referral_code, referred_by, device_hash exist
     */
    public static function ensureAddonColumnsExist(): void {
        static $migrated = false;
        if ($migrated) return;
        $migrated = true;

        // Skip schema checks during ordinary traffic; run only if explicitly requested
        if (!isset($_GET['check_schema']) && !isset($_GET['force_migrate'])) {
            return;
        }

        try {
            $db = Database::getConnection();
            $cols = $db->query("SHOW COLUMNS FROM `users`")->fetchAll(PDO::FETCH_COLUMN);

            if (!in_array('bio', $cols)) {
                $db->exec("ALTER TABLE `users` ADD COLUMN `bio` TEXT DEFAULT NULL");
            }
            if (!in_array('avatar_icon', $cols)) {
                $db->exec("ALTER TABLE `users` ADD COLUMN `avatar_icon` VARCHAR(50) DEFAULT 'bi-person-circle'");
            }
            if (!in_array('referral_code', $cols)) {
                $db->exec("ALTER TABLE `users` ADD COLUMN `referral_code` VARCHAR(20) DEFAULT NULL");
            }
            if (!in_array('referred_by', $cols)) {
                $db->exec("ALTER TABLE `users` ADD COLUMN `referred_by` INT DEFAULT NULL");
            }
            if (!in_array('device_hash', $cols)) {
                $db->exec("ALTER TABLE `users` ADD COLUMN `device_hash` VARCHAR(255) DEFAULT NULL");
            }
            if (!in_array('role', $cols)) {
                $db->exec("ALTER TABLE `users` ADD COLUMN `role` VARCHAR(20) DEFAULT 'user'");
            }
        } catch (\Throwable $e) {
            // Ignore if DB is currently unreachable
        }
    }

    public static function findById(int $id): ?array {
        if (isset(self::$idCache[$id])) {
            return self::$idCache[$id];
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        // Auto-generate referral_code if missing for existing student
        if ($user && empty($user['referral_code'])) {
            $code = 'APTT-' . strtoupper(substr(md5($user['id'] . $user['email'] . microtime()), 0, 6));
            try {
                $db->prepare("UPDATE users SET referral_code = ? WHERE id = ?")->execute([$code, $user['id']]);
                $user['referral_code'] = $code;
            } catch (\Throwable $e) {}
        }

        $res = $user ?: null;
        self::$idCache[$id] = $res;
        if ($res && !empty($res['email'])) {
            self::$emailCache[$res['email']] = $res;
        }
        return $res;
    }

    public static function findByEmail(string $email): ?array {
        if (isset(self::$emailCache[$email])) {
            return self::$emailCache[$email];
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch() ?: null;
        self::$emailCache[$email] = $user;
        if ($user && !empty($user['id'])) {
            self::$idCache[(int)$user['id']] = $user;
        }
        return $user;
    }

    public static function register(string $name, string $email, string $password, ?string $referralCode = null, ?int $collegeId = null, bool $showNameOnBoards = false): int {
        self::ensureAddonColumnsExist();
        $db = Database::getConnection();

        // 1. Explicit check for existing email
        $existing = self::findByEmail($email);
        if ($existing) {
            throw new Exception("An account with this email address already exists. Please log in.");
        }

        // Validate college_id if provided (CL1: must be active college)
        if ($collegeId !== null) {
            $cCheck = $db->prepare("SELECT COUNT(*) FROM colleges WHERE id = ? AND status = 'active'");
            $cCheck->execute([$collegeId]);
            if ((int)$cCheck->fetchColumn() === 0) {
                throw new InvalidArgumentException("Selected college is invalid or not yet approved.");
            }
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $userRefCode = 'APTT-' . strtoupper(substr(md5(microtime() . $email), 0, 6));

        $cfg = GamificationEngine::getConfig();
        $signupCoins = (int)($cfg['signup_bonus_coins'] ?? 25);
        $hideName = $showNameOnBoards ? 0 : 1; // Option B: Default to 1 (hidden) unless explicitly opted in

        try {
            $stmt = $db->prepare("
                INSERT INTO users (name, email, password_hash, plan, total_xp, coins, tier, current_streak, referral_code, college_id, college_changed_at, hide_name_on_boards) 
                VALUES (?, ?, ?, 'free', 0, ?, 'Bronze', 0, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $name, $email, $hash, $signupCoins, $userRefCode, 
                $collegeId, 
                $collegeId ? date('Y-m-d H:i:s') : null,
                $hideName
            ]);
            $userId = (int)$db->lastInsertId();
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry') || (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062)) {
                throw new Exception("An account with this email address already exists. Please log in.");
            }
            throw $e;
        }

        // Log welcome speed coins bonus from economy config
        $db->prepare("
            INSERT INTO coin_transactions (user_id, amount, transaction_type, reference_description)
            VALUES (?, ?, 'admin_grant', 'Welcome Signup Speed Coins Bonus')
        ")->execute([$userId, $signupCoins]);

        // Process referral code if provided
        if (!empty($referralCode)) {
            require_once __DIR__ . '/../Services/ReferralService.php';
            ReferralService::processReferral($userId, $referralCode);
        }

        return $userId;
    }


    public static function findOrCreateGoogleUser(string $email, string $name): array {
        $existing = self::findByEmail($email);
        if ($existing) {
            return $existing;
        }

        try {
            // Register new Google SSO user
            $randomPass = bin2hex(random_bytes(8));
            $userId = self::register($name, $email, $randomPass);
            return self::findById($userId);
        } catch (\Throwable $e) {
            $fallback = self::findByEmail($email);
            if ($fallback) {
                return $fallback;
            }
            throw $e;
        }
    }

    public static function authenticate(string $email, string $password): ?array {
        $user = self::findByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }

    public static function clearCache(?int $id = null): void {
        if ($id !== null) {
            if (isset(self::$idCache[$id])) {
                $email = self::$idCache[$id]['email'] ?? null;
                if ($email && isset(self::$emailCache[$email])) {
                    unset(self::$emailCache[$email]);
                }
                unset(self::$idCache[$id]);
            }
        } else {
            self::$idCache = [];
            self::$emailCache = [];
        }
    }

    public static function addXP(int $userId, int $xp): array {
        $db = Database::getConnection();
        $user = self::findById($userId);
        if (!$user) return [];

        $newXP = max(0, (int)$user['total_xp'] + $xp);
        $currentTier = $user['tier'] ?? 'Bronze';
        $newTier = GamificationEngine::getTierForXP($newXP, $currentTier);

        $stmt = $db->prepare("UPDATE users SET total_xp = ?, tier = ? WHERE id = ?");
        $stmt->execute([$newXP, $newTier, $userId]);

        // Invalidate in-memory user cache on write
        self::clearCache($userId);

        // Update leaderboard table (exclude admins from leaderboard_scores)
        if (($user['role'] ?? 'user') !== 'admin') {
            $lbStmt = $db->prepare("
                INSERT INTO leaderboard_scores (user_id, period, tier, period_xp)
                VALUES (?, 'weekly', ?, ?)
                ON DUPLICATE KEY UPDATE period_xp = GREATEST(0, period_xp + ?), tier = VALUES(tier)
            ");
            $lbStmt->execute([$userId, $newTier, max(0, $xp), $xp]);
        }

        return [
            'previous_xp' => $user['total_xp'],
            'new_xp' => $newXP,
            'gained_xp' => $xp,
            'previous_tier' => $user['tier'],
            'new_tier' => $newTier,
            'tier_upgraded' => ($newTier !== $user['tier'])
        ];
    }
}
