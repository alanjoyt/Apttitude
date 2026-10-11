<?php
require_once __DIR__ . '/../../config/db.php';

class TechnicalController {

    /**
     * Get statistics of questions per language and user mastery
     */
    public static function getLanguageStatsJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $db = Database::getConnection();

        // 1. Total counts per language in database
        $stmt = $db->query("
            SELECT language, COUNT(*) as total_questions 
            FROM questions 
            WHERE language IS NOT NULL AND language != ''
            GROUP BY language
        ");
        $totalByLang = [];
        foreach ($stmt->fetchAll() as $row) {
            $totalByLang[strtolower($row['language'])] = (int)$row['total_questions'];
        }

        // 2. User attempts per language if logged in
        $userSolved = [];
        if ($userId) {
            $uStmt = $db->prepare("
                SELECT q.language, 
                       COUNT(a.id) as attempted, 
                       SUM(CASE WHEN a.is_correct = 1 THEN 1 ELSE 0 END) as correct
                FROM attempts a
                JOIN questions q ON a.question_id = q.id
                WHERE a.user_id = ? AND q.language IS NOT NULL
                GROUP BY q.language
            ");
            $uStmt->execute([$userId]);
            foreach ($uStmt->fetchAll() as $row) {
                $userSolved[strtolower($row['language'])] = [
                    'attempted' => (int)$row['attempted'],
                    'correct' => (int)$row['correct']
                ];
            }
        }

        // Core technical catalog: Programming Languages & IT Company PYQs
        $catalog = [
            'all' => ['name' => 'All Categories', 'icon' => 'bi-code-square', 'badge' => 'Full Stack', 'color' => '#6366f1', 'category' => 'all'],
            'c' => ['name' => 'C Language', 'icon' => 'bi-terminal', 'badge' => 'Low-Level Core', 'color' => '#0284c7', 'category' => 'language'],
            'cpp' => ['name' => 'C++', 'icon' => 'bi-cpu', 'badge' => 'OOP & Systems', 'color' => '#0369a1', 'category' => 'language'],
            'java' => ['name' => 'Java', 'icon' => 'bi-cup-hot-fill', 'badge' => 'Enterprise & JVM', 'color' => '#ea580c', 'category' => 'language'],
            'php' => ['name' => 'PHP', 'icon' => 'bi-filetype-php', 'badge' => 'Web Architecture', 'color' => '#7c3aed', 'category' => 'language'],
            'csharp' => ['name' => 'C# (.NET)', 'icon' => 'bi-microsoft', 'badge' => '.NET & Cloud', 'color' => '#4f46e5', 'category' => 'language'],
            'python' => ['name' => 'Python', 'icon' => 'bi-filetype-py', 'badge' => 'Scripting & AI', 'color' => '#16a34a', 'category' => 'language'],
            'sql' => ['name' => 'SQL & DBMS', 'icon' => 'bi-database-fill', 'badge' => 'Relational Data', 'color' => '#d97706', 'category' => 'language'],
            'dsa' => ['name' => 'DSA', 'icon' => 'bi-diagram-3-fill', 'badge' => 'Algorithms & Trees', 'color' => '#e11d48', 'category' => 'language'],
            'innovature' => ['name' => 'Innovature Labs PYQ', 'icon' => 'bi-building-fill-gear', 'badge' => 'Campus Drive 2025', 'color' => '#059669', 'category' => 'company']
        ];

        $result = [];
        $totalAllQuestions = 0;
        $totalAllSolved = 0;

        foreach ($catalog as $key => $meta) {
            $totalQ = ($key === 'all') ? array_sum($totalByLang) : ($totalByLang[$key] ?? 0);
            $userStats = ($key === 'all') 
                ? [
                    'attempted' => array_sum(array_column($userSolved, 'attempted')),
                    'correct' => array_sum(array_column($userSolved, 'correct'))
                ]
                : ($userSolved[$key] ?? ['attempted' => 0, 'correct' => 0]);

            $result[$key] = [
                'key' => $key,
                'name' => $meta['name'],
                'icon' => $meta['icon'],
                'badge' => $meta['badge'],
                'color' => $meta['color'],
                'category' => $meta['category'],
                'total_questions' => $totalQ,
                'user_attempted' => $userStats['attempted'],
                'user_correct' => $userStats['correct'],
                'progress_percent' => $totalQ > 0 ? min(100, round(($userStats['correct'] / $totalQ) * 100)) : 0
            ];
        }

        echo json_encode([
            'status' => 'success',
            'data' => $result
        ]);
        exit;
    }
}
