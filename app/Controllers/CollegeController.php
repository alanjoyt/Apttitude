<?php
/**
 * CollegeController: Handles HTTP requests for college directory, 30-day throttle,
 * privacy toggle, rankings, and administrative management.
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Services/CollegeService.php';
require_once __DIR__ . '/../Services/CsrfService.php';
require_once __DIR__ . '/../Models/User.php';

class CollegeController {

    /**
     * GET active colleges for dropdowns (Registration & Profile)
     */
    public static function getCollegesJSON(): void {
        header('Content-Type: application/json');
        $colleges = CollegeService::getActiveColleges();
        echo json_encode([
            'status' => 'success',
            'colleges' => $colleges
        ]);
        exit;
    }

    /**
     * POST request a missing college (creates pending row)
     */
    public static function requestCollegeJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'] ?? null;

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (!CsrfService::verify($input)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Invalid or missing CSRF token.']);
            exit;
        }

        $name = trim($input['name'] ?? '');
        $city = trim($input['city'] ?? '');
        $state = trim($input['state'] ?? '');

        if (empty($name) || empty($city) || empty($state)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Please provide college name, city, and state.']);
            exit;
        }

        $res = CollegeService::requestCollege($name, $city, $state, $userId);
        if (($res['status'] ?? '') === 'error') {
            http_response_code($res['code'] ?? 400);
        }
        echo json_encode($res);
        exit;
    }

    /**
     * POST update user's college (30-day throttle)
     */
    public static function updateCollegeJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Please log in to update your college.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (!CsrfService::verify($input)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Invalid or missing CSRF token.']);
            exit;
        }

        $collegeId = isset($input['college_id']) && $input['college_id'] !== '' ? (int)$input['college_id'] : null;
        $showName = isset($input['show_name_on_boards']) ? (bool)$input['show_name_on_boards'] : null;

        $res = CollegeService::setUserCollege($userId, $collegeId, $showName);
        if (($res['status'] ?? '') === 'error') {
            http_response_code($res['code'] ?? 400);
        }
        echo json_encode($res);
        exit;
    }

    /**
     * POST toggle leaderboard privacy (Hide name on college boards)
     */
    public static function togglePrivacyJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (!CsrfService::verify($input)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Invalid or missing CSRF token.']);
            exit;
        }

        $hideName = !empty($input['hide_name_on_boards']);

        $res = CollegeService::toggleBoardPrivacy($userId, $hideName);
        echo json_encode($res);
        exit;
    }

    /**
     * GET College vs College Leaderboard (>= 5 active members threshold)
     */
    public static function getCollegeVsCollegeJSON(): void {
        header('Content-Type: application/json');
        $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 50;
        $board = CollegeService::getCollegeVsCollegeBoard($limit);

        echo json_encode([
            'status' => 'success',
            'min_members_threshold' => 5,
            'colleges' => $board
        ]);
        exit;
    }

    /**
     * GET Intra-College Student Ranking
     */
    public static function getCollegeStudentsJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $currentUserId = $_SESSION['user_id'] ?? null;

        $collegeId = isset($_GET['college_id']) ? (int)$_GET['college_id'] : null;
        if (!$collegeId) {
            // Default to logged-in user's college if available
            if ($currentUserId) {
                $db = Database::getConnection();
                $stmt = $db->prepare("SELECT college_id FROM users WHERE id = ?");
                $stmt->execute([$currentUserId]);
                $cid = $stmt->fetchColumn();
                if ($cid) $collegeId = (int)$cid;
            }
        }

        if (!$collegeId) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Please provide a valid college_id or assign a college to your profile.']);
            exit;
        }

        $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 100;
        $res = CollegeService::getCollegeStudentBoard($collegeId, $currentUserId, $limit);

        if (($res['status'] ?? '') === 'error') {
            http_response_code($res['code'] ?? 400);
        }
        echo json_encode($res);
        exit;
    }

    /**
     * Admin: Get all colleges (active, pending, rejected)
     */
    public static function adminGetCollegesJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Admin authorization required.']);
            exit;
        }

        $colleges = CollegeService::getAllCollegesForAdmin();
        echo json_encode([
            'status' => 'success',
            'colleges' => $colleges
        ]);
        exit;
    }

    /**
     * Admin: Approve a pending college
     */
    public static function adminApproveCollegeJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Admin authorization required.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (!CsrfService::verify($input)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Invalid or missing CSRF token.']);
            exit;
        }

        $collegeId = (int)($input['college_id'] ?? 0);

        if (!$collegeId) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing college_id.']);
            exit;
        }

        $res = CollegeService::approveCollege($collegeId);
        echo json_encode($res);
        exit;
    }

    /**
     * Admin: Reject a pending college
     */
    public static function adminRejectCollegeJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Admin authorization required.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (!CsrfService::verify($input)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Invalid or missing CSRF token.']);
            exit;
        }

        $collegeId = (int)($input['college_id'] ?? 0);

        if (!$collegeId) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing college_id.']);
            exit;
        }

        $res = CollegeService::rejectCollege($collegeId);
        echo json_encode($res);
        exit;
    }

    /**
     * Admin: Merge colleges cleanly
     */
    public static function adminMergeCollegesJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Admin authorization required.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (!CsrfService::verify($input)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Invalid or missing CSRF token.']);
            exit;
        }

        $fromId = (int)($input['from_college_id'] ?? 0);
        $toId = (int)($input['to_college_id'] ?? 0);

        if (!$fromId || !$toId) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Both from_college_id and to_college_id are required.']);
            exit;
        }

        $res = CollegeService::mergeColleges($fromId, $toId);
        if (($res['status'] ?? '') === 'error') {
            http_response_code($res['code'] ?? 400);
        }
        echo json_encode($res);
        exit;
    }
}
