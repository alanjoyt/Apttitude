<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Services/FixPlanService.php';
require_once __DIR__ . '/../Services/CsrfService.php';
require_once __DIR__ . '/../Models/User.php';

class FixPlanController {

    public static function getPlanJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $planId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if ($planId) {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM fix_plans WHERE id = ? LIMIT 1");
            $stmt->execute([$planId]);
            $plan = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$plan) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Plan not found']);
                exit;
            }

            // IDOR Protection: verify plan belongs to logged-in user
            if ((int)$plan['user_id'] !== (int)$userId) {
                http_response_code(403);
                echo json_encode(['status' => 'error', 'message' => 'Forbidden: access denied']);
                exit;
            }

            FixPlanService::refreshDayUnlockStates($plan);
            $daysStmt = $db->prepare("SELECT * FROM fix_plan_days WHERE plan_id = ? ORDER BY day_no ASC");
            $daysStmt->execute([$planId]);
            $days = $daysStmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'status' => 'success',
                'plan' => $plan,
                'days' => $days,
                'weakest_subtopics' => json_decode($plan['weakest_subtopics'] ?? '[]', true) ?: []
            ]);
            exit;
        }

        $summary = FixPlanService::getActivePlanSummary($userId);
        echo json_encode([
            'status' => 'success',
            'data' => $summary
        ]);
        exit;
    }

    public static function verifyTaskJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $planId = (int)($input['plan_id'] ?? 0);
        $dayNo = (int)($input['day_no'] ?? 0);

        if (!$planId || !$dayNo) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing plan_id or day_no']);
            exit;
        }

        $result = FixPlanService::verifyTaskCompletion($userId, $planId, $dayNo);
        if (($result['status'] ?? '') === 'error') {
            http_response_code($result['code'] ?? 400);
        }
        echo json_encode($result);
        exit;
    }

    public static function replacePlanJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $mockSessionId = trim($input['mock_session_id'] ?? '');

        if (empty($mockSessionId)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing mock_session_id']);
            exit;
        }

        $result = FixPlanService::replacePlan($userId, $mockSessionId);
        echo json_encode($result);
        exit;
    }

    public static function getDayQuestionsJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $planId = (int)($_GET['plan_id'] ?? 0);
        $dayNo = (int)($_GET['day_no'] ?? 0);

        $db = Database::getConnection();
        $planStmt = $db->prepare("SELECT * FROM fix_plans WHERE id = ? LIMIT 1");
        $planStmt->execute([$planId]);
        $plan = $planStmt->fetch(PDO::FETCH_ASSOC);

        if (!$plan || (int)$plan['user_id'] !== $userId) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Forbidden']);
            exit;
        }

        $dayStmt = $db->prepare("SELECT * FROM fix_plan_days WHERE plan_id = ? AND day_no = ? LIMIT 1");
        $dayStmt->execute([$planId, $dayNo]);
        $day = $dayStmt->fetch(PDO::FETCH_ASSOC);

        if (!$day) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Day not found']);
            exit;
        }

        $targetCount = (int)$day['target_count'];
        $taskType = $day['task_type'];
        $subtopicId = $day['subtopic_id'] ? (int)$day['subtopic_id'] : null;
        $weakest = json_decode($plan['weakest_subtopics'] ?? '[]', true) ?: [];
        $weakSubtopicIds = array_filter(array_column($weakest, 'subtopic_id'));

        $questions = [];
        if ($taskType === 'targeted_practice' && $subtopicId) {
            $qStmt = $db->prepare("SELECT id, question_text, option_a, option_b, option_c, option_d, code_snippet, language FROM questions WHERE subtopic_id = ? AND is_published = 1 ORDER BY RAND() LIMIT ?");
            $qStmt->bindValue(1, $subtopicId, PDO::PARAM_INT);
            $qStmt->bindValue(2, $targetCount, PDO::PARAM_INT);
            $qStmt->execute();
            $questions = $qStmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($taskType === 'retest' && !empty($weakSubtopicIds)) {
            $inClause = implode(',', array_fill(0, count($weakSubtopicIds), '?'));
            $qStmt = $db->prepare("SELECT id, question_text, option_a, option_b, option_c, option_d, code_snippet, language FROM questions WHERE subtopic_id IN ($inClause) AND is_published = 1 ORDER BY RAND() LIMIT ?");
            $idx = 1;
            foreach ($weakSubtopicIds as $sid) {
                $qStmt->bindValue($idx++, $sid, PDO::PARAM_INT);
            }
            $qStmt->bindValue($idx, $targetCount, PDO::PARAM_INT);
            $qStmt->execute();
            $questions = $qStmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // General or mistake review fallback
            $qStmt = $db->prepare("SELECT id, question_text, option_a, option_b, option_c, option_d, code_snippet, language FROM questions WHERE is_published = 1 ORDER BY RAND() LIMIT ?");
            $qStmt->bindValue(1, $targetCount, PDO::PARAM_INT);
            $qStmt->execute();
            $questions = $qStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode([
            'status' => 'success',
            'day' => $day,
            'questions' => $questions
        ]);
        exit;
    }
}
