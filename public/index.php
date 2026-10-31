<?php
// Static file passthrough for PHP CLI development server only
if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file) && !preg_match('/\.php$/', $file)) {
        return false;
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../config/app.php';

// Strictly guarded local test session helper:
// Requires: 1) PHP CLI server, 2) Client IP is 127.0.0.1 or ::1, 3) APP_ENV explicitly set to 'local'
$clientIp = $_SERVER['REMOTE_ADDR'] ?? '';
$isLocalIp = in_array($clientIp, ['127.0.0.1', '::1', 'localhost'], true);
if (php_sapi_name() === 'cli-server' && $isLocalIp && defined('APP_ENV') && APP_ENV === 'local' && isset($_GET['dev_login'])) {
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../app/Models/User.php';
    require_once __DIR__ . '/../app/Services/DeviceLockService.php';
    $uid = (int)$_GET['dev_login'];
    $u = User::findById($uid);
    if ($u) {
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['user_name'] = $u['name'];
        $_SESSION['role'] = $u['role'] ?? 'user';
        DeviceLockService::registerDeviceLock($u['id']);
        header('Location: index.php?action=' . ($_GET['redirect'] ?? 'dashboard'));
        exit;
    }
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Models/Leaderboard.php';
require_once __DIR__ . '/../app/Controllers/PracticeController.php';
require_once __DIR__ . '/../app/Controllers/MockTestController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';
require_once __DIR__ . '/../app/Controllers/StoreController.php';
require_once __DIR__ . '/../app/Controllers/AdminController.php';
require_once __DIR__ . '/../app/Controllers/DiagnosticController.php';
require_once __DIR__ . '/../app/Controllers/QuestController.php';
require_once __DIR__ . '/../app/Controllers/SocialController.php';
require_once __DIR__ . '/../app/Controllers/DuelController.php';
require_once __DIR__ . '/../app/Controllers/ProfileController.php';
require_once __DIR__ . '/../app/Controllers/TechnicalController.php';
require_once __DIR__ . '/../app/Controllers/StudentSubmissionController.php';
require_once __DIR__ . '/../app/Controllers/CompanyController.php';
require_once __DIR__ . '/../app/Services/DeviceLockService.php';
require_once __DIR__ . '/../app/Services/CsrfService.php';

// Enforce single active session device lock validation
if (isset($_SESSION['user_id'])) {
    DeviceLockService::validateDeviceLock((int)$_SESSION['user_id']);
}

$action = $_GET['action'] ?? 'landing';

// Shield all API AJAX routes: suppress warnings from breaking JSON payloads and log them server-side
if (str_starts_with($action, 'api_')) {
    ob_start();
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        error_log("[API ERROR {$errno}] {$errstr} in {$errfile}:{$errline}");
        return true; // Suppress stdout emission
    }, E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE | E_DEPRECATED | E_USER_DEPRECATED);
}

// Central Server-Side Admin Guard: protects all admin pages and api_admin_* endpoints
$isAdminAction = (
    $action === 'admin' ||
    str_starts_with($action, 'admin_') ||
    str_starts_with($action, 'api_admin_') ||
    $action === 'api_toggle_dev_mode'
);

if ($isAdminAction) {
    // 1. Guest Check: Redirect to login for pages, 401 for API
    if (!isset($_SESSION['user_id'])) {
        if (str_starts_with($action, 'api_')) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Authentication required: Please log in to access this resource.'
            ]);
            exit;
        } else {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? 'index.php?action=admin';
            header('Location: index.php?action=login');
            exit;
        }
    }

    // 2. Role Check: Verify 'admin' role in DB/Session
    $currentRole = $_SESSION['user_role'] ?? null;
    if (!$currentRole) {
        $userRow = User::findById((int)$_SESSION['user_id']);
        $currentRole = $userRow['role'] ?? 'student';
        $_SESSION['user_role'] = $currentRole;
    }

    if ($currentRole !== 'admin') {
        http_response_code(403);
        if (str_starts_with($action, 'api_')) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Access Denied: Administrator privileges required.'
            ]);
            exit;
        } else {
            $_SESSION['auth_error'] = "Access Denied: Administrator privileges required.";
            header('Content-Type: text/html; charset=utf-8');
            echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>403 Forbidden</title><link rel='stylesheet' href='css/style.min.css'></head><body class='bg-light text-center py-5'><div class='container'><h1 class='display-4 text-danger fw-bold'>403 Forbidden</h1><p class='lead'>Access Denied: Administrator privileges required.</p><a href='index.php?action=dashboard' class='btn btn-primary'>Return to Dashboard</a></div></body></html>";
            exit;
        }
    }
}

