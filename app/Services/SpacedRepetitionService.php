<?php
require_once __DIR__ . '/../../config/db.php';

class SpacedRepetitionService {

    /**
     * Record progress after an attempt & schedule next revision date weighted by confidence
     */
    public static function recordAttemptProgress(int $userId, int $subtopicId, bool $isCorrect, string $confidence = 'sure'): void {
        $db = Database::getConnection();

        // Calculate days until next review based on correctness and confidence tag
        $daysUntilReview = 7;
        if (!$isCorrect) {
            $daysUntilReview = match ($confidence) {
                'sure' => 1,       // Confident but wrong = High priority review tomorrow!
                'not_sure' => 2,   // Unsure & wrong = Review in 2 days
                'guessing' => 3,   // Guessing & wrong = Review in 3 days
                default => 1
            };
        } else {
            $daysUntilReview = ($confidence === 'guessing') ? 3 : 7;
        }

        $nextReviewAt = date('Y-m-d H:i:s', strtotime("+$daysUntilReview days"));

        $stmt = $db->prepare("
            INSERT INTO user_progress (user_id, subtopic_id, total_attempted, total_correct, current_mastery_level, next_review_at)
            VALUES (?, ?, 1, ?, 1, ?)
            ON DUPLICATE KEY UPDATE 
                total_attempted = total_attempted + 1,
                total_correct = total_correct + VALUES(total_correct),
                current_mastery_level = LEAST(5, GREATEST(1, FLOOR((total_correct + VALUES(total_correct)) / GREATEST(1, total_attempted + 1) * 5))),
                next_review_at = VALUES(next_review_at)
        ");

        $stmt->execute([$userId, $subtopicId, $isCorrect ? 1 : 0, $nextReviewAt]);
    }

    /**
     * Get deck of questions due for revision for the student
     */
    public static function getWeakTopicsDeck(int $userId, int $limit = 5): array {
        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT DISTINCT q.*, s.name as subtopic_name, t.name as topic_name, a.confidence, a.attempted_at
            FROM attempts a
            JOIN questions q ON a.question_id = q.id
            JOIN subtopics s ON q.subtopic_id = s.id
            JOIN topics t ON s.topic_id = t.id
            WHERE a.user_id = ? 
              AND a.is_correct = 0
            ORDER BY 
              CASE WHEN a.confidence = 'sure' THEN 1 WHEN a.confidence = 'not_sure' THEN 2 ELSE 3 END ASC,
              a.attempted_at DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
