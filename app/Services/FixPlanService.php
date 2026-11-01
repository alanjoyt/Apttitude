<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/WalletService.php';

class FixPlanService {

    /**
     * Get active plan summary for dashboard widget
     */
    public static function getActivePlanSummary(int $userId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM fix_plans WHERE user_id = ? AND status = 'active' ORDER BY id DESC LIMIT 1");
        $stmt->execute([$userId]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$plan) {
            return null;
        }

        if ($plan['plan_type'] === 'needs_data') {
            return [
                'id' => (int)$plan['id'],
                'plan_type' => 'needs_data',
                'status' => $plan['status'],
                'message' => 'Complete another mock test to unlock your personalized 7-Day Fix Plan.'
            ];
        }

        // Auto-refresh day unlock states based on IST calendar days
        self::refreshDayUnlockStates($plan);

        // Fetch days
        $daysStmt = $db->prepare("SELECT * FROM fix_plan_days WHERE plan_id = ? ORDER BY day_no ASC");
        $daysStmt->execute([$plan['id']]);
        $days = $daysStmt->fetchAll(PDO::FETCH_ASSOC);

        // Identify current pending or active day
        $currentDay = null;
        $completedCount = 0;
        foreach ($days as $day) {
            if ($day['status'] === 'completed') {
                $completedCount++;
            } elseif ($day['status'] === 'pending' && $currentDay === null) {
                $currentDay = $day;
            }
        }

        // If all 7 days completed
        if ($completedCount >= 7) {
            $currentDay = end($days);
        } elseif ($currentDay === null) {
            // First locked day if all pending completed
            foreach ($days as $day) {
                if ($day['status'] === 'locked') {
                    $currentDay = $day;
                    break;
                }
            }
            if ($currentDay === null) {
                $currentDay = $days[0] ?? null;
            }
        }

        $weakSubtopics = json_decode($plan['weakest_subtopics'] ?? '[]', true) ?: [];

        return [
            'id' => (int)$plan['id'],
            'mock_session_id' => $plan['mock_session_id'],
            'plan_type' => $plan['plan_type'],
            'status' => $plan['status'],
            'baseline_accuracy' => (float)$plan['baseline_accuracy'],
            'retest_accuracy' => $plan['retest_accuracy'] !== null ? (float)$plan['retest_accuracy'] : null,
            'started_at' => $plan['started_at'],
            'completed_count' => $completedCount,
            'current_day' => $currentDay,
            'days' => $days,
            'weakest_subtopics' => $weakSubtopics
        ];
    }

    /**
     * Refresh unlock states of days based on IST calendar days and sequential progress
     */
    public static function refreshDayUnlockStates(array &$plan): void {
        $db = Database::getConnection();
        $tz = new DateTimeZone('Asia/Kolkata');

        $startedAt = new DateTime($plan['started_at'], $tz);
        $now = new DateTime('now', $tz);

        $startDateStr = $startedAt->format('Y-m-d');
        $nowDateStr = $now->format('Y-m-d');

        $startCal = new DateTime($startDateStr, $tz);
        $nowCal = new DateTime($nowDateStr, $tz);

        $diffDays = (int)$startCal->diff($nowCal)->format('%r%a');
        $maxAllowedDay = max(1, min(7, 1 + $diffDays));

        // Fetch days ordered
        $stmt = $db->prepare("SELECT id, day_no, status FROM fix_plan_days WHERE plan_id = ? ORDER BY day_no ASC");
        $stmt->execute([$plan['id']]);
        $days = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $priorCompleted = true; // Day 1 doesn't need a prior day
        foreach ($days as $d) {
            $dayNo = (int)$d['day_no'];
            $status = $d['status'];

            if ($dayNo === 1 && $status === 'locked') {
                $db->prepare("UPDATE fix_plan_days SET status = 'pending', unlocked_at = NOW() WHERE id = ?")->execute([$d['id']]);
                $status = 'pending';
            } elseif ($dayNo > 1 && $status === 'locked' && $priorCompleted && $dayNo <= $maxAllowedDay) {
                $db->prepare("UPDATE fix_plan_days SET status = 'pending', unlocked_at = NOW() WHERE id = ?")->execute([$d['id']]);
                $status = 'pending';
            }

            $priorCompleted = ($status === 'completed');
        }
    }

