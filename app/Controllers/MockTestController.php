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

        $mode = strtolower(trim($_GET['mode'] ?? 'standard'));
        if (!in_array($mode, ['quick', 'standard', 'full'], true)) {
            $mode = 'standard';
        }

        // Check for active session to resume after refresh or crash
        if ($userId && self::$hasMockSessionsTable) {
            $actStmt = $db->prepare("
                SELECT * FROM mock_sessions 
                WHERE user_id = ? AND pack_code = ? AND mode = ? AND status = 'active'
                ORDER BY started_at DESC LIMIT 1
            ");
            $actStmt->execute([$userId, $packCode ?: 'GENERAL_MOCK', $mode]);
            $existingSess = $actStmt->fetch(PDO::FETCH_ASSOC);

            // Backward compatibility: match active session without mode constraint
            if (!$existingSess) {
                $actStmtOld = $db->prepare("
                    SELECT * FROM mock_sessions 
                    WHERE user_id = ? AND pack_code = ? AND status = 'active'
                    ORDER BY started_at DESC LIMIT 1
                ");
                $actStmtOld->execute([$userId, $packCode ?: 'GENERAL_MOCK']);
                $existingSess = $actStmtOld->fetch(PDO::FETCH_ASSOC);
            }

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
                            'mode' => $existingSess['mode'] ?? $mode,
                            'total_questions' => (int)($existingSess['total_questions'] ?? count($resumedQuestions)),
                            'question_count' => (int)($existingSess['question_count'] ?? count($resumedQuestions)),
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

        // Check-only query: if client requested only checking for an active session, return now without creating one
        if (!empty($_GET['check_active'])) {
            echo json_encode([
                'status' => 'no_active_session',
                'is_resumed' => false,
                'message' => 'No active session found.'
            ]);
            exit;
        }

        // Fetch mock configuration for this pack and mode
        $mockConfig = null;
        try {
            $cfgStmt = $db->prepare("
                SELECT mc.* FROM mock_configs mc
                JOIN unlockables u ON mc.unlockable_id = u.id
                WHERE (u.code = ? OR mc.unlockable_id = ?) AND mc.mode = ? AND mc.active = 1
                LIMIT 1
            ");
            $cfgStmt->execute([$packCode, $unlockableId ?? 0, $mode]);
            $mockConfig = $cfgStmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            $mockConfig = null;
        }

        $isTCS = ($packCode === 'TCS_MOCK_PACK');
        $targetCount = $mockConfig ? (int)$mockConfig['question_count'] : ($mode === 'quick' ? 30 : ($mode === 'full' ? 80 : 60));
        $durationOverride = ($mockConfig && !empty($mockConfig['duration_minutes'])) ? (int)$mockConfig['duration_minutes'] : null;
        $negMarking = $mockConfig ? (float)$mockConfig['negative_marking'] : ($isTCS ? 0.00 : 0.25);
        $blueprintJson = $mockConfig['blueprint'] ?? null;

        // Fetch candidate questions from database
        $poolQuestions = [];
        if ($tagFilter) {
            $stmt = $db->prepare("
                SELECT q.id, q.subtopic_id, q.difficulty, q.question_text, q.code_snippet, q.language,
                       q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, q.explanation,
                       q.est_time_seconds, q.source_tag, q.company_name,
                       s.name as subtopic_name, s.code as subtopic_code,
                       t.id as topic_id, t.name as topic_name, t.code as topic_code
                FROM questions q
                JOIN subtopics s ON q.subtopic_id = s.id
                JOIN topics t ON s.topic_id = t.id
                WHERE (q.source_tag LIKE ? OR q.company_name LIKE ?)
                  AND q.is_published = 1
            ");
            $stmt->execute(['%' . $tagFilter . '%', '%' . $tagFilter . '%']);
            $poolQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Deduplicate pool questions (strictly unique by id and normalized text)
        $poolById = [];
        $seenTexts = [];
        foreach ($poolQuestions as $q) {
            $norm = trim(mb_strtolower($q['question_text']));
            if (!isset($seenTexts[$norm]) && !isset($poolById[$q['id']])) {
                $seenTexts[$norm] = true;
                $poolById[$q['id']] = $q;
            }
        }
        $poolSize = count($poolById);

        // Fallback for general practice or small pool
        if ($poolSize < 5) {
            $stmt = $db->query("
                SELECT q.id, q.subtopic_id, q.difficulty, q.question_text, q.code_snippet, q.language,
                       q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, q.explanation,
                       q.est_time_seconds, q.source_tag, q.company_name,
                       s.name as subtopic_name, s.code as subtopic_code,
                       t.id as topic_id, t.name as topic_name, t.code as topic_code
                FROM questions q
                JOIN subtopics s ON q.subtopic_id = s.id
                JOIN topics t ON s.topic_id = t.id
                WHERE q.is_published = 1
            ");
            while ($q = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $norm = trim(mb_strtolower($q['question_text']));
                if (!isset($seenTexts[$norm]) && !isset($poolById[$q['id']])) {
                    $seenTexts[$norm] = true;
                    $poolById[$q['id']] = $q;
                }
            }
            $poolSize = count($poolById);
        }

        // Capping Rule: If pool is smaller than requested, cap at pool size; show real length, never pad with repeats
        $isCapped = false;
        $effectiveCount = $targetCount;
        if ($poolSize < $targetCount) {
            $effectiveCount = $poolSize;
            $isCapped = true;
        }

        // Blueprint-based Sampling
        $selectedQuestions = [];
        $selectedIds = [];
        $bp = !empty($blueprintJson) ? json_decode($blueprintJson, true) : null;
        $bpSections = $bp['sections'] ?? [];

        if (!empty($bpSections) && $poolSize > 0) {
            // Group pool by section (topic_code)
            $poolBySection = [];
            foreach ($poolById as $q) {
                $secKey = $q['topic_code'] ?? 'OTHER';
                $poolBySection[$secKey][] = $q;
                if ($secKey === 'LR') {
                    $poolBySection['LOGICAL'][] = $q;
                } elseif ($secKey === 'LOGICAL') {
                    $poolBySection['LR'][] = $q;
                }
            }

            $sumBpCounts = array_sum(array_column($bpSections, 'count'));
            $scale = ($sumBpCounts > 0 && $effectiveCount < $sumBpCounts) ? ($effectiveCount / $sumBpCounts) : 1.0;

            foreach ($bpSections as $sec) {
                $secKey = $sec['topic_code'] ?? 'OTHER';
                $secCount = ($scale < 1.0) ? (int)round($sec['count'] * $scale) : (int)$sec['count'];
                if ($secCount <= 0 && $effectiveCount > count($selectedQuestions)) {
                    $secCount = 1;
                }

                $candidates = $poolBySection[$secKey] ?? [];
                $availableCandidates = array_values(array_filter($candidates, fn($cq) => !isset($selectedIds[$cq['id']])));

                $diffMix = $sec['difficulty_mix'] ?? ['easy' => 0, 'medium' => 0, 'hard' => 0];
                $byDiff = ['easy' => [], 'medium' => [], 'hard' => []];
                foreach ($availableCandidates as $ac) {
                    $d = strtolower($ac['difficulty'] ?? 'medium');
                    if (!isset($byDiff[$d])) $d = 'medium';
                    $byDiff[$d][] = $ac;
                }
                foreach ($byDiff as $d => &$dList) {
                    shuffle($dList);
                }
                unset($dList);

                $secPicked = [];
                // Sample matching difficulty mix
                foreach (['easy', 'medium', 'hard'] as $d) {
                    $needed = isset($diffMix[$d]) ? (int)round($diffMix[$d] * ($scale < 1.0 ? $scale : 1.0)) : 0;
                    while ($needed > 0 && !empty($byDiff[$d]) && count($secPicked) < $secCount) {
                        $chosen = array_pop($byDiff[$d]);
                        if (!isset($selectedIds[$chosen['id']])) {
                            $secPicked[] = $chosen;
                            $selectedIds[$chosen['id']] = true;
                            $needed--;
                        }
                    }
                }

                // Backfill shortfall within section
                foreach (['medium', 'easy', 'hard'] as $d) {
                    while (!empty($byDiff[$d]) && count($secPicked) < $secCount) {
                        $chosen = array_pop($byDiff[$d]);
                        if (!isset($selectedIds[$chosen['id']])) {
                            $secPicked[] = $chosen;
                            $selectedIds[$chosen['id']] = true;
                        }
                    }
                }

                foreach ($secPicked as $pQ) {
                    $selectedQuestions[] = $pQ;
                }
            }
        }

        // Backfill remaining from pool to reach effectiveCount without repeats
        if (count($selectedQuestions) < $effectiveCount) {
            $remainingCandidates = array_values(array_filter($poolById, fn($q) => !isset($selectedIds[$q['id']])));
            shuffle($remainingCandidates);
            while (count($selectedQuestions) < $effectiveCount && !empty($remainingCandidates)) {
                $chosen = array_pop($remainingCandidates);
                $selectedQuestions[] = $chosen;
                $selectedIds[$chosen['id']] = true;
            }
        }

        $selectedQuestions = array_slice($selectedQuestions, 0, $effectiveCount);
        shuffle($selectedQuestions);

        $sessionId = 'mock_' . bin2hex(random_bytes(8));
        $_SESSION['mock_test_mappings'][$sessionId] = [];

        $shuffledQuestions = [];
        foreach ($selectedQuestions as $q) {
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
                'topic_code' => $q['topic_code'] ?? null,
                'subtopic_name' => $q['subtopic_name'],
                'difficulty' => $q['difficulty'] ?? 'medium',
                'question_text' => $parsed['full_text'],
                'raw_question_text' => $q['question_text'],
                'source_tag' => $parsed['source_tag'] ?? $q['source_tag'] ?? null,
                'code_snippet' => $q['code_snippet'] ?? null,
                'language' => $q['language'] ?? null,
                'options' => $shuffledOptions
            ];
        }

        $actualCount = count($shuffledQuestions);
        // Time Rule: Total minutes = number of questions (duration_seconds = count * 60)
        // Allow optional per-pack duration_override_minutes for Full-length, off by default
        if ($mode === 'full' && $durationOverride && $durationOverride > 0) {
            $durationSeconds = $durationOverride * 60;
        } else {
            $durationSeconds = max(60, $actualCount * 60);
        }

        $startedAt = date('Y-m-d H:i:s');
        $deadline = date('Y-m-d H:i:s', time() + $durationSeconds);
        $qIdsList = implode(',', array_column($shuffledQuestions, 'id'));

        // Server-authoritative session persistence in database
        if ($userId && self::$hasMockSessionsTable) {
            try {
                $db->prepare("
                    INSERT INTO mock_sessions 
                    (id, user_id, pack_code, pack_title, mode, total_questions, question_count, duration_seconds, started_at, deadline, status, question_ids)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?)
                ")->execute([
                    $sessionId,
                    $userId,
                    $packCode ?: 'GENERAL_MOCK',
                    $packTitle ?? 'Placement Practice Mock',
                    $mode,
                    $actualCount,
                    $actualCount,
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
            'mode' => $mode,
            'total_questions' => $actualCount,
            'question_count' => $actualCount,
            'requested_count' => $targetCount,
            'pool_size' => $poolSize,
            'is_capped' => $isCapped,
            'duration_seconds' => $durationSeconds,
            'remaining_seconds' => $durationSeconds,
            'negative_marking' => $negMarking,
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
        $confidences = $input['confidences'] ?? []; // Map of question_id => confidence (sure, not_sure, guessing)
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
                    $userConf = isset($confidences[$qId]) ? strtolower(trim((string)$confidences[$qId])) : 'sure';
                    if (!in_array($userConf, ['sure', 'not_sure', 'guessing'], true)) {
                        $userConf = 'sure';
                    }
                    $cols = ['user_id', 'question_id', 'selected_option', 'is_correct', 'confidence', 'time_taken_seconds', 'mode'];
                    $vals = [$userId, $qId, $userAns ?: 'A', $isCorrect ? 1 : 0, $userConf, $timeTaken, 'mock'];

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

        $mode = $mockSession['mode'] ?? (isset($input['mode']) ? strtolower(trim((string)$input['mode'])) : 'standard');
        if (!in_array($mode, ['quick', 'standard', 'full'], true)) {
            $mode = 'standard';
        }

        // Fetch pack negative marking setting
        $isTCS = ($packCode === 'TCS_MOCK_PACK');
        $negPenalty = $isTCS ? 0.00 : 0.25;
        try {
            $cfgStmt = $db->prepare("
                SELECT mc.negative_marking 
                FROM mock_configs mc
                JOIN unlockables u ON mc.unlockable_id = u.id
                WHERE (u.code = ? OR mc.unlockable_id = ?) AND mc.mode = ?
                LIMIT 1
            ");
            $cfgStmt->execute([$packCode, $unlockableId ?? 0, $mode]);
            $configuredNeg = $cfgStmt->fetchColumn();
            if ($configuredNeg !== false) {
                $negPenalty = (float)$configuredNeg;
            }
        } catch (\Throwable $e) {
            $negPenalty = $isTCS ? 0.00 : 0.25;
        }

        // Negative Marking Formula: +1 for correct, -$negPenalty for wrong, 0 for skipped
        $rawScore = ($correctCount * 1.0) - ($wrongCount * $negPenalty);
        $scoreMarks = round(max(-2.5, $rawScore), 2); // Cap minimum loss gracefully
        $totalMarks = round($totalQuestions * 1.0, 2);
        $attemptedCount = $correctCount + $wrongCount;
        $accuracyPercent = ($attemptedCount > 0) ? round(max(0.0, min(100.0, ($correctCount / $attemptedCount) * 100)), 1) : 0.0;
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
            $secMarks = round(($sData['correct'] * 1.0) - ($sData['wrong'] * $negPenalty), 2);
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
                            mode = ?,
                            question_count = ?,
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
                        $mode,
                        $totalQuestions,
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
                        (id, user_id, pack_code, pack_title, mode, total_questions, question_count, duration_seconds, started_at, deadline, status, correct_count, wrong_count, skipped_count, score_marks, total_marks, accuracy_percent, total_time_seconds, tab_switch_count, user_answers, completed_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'completed', ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ")->execute([
                        $clientSessionId,
                        $userId,
                        $packCode,
                        $packTitle,
                        $mode,
                        $totalQuestions,
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

        // Calculate percentile separated strictly by mode (ML8)
        $percentile = 100.0;
        if (self::$hasMockSessionsTable) {
            try {
                $pctStmt = $db->prepare("
                    SELECT 
                        COUNT(*) as total_attempts,
                        SUM(CASE WHEN score_marks < ? THEN 1 ELSE 0 END) as lower_scores
                    FROM mock_sessions
                    WHERE pack_code = ? AND mode = ? AND status = 'completed' AND id != ?
                ");
                $pctStmt->execute([$scoreMarks, $packCode, $mode, $clientSessionId]);
                $pctData = $pctStmt->fetch(PDO::FETCH_ASSOC);
                $totalOther = (int)($pctData['total_attempts'] ?? 0);
                $lowerScores = (int)($pctData['lower_scores'] ?? 0);
                if ($totalOther > 0) {
                    $percentile = round(($lowerScores / $totalOther) * 100, 1);
                } else {
                    $percentile = 100.0;
                }
            } catch (\Throwable $e) {
                $percentile = 100.0;
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

        $fixPlanData = null;
        if ($userId) {
            try {
                require_once __DIR__ . '/../Services/FixPlanService.php';
                $forceReplace = !empty($input['replace_fix_plan']);
                $fixPlanData = FixPlanService::processMockSubmission((int)$userId, $clientSessionId, (float)$scorePercent, $forceReplace);
            } catch (\Throwable $e) {
                error_log("FixPlanService error: " . $e->getMessage());
            }
        }

        $formulaStr = ($negPenalty > 0)
            ? "+1 per correct, -" . number_format($negPenalty, 2) . " per wrong"
            : "+1 per correct, no penalty for wrong";

        echo json_encode([
            'status' => 'success',
            'session_id' => $clientSessionId,
            'pack_code' => $packCode,
            'pack_title' => $packTitle,
            'mode' => $mode,
            'percentile' => $percentile,
            'negative_marking' => $negPenalty,
            'score' => [
                'marks' => $scoreMarks,
                'total_marks' => $totalMarks,
                'percentage' => $scorePercent,
                'accuracy' => $accuracyPercent,
                'formula' => $formulaStr,
                'band' => $band,
                'qualifies_confetti' => ($scorePercent >= 60)
            ],
            'fix_plan' => $fixPlanData,
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
        $attemptedCount = $correctCount + $wrongCount;
        $accuracyPercent = ($attemptedCount > 0) ? round(max(0.0, min(100.0, ($correctCount / $attemptedCount) * 100)), 1) : 0.0;
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

    /**
     * Retrieve per-mode leaderboard for a company mock pack
     */
    public static function getMockLeaderboardJSON(): void {
        header('Content-Type: application/json');
        $packCode = trim($_GET['pack'] ?? 'TCS_MOCK_PACK');
        $mode = strtolower(trim($_GET['mode'] ?? 'standard'));
        if (!in_array($mode, ['quick', 'standard', 'full'], true)) {
            $mode = 'standard';
        }

        $db = Database::getConnection();
        self::checkSchemaCapabilities($db);

        $hasHideCol = false;
        try {
            $cCheck = $db->query("SHOW COLUMNS FROM users LIKE 'hide_name_on_boards'")->fetch();
            $hasHideCol = !empty($cCheck);
        } catch (\Throwable $e) {}

        $hideColSelect = $hasHideCol ? "COALESCE(u.hide_name_on_boards, 0) as hide_name" : "0 as hide_name";

        $stmt = $db->prepare("
            SELECT ms.id as session_id, ms.user_id, ms.score_marks, ms.total_marks, ms.accuracy_percent,
                   ms.total_time_seconds, ms.completed_at, ms.mode,
                   u.name as user_name, u.avatar_icon,
                   $hideColSelect
            FROM mock_sessions ms
            LEFT JOIN users u ON ms.user_id = u.id
            WHERE ms.pack_code = ? AND ms.mode = ? AND ms.status = 'completed'
            ORDER BY ms.score_marks DESC, ms.total_time_seconds ASC, ms.completed_at ASC
            LIMIT 50
        ");
        $stmt->execute([$packCode, $mode]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $leaderboard = [];
        $rank = 1;
        foreach ($rows as $r) {
            $displayName = !empty($r['hide_name']) ? 'Anonymous Cadet' : ($r['user_name'] ?: 'Cadet');
            $leaderboard[] = [
                'rank' => $rank++,
                'session_id' => $r['session_id'],
                'user_name' => htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'),
                'avatar_icon' => $r['avatar_icon'] ?: 'bi-person-circle',
                'score_marks' => (float)$r['score_marks'],
                'total_marks' => (float)$r['total_marks'],
                'accuracy_percent' => (float)$r['accuracy_percent'],
                'total_time_seconds' => (int)$r['total_time_seconds'],
                'completed_at' => $r['completed_at'],
                'mode' => $r['mode']
            ];
        }

        echo json_encode([
            'status' => 'success',
            'pack_code' => $packCode,
            'mode' => $mode,
            'total_entries' => count($leaderboard),
            'leaderboard' => $leaderboard
        ]);
        exit;
    }

    /**
     * Retrieve mode info, available pool size, capping status and duration for UI
     */
    public static function getMockModesInfoJSON(): void {
        header('Content-Type: application/json');
        $packCode = trim($_GET['pack'] ?? 'TCS_MOCK_PACK');
        $db = Database::getConnection();

        $uStmt = $db->prepare("SELECT id, code, title FROM unlockables WHERE code = ? LIMIT 1");
        $uStmt->execute([$packCode]);
        $unlockable = $uStmt->fetch(PDO::FETCH_ASSOC);
        $unlockableId = $unlockable ? (int)$unlockable['id'] : 0;

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

        $poolSize = 0;
        if ($tagFilter) {
            $poolStmt = $db->prepare("
                SELECT COUNT(DISTINCT q.id) 
                FROM questions q
                WHERE (q.source_tag LIKE ? OR q.company_name LIKE ?) AND q.is_published = 1
            ");
            $poolStmt->execute(["%$tagFilter%", "%$tagFilter%"]);
            $poolSize = (int)$poolStmt->fetchColumn();
        }

        $configs = [];
        if ($unlockableId) {
            $cfgStmt = $db->prepare("
                SELECT mode, question_count, duration_minutes, negative_marking, blueprint 
                FROM mock_configs 
                WHERE unlockable_id = ? AND active = 1
            ");
            $cfgStmt->execute([$unlockableId]);
            $configs = $cfgStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $configsByMode = [];
        foreach ($configs as $c) {
            $configsByMode[$c['mode']] = $c;
        }

        $modes = ['quick', 'standard', 'full'];
        $modesInfo = [];
        $isTCS = ($packCode === 'TCS_MOCK_PACK');

        foreach ($modes as $m) {
            $cfg = $configsByMode[$m] ?? null;
            $reqCount = $cfg ? (int)$cfg['question_count'] : ($m === 'quick' ? 30 : ($m === 'full' ? 80 : 60));
            $negMarking = $cfg ? (float)$cfg['negative_marking'] : ($isTCS ? 0.00 : 0.25);
            $durationOverride = ($cfg && !empty($cfg['duration_minutes'])) ? (int)$cfg['duration_minutes'] : null;

            $effectiveCount = ($poolSize > 0 && $poolSize < $reqCount) ? $poolSize : $reqCount;
            $isCapped = ($poolSize > 0 && $poolSize < $reqCount);
            $durationMinutes = ($m === 'full' && $durationOverride) ? $durationOverride : $effectiveCount;

            $modesInfo[$m] = [
                'mode' => $m,
                'title' => match($m) {
                    'quick' => 'Quick Mode',
                    'standard' => 'Standard Mode',
                    'full' => 'Full-Length Mode'
                },
                'requested_questions' => $reqCount,
                'effective_questions' => $effectiveCount,
                'is_capped' => $isCapped,
                'pool_size' => $poolSize,
                'duration_minutes' => $durationMinutes,
                'duration_seconds' => $durationMinutes * 60,
                'negative_marking' => $negMarking
            ];
        }

        echo json_encode([
            'status' => 'success',
            'pack_code' => $packCode,
            'pack_title' => $unlockable['title'] ?? 'Company Mock Pack',
            'pool_size' => $poolSize,
            'modes' => $modesInfo
        ]);
        exit;
    }
}
