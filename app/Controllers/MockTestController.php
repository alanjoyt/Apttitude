<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Services/GamificationEngine.php';
require_once __DIR__ . '/../Services/QuestionParser.php';
require_once __DIR__ . '/../Models/User.php';

class MockTestController {

    private static ?bool $hasMockSessionIdCol = null;
    private static ?bool $hasPackCodeCol = null;
    private static ?bool $hasSkipReasonCol = null;
    private static ?bool $hasMockSessionsTable = null;

    /**
     * Inspect database columns safely to ensure zero crash if migration has not run yet.
     */
    private static function checkSchemaCapabilities(PDO $db): void {
        if (self::$hasMockSessionIdCol === null) {
            try {
                $cols = $db->query("SHOW COLUMNS FROM `attempts` LIKE 'mock_session_id'")->fetchAll();
                self::$hasMockSessionIdCol = !empty($cols);
            } catch (\Throwable $e) {
                self::$hasMockSessionIdCol = false;
            }
        }

        if (self::$hasPackCodeCol === null) {
            try {
                $cols = $db->query("SHOW COLUMNS FROM `attempts` LIKE 'pack_code'")->fetchAll();
                self::$hasPackCodeCol = !empty($cols);
            } catch (\Throwable $e) {
                self::$hasPackCodeCol = false;
            }
        }

        if (self::$hasSkipReasonCol === null) {
            try {
                $cols = $db->query("SHOW COLUMNS FROM `attempts` LIKE 'skip_reason'")->fetchAll();
                self::$hasSkipReasonCol = !empty($cols);
            } catch (\Throwable $e) {
                self::$hasSkipReasonCol = false;
            }
        }

        if (self::$hasMockSessionsTable === null) {
            try {
                $tables = $db->query("SHOW TABLES LIKE 'mock_sessions'")->fetchAll();
                self::$hasMockSessionsTable = !empty($tables);
            } catch (\Throwable $e) {
                self::$hasMockSessionsTable = false;
            }
        }
    }

    /**
     * Start a timed mock test set
     */
    public static function getMockTestJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $db = Database::getConnection();
        self::checkSchemaCapabilities($db);
        $packCode = trim($_GET['pack'] ?? '');
        $userId = $_SESSION['user_id'] ?? null;
        $userRole = $_SESSION['role'] ?? 'user';

        // Pack purchase gating: map packCode to unlockable_id
        $packUnlockableMap = [
            'TCS_MOCK_PACK' => 1,
            'INFOSYS_MOCK_PACK' => 2,
            'WIPRO_MOCK_PACK' => 3,
            'INNOVATURE_MOCK_PACK' => 9,
            'ACCENTURE_MOCK_PACK' => 10,
            'AMAZON_MOCK_PACK' => 11,
            'ZOHO_MOCK_PACK' => 12,
            'DELOITTE_MOCK_PACK' => 13,
            'CAPGEMINI_MOCK_PACK' => 14,
            'COGNIZANT_MOCK_PACK' => 15,
            'TECH_MAHINDRA_MOCK_PACK' => 16,
            'IBM_MOCK_PACK' => 17,
            'LTI_MINDTREE_MOCK_PACK' => 18,
            'UST_MOCK_PACK' => 19,
            'HCL_MOCK_PACK' => 20,
            'MPHASIS_MOCK_PACK' => 21,
            'HEXAWARE_MOCK_PACK' => 22,
            'PERSISTENT_MOCK_PACK' => 23,
            'VIRTUSA_MOCK_PACK' => 24,
            'ORACLE_MOCK_PACK' => 25,
            'COFORGE_MOCK_PACK' => 26,
        ];

