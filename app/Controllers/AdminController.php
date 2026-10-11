<?php
require_once __DIR__ . '/../../config/db.php';

class AdminController {

    /**
     * Submit question report from user via AJAX
     */
    public static function submitReportJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $questionId = (int)($input['question_id'] ?? 0);
        $reason = $input['reason'] ?? 'wrong_answer';
        $comment = trim($input['comment'] ?? '');

        if (!$questionId) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid question ID']);
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO question_reports (question_id, user_id, reason, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$questionId, $userId, $reason, $comment]);

        echo json_encode(['status' => 'success', 'message' => 'Thank you! The content team has received your report.']);
        exit;
    }

    /**
     * Fetch pending question reports for Admin CMS
     */
    public static function getPendingReports(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT r.*, q.question_text, u.name as reporter_name 
            FROM question_reports r
            JOIN questions q ON r.question_id = q.id
            LEFT JOIN users u ON r.user_id = u.id
            WHERE r.status = 'pending'
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    private static function checkAdminAuth(): void {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Login required.']);
            exit;
        }
        require_once __DIR__ . '/../Models/User.php';
        $user = User::findById((int)$userId);
        if (!$user || ($user['role'] ?? 'user') !== 'admin') {
            echo json_encode(['status' => 'error', 'message' => 'Forbidden. Admin privileges required.']);
            exit;
        }
    }

    /**
     * Toggle Developer Mode state via Admin AJAX
     */
    public static function toggleDevModeJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        self::checkAdminAuth();

        require_once __DIR__ . '/../Services/DeviceLockService.php';

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $enabled = isset($input['enabled']) ? (bool)$input['enabled'] : !DeviceLockService::isDeveloperMode();

        $success = DeviceLockService::setDeveloperMode($enabled);
        $currentState = DeviceLockService::isDeveloperMode();

        if ($success) {
            echo json_encode([
                'status' => 'success',
                'developer_mode' => $currentState,
                'message' => $currentState 
                    ? 'Developer Mode ENABLED: Multi-account testing unlocked on 1 device.' 
                    : 'Developer Mode DISABLED: Strict single-device lock active.'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update system settings.']);
        }
        exit;
    }

    /**
     * Import bulk questions from uploaded CSV file
     */
    public static function importCSVQuestionsJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        self::checkAdminAuth();

        if (empty($_FILES['csv_file']['tmp_name'])) {
            echo json_encode(['status' => 'error', 'message' => 'Please select a valid CSV file to upload.']);
            exit;
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');
        if (!$handle) {
            echo json_encode(['status' => 'error', 'message' => 'Unable to read uploaded CSV file.']);
            exit;
        }

        $header = fgetcsv($handle);
        $db = Database::getConnection();

        // Get subtopic mapping by code
        $subtopicStmt = $db->query("SELECT id, code FROM subtopics");
        $subtopicMap = [];
        while ($row = $subtopicStmt->fetch()) {
            $subtopicMap[$row['code']] = (int)$row['id'];
        }

        $stmt = $db->prepare("
            INSERT INTO questions 
            (subtopic_id, difficulty, question_text, option_a, option_b, option_c, option_d, correct_option, explanation, source_tag)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $importedCount = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 9) continue;
            
            $subtopicCode = trim($row[1] ?? 'QUANT_SDT');
            $subtopicId = $subtopicMap[$subtopicCode] ?? 1;
            $difficulty = strtolower(trim($row[2] ?? 'medium'));
            if (!in_array($difficulty, ['easy', 'medium', 'hard'])) $difficulty = 'medium';

            $questionText = trim($row[3] ?? '');
            $optA = trim($row[4] ?? '');
            $optB = trim($row[5] ?? '');
            $optC = trim($row[6] ?? '');
            $optD = trim($row[7] ?? '');
            $correctOpt = strtoupper(trim($row[8] ?? 'A'));
            if (!in_array($correctOpt, ['A', 'B', 'C', 'D'])) $correctOpt = 'A';

            $explanation = trim($row[9] ?? 'Step-by-step solution.');
            $sourceTag = trim($row[10] ?? 'CSV Import');

            if (!empty($questionText) && !empty($optA)) {
                $stmt->execute([
                    $subtopicId, $difficulty, $questionText, $optA, $optB, $optC, $optD, $correctOpt, $explanation, $sourceTag
                ]);
                $importedCount++;
            }
        }
        fclose($handle);

        echo json_encode([
            'status' => 'success',
            'imported_count' => $importedCount,
            'message' => "Successfully imported {$importedCount} questions into Apttitudee question bank!"
        ]);
        exit;
    }
}