    /**
     * Process mock submission: check idempotency, analyze attempts, and create/offer fix plan
     */
    public static function processMockSubmission(int $userId, string $mockSessionId, float $scorePercent, bool $forceReplace = false): array {
        $db = Database::getConnection();

        // 1. Idempotency check: check if plan already exists for this mock_session_id
        $checkStmt = $db->prepare("SELECT * FROM fix_plans WHERE mock_session_id = ? LIMIT 1");
        $checkStmt->execute([$mockSessionId]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
        if ($existing) {
            return [
                'status' => 'success',
                'action' => 'loaded_existing',
                'plan' => self::getActivePlanSummary($userId)
            ];
        }

        // 2. Check if user already has an active plan
        $activeStmt = $db->prepare("SELECT * FROM fix_plans WHERE user_id = ? AND status = 'active' LIMIT 1");
        $activeStmt->execute([$userId]);
        $activePlan = $activeStmt->fetch(PDO::FETCH_ASSOC);

        if ($activePlan && !$forceReplace) {
            // An active plan exists; analyze this mock to return a preview offer
            $analysis = self::analyzeMockWeaknesses($userId, $mockSessionId);
            return [
                'status' => 'has_active_plan',
                'active_plan_id' => (int)$activePlan['id'],
                'active_plan_started' => $activePlan['started_at'],
                'new_mock_session_id' => $mockSessionId,
                'can_replace' => true,
                'proposed_plan' => $analysis
            ];
        }

        // 3. If forceReplace and active plan exists, archive it
        if ($activePlan && $forceReplace) {
            $db->prepare("UPDATE fix_plans SET status = 'replaced' WHERE id = ?")->execute([$activePlan['id']]);
        }

        // 4. Create the new plan
        return self::createPlan($userId, $mockSessionId, $scorePercent);
    }

    /**
     * Analyze subtopics from mock attempt data
     */
    public static function analyzeMockWeaknesses(int $userId, string $mockSessionId): array {
        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT 
                a.id, a.is_correct, a.confidence, a.time_taken_seconds,
                q.id AS question_id, q.subtopic_id,
                COALESCE(s.name, 'General') AS subtopic_name,
                COALESCE(t.name, 'Aptitude') AS topic_name
            FROM attempts a
            JOIN questions q ON a.question_id = q.id
            LEFT JOIN subtopics s ON q.subtopic_id = s.id
            LEFT JOIN topics t ON s.topic_id = t.id
            WHERE a.mock_session_id = ? AND a.user_id = ?
        ");
        $stmt->execute([$mockSessionId, $userId]);
        $attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stats = [];
        foreach ($attempts as $att) {
            $sId = (int)$att['subtopic_id'];
            if (!isset($stats[$sId])) {
                $stats[$sId] = [
                    'subtopic_id' => $sId,
                    'subtopic_name' => $att['subtopic_name'],
                    'topic_name' => $att['topic_name'],
                    'total' => 0,
                    'correct' => 0,
                    'wrong' => 0,
                    'sure_but_wrong' => 0,
                    'lucky_guesses' => 0,
                    'total_time' => 0
                ];
            }

            $stats[$sId]['total']++;
            $isCorrect = (int)$att['is_correct'] === 1;
            $conf = strtolower(trim((string)($att['confidence'] ?? 'sure')));
            $time = max(0, (int)$att['time_taken_seconds']);

            if ($isCorrect) {
                $stats[$sId]['correct']++;
                if ($conf === 'guessing') {
                    $stats[$sId]['lucky_guesses']++;
                }
            } else {
                $stats[$sId]['wrong']++;
                if ($conf === 'sure') {
                    $stats[$sId]['sure_but_wrong']++;
                }
            }
            $stats[$sId]['total_time'] += $time;
        }

        foreach ($stats as $sId => &$s) {
            $s['accuracy'] = round(($s['correct'] / $s['total']) * 100, 2);
            $s['avg_time'] = round($s['total_time'] / $s['total'], 1);
        }
        unset($s);

        // Minimum sample filter: at least 3 questions in a subtopic
        $eligible = array_filter($stats, function($item) {
            return $item['total'] >= 3;
        });

        // Sort eligible subtopics by weakness
        usort($eligible, function($a, $b) {
            // Primary: accuracy ASC (lower is weaker)
            if ($a['accuracy'] !== $b['accuracy']) {
                return $a['accuracy'] <=> $b['accuracy'];
            }
            // Tiebreak 1: sure_but_wrong DESC (more sure-but-wrong = weaker)
            if ($a['sure_but_wrong'] !== $b['sure_but_wrong']) {
                return $b['sure_but_wrong'] <=> $a['sure_but_wrong'];
            }
            // Tiebreak 2: avg_time DESC (slower = weaker)
            if ($a['avg_time'] !== $b['avg_time']) {
                return $b['avg_time'] <=> $a['avg_time'];
            }
            // Tiebreak 3: subtopic_id ASC (deterministic)
            return $a['subtopic_id'] <=> $b['subtopic_id'];
        });

        return [
            'total_attempts' => count($attempts),
            'all_subtopics' => array_values($stats),
            'eligible_subtopics' => array_values($eligible),
            'has_sufficient_data' => count($eligible) >= 3
        ];
    }

    /**
     * Create fix plan row and 7 days
     */
    public static function createPlan(int $userId, string $mockSessionId, float $scorePercent): array {
        $db = Database::getConnection();

        $analysis = self::analyzeMockWeaknesses($userId, $mockSessionId);

        // Check if sufficient data
        if (!$analysis['has_sufficient_data']) {
            $insert = $db->prepare("
                INSERT INTO fix_plans (user_id, mock_session_id, plan_type, status, weakest_subtopics, baseline_accuracy, started_at)
                VALUES (?, ?, 'needs_data', 'active', '[]', 0.00, NOW())
            ");
            $insert->execute([$userId, $mockSessionId]);
            $planId = (int)$db->lastInsertId();

            return [
                'status' => 'success',
                'plan_type' => 'needs_data',
                'plan_id' => $planId,
                'message' => 'Not enough data yet. Complete another mock to unlock a personalized 7-day fix plan.'
            ];
        }

        $eligible = $analysis['eligible_subtopics'];
        $isChallenge = ($scorePercent >= 90.0);
        $planType = $isChallenge ? 'challenge' : 'standard';

        // Select top 3 weakest
        $weakest = array_slice($eligible, 0, 3);
        $s1 = $weakest[0];
        $s2 = $weakest[1];
        $s3 = $weakest[2];

        $baselineTotal = $s1['total'] + $s2['total'] + $s3['total'];
        $baselineCorrect = $s1['correct'] + $s2['correct'] + $s3['correct'];
        $baselineAccuracy = $baselineTotal > 0 ? round(($baselineCorrect / $baselineTotal) * 100, 2) : 0.00;

        $weakestJSON = json_encode([
            ['subtopic_id' => $s1['subtopic_id'], 'name' => $s1['subtopic_name'], 'topic' => $s1['topic_name'], 'accuracy' => $s1['accuracy'], 'total' => $s1['total']],
            ['subtopic_id' => $s2['subtopic_id'], 'name' => $s2['subtopic_name'], 'topic' => $s2['topic_name'], 'accuracy' => $s2['accuracy'], 'total' => $s2['total']],
            ['subtopic_id' => $s3['subtopic_id'], 'name' => $s3['subtopic_name'], 'topic' => $s3['topic_name'], 'accuracy' => $s3['accuracy'], 'total' => $s3['total']],
        ]);

        $db->beginTransaction();
        try {
            $planStmt = $db->prepare("
                INSERT INTO fix_plans (user_id, mock_session_id, plan_type, status, weakest_subtopics, baseline_accuracy, started_at)
                VALUES (?, ?, ?, 'active', ?, ?, NOW())
            ");
            $planStmt->execute([$userId, $mockSessionId, $planType, $weakestJSON, $baselineAccuracy]);
            $planId = (int)$db->lastInsertId();

            // Seed 7 days
            if ($isChallenge) {
                $days = [
                    [1, "Advanced Challenge: {$s1['subtopic_name']}", 'targeted_practice', $s1['subtopic_id'], 5, 'pending', 30, 10],
                    [2, "Advanced Challenge: {$s2['subtopic_name']}", 'targeted_practice', $s2['subtopic_id'], 5, 'locked', 30, 10],
                    [3, "High-Pace Speed Drill", 'speed_drill', null, 5, 'locked', 30, 10],
                    [4, "Advanced Challenge: {$s3['subtopic_name']}", 'targeted_practice', $s3['subtopic_id'], 5, 'locked', 30, 10],
                    [5, "Speed Mastery Drill", 'speed_drill', null, 5, 'locked', 30, 10],
                    [6, "Mistake Review & Synthesis", 'mistake_review', null, 5, 'locked', 30, 10],
                    [7, "15-Question Challenge Retest", 'retest', null, 15, 'locked', 50, 15],
                ];
            } else {
                $days = [
                    [1, "Targeted Practice: {$s1['subtopic_name']}", 'targeted_practice', $s1['subtopic_id'], 5, 'pending', 20, 5],
                    [2, "Targeted Practice: {$s2['subtopic_name']}", 'targeted_practice', $s2['subtopic_id'], 5, 'locked', 20, 5],
                    [3, "Mistake Review: Analyze Errors", 'mistake_review', null, 5, 'locked', 20, 5],
                    [4, "Targeted Practice: {$s3['subtopic_name']}", 'targeted_practice', $s3['subtopic_id'], 5, 'locked', 20, 5],
                    [5, "Speed Drill: Rapid Reasoning", 'speed_drill', null, 5, 'locked', 20, 5],
                    [6, "Reinforcement Practice", 'targeted_practice', $s1['subtopic_id'], 5, 'locked', 20, 5],
                    [7, "15-Question Retest: Measure Progress", 'retest', null, 15, 'locked', 40, 10],
                ];
            }

            $dayInsert = $db->prepare("
                INSERT INTO fix_plan_days (plan_id, day_no, title, task_type, subtopic_id, target_count, status, xp_reward, coin_reward, unlocked_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($days as $d) {
                $unlockedAt = ($d[5] === 'pending') ? date('Y-m-d H:i:s') : null;
                $dayInsert->execute([$planId, $d[0], $d[1], $d[2], $d[3], $d[4], $d[5], $d[6], $d[7], $unlockedAt]);
            }

            $db->commit();
            return [
                'status' => 'success',
                'plan_type' => $planType,
                'plan_id' => $planId,
                'baseline_accuracy' => $baselineAccuracy,
                'weakest_subtopics' => json_decode($weakestJSON, true)
            ];
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Server-side verification of task completion (anti-forgery & anti-tamper)
     */
    public static function verifyTaskCompletion(int $userId, int $planId, int $dayNo): array {
        $db = Database::getConnection();

        // 1. Ownership & IDOR check
        $planStmt = $db->prepare("SELECT * FROM fix_plans WHERE id = ? LIMIT 1");
        $planStmt->execute([$planId]);
        $plan = $planStmt->fetch(PDO::FETCH_ASSOC);

        if (!$plan) {
            return ['status' => 'error', 'code' => 404, 'message' => 'Fix plan not found'];
        }

        if ((int)$plan['user_id'] !== $userId) {
            return ['status' => 'error', 'code' => 403, 'message' => 'Unauthorized: plan does not belong to user'];
        }

        if ($plan['status'] !== 'active') {
            return ['status' => 'error', 'code' => 400, 'message' => 'Plan is not currently active'];
        }

        // Auto-refresh unlock states
        self::refreshDayUnlockStates($plan);

        // 2. Fetch specific day
        $dayStmt = $db->prepare("SELECT * FROM fix_plan_days WHERE plan_id = ? AND day_no = ? LIMIT 1");
        $dayStmt->execute([$planId, $dayNo]);
        $day = $dayStmt->fetch(PDO::FETCH_ASSOC);

        if (!$day) {
            return ['status' => 'error', 'code' => 404, 'message' => 'Day not found'];
        }

        if ($day['status'] === 'completed') {
            return ['status' => 'success', 'already_completed' => true, 'message' => 'Day is already completed'];
        }

        if ($day['status'] === 'locked') {
            return ['status' => 'error', 'code' => 400, 'message' => 'Day is locked. Complete prior days and wait for IST calendar day unlock.'];
        }

        // 3. SERVER VERIFICATION OF ATTEMPTS (Anti-tamper)
        $since = $day['unlocked_at'] ?: $plan['started_at'];
        $targetCount = (int)$day['target_count'];
        $taskType = $day['task_type'];
        $subtopicId = $day['subtopic_id'] ? (int)$day['subtopic_id'] : null;

        $verifiedCount = 0;
        $weakest = json_decode($plan['weakest_subtopics'] ?? '[]', true) ?: [];
        $weakSubtopicIds = array_filter(array_column($weakest, 'subtopic_id'));

        if ($taskType === 'targeted_practice') {
            if ($subtopicId) {
                $vStmt = $db->prepare("
                    SELECT COUNT(*) FROM attempts a
                    JOIN questions q ON a.question_id = q.id
                    WHERE a.user_id = ? AND q.subtopic_id = ? AND a.mode = 'practice' AND a.attempted_at >= DATE_SUB(?, INTERVAL 2 SECOND)
                ");
                $vStmt->execute([$userId, $subtopicId, $since]);
                $verifiedCount = (int)$vStmt->fetchColumn();
            } else {
                $inClause = implode(',', array_fill(0, count($weakSubtopicIds), '?'));
                $params = array_merge([$userId], $weakSubtopicIds, [$since]);
                $vStmt = $db->prepare("
                    SELECT COUNT(*) FROM attempts a
                    JOIN questions q ON a.question_id = q.id
                    WHERE a.user_id = ? AND q.subtopic_id IN ($inClause) AND a.mode = 'practice' AND a.attempted_at >= DATE_SUB(?, INTERVAL 2 SECOND)
                ");
                $vStmt->execute($params);
                $verifiedCount = (int)$vStmt->fetchColumn();
            }
        } elseif ($taskType === 'mistake_review') {
            // Count attempts after unlock on questions previously answered incorrectly
            $vStmt = $db->prepare("
                SELECT COUNT(*) FROM attempts a
                WHERE a.user_id = ? AND a.mode = 'practice' AND a.attempted_at >= DATE_SUB(?, INTERVAL 2 SECOND)
                  AND a.question_id IN (
                      SELECT DISTINCT question_id FROM attempts WHERE user_id = ? AND is_correct = 0
                  )
            ");
            $vStmt->execute([$userId, $since, $userId]);
            $verifiedCount = (int)$vStmt->fetchColumn();
        } elseif ($taskType === 'speed_drill') {
            // Count attempts with rapid answering (<= 45 seconds)
            $vStmt = $db->prepare("
                SELECT COUNT(*) FROM attempts a
                WHERE a.user_id = ? AND a.mode = 'practice' AND a.attempted_at >= DATE_SUB(?, INTERVAL 2 SECOND) AND a.time_taken_seconds > 0 AND a.time_taken_seconds <= 45
            ");
            $vStmt->execute([$userId, $since]);
            $verifiedCount = (int)$vStmt->fetchColumn();
        } elseif ($taskType === 'retest') {
            // 15 questions in the weak subtopics
            $inClause = implode(',', array_fill(0, count($weakSubtopicIds), '?'));
            $params = array_merge([$userId], $weakSubtopicIds, [$since]);
            $vStmt = $db->prepare("
                SELECT COUNT(*), COALESCE(SUM(a.is_correct), 0) FROM attempts a
                JOIN questions q ON a.question_id = q.id
                WHERE a.user_id = ? AND q.subtopic_id IN ($inClause) AND a.mode != 'mock' AND a.attempted_at >= DATE_SUB(?, INTERVAL 2 SECOND)
            ");
            $vStmt->execute($params);
            $row = $vStmt->fetch(PDO::FETCH_NUM);
            $verifiedCount = (int)($row[0] ?? 0);
            $retestCorrect = (int)($row[1] ?? 0);
        }

        if ($verifiedCount < $targetCount) {
            return [
                'status' => 'error',
                'code' => 400,
                'message' => "Insufficient verified attempts. Completed {$verifiedCount} of {$targetCount} required questions.",
                'progress' => $verifiedCount,
                'target' => $targetCount
            ];
        }

        // 4. Rewards & Ledger Write (FP5: written to ledger, daily cap)
        $coinReward = (int)$day['coin_reward'];
        $xpReward = (int)$day['xp_reward'];

        // Daily cap: 1 fix-plan coin reward per calendar day in IST
        $capStmt = $db->prepare("
            SELECT COUNT(*) FROM coin_transactions
            WHERE user_id = ? 
              AND transaction_type = 'fix_plan_reward'
              AND DATE(created_at) = CURRENT_DATE
        ");
        $capStmt->execute([$userId]);
        $alreadyRewardedToday = (int)$capStmt->fetchColumn() > 0;

        $awardedCoins = 0;
        if (!$alreadyRewardedToday && $coinReward > 0) {
            $coinSuccess = WalletService::addCoins($userId, $coinReward, 'fix_plan_reward', "7-Day Fix Plan Day {$dayNo} Reward");
            if ($coinSuccess) {
                $awardedCoins = $coinReward;
            }
        }

        if ($xpReward > 0) {
            User::addXP($userId, $xpReward);
        }

        // Mark Day completed
        $db->prepare("
            UPDATE fix_plan_days 
            SET status = 'completed', progress_count = ?, completed_at = NOW() 
            WHERE id = ?
        ")->execute([$verifiedCount, $day['id']]);

        // If Day 7: record retest accuracy & mark plan completed
        $retestAccuracy = null;
        if ($dayNo === 7 || $taskType === 'retest') {
            $retestAccuracy = $verifiedCount > 0 ? round(($retestCorrect / $verifiedCount) * 100, 2) : 0.00;
            $db->prepare("
                UPDATE fix_plans 
                SET retest_accuracy = ?, status = 'completed', completed_at = NOW() 
                WHERE id = ?
            ")->execute([$retestAccuracy, $planId]);
        }

        // Refresh next day unlock
        self::refreshDayUnlockStates($plan);

        return [
            'status' => 'success',
            'day_no' => $dayNo,
            'completed' => true,
            'xp_awarded' => $xpReward,
            'coins_awarded' => $awardedCoins,
            'retest_accuracy' => $retestAccuracy,
            'baseline_accuracy' => (float)$plan['baseline_accuracy']
        ];
    }

    /**
     * Replace active plan with a new one from a mock session
     */
    public static function replacePlan(int $userId, string $mockSessionId): array {
        return self::processMockSubmission($userId, $mockSessionId, 0.0, true);
    }
}