        if (!empty($packCode)) {
            $unlockableId = $packUnlockableMap[$packCode] ?? null;
            // Also lookup dynamically by code if not found in static map
            if (!$unlockableId) {
                $uStmt = $db->prepare("SELECT id FROM unlockables WHERE code = ? LIMIT 1");
                $uStmt->execute([$packCode]);
                $foundId = $uStmt->fetchColumn();
                if ($foundId) $unlockableId = (int)$foundId;
            }

            if ($unlockableId) {
                // Admin always has preview / testing access
                if ($userRole !== 'admin') {
                    if (!$userId) {
                        http_response_code(401);
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Please log in to access this company mock pack.'
                        ]);
                        exit;
                    }
                    $checkUnlocked = $db->prepare("SELECT COUNT(*) FROM user_unlocks WHERE user_id = ? AND unlockable_id = ?");
                    $checkUnlocked->execute([$userId, $unlockableId]);
                    if ((int)$checkUnlocked->fetchColumn() === 0) {
                        http_response_code(403);
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'You have not unlocked this company mock pack yet. Please purchase it in the Speed Coins store.'
                        ]);
                        exit;
                    }
                }
            }
        }
        
        $tagMap = [
            'TCS_MOCK_PACK' => 'TCS',
            'INFOSYS_MOCK_PACK' => 'Infosys',
            'WIPRO_MOCK_PACK' => 'Wipro',
            'ACCENTURE_MOCK_PACK' => 'Accenture',
            'INNOVATURE_MOCK_PACK' => 'Innovature',
            'AMAZON_MOCK_PACK' => 'Amazon',
            'ZOHO_MOCK_PACK' => 'Zoho',
            'DELOITTE_MOCK_PACK' => 'Deloitte',
            'CAPGEMINI_MOCK_PACK' => 'Capgemini',
            'COGNIZANT_MOCK_PACK' => 'Cognizant',
            'TECH_MAHINDRA_MOCK_PACK' => 'Tech Mahindra',
            'IBM_MOCK_PACK' => 'IBM',
            'LTI_MINDTREE_MOCK_PACK' => 'LTI Mindtree',
            'UST_MOCK_PACK' => 'UST',
            'HCL_MOCK_PACK' => 'HCL',
            'MPHASIS_MOCK_PACK' => 'Mphasis',
            'HEXAWARE_MOCK_PACK' => 'Hexaware',
            'PERSISTENT_MOCK_PACK' => 'Persistent',
            'VIRTUSA_MOCK_PACK' => 'Virtusa',
            'ORACLE_MOCK_PACK' => 'Oracle',
            'COFORGE_MOCK_PACK' => 'Coforge',
        ];
        $tagFilter = $tagMap[$packCode] ?? '';

        // Check for active session to resume after refresh or crash
        if ($userId && self::$hasMockSessionsTable) {
            $actStmt = $db->prepare("
                SELECT * FROM mock_sessions 
                WHERE user_id = ? AND pack_code = ? AND status = 'active'
                ORDER BY started_at DESC LIMIT 1
            ");
            $actStmt->execute([$userId, $packCode ?: 'GENERAL_MOCK']);
            $existingSess = $actStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingSess) {
                $remSec = strtotime($existingSess['deadline']) - time();
                if ($remSec <= 0) {
                    $db->prepare("UPDATE mock_sessions SET status = 'expired' WHERE id = ?")->execute([$existingSess['id']]);
                } else {
                    $qIds = array_filter(array_map('intval', explode(',', $existingSess['question_ids'] ?? '')));
                    if (!empty($qIds)) {
                        $inQ = implode(',', $qIds);
                        $savedList = $db->query("
                            SELECT q.id, q.question_text, q.code_snippet, q.language, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, q.est_time_seconds,
                                   q.source_tag, q.company_name, s.name as subtopic_name, t.name as topic_name
                            FROM questions q
                            JOIN subtopics s ON q.subtopic_id = s.id
                            JOIN topics t ON s.topic_id = t.id
                            WHERE q.id IN ($inQ)
                        ")->fetchAll(PDO::FETCH_ASSOC);

                        $orderMap = array_flip($qIds);
                        usort($savedList, fn($a, $b) => ($orderMap[$a['id']] ?? 0) <=> ($orderMap[$b['id']] ?? 0));

                        $resumedQuestions = [];
                        foreach ($savedList as $q) {
                            $savedMap = $_SESSION['mock_test_mappings'][$existingSess['id']][$q['id']] ?? null;
                            $options = $savedMap ? $savedMap['options'] : [
                                'A' => $q['option_a'], 'B' => $q['option_b'], 'C' => $q['option_c'], 'D' => $q['option_d']
                            ];
                            $parsed = QuestionParser::parse($q['question_text']);
                            $resumedQuestions[] = [
                                'id' => (int)$q['id'],
                                'topic_name' => $q['topic_name'],
                                'subtopic_name' => $q['subtopic_name'],
                                'question_text' => $parsed['full_text'],
                                'raw_question_text' => $q['question_text'],
                                'source_tag' => $parsed['source_tag'] ?? $q['source_tag'] ?? null,
                                'code_snippet' => $q['code_snippet'] ?? null,
                                'language' => $q['language'] ?? null,
                                'options' => $options
                            ];
                        }

                        echo json_encode([
                            'status' => 'success',
                            'session_id' => $existingSess['id'],
                            'duration_seconds' => (int)$existingSess['duration_seconds'],
                            'remaining_seconds' => (int)$remSec,
                            'started_at' => $existingSess['started_at'],
                            'deadline' => $existingSess['deadline'],
                            'is_resumed' => true,
                            'saved_answers' => json_decode($existingSess['user_answers'] ?? '{}', true) ?: (object)[],
                            'pack_code' => $existingSess['pack_code'],
                            'questions' => $resumedQuestions
                        ]);
                        exit;
                    }
                }
            }
        }

        if ($tagFilter) {
            $stmt = $db->prepare("
                SELECT q.id, q.question_text, q.code_snippet, q.language, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, q.est_time_seconds,
                       q.source_tag, q.company_name, s.name as subtopic_name, t.name as topic_name
                FROM questions q
                JOIN subtopics s ON q.subtopic_id = s.id
                JOIN topics t ON s.topic_id = t.id
                WHERE (q.source_tag LIKE ? OR q.company_name LIKE ?)
                  AND q.is_published = 1
                ORDER BY RAND() LIMIT 10
            ");
            $stmt->execute(['%' . $tagFilter . '%', '%' . $tagFilter . '%']);
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $questions = [];
        }

        // Fallback if not enough tagged questions
        if (count($questions) < 5) {
            $stmt = $db->query("
                SELECT q.id, q.question_text, q.code_snippet, q.language, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, q.est_time_seconds,
                       s.name as subtopic_name, t.name as topic_name
                FROM questions q
                JOIN subtopics s ON q.subtopic_id = s.id
                JOIN topics t ON s.topic_id = t.id
                WHERE q.is_published = 1
                ORDER BY RAND() LIMIT 10
            ");
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Guarantee 10 strictly distinct questions by question_text and id
        $distinctQuestions = [];
        $seenTexts = [];
        foreach ($questions as $q) {
            $norm = trim(mb_strtolower($q['question_text']));
            if (!isset($seenTexts[$norm])) {
                $seenTexts[$norm] = true;
                $distinctQuestions[] = $q;
            }
        }
        $questions = array_slice($distinctQuestions, 0, 10);

        $sessionId = 'mock_' . bin2hex(random_bytes(8));
        $_SESSION['mock_test_mappings'][$sessionId] = [];

        $shuffledQuestions = [];
        foreach ($questions as $q) {
            $parsed = QuestionParser::parse($q['question_text']);
            $rawOpts = [
                'A' => $q['option_a'],
                'B' => $q['option_b'],
                'C' => $q['option_c'],
                'D' => $q['option_d']
            ];
            $dbCorrectLetter = strtoupper(trim($q['correct_option'] ?: 'A'));

            $keys = ['A', 'B', 'C', 'D'];
            $shuffledKeys = $keys;
            shuffle($shuffledKeys);

            $shuffledOptions = [];
            $shuffledCorrect = 'A';
            foreach ($keys as $idx => $origKey) {
                $newKey = $shuffledKeys[$idx];
                $shuffledOptions[$newKey] = $rawOpts[$origKey];
                if ($origKey === $dbCorrectLetter) {
                    $shuffledCorrect = $newKey;
                }
            }
            ksort($shuffledOptions);

            $_SESSION['mock_test_mappings'][$sessionId][$q['id']] = [
                'correct_option' => $shuffledCorrect,
                'options' => $shuffledOptions
            ];

            $shuffledQuestions[] = [
                'id' => (int)$q['id'],
                'topic_name' => $q['topic_name'],
                'subtopic_name' => $q['subtopic_name'],
                'question_text' => $parsed['full_text'],
                'raw_question_text' => $q['question_text'],
                'source_tag' => $parsed['source_tag'] ?? $q['source_tag'] ?? null,
                'code_snippet' => $q['code_snippet'] ?? null,
                'language' => $q['language'] ?? null,
                'options' => $shuffledOptions
            ];
        }

        $totalQuestions = count($shuffledQuestions);
        // Total time rule: Total time in minutes = number of questions (N questions = N minutes)
        $durationSeconds = max(60, $totalQuestions * 60);
        $startedAt = date('Y-m-d H:i:s');
        $deadline = date('Y-m-d H:i:s', time() + $durationSeconds);
        $qIdsList = implode(',', array_column($shuffledQuestions, 'id'));

        // Server-authoritative session persistence in database
        if ($userId && self::$hasMockSessionsTable) {
            try {
                $db->prepare("
                    INSERT INTO mock_sessions 
                    (id, user_id, pack_code, pack_title, total_questions, duration_seconds, started_at, deadline, status, question_ids)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', ?)
                ")->execute([
                    $sessionId,
                    $userId,
                    $packCode ?: 'GENERAL_MOCK',
                    $packTitle ?? 'Placement Practice Mock',
                    $totalQuestions,
                    $durationSeconds,
                    $startedAt,
                    $deadline,
                    $qIdsList
                ]);
            } catch (\Throwable $e) {
                error_log("Failed to insert mock session: " . $e->getMessage());
            }
        }

        echo json_encode([
            'status' => 'success',
            'session_id' => $sessionId,
            'duration_seconds' => $durationSeconds,
            'remaining_seconds' => $durationSeconds,
            'started_at' => $startedAt,
            'deadline' => $deadline,
            'is_resumed' => false,
            'pack_code' => $packCode ?: 'GENERAL_MOCK',
            'questions' => $shuffledQuestions
        ]);
        exit;
    }

    /**
     * Submit full mock test answers and calculate real Exam Hall scorecard
     */
    public static function submitMockTestJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $answers = $input['answers'] ?? []; // Map of question_id => selected_option
        $questionTimes = $input['question_times'] ?? []; // Map of question_id => seconds
        $questionIds = $input['question_ids'] ?? [];
        $packCode = $input['pack_code'] ?? 'GENERAL_MOCK';
        $packTitle = $input['pack_title'] ?? 'Company Placement Mock';
        $totalTimeTaken = (int)($input['total_time_seconds'] ?? 0);
        $clientSessionId = trim($input['session_id'] ?? ('mock_' . bin2hex(random_bytes(8))));

        // Fallback: if question_ids not explicitly sent, use keys of answers
        if (empty($questionIds) && !empty($answers)) {
            $questionIds = array_map('intval', array_keys($answers));
        }

        if (empty($questionIds)) {
            echo json_encode(['status' => 'error', 'message' => 'No test questions specified']);
            exit;
        }

        $db = Database::getConnection();
        self::checkSchemaCapabilities($db);

        // Server-authoritative deadline validation
        $mockSession = null;
        if ($userId && self::$hasMockSessionsTable) {
            $sessStmt = $db->prepare("SELECT * FROM mock_sessions WHERE id = ? LIMIT 1");
            $sessStmt->execute([$clientSessionId]);
            $mockSession = $sessStmt->fetch(PDO::FETCH_ASSOC);
            if ($mockSession) {
                $now = time();
                $deadlineTs = strtotime($mockSession['deadline']);
                // 30 seconds network latency grace period
                if ($deadlineTs && $now > ($deadlineTs + 30)) {
                    if ($mockSession['status'] === 'active') {
                        $db->prepare("UPDATE mock_sessions SET status = 'expired' WHERE id = ?")->execute([$clientSessionId]);
                    }
                    http_response_code(400);
                    echo json_encode([
                        'status' => 'error',
                        'code' => 'DEADLINE_EXCEEDED',
                        'message' => 'The time limit for this mock test has expired. Submissions after deadline are rejected.'
                    ]);
                    exit;
                }
            }
        }

        $tabSwitchSkipped = $input['tab_switch_skipped'] ?? [];
        if (!is_array($tabSwitchSkipped)) {
            $tabSwitchSkipped = [];
        }
        $tabSwitchSkipped = array_map('intval', $tabSwitchSkipped);
        $tabSwitchCount = count($tabSwitchSkipped);

        $inClause = implode(',', array_fill(0, count($questionIds), '?'));
        
        $stmt = $db->prepare("
            SELECT q.*, s.name as subtopic_name, t.name as topic_name 
            FROM questions q
            JOIN subtopics s ON q.subtopic_id = s.id
            JOIN topics t ON s.topic_id = t.id
            WHERE q.id IN ($inClause)
        ");
        $stmt->execute(array_map('intval', $questionIds));
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sort questions in the exact order requested by the client
        $orderMap = array_flip($questionIds);
        usort($questions, function($a, $b) use ($orderMap) {
            return ($orderMap[$a['id']] ?? 0) <=> ($orderMap[$b['id']] ?? 0);
        });

        $totalQuestions = count($questions);
        $correctCount = 0;
        $wrongCount = 0;
        $skippedCount = 0;
        $hasAnyTime = false;

        $detailedResults = [];
        $sectionMap = [];
        $timingRecords = [];

        $sessionMockMap = $_SESSION['mock_test_mappings'][$clientSessionId] ?? [];

        foreach ($questions as $index => $q) {
            $qId = (int)$q['id'];
            $qNum = $index + 1;
            $userAns = $answers[$qId] ?? null;
            if (empty($userAns) || $userAns === 'null') {
                $userAns = null;
            } else {
                $userAns = strtoupper(trim((string)$userAns));
            }

            $rawTime = isset($questionTimes[$qId]) ? (int)$questionTimes[$qId] : 0;
            // Cap time at 300s maximum per question for sane statistics
            $timeTaken = min(max(0, $rawTime), 300);
            if ($timeTaken > 0) {
                $hasAnyTime = true;
            }

            $isCorrect = false;
            $status = 'skipped';
            $skipReason = null;

            $expectedCorrect = $sessionMockMap[$qId]['correct_option'] ?? strtoupper(trim($q['correct_option'] ?: 'A'));

            $isTabSwitched = in_array($qId, $tabSwitchSkipped, true) || (($input['skip_reasons'][$qId] ?? '') === 'tab_switch');

            if ($isTabSwitched) {
                $status = 'skipped';
                $skipReason = 'tab_switch';
                $isCorrect = false;
                $userAns = null;
                $skippedCount++;
            } elseif ($userAns !== null) {
                if ($userAns === $expectedCorrect) {
                    $isCorrect = true;
                    $status = 'correct';
                    $correctCount++;
                } else {
                    // Also check text match if options were recorded
                    $shuffledOpts = $sessionMockMap[$qId]['options'] ?? null;
                    if ($shuffledOpts && isset($shuffledOpts[$userAns]) && isset($shuffledOpts[$expectedCorrect])) {
                        if (strtolower(trim($shuffledOpts[$userAns])) === strtolower(trim($shuffledOpts[$expectedCorrect]))) {
                            $isCorrect = true;
                            $status = 'correct';
                            $correctCount++;
                        } else {
                            $status = 'wrong';
                            $wrongCount++;
                        }
                    } else {
                        $status = 'wrong';
                        $wrongCount++;
                    }
                }
            } else {
                $skippedCount++;
                if (in_array($qId, $tabSwitchSkipped, true) || (($input['skip_reasons'][$qId] ?? '') === 'tab_switch')) {
                    $skipReason = 'tab_switch';
                } else {
                    $skipReason = 'unanswered';
                }
            }

            $sectionName = $q['topic_name'] ?? 'General Aptitude';
            if (!isset($sectionMap[$sectionName])) {
                $sectionMap[$sectionName] = [
                    'name' => $sectionName,
                    'total' => 0,
                    'correct' => 0,
                    'wrong' => 0,
                    'skipped' => 0,
                    'time_seconds' => 0,
                ];
            }
            $sectionMap[$sectionName]['total']++;
            $sectionMap[$sectionName][$status]++;
            $sectionMap[$sectionName]['time_seconds'] += $timeTaken;

            $timingRecords[] = [
                'question_number' => $qNum,
                'question_id' => $qId,
                'status' => $status,
                'time_seconds' => $timeTaken,
                'is_correct' => $isCorrect
            ];

            $parsed = QuestionParser::parse($q['question_text']);
            $detailedResults[] = [
                'question_id' => $qId,
                'question_number' => $qNum,
                'topic_name' => $sectionName,
                'subtopic_name' => $q['subtopic_name'] ?? '',
                'question_text' => $parsed['full_text'],
                'raw_question_text' => $q['question_text'],
                'source_tag' => $parsed['source_tag'] ?? $q['source_tag'] ?? null,
                'code_snippet' => $q['code_snippet'] ?? null,
                'language' => $q['language'] ?? null,
                'user_answer' => $userAns ?: 'Not Attempted',
                'correct_option' => $expectedCorrect,
                'is_correct' => $isCorrect,
                'status' => $status,
                'time_seconds' => $timeTaken,
                'has_time' => ($timeTaken > 0),
                'explanation' => $q['explanation'] ?? 'No detailed solution provided.'
            ];

            // Save individual question attempt to attempts table if logged in
            if ($userId) {
                try {
                    $cols = ['user_id', 'question_id', 'selected_option', 'is_correct', 'confidence', 'time_taken_seconds', 'mode'];
                    $vals = [$userId, $qId, $userAns ?: 'A', $isCorrect ? 1 : 0, 'sure', $timeTaken, 'mock'];

                    if (self::$hasMockSessionIdCol) {
                        $cols[] = 'mock_session_id';
                        $vals[] = $clientSessionId;
                    }
                    if (self::$hasPackCodeCol) {
                        $cols[] = 'pack_code';
                        $vals[] = $packCode;
                    }
                    if (self::$hasSkipReasonCol) {
                        $cols[] = 'skip_reason';
                        $vals[] = $skipReason;
                    }
                    $colStr = implode(', ', $cols);
                    $placeholders = implode(', ', array_fill(0, count($vals), '?'));
                    $db->prepare("INSERT INTO attempts ($colStr) VALUES ($placeholders)")->execute($vals);
                } catch (\Throwable $e) {
                    error_log("Failed to insert mock attempt for question $qId: " . $e->getMessage());
                }
            }
        }

        // Negative Marking Formula: +1 for correct, -0.25 for wrong, 0 for skipped
        $rawScore = ($correctCount * 1.0) - ($wrongCount * 0.25);
        $scoreMarks = round(max(-2.5, $rawScore), 2); // Cap minimum loss gracefully
        $totalMarks = round($totalQuestions * 1.0, 2);
        $accuracyPercent = ($totalQuestions > 0) ? round(($correctCount / $totalQuestions) * 100, 1) : 0;
        $scorePercent = ($totalMarks > 0) ? round(($scoreMarks / $totalMarks) * 100, 1) : 0;

        // Band Determination
        $band = 'Needs Practice';
        if ($scorePercent >= 75) {
            $band = 'Distinction';
        } elseif ($scorePercent >= 60) {
            $band = 'First Class';
        } elseif ($scorePercent >= 40) {
            $band = 'Pass';
        }

        // Time Statistics
        $timeAllowed = $totalQuestions * 60; // Overall timer rule: N mins = N questions
        if ($mockSession && !empty($mockSession['duration_seconds'])) {
            $timeAllowed = (int)$mockSession['duration_seconds'];
        }
        if ($totalTimeTaken <= 0) {
            $totalTimeTaken = array_sum(array_column($timingRecords, 'time_seconds'));
        }
        $totalTimeTaken = min(max(0, $totalTimeTaken), $timeAllowed);

        $avgTimePerQ = ($totalQuestions > 0 && $totalTimeTaken > 0) ? round($totalTimeTaken / $totalQuestions, 1) : 0;

        // Fastest & Slowest Questions
        $timedQuestions = array_filter($timingRecords, fn($r) => $r['time_seconds'] > 0);
        $fastestQ = null;
        $slowestQ = null;
        if (!empty($timedQuestions)) {
            usort($timedQuestions, fn($a, $b) => $a['time_seconds'] <=> $b['time_seconds']);
            $fastestQ = $timedQuestions[0];
            $slowestQ = end($timedQuestions);
        }

        // Average Time on Correct vs Wrong
        $correctTimes = array_map(fn($r) => $r['time_seconds'], array_filter($timingRecords, fn($r) => $r['status'] === 'correct' && $r['time_seconds'] > 0));
        $wrongTimes = array_map(fn($r) => $r['time_seconds'], array_filter($timingRecords, fn($r) => $r['status'] === 'wrong' && $r['time_seconds'] > 0));

        $avgTimeCorrect = count($correctTimes) > 0 ? round(array_sum($correctTimes) / count($correctTimes), 1) : 0;
        $avgTimeWrong = count($wrongTimes) > 0 ? round(array_sum($wrongTimes) / count($wrongTimes), 1) : 0;

        // Section Breakdown Calculations
        $sections = [];
        foreach ($sectionMap as $sName => $sData) {
            $secMarks = round(($sData['correct'] * 1.0) - ($sData['wrong'] * 0.25), 2);
            $secAcc = $sData['total'] > 0 ? round(($sData['correct'] / $sData['total']) * 100, 1) : 0;
            $secAvgTime = $sData['total'] > 0 ? round($sData['time_seconds'] / $sData['total'], 1) : 0;
            $sections[] = [
                'name' => $sName,
                'total' => $sData['total'],
                'correct' => $sData['correct'],
                'wrong' => $sData['wrong'],
                'skipped' => $sData['skipped'],
                'score_marks' => $secMarks,
                'accuracy_percent' => $secAcc,
                'avg_time_seconds' => $secAvgTime,
                'has_time' => ($secAvgTime > 0)
            ];
        }

        // Rule-Based Data-Driven Insights (2-3 sentences based strictly on real calculations)
        $insights = [];
        if ($avgTimeWrong > 0 && $avgTimeCorrect > 0) {
            $ratio = round($avgTimeWrong / $avgTimeCorrect, 1);
            if ($ratio >= 1.5) {
                $insights[] = "You spent {$ratio}x longer on wrong answers ({$avgTimeWrong}s avg) than correct ones ({$avgTimeCorrect}s avg). Flag doubts sooner to preserve time.";
            } elseif ($ratio <= 0.7) {
                $insights[] = "You solved correct questions with deep deliberation ({$avgTimeCorrect}s avg) compared to rushed wrong attempts ({$avgTimeWrong}s avg).";
            } else {
                $insights[] = "Pacing remained well-balanced across correct ({$avgTimeCorrect}s avg) and incorrect ({$avgTimeWrong}s avg) answers.";
            }
        } elseif ($wrongCount === 0 && $correctCount > 0) {
            $insights[] = "Flawless accuracy on all attempted questions with zero negative marking deductions.";
        }

        // Section Insight
        if (count($sections) > 1) {
            usort($sections, fn($a, $b) => $b['accuracy_percent'] <=> $a['accuracy_percent']);
            $bestSec = $sections[0];
            $slowestSec = $sections[0];
            foreach ($sections as $sec) {
                if ($sec['avg_time_seconds'] > $slowestSec['avg_time_seconds']) {
                    $slowestSec = $sec;
                }
            }

            if ($bestSec['accuracy_percent'] >= 70) {
                $insights[] = "Strongest section: {$bestSec['name']} at {$bestSec['accuracy_percent']}% accuracy.";
            }
            if ($slowestSec['has_time'] && $slowestSec['avg_time_seconds'] > 40) {
                $insights[] = "Pacing bottleneck: {$slowestSec['name']} required {$slowestSec['avg_time_seconds']}s average per question.";
            }
        }

        if ($skippedCount > 0) {
            $savedDeductions = round($skippedCount * 0.25, 2);
            $insights[] = "Skipping {$skippedCount} uncertain question(s) strategically avoided -{$savedDeductions} marks in negative penalties.";
        } elseif (count($insights) < 2) {
            $insights[] = "All {$totalQuestions} questions attempted under test conditions.";
        }

        // Record or update session summary in mock_sessions if table exists
        if ($userId && self::$hasMockSessionsTable) {
            try {
                if ($mockSession) {
                    $db->prepare("
                        UPDATE mock_sessions 
                        SET status = 'completed',
                            correct_count = ?,
                            wrong_count = ?,
                            skipped_count = ?,
                            score_marks = ?,
                            total_marks = ?,
                            accuracy_percent = ?,
                            total_time_seconds = ?,
                            tab_switch_count = ?,
                            user_answers = ?,
                            completed_at = NOW()
                        WHERE id = ?
                    ")->execute([
                        $correctCount,
                        $wrongCount,
                        $skippedCount,
                        $scoreMarks,
                        $totalMarks,
                        $accuracyPercent,
                        $totalTimeTaken,
                        $tabSwitchCount,
                        json_encode($answers),
                        $clientSessionId
                    ]);
                } else {
                    $db->prepare("
                        INSERT INTO mock_sessions 
                        (id, user_id, pack_code, pack_title, total_questions, duration_seconds, started_at, deadline, status, correct_count, wrong_count, skipped_count, score_marks, total_marks, accuracy_percent, total_time_seconds, tab_switch_count, user_answers, completed_at)
                        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW(), 'completed', ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ")->execute([
                        $clientSessionId,
                        $userId,
                        $packCode,
                        $packTitle,
                        $totalQuestions,
                        $timeAllowed,
                        $correctCount,
                        $wrongCount,
                        $skippedCount,
                        $scoreMarks,
                        $totalMarks,
                        $accuracyPercent,
                        $totalTimeTaken,
                        $tabSwitchCount,
                        json_encode($answers)
                    ]);
                }
            } catch (\Throwable $e) {
                error_log("Failed to insert/update mock_sessions record: " . $e->getMessage());
            }
        }

        // XP Awards from centralized economy config
        $cfg = GamificationEngine::getConfig();
        $mockBase = (int)($cfg['mock_test']['base_xp'] ?? 50);
        $mockMult = (float)($cfg['mock_test']['accuracy_xp_multiplier'] ?? 1.5);
        $mockXP = $mockBase + (int)round($accuracyPercent * $mockMult);

        $xpResult = [];
        if ($userId && $correctCount > 0) {
            $xpResult = User::addXP($userId, $mockXP);
        }

        echo json_encode([
            'status' => 'success',
            'session_id' => $clientSessionId,
            'pack_code' => $packCode,
            'pack_title' => $packTitle,
            'score' => [
                'marks' => $scoreMarks,
                'total_marks' => $totalMarks,
                'percentage' => $scorePercent,
                'formula' => '+1 per correct, -0.25 per wrong',
                'band' => $band,
                'qualifies_confetti' => ($scorePercent >= 60)
            ],
            'counts' => [
                'total' => $totalQuestions,
                'correct' => $correctCount,
                'wrong' => $wrongCount,
                'skipped' => $skippedCount,
                'accuracy_percent' => $accuracyPercent
            ],
            'time' => [
                'total_taken_seconds' => $totalTimeTaken,
                'time_allowed_seconds' => $timeAllowed,
                'avg_time_per_question' => $avgTimePerQ,
                'fastest_question' => $fastestQ,
                'slowest_question' => $slowestQ,
                'avg_time_correct' => $avgTimeCorrect,
                'avg_time_wrong' => $avgTimeWrong,
                'has_time_data' => $hasAnyTime
            ],
            'bubbles' => $timingRecords,
            'sections' => $sections,
            'insights' => array_slice($insights, 0, 3),
            'detailed_results' => $detailedResults,
            'xp_earned' => ($userId && $correctCount > 0) ? $mockXP : 0,
            'xp_data' => $xpResult
        ]);
        exit;
    }

    /**
     * Retrieve a historical mock test scorecard for review
     */
    public static function getMockScorecardJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $sessionId = trim($_GET['session_id'] ?? '');
        $dateParam = trim($_GET['date'] ?? '');
        $db = Database::getConnection();
        self::checkSchemaCapabilities($db);

        // Fetch attempts for this session or date
        $attempts = [];
        if ($sessionId && self::$hasMockSessionIdCol) {
            $stmt = $db->prepare("
                SELECT a.*, q.question_text, q.code_snippet, q.language, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, q.explanation,
                       s.name as subtopic_name, t.name as topic_name
                FROM attempts a
                JOIN questions q ON a.question_id = q.id
                JOIN subtopics s ON q.subtopic_id = s.id
                JOIN topics t ON s.topic_id = t.id
                WHERE a.user_id = ? AND a.mock_session_id = ?
                ORDER BY a.id ASC
            ");
            $stmt->execute([$userId, $sessionId]);
            $attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (empty($attempts) && $dateParam) {
            $stmt = $db->prepare("
                SELECT a.*, q.question_text, q.code_snippet, q.language, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, q.explanation,
                       s.name as subtopic_name, t.name as topic_name
                FROM attempts a
                JOIN questions q ON a.question_id = q.id
                JOIN subtopics s ON q.subtopic_id = s.id
                JOIN topics t ON s.topic_id = t.id
                WHERE a.user_id = ? AND a.mode = 'mock' AND DATE(a.attempted_at) = DATE(?)
                ORDER BY a.id ASC
                LIMIT 10
            ");
            $stmt->execute([$userId, $dateParam]);
            $attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (empty($attempts)) {
            echo json_encode(['status' => 'error', 'message' => 'Mock test attempt not found']);
            exit;
        }

        $totalQuestions = count($attempts);
        $correctCount = 0;
        $wrongCount = 0;
        $skippedCount = 0;
        $hasAnyTime = false;
        $timingRecords = [];
        $detailedResults = [];
        $sectionMap = [];

        foreach ($attempts as $idx => $att) {
            $qNum = $idx + 1;
            $qId = (int)$att['question_id'];
            $timeTaken = (int)($att['time_taken_seconds'] ?? 0);
            if ($timeTaken > 0) $hasAnyTime = true;

            $isCorrect = (bool)$att['is_correct'];
            $userAns = $att['selected_option'] ?? null;
            $status = $isCorrect ? 'correct' : ($userAns ? 'wrong' : 'skipped');
            if ($status === 'correct') $correctCount++;
            elseif ($status === 'wrong') $wrongCount++;
            else $skippedCount++;

            $sectionName = $att['topic_name'] ?? 'General Aptitude';
            if (!isset($sectionMap[$sectionName])) {
                $sectionMap[$sectionName] = [
                    'name' => $sectionName,
                    'total' => 0,
                    'correct' => 0,
                    'wrong' => 0,
                    'skipped' => 0,
                    'time_seconds' => 0
                ];
            }
            $sectionMap[$sectionName]['total']++;
            $sectionMap[$sectionName][$status]++;
            $sectionMap[$sectionName]['time_seconds'] += $timeTaken;

            $timingRecords[] = [
                'question_number' => $qNum,
                'question_id' => $qId,
                'status' => $status,
                'time_seconds' => $timeTaken,
                'is_correct' => $isCorrect
            ];

            $parsed = QuestionParser::parse($att['question_text']);
            $detailedResults[] = [
                'question_id' => $qId,
                'question_number' => $qNum,
                'topic_name' => $sectionName,
                'subtopic_name' => $att['subtopic_name'] ?? '',
                'question_text' => $parsed['full_text'],
                'raw_question_text' => $att['question_text'],
                'source_tag' => $parsed['source_tag'] ?? $att['source_tag'] ?? null,
                'code_snippet' => $att['code_snippet'] ?? null,
                'language' => $att['language'] ?? null,
                'user_answer' => $userAns ?: 'Not Attempted',
                'correct_option' => $att['correct_option'],
                'is_correct' => $isCorrect,
                'status' => $status,
                'time_seconds' => $timeTaken,
                'has_time' => ($timeTaken > 0),
                'explanation' => $att['explanation'] ?? 'No detailed solution provided.'
            ];
        }

        $rawScore = ($correctCount * 1.0) - ($wrongCount * 0.25);
        $scoreMarks = round(max(-2.5, $rawScore), 2);
        $totalMarks = round($totalQuestions * 1.0, 2);
        $accuracyPercent = ($totalQuestions > 0) ? round(($correctCount / $totalQuestions) * 100, 1) : 0;
        $scorePercent = ($totalMarks > 0) ? round(($scoreMarks / $totalMarks) * 100, 1) : 0;

        $band = 'Needs Practice';
        if ($scorePercent >= 75) $band = 'Distinction';
        elseif ($scorePercent >= 60) $band = 'First Class';
        elseif ($scorePercent >= 40) $band = 'Pass';

        $totalTimeTaken = array_sum(array_column($timingRecords, 'time_seconds'));
        $avgTimePerQ = ($totalQuestions > 0 && $totalTimeTaken > 0) ? round($totalTimeTaken / $totalQuestions, 1) : 0;

        $sections = [];
        foreach ($sectionMap as $sName => $sData) {
            $secMarks = round(($sData['correct'] * 1.0) - ($sData['wrong'] * 0.25), 2);
            $secAcc = $sData['total'] > 0 ? round(($sData['correct'] / $sData['total']) * 100, 1) : 0;
            $secAvgTime = $sData['total'] > 0 ? round($sData['time_seconds'] / $sData['total'], 1) : 0;
            $sections[] = [
                'name' => $sName,
                'total' => $sData['total'],
                'correct' => $sData['correct'],
                'wrong' => $sData['wrong'],
                'skipped' => $sData['skipped'],
                'score_marks' => $secMarks,
                'accuracy_percent' => $secAcc,
                'avg_time_seconds' => $secAvgTime,
                'has_time' => ($secAvgTime > 0)
            ];
        }

        echo json_encode([
            'status' => 'success',
            'session_id' => $sessionId ?: ('mock_' . date('Ymd_His', strtotime($attempts[0]['attempted_at']))),
            'pack_title' => 'Placement Practice Mock',
            'attempted_at' => $attempts[0]['attempted_at'],
            'score' => [
                'marks' => $scoreMarks,
                'total_marks' => $totalMarks,
                'percentage' => $scorePercent,
                'formula' => '+1 per correct, -0.25 per wrong',
                'band' => $band,
                'qualifies_confetti' => false
            ],
            'counts' => [
                'total' => $totalQuestions,
                'correct' => $correctCount,
                'wrong' => $wrongCount,
                'skipped' => $skippedCount,
                'accuracy_percent' => $accuracyPercent
            ],
            'time' => [
                'total_taken_seconds' => $totalTimeTaken,
                'time_allowed_seconds' => 600,
                'avg_time_per_question' => $avgTimePerQ,
                'has_time_data' => $hasAnyTime
            ],
            'bubbles' => $timingRecords,
            'sections' => $sections,
            'detailed_results' => $detailedResults
        ]);
        exit;
    }
}
