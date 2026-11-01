<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/User.php';

class StudentSubmissionController {

    /**
     * Submit question from recent campus placement drive
     */
    public static function submitQuestionJSON(bool $returnArray = false) {
        if (!$returnArray) header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $companyName = trim($input['company_name'] ?? '');
        $driveDate = !empty($input['drive_date']) ? trim($input['drive_date']) : date('Y-m-d');
        $roundPattern = trim($input['round_pattern'] ?? 'Written Assessment');
        $topicName = trim($input['topic_name'] ?? 'General Aptitude');
        $difficulty = strtolower(trim($input['difficulty'] ?? 'medium'));
        if (!in_array($difficulty, ['easy', 'medium', 'hard'])) $difficulty = 'medium';

        $questionText = trim($input['question_text'] ?? '');
        $codeSnippet = !empty($input['code_snippet']) ? trim($input['code_snippet']) : null;
        $language = !empty($input['language']) ? strtolower(trim($input['language'])) : null;
        $optA = trim($input['option_a'] ?? '');
        $optB = trim($input['option_b'] ?? '');
        $optC = trim($input['option_c'] ?? '');
        $optD = trim($input['option_d'] ?? '');
        $correctOpt = strtoupper(trim($input['correct_option'] ?? 'A'));
        if (!in_array($correctOpt, ['A', 'B', 'C', 'D'])) $correctOpt = 'A';
        $explanation = trim($input['explanation'] ?? 'Student-submitted placement memory question.');

        if (empty($companyName) || empty($questionText) || empty($optA) || empty($optB)) {
            $err = [
                'status' => 'error',
                'message' => 'Please provide company name, question text, and at least options A & B.'
            ];
            if ($returnArray) return $err;
            echo json_encode($err);
            exit;
        }

        $db = Database::getConnection();

        // Check if identical question already submitted or existing in questions
        $existingCheck = $db->prepare("
            SELECT COUNT(*) FROM student_question_submissions 
            WHERE question_text = ? AND company_name = ?
        ");
        $existingCheck->execute([$questionText, $companyName]);
        if ((int)$existingCheck->fetchColumn() > 0) {
            $err = [
                'status' => 'error',
                'message' => 'This question has already been submitted for review. Thank you!'
            ];
            if ($returnArray) return $err;
            echo json_encode($err);
            exit;
        }

        $stmt = $db->prepare("
            INSERT INTO student_question_submissions
            (user_id, company_name, drive_date, round_pattern, topic_name, difficulty, question_text, code_snippet, language, option_a, option_b, option_c, option_d, correct_option, explanation, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([
            $userId, $companyName, $driveDate, $roundPattern, $topicName, $difficulty,
            $questionText, $codeSnippet, $language, $optA, $optB, $optC, $optD, $correctOpt, $explanation
        ]);

        $res = [
            'status' => 'success',
            'message' => 'Question submitted successfully! Our content moderators will review and publish it to the company question bank.'
        ];
        if ($returnArray) return $res;
        echo json_encode($res);
        exit;
    }
}
