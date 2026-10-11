<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Services/WalletService.php';

class DiagnosticController {

    /**
     * Get 12 mixed-topic questions for onboarding diagnostic test
     */
    public static function getDiagnosticTestJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $db = Database::getConnection();

        // Select 3 questions per topic category
        $stmt = $db->query("
            SELECT q.id, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, q.est_time_seconds,
                   s.id as subtopic_id, s.name as subtopic_name, t.name as topic_name
            FROM questions q
            JOIN subtopics s ON q.subtopic_id = s.id
            JOIN topics t ON s.topic_id = t.id
            ORDER BY RAND() LIMIT 12
        ");
        $questions = $stmt->fetchAll();

        echo json_encode([
            'status' => 'success',
            'duration_seconds' => 300, // 5 minute diagnostic
            'questions' => array_map(function($q) {
                return [
                    'id' => $q['id'],
                    'subtopic_id' => $q['subtopic_id'],
                    'topic_name' => $q['topic_name'],
                    'subtopic_name' => $q['subtopic_name'],
                    'question_text' => $q['question_text'],
                    'options' => [
                        'A' => $q['option_a'],
                        'B' => $q['option_b'],
                        'C' => $q['option_c'],
                        'D' => $q['option_d']
                    ]
                ];
            }, $questions)
        ]);
        exit;
    }

    /**
     * Evaluate diagnostic test and set initial mastery levels in user_progress
     */
    public static function submitDiagnosticTestJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $answers = $input['answers'] ?? []; // Map of question_id => selected_option

        if (empty($answers)) {
            echo json_encode(['status' => 'error', 'message' => 'No answers submitted']);
            exit;
        }

        $db = Database::getConnection();
        $questionIds = array_map('intval', array_keys($answers));
        $inClause = implode(',', array_fill(0, count($questionIds), '?'));

        $stmt = $db->prepare("SELECT * FROM questions WHERE id IN ($inClause)");
        $stmt->execute($questionIds);
        $questions = $stmt->fetchAll();

        $subtopicStats = [];
        $correctCount = 0;
        $totalCount = count($questions);

        foreach ($questions as $q) {
            $subId = $q['subtopic_id'];
            if (!isset($subtopicStats[$subId])) {
                $subtopicStats[$subId] = ['total' => 0, 'correct' => 0];
            }
            $subtopicStats[$subId]['total']++;

            $userAns = $answers[$q['id']] ?? null;
            $isCorrect = ($userAns && $userAns === $q['correct_option']);
            if ($isCorrect) {
                $correctCount++;
                $subtopicStats[$subId]['correct']++;
            }

            if ($userId && $userAns) {
                $db->prepare("
                    INSERT INTO attempts (user_id, question_id, selected_option, is_correct, mode)
                    VALUES (?, ?, ?, ?, 'practice')
                ")->execute([$userId, $q['id'], $userAns, $isCorrect ? 1 : 0]);
            }
        }

        if ($userId) {
            // Update starting mastery levels in user_progress based on diagnostic accuracy
            foreach ($subtopicStats as $subId => $stat) {
                $accuracy = ($stat['total'] > 0) ? ($stat['correct'] / $stat['total']) : 0;
                $startingLevel = 1;
                if ($accuracy >= 0.8) $startingLevel = 3;
                else if ($accuracy >= 0.5) $startingLevel = 2;

                $db->prepare("
                    INSERT INTO user_progress (user_id, subtopic_id, total_attempted, total_correct, current_mastery_level)
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    total_attempted = total_attempted + VALUES(total_attempted),
                    total_correct = total_correct + VALUES(total_correct),
                    current_mastery_level = GREATEST(current_mastery_level, VALUES(current_mastery_level))
                ")->execute([$userId, $subId, $stat['total'], $stat['correct'], $startingLevel]);
            }

            // Anti-Exploit: Mark onboarding completed atomically; award bonus ONLY ONCE!
            $updateStmt = $db->prepare("UPDATE users SET onboarding_completed = 1 WHERE id = ? AND (onboarding_completed = 0 OR onboarding_completed IS NULL)");
            $updateStmt->execute([$userId]);

            $bonusAwarded = false;
            if ($updateStmt->rowCount() > 0) {
                User::addXP($userId, 250);
                WalletService::addCoins($userId, 50, 'admin_grant', 'Onboarding Diagnostic Completion Bonus');
                $bonusAwarded = true;
            }
        }

        echo json_encode([
            'status' => 'success',
            'correct_count' => $correctCount,
            'total_questions' => $totalCount,
            'accuracy_percent' => round(($correctCount / max(1, $totalCount)) * 100),
            'bonus_xp' => isset($bonusAwarded) && $bonusAwarded ? 250 : 0,
            'bonus_coins' => isset($bonusAwarded) && $bonusAwarded ? 50 : 0
        ]);
        exit;
    }
}
