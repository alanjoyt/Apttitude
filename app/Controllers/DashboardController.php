<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Services/GamificationEngine.php';
require_once __DIR__ . '/../Services/SpacedRepetitionService.php';
require_once __DIR__ . '/../Models/User.php';

class DashboardController {

    public static function getDashboardData(int $userId): array {
        $db = Database::getConnection();

        $user = User::findById($userId);
        if (!$user) return [];

        $tierProgress = GamificationEngine::getTierProgress($user['total_xp']);

        // Fetch Topic Proficiency Radar data
        $stmt = $db->prepare("
            SELECT t.name as topic_name, 
                   COUNT(a.id) as total_attempts,
                   SUM(a.is_correct) as correct_attempts,
                   ROUND(AVG(a.time_taken_seconds)) as avg_speed_seconds
            FROM topics t
            JOIN subtopics s ON t.id = s.topic_id
            JOIN questions q ON s.id = q.subtopic_id
            LEFT JOIN attempts a ON q.id = a.question_id AND a.user_id = ?
            GROUP BY t.id, t.name
        ");
        $stmt->execute([$userId]);
        $topicStats = $stmt->fetchAll();

        // Weak topics deck count
        $weakDeck = SpacedRepetitionService::getWeakTopicsDeck($userId, 10);

        // Fetch Mistake Breakdown by category tag
        $mistakeStmt = $db->prepare("
            SELECT mistake_type, COUNT(*) as total_count
            FROM attempts
            WHERE user_id = ? AND mistake_type IS NOT NULL
            GROUP BY mistake_type
        ");
        $mistakeStmt->execute([$userId]);
        $mistakeBreakdown = $mistakeStmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return [
            'user' => $user,
            'tier_progress' => $tierProgress,
            'topic_stats' => $topicStats,
            'weak_deck_count' => count($weakDeck),
            'weak_deck' => $weakDeck,
            'mistake_breakdown' => $mistakeBreakdown
        ];
    }
}