// Router Dispatcher
switch ($action) {
    // API AJAX Routes
    case 'api_update_profile':
        CsrfService::verify();
        ProfileController::updateProfileJSON();
        break;

    case 'api_change_password':
        CsrfService::verify();
        ProfileController::changePasswordJSON();
        break;
    case 'api_create_duel':
        CsrfService::verify();
        DuelController::createDuelJSON();
        break;
    case 'api_create_bot_duel':
        CsrfService::verify();
        DuelController::createBotDuelJSON();
        break;

    case 'api_join_duel':
        CsrfService::verify();
        DuelController::joinDuelJSON();
        break;

    case 'api_get_duel':
        DuelController::getDuelQuestionsJSON();
        break;

    case 'api_check_duel_answer':
        CsrfService::verify();
        DuelController::checkDuelAnswerJSON();
        break;

    case 'api_submit_duel':
        CsrfService::verify();
        DuelController::submitDuelJSON();
        break;
    case 'api_add_friend':
        CsrfService::verify();
        SocialController::addFriendJSON();
        break;

    case 'api_accept_friend':
        CsrfService::verify();
        SocialController::acceptFriendJSON();
        break;

    case 'api_toggle_dev_mode':
        AdminController::toggleDevModeJSON();
        break;

    case 'api_import_csv':
    case 'api_admin_import_batch':
        AdminController::importBatchQuestionsJSON();
        break;

    case 'api_submit_user_question':
        CsrfService::verify();
        StudentSubmissionController::submitQuestionJSON();
        break;

    case 'api_admin_approve_submission':
        AdminController::approveSubmissionJSON();
        break;

    case 'api_admin_reject_submission':
        AdminController::rejectSubmissionJSON();
        break;

    case 'api_admin_update_company_profile':
        AdminController::updateCompanyProfileJSON();
        break;

    case 'api_admin_mark_reviewed':
        AdminController::markQuestionReviewedJSON();
        break;


    case 'api_create_squad':
        CsrfService::verify();
        SocialController::createSquadJSON();
        break;

    case 'api_join_squad':
        CsrfService::verify();
        SocialController::joinSquadJSON();
        break;
    case 'api_get_quests':
        QuestController::getDailyQuestsJSON();
        break;

    case 'api_claim_quest':
        CsrfService::verify();
        QuestController::claimQuestJSON();
        break;
    case 'api_get_diagnostic':
        DiagnosticController::getDiagnosticTestJSON();
        break;

    case 'api_submit_diagnostic':
        CsrfService::verify();
        DiagnosticController::submitDiagnosticTestJSON();
        break;

    case 'api_next_question':
        PracticeController::getNextQuestionJSON();
        break;

    case 'api_technical_stats':
        TechnicalController::getLanguageStatsJSON();
        break;

    case 'api_submit_answer':
        CsrfService::verify();
        PracticeController::submitAnswerJSON();
        break;

    case 'api_tag_mistake':
        CsrfService::verify();
        PracticeController::tagMistakeJSON();
        break;

    case 'api_save_progress':
        PracticeController::saveProgressJSON();
        break;

    case 'api_save_quiz_session':
        PracticeController::saveQuizSessionJSON();
        break;

    case 'api_get_quiz_session':
        PracticeController::getQuizSessionJSON();
        break;

    case 'api_clear_quiz_session':
        PracticeController::clearQuizSessionJSON();
        break;

    case 'api_restart_topic':
        CsrfService::verify();
        PracticeController::restartTopicJSON();
        break;

    case 'api_use_hint':
        CsrfService::verify();
        PracticeController::useHintJSON();
        break;

    case 'api_get_mock':
        MockTestController::getMockTestJSON();
        break;

    case 'api_submit_mock':
        CsrfService::verify();
        MockTestController::submitMockTestJSON();
        break;

    case 'api_get_mock_scorecard':
        MockTestController::getMockScorecardJSON();
        break;

    case 'api_profile_progress':
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $uid = $_SESSION['user_id'] ?? null;
        if (!$uid) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        require_once __DIR__ . '/../app/Services/ProgressService.php';
        $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
        echo json_encode(['status' => 'success', 'data' => ProgressService::getProgressReport($uid, $days)]);
        exit;

    case 'api_buy_unlockable':
        StoreController::unlockItemJSON();
        break;

    case 'api_preview_coupon':
        StoreController::previewCouponJSON();
        break;

    case 'api_admin_create_coupon':
        AdminController::createCouponJSON();
        break;

    case 'api_admin_bulk_coupons':
        AdminController::bulkCouponsJSON();
        break;

    case 'api_admin_toggle_coupon':
        AdminController::toggleCouponJSON();
        break;

    case 'api_admin_list_coupons':
        AdminController::listCouponsJSON();
        break;

    case 'api_admin_coupon_redemptions':
        AdminController::couponRedemptionsJSON();
        break;

    case 'api_admin_migrate':
        AdminController::migrateDatabaseJSON();
        break;

    case 'api_admin_rollback':
        AdminController::rollbackDatabaseJSON();
        break;

    case 'api_admin_search_users':
    case 'api_admin_users':
        AdminController::searchUsersJSON();
        break;

    case 'api_admin_user_details':
        AdminController::getUserDetailsJSON();
        break;

    case 'api_admin_credit_coins':
        CsrfService::verify();
        AdminController::creditUserCoinsJSON();
        break;

    case 'api_admin_update_user_role':
        CsrfService::verify();
        AdminController::updateUserRoleJSON();
        break;

    case 'api_admin_update_user_stats':
        CsrfService::verify();
        AdminController::updateUserStatsJSON();
        break;

    case 'api_admin_reset_user_password':
        CsrfService::verify();
        AdminController::resetUserPasswordAdminJSON();
        break;

    case 'api_admin_reset_user_freeze':
        CsrfService::verify();
        AdminController::resetUserStreakFreezeJSON();
        break;

    case 'api_admin_list_questions':
        AdminController::listQuestionsAdminJSON();
        break;

    case 'api_admin_bulk_questions':
        CsrfService::verify();
        AdminController::bulkQuestionActionJSON();
        break;

    case 'api_admin_audit_logs':
        AdminController::getAuditLogsJSON();
        break;

    case 'api_submit_report':
        CsrfService::verify();
        AdminController::submitReportJSON();
        break;

    case 'api_google_login':
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $email = null;
        $name = 'Google Student';

        // 1. Check if real Google OAuth JWT Token (credential) is passed
        if (!empty($input['credential'])) {
            $jwtParts = explode('.', $input['credential']);
            if (count($jwtParts) === 3) {
                $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $jwtParts[1])), true);
                if (!empty($payload['email'])) {
                    $email = filter_var(trim($payload['email']), FILTER_VALIDATE_EMAIL);
                    $name = trim($payload['name'] ?? $payload['given_name'] ?? 'Google Student');
                }
            }
        }

        // 2. Direct email input fallback
        if (!$email && !empty($input['email'])) {
            $email = filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL);
            $name = trim($input['name'] ?? 'Google Student');
        }

        if ($email) {
            $user = User::findOrCreateGoogleUser($email, $name);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            DeviceLockService::registerDeviceLock($user['id']);
            echo json_encode(['status' => 'success', 'user' => $user]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Google authentication credential or email']);
        }
        exit;

    // Auth Routes
    case 'login':
        if (isset($_SESSION['device_lock_error'])) {
            $error = $_SESSION['device_lock_error'];
            unset($_SESSION['device_lock_error']);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = User::authenticate($_POST['email'], $_POST['password']);
            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'] ?? 'student';
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                DeviceLockService::registerDeviceLock($user['id']);
                $redirectTo = !empty($_POST['redirect']) ? htmlspecialchars($_POST['redirect'], ENT_QUOTES) : (!empty($_GET['redirect']) ? htmlspecialchars($_GET['redirect'], ENT_QUOTES) : 'dashboard');
                header('Location: index.php?action=' . $redirectTo);
                exit;
            } else {
                $error = "Invalid email or password";
            }
        }
        require __DIR__ . '/../app/Views/login.php';
        break;

    case 'register':
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $refCode = trim($_POST['referral_code'] ?? '');

            if (empty($name) || empty($email) || empty($password)) {
                $error = "Please fill in all required fields.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Please enter a valid email address.";
            } elseif (strlen($password) < 6) {
                $error = "Password must be at least 6 characters.";
            } else {
                try {
                    $userId = User::register($name, $email, $password, $refCode);
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_role'] = 'student';
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    DeviceLockService::registerDeviceLock($userId);
                    header('Location: index.php?action=dashboard');
                    exit;
                } catch (\Throwable $e) {
                    $error = $e->getMessage();
                }
            }
        }
        require __DIR__ . '/../app/Views/register.php';
        break;

    case 'logout':
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: index.php?action=landing');
        exit;

    // Page Views
    case 'privacy':
        $pageTitle = "Privacy Policy & Data Protection | Apttitudee";
        $metaDescription = "Read Apttitudee's Privacy Policy, user data collection disclosure, and compliance with DPDP 2023 & Indian IT Act 2000.";
        require __DIR__ . '/../app/Views/privacy.php';
        break;

    case 'terms':
        $pageTitle = "Terms & Educational Disclaimer | Apttitudee";
        $metaDescription = "Review Apttitudee's Terms & Conditions, fair use anti-cheating policy, and corporate trademark educational disclaimer.";
        require __DIR__ . '/../app/Views/terms.php';
        break;

    case 'profile':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $pageTitle = "User Profile & Progress Report | Apttitudee";
        $metaDescription = "Track your learning progress, accuracy trends, topic strength maps, and mock examination scorecards on Apttitudee.";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/profile.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'skill_tree':
        $pageTitle = "Aptitude Skill Trees & RPG Quests | Apttitudee";
        $metaDescription = "Unlock Quantitative Aptitude, Logical Reasoning, and Verbal Ability skill trees. Master topics level-by-level with spaced repetition and XP rewards.";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/skill_tree.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'trophy_room':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $pageTitle = "Aptitude Showcase & Career Badges | Apttitudee";
        $metaDescription = "View your earned achievement badges, performance trophies, streak records, and milestone rewards on Apttitudee.";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/trophy_room.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'social':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $pageTitle = "Study Squads & Peer Leaderboards | Apttitudee";
        $metaDescription = "Form study squads, connect with fellow campus candidates, challenge friends to aptitude duels, and dominate competitive leaderboards.";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/social.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'duel':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $pageTitle = "1v1 Speed Quiz Duels & Battles | Apttitudee";
        $metaDescription = "Challenge peers or AI bots to 1v1 real-time speed aptitude duels. Solve Quant and Reasoning questions faster to win Speed Coins and XP.";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/duel.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'time_attack':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login&redirect=time_attack');
            exit;
        }
        $pageTitle = "60s Time Attack Aptitude Speed Test | Apttitudee";
        $metaDescription = "Test your speed and accuracy in 60-second rapid-fire Aptitude Time Attack mode. Solve consecutive questions to multiplier combos!";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/time_attack.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'campaign':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login&redirect=campaign');
            exit;
        }
        $pageTitle = "Career Quest Map & Story Campaign | Apttitudee";
        $metaDescription = "Embark on an interactive RPG career campaign. Complete placement chapter quests across TCS NQT, Infosys, Wipro, and Accenture levels.";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/campaign.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'mock':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login&redirect=mock');
            exit;
        }
        $pageTitle = "Placement Mock Tests & Score Sheet | Apttitudee";
        $metaDescription = "Take full-length simulated company placement mock tests matching actual exam patterns of TCS NQT, Infosys, Wipro NLTH, and Accenture.";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/mock.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'leaderboard':
        $pageTitle = "Global Ranks & Tier Leaderboards | Apttitudee";
        $metaDescription = "Check weekly and all-time student rankings across Bronze, Silver, Gold, Platinum, and Diamond tiers on Apttitudee.";
        $period = $_GET['period'] ?? 'weekly';
        $tier = $_GET['tier'] ?? null;
        $rankings = Leaderboard::getRankings($period, $tier);
        $userRank = isset($_SESSION['user_id']) ? Leaderboard::getUserRankContext($_SESSION['user_id'], $period) : null;
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/leaderboard.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'store':
        $pageTitle = "Speed Coins Store & Mock Packs | Apttitudee";
        $metaDescription = "Redeem your earned Speed Coins for TCS NQT & Infosys mock packs, Streak Freeze protection tokens, and glowing profile cosmetics.";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/store.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'dashboard':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $pageTitle = "Student Analytics Dashboard | Apttitudee";
        $metaDescription = "Track your topic accuracy, XP progression, daily practice streaks, and company mock readiness analytics on Apttitudee.";
        $dashboardData = DashboardController::getDashboardData($_SESSION['user_id']);
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/dashboard.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'admin':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $currentUser = User::findById((int)$_SESSION['user_id']);
        if (!$currentUser || ($currentUser['role'] ?? 'user') !== 'admin') {
            $_SESSION['auth_error'] = "Access Denied: Admin privileges required.";
            header('Location: index.php?action=dashboard');
            exit;
        }
        $pageTitle = "Admin CMS & Moderation | Apttitudee";
        $metaDescription = "Admin content management system, CSV bulk question importer, moderation queue, and Developer Mode controls.";
        $pendingReports = AdminController::getPendingReports();
        $pendingSubmissions = AdminController::getPendingSubmissions();
        $isDevMode = DeviceLockService::isDeveloperMode();
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/admin.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'companies':
        $pageTitle = "Company Placement Hub & Recruitment Tracks | Apttitudee";
        $metaDescription = "Explore placement test patterns, selection rounds, and 1,500+ verified practice questions for TCS NQT, Infosys, Wipro, Accenture, Amazon, and 15 top recruiters.";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/companies.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'company':
        $slug = $_GET['slug'] ?? '';
        $pageTitle = "Company Placement Pattern & Rounds | Apttitudee";
        $metaDescription = "Detailed assessment patterns, round structures, syllabus, and curated practice questions on Apttitudee.";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/company_detail.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'technical':
        $pageTitle = "Technical Coding Hub & PYQs | Apttitudee";
        $metaDescription = "Practice 200+ hard technical questions categorized by language (C, C++, Java, PHP, C#, Python, SQL, DSA) and authentic Innovature Labs placement PYQs.";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/technical.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'practice':
        $pageTitle = "Aptitude Practice & Syllabus | Apttitudee";
        $metaDescription = "Practice adaptive aptitude questions with dynamic difficulty scaling, step-by-step solutions, confidence tagging, and Speed Coin rewards.";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/practice.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'diagnostic':
        $pageTitle = "Diagnostic Placement Evaluation | Apttitudee";
        $metaDescription = "Take a 10-minute diagnostic placement test to evaluate your strengths in Quantitative Aptitude, Reasoning, and Verbal Ability.";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/diagnostic.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;

    case 'landing':
    default:
        $pageTitle = "Apttitudee - Gamified Placement Prep Platform";
        $metaDescription = "Prepare for TCS NQT, Infosys, Wipro, Accenture, and Campus Placements with gamified adaptive practice, skill trees, 1v1 duels, and company PYQ mock tests.";
        require __DIR__ . '/../app/Views/header.php';
        require __DIR__ . '/../app/Views/landing.php';
        require __DIR__ . '/../app/Views/footer.php';
        break;
}

