<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/User.php';

class ProgressService {

    /**
     * Get consolidated progress report data for profile
     */
    public static function getProgressReport(int $userId, int $days = 30): array {
        $db = Database::getConnection();

        // 1. User Base Info
        $user = User::findById($userId);
        if (!$user) {
            return ['empty' => true];
        }

        // Check if mock_session_id column exists
        static $hasMockSessionId = null;
        if ($hasMockSessionId === null) {
            try {
                $cols = $db->query("SHOW COLUMNS FROM `attempts` LIKE 'mock_session_id'")->fetchAll();
                $hasMockSessionId = !empty($cols);
            } catch (\Throwable $e) {
                $hasMockSessionId = false;
            }
        }

        // Date Boundary for Range Filter
        $dateCondition = ($days > 0) ? "AND attempted_at >= DATE_SUB(NOW(), INTERVAL ? DAY)" : "";
        $queryParams = ($days > 0) ? [$userId, $days] : [$userId];

        // 2. Overview Statistics (Filtered by Range & Overall)
        $rangeStmt = $db->prepare("
            SELECT 
                COUNT(*) as range_attempts,
                SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as range_correct,
                AVG(CASE WHEN time_taken_seconds > 0 THEN time_taken_seconds ELSE NULL END) as range_avg_time
            FROM attempts
            WHERE user_id = ? $dateCondition
        ");
        $rangeStmt->execute($queryParams);
        $rangeStats = $rangeStmt->fetch(PDO::FETCH_ASSOC);

        $rangeAttempts = (int)($rangeStats['range_attempts'] ?? 0);
        $rangeCorrect = (int)($rangeStats['range_correct'] ?? 0);
        $rangeAccuracy = $rangeAttempts > 0 ? round(($rangeCorrect / $rangeAttempts) * 100, 1) : 0;
        $rangeAvgTime = $rangeStats['range_avg_time'] !== null ? round((float)$rangeStats['range_avg_time'], 1) : null;

        // Overall Lifetime Stats
        $overallStmt = $db->prepare("
            SELECT 
                COUNT(*) as total_attempts,
                SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as total_correct,
                COUNT(DISTINCT DATE(attempted_at)) as total_active_days,
                COUNT(DISTINCT CASE WHEN MONTH(attempted_at) = MONTH(NOW()) AND YEAR(attempted_at) = YEAR(NOW()) THEN DATE(attempted_at) END) as active_days_this_month
            FROM attempts
            WHERE user_id = ?
        ");
        $overallStmt->execute([$userId]);
        $overall = $overallStmt->fetch(PDO::FETCH_ASSOC);

        $totalAttempts = (int)($overall['total_attempts'] ?? 0);
        $totalCorrect = (int)($overall['total_correct'] ?? 0);
        $overallAccuracy = $totalAttempts > 0 ? round(($totalCorrect / $totalAttempts) * 100, 1) : 0;
        $activeDaysThisMonth = (int)($overall['active_days_this_month'] ?? 0);

        if ($totalAttempts === 0) {
            return [
                'empty' => true,
                'user' => $user,
                'streak' => (int)($user['current_streak'] ?? 0),
                'total_xp' => (int)($user['total_xp'] ?? 0),
                'tier' => $user['tier'] ?? 'Bronze'
            ];
        }

        // 3. Accuracy Trend (Daily data points for SVG line chart)
        $trendParams = ($days > 0) ? [$userId, $days] : [$userId, 365];
        $trendStmt = $db->prepare("
            SELECT 
                DATE(attempted_at) as attempt_date,
                COUNT(*) as attempts_count,
                SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_count
            FROM attempts
            WHERE user_id = ? AND attempted_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(attempted_at)
            ORDER BY attempt_date ASC
        ");
        $trendStmt->execute($trendParams);
        $trendRows = $trendStmt->fetchAll(PDO::FETCH_ASSOC);

        $trendPoints = [];
        foreach ($trendRows as $row) {
            $att = (int)$row['attempts_count'];
            $corr = (int)$row['correct_count'];
            $acc = $att > 0 ? round(($corr / $att) * 100) : 0;
            $trendPoints[] = [
                'date' => $row['attempt_date'],
                'label' => date('M j', strtotime($row['attempt_date'])),
                'attempts' => $att,
                'correct' => $corr,
                'accuracy' => $acc
            ];
        }

        // 4. Topic Strength Map (Grouped by Topic, sorted weakest first)
        $topicStmt = $db->prepare("
            SELECT 
                t.id as topic_id,
                t.name as topic_name,
                t.code as topic_code,
                COUNT(a.id) as attempts_count,
                SUM(CASE WHEN a.is_correct = 1 THEN 1 ELSE 0 END) as correct_count,
                AVG(CASE WHEN a.time_taken_seconds > 0 THEN a.time_taken_seconds ELSE NULL END) as avg_time_seconds,
                (
                    SELECT s.id 
                    FROM subtopics s 
                    WHERE s.topic_id = t.id 
                    ORDER BY s.id ASC 
                    LIMIT 1
                ) as default_subtopic_id
            FROM topics t
            LEFT JOIN subtopics s ON s.topic_id = t.id
            LEFT JOIN questions q ON q.subtopic_id = s.id
            LEFT JOIN attempts a ON a.question_id = q.id AND a.user_id = ?
            GROUP BY t.id, t.name, t.code
            ORDER BY t.id ASC
        ");
        $topicStmt->execute([$userId]);
        $topicRows = $topicStmt->fetchAll(PDO::FETCH_ASSOC);

        $topics = [];
        foreach ($topicRows as $t) {
            $tAtt = (int)$t['attempts_count'];
            $tCorr = (int)$t['correct_count'];
            $tAcc = $tAtt > 0 ? round(($tCorr / $tAtt) * 100, 1) : null;
            $tTime = $t['avg_time_seconds'] !== null ? round((float)$t['avg_time_seconds'], 1) : null;

            // Mastery level 1 to 5 based on accuracy and volume
            $mastery = 1;
            if ($tAtt >= 5) {
                if ($tAcc >= 85) $mastery = 5;
                elseif ($tAcc >= 70) $mastery = 4;
                elseif ($tAcc >= 55) $mastery = 3;
                elseif ($tAcc >= 40) $mastery = 2;
            }

            $topics[] = [
                'topic_id' => (int)$t['topic_id'],
                'name' => $t['topic_name'] ?? 'General',
                'code' => $t['topic_code'] ?? 'GENERAL',
                'attempts' => $tAtt,
                'correct' => $tCorr,
                'accuracy' => $tAcc,
                'avg_time' => $tTime,
                'mastery' => $mastery,
                'default_subtopic_id' => (int)($t['default_subtopic_id'] ?? 1),
                'is_low_sample' => ($tAtt > 0 && $tAtt < 5)
            ];
        }

        // Sort topics weakest first (topics with attempts and lowest accuracy come first)
        usort($topics, function($a, $b) {
            if ($a['attempts'] === 0 && $b['attempts'] === 0) return 0;
            if ($a['attempts'] === 0) return 1;
            if ($b['attempts'] === 0) return -1;
            return ($a['accuracy'] ?? 0) <=> ($b['accuracy'] ?? 0);
        });

        // 5. Time Analysis & Speed Trends (Compare last 14 days vs prior 14-28 days)
        $speedCurrentStmt = $db->prepare("
            SELECT AVG(time_taken_seconds) as current_avg
            FROM attempts
            WHERE user_id = ? AND time_taken_seconds > 0 AND attempted_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
        ");
        $speedCurrentStmt->execute([$userId]);
        $currentSpeed = $speedCurrentStmt->fetchColumn();

        $speedPriorStmt = $db->prepare("
            SELECT AVG(time_taken_seconds) as prior_avg
            FROM attempts
            WHERE user_id = ? AND time_taken_seconds > 0 
              AND attempted_at >= DATE_SUB(NOW(), INTERVAL 28 DAY)
              AND attempted_at < DATE_SUB(NOW(), INTERVAL 14 DAY)
        ");
        $speedPriorStmt->execute([$userId]);
        $priorSpeed = $speedPriorStmt->fetchColumn();

        $speedDelta = null;
        $speedTrendLabel = "Pacing baseline establishing";
        if ($currentSpeed !== false && $priorSpeed !== false && (float)$priorSpeed > 0) {
            $diff = round((float)$currentSpeed - (float)$priorSpeed, 1);
            $speedDelta = $diff;
            if ($diff < -1) {
                $speedTrendLabel = abs($diff) . "s faster than last month";
            } elseif ($diff > 1) {
                $speedTrendLabel = abs($diff) . "s slower (more deliberate pacing)";
            } else {
                $speedTrendLabel = "Steady pacing maintained";
            }
        }

        // 6. Mock History (Last 20 attempts grouped by session or minute)
        $mockList = [];
        if ($hasMockSessionId) {
            $mockHistoryStmt = $db->prepare("
                SELECT 
                    COALESCE(a.mock_session_id, DATE_FORMAT(a.attempted_at, '%Y-%m-%d %H:%i')) as mock_key,
                    COALESCE(a.pack_code, 'GENERAL_MOCK') as pack_code,
                    DATE(a.attempted_at) as attempt_date,
                    a.attempted_at as exact_time,
                    COUNT(a.id) as total_questions,
                    SUM(CASE WHEN a.is_correct = 1 THEN 1 ELSE 0 END) as correct_count,
                    SUM(CASE WHEN a.is_correct = 0 THEN 1 ELSE 0 END) as wrong_count,
                    SUM(a.time_taken_seconds) as total_time_seconds
                FROM attempts a
                WHERE a.user_id = ? AND a.mode = 'mock'
                GROUP BY mock_key, pack_code, attempt_date, exact_time
                ORDER BY exact_time DESC
                LIMIT 20
            ");
            $mockHistoryStmt->execute([$userId]);
            $mockRaw = $mockHistoryStmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $mockHistoryStmt = $db->prepare("
                SELECT 
                    DATE_FORMAT(a.attempted_at, '%Y-%m-%d %H:%i') as mock_key,
                    'GENERAL_MOCK' as pack_code,
                    DATE(a.attempted_at) as attempt_date,
                    a.attempted_at as exact_time,
                    COUNT(a.id) as total_questions,
                    SUM(CASE WHEN a.is_correct = 1 THEN 1 ELSE 0 END) as correct_count,
                    SUM(CASE WHEN a.is_correct = 0 THEN 1 ELSE 0 END) as wrong_count,
                    SUM(a.time_taken_seconds) as total_time_seconds
                FROM attempts a
                WHERE a.user_id = ? AND a.mode = 'mock'
                GROUP BY mock_key, attempt_date, exact_time
                ORDER BY exact_time DESC
                LIMIT 20
            ");
            $mockHistoryStmt->execute([$userId]);
            $mockRaw = $mockHistoryStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $packTitles = [
            'TCS_MOCK_PACK' => 'TCS NQT Pattern Mock',
            'INFOSYS_MOCK_PACK' => 'Infosys Placement Pack',
            'WIPRO_MOCK_PACK' => 'Wipro NLTH Challenge',
            'INNOVATURE_MOCK_PACK' => 'Innovature Labs Mock',
            'GENERAL_MOCK' => 'Placement Practice Mock'
        ];

        $bestScore = 0;
        foreach ($mockRaw as $m) {
            $tot = (int)$m['total_questions'];
            $corr = (int)$m['correct_count'];
            $wrg = (int)$m['wrong_count'];
            $marks = round(($corr * 1.0) - ($wrg * 0.25), 2);
            $acc = $tot > 0 ? round(($corr / $tot) * 100) : 0;
            $timeSec = (int)$m['total_time_seconds'];

            if ($marks > $bestScore) $bestScore = $marks;

            $band = 'Needs Practice';
            if ($acc >= 75) $band = 'Distinction';
            elseif ($acc >= 60) $band = 'First Class';
            elseif ($acc >= 40) $band = 'Pass';

            $mockList[] = [
                'session_key' => $m['mock_key'],
                'pack_code' => $m['pack_code'],
                'title' => $packTitles[$m['pack_code']] ?? 'Placement Mock Exam',
                'date' => date('M j, Y', strtotime($m['exact_time'])),
                'total_questions' => $tot,
                'correct_count' => $corr,
                'wrong_count' => $wrg,
                'score_marks' => $marks,
                'accuracy' => $acc,
                'total_time_seconds' => $timeSec,
                'has_time' => ($timeSec > 0),
                'band' => $band
            ];
        }

        // 7. Activity Calendar Data (Dates practiced in the last 30 days)
        $calStmt = $db->prepare("
            SELECT DISTINCT DATE(attempted_at) as active_date
            FROM attempts
            WHERE user_id = ? AND attempted_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $calStmt->execute([$userId]);
        $activeCalendarDates = $calStmt->fetchAll(PDO::FETCH_COLUMN);

        return [
            'empty' => false,
            'user' => $user,
            'selected_days' => $days,
            'overview' => [
                'range_attempts' => $rangeAttempts,
                'range_correct' => $rangeCorrect,
                'range_accuracy' => $rangeAccuracy,
                'range_avg_time' => $rangeAvgTime,
                'total_attempts' => $totalAttempts,
                'total_correct' => $totalCorrect,
                'overall_accuracy' => $overallAccuracy,
                'active_days_this_month' => $activeDaysThisMonth,
                'current_streak' => (int)($user['current_streak'] ?? 0),
                'best_streak' => (int)($user['current_streak'] ?? 0),
                'total_xp' => (int)($user['total_xp'] ?? 0),
                'tier' => $user['tier'] ?? 'Bronze'
            ],
            'trend' => [
                'points' => $trendPoints,
                'has_enough_data' => count($trendPoints) >= 2 && $totalAttempts >= 10
            ],
            'topics' => $topics,
            'time_analysis' => [
                'current_avg_seconds' => $currentSpeed !== false ? round((float)$currentSpeed, 1) : null,
                'prior_avg_seconds' => $priorSpeed !== false ? round((float)$priorSpeed, 1) : null,
                'speed_delta' => $speedDelta,
                'speed_trend_label' => $speedTrendLabel
            ],
            'mocks' => [
                'list' => $mockList,
                'count' => count($mockList),
                'best_score' => $bestScore
            ],
            'calendar' => [
                'active_dates' => $activeCalendarDates
            ]
        ];
    }
}
