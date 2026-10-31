<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Services/AdaptiveEngine.php';
require_once __DIR__ . '/../Services/GamificationEngine.php';
require_once __DIR__ . '/../Services/SpacedRepetitionService.php';
require_once __DIR__ . '/../Services/WalletService.php';
require_once __DIR__ . '/../Services/QuestionParser.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Services/RateLimiter.php';

class PracticeController {

    /**
     * Get next question via AJAX
     */
    public static function getNextQuestionJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $subtopicId = isset($_GET['subtopic_id']) ? (int)$_GET['subtopic_id'] : null;
        $language = !empty($_GET['language']) ? trim($_GET['language']) : null;
        $difficulty = !empty($_GET['difficulty']) ? trim($_GET['difficulty']) : null;
        $track = !empty($_GET['track']) ? trim($_GET['track']) : null;

        // If client requested instant topic restart via reset=1
        if (!empty($_GET['reset']) && $subtopicId) {
            self::executeRestartTopic($userId, $subtopicId);
        }

        // Check Guest Practice Trial limit (5 guest questions)
        if (!$userId) {
            $_SESSION['guest_count'] = ($_SESSION['guest_count'] ?? 0);
            if ($_SESSION['guest_count'] >= 5) {
                echo json_encode([
                    'status' => 'guest_limit_reached',
                    'message' => 'You completed 5 free trial questions! Sign up in 10 seconds to save your XP, Speed Coins & 1-day Streak!'
                ]);
                exit;
            }
        }

        // Session recent question exclusion buffer (anti-repetition)
        $_SESSION['recent_question_ids'] = $_SESSION['recent_question_ids'] ?? [];

        // Time Attack: use a dedicated server-side session key to track seen IDs,
        // avoiding GET URL length limits that cause repeats when the list grows large.
        $mode = !empty($_GET['mode']) ? trim($_GET['mode']) : '';
        if ($mode === 'time_attack') {
            // A new run is signalled by the client passing reset=1
            if (!empty($_GET['reset'])) {
                $_SESSION['ta_seen_ids'] = [];
            }
            $_SESSION['ta_seen_ids'] = $_SESSION['ta_seen_ids'] ?? [];
            $clientExclude = $_SESSION['ta_seen_ids'];
        } else {
            // Support client-passed excluded IDs (from Practice deck, Technical drill, etc.)
            $clientExclude = [];
            if (!empty($_GET['exclude'])) {
                $clientExclude = array_filter(array_map('intval', explode(',', $_GET['exclude'])));
            } else if (!empty($_POST['exclude'])) {
                $clientExclude = array_filter(array_map('intval', explode(',', $_POST['exclude'])));
            }
        }
        $allExclude = array_values(array_unique(array_merge($_SESSION['recent_question_ids'], $clientExclude)));

        $question = AdaptiveEngine::getNextQuestion(
            $userId, 
            $subtopicId, 
            'medium', 
            $allExclude,
            $language,
            $difficulty,
            $track
        );

        if (!$question) {
            // Check true completion in DB: if user has unattempted questions in DB that were excluded by stale session buffer, recover them!
            if ($subtopicId && $userId) {
                try {
                    $db = Database::getConnection();
                    $tqStmt = $db->prepare("SELECT COUNT(*) FROM questions WHERE subtopic_id = ? AND is_published = 1");
                    $tqStmt->execute([$subtopicId]);
                    $totalQ = (int)$tqStmt->fetchColumn();

                    $attStmt = $db->prepare("
                        SELECT COUNT(DISTINCT q.id) 
                        FROM attempts a 
                        JOIN questions q ON a.question_id = q.id 
                        LEFT JOIN user_topic_resets r ON r.user_id = a.user_id AND r.subtopic_id = q.subtopic_id 
                        WHERE a.user_id = ? AND q.subtopic_id = ? AND (r.reset_at IS NULL OR a.attempted_at > r.reset_at) AND q.is_published = 1
                    ");
                    $attStmt->execute([$userId, $subtopicId]);
                    $attemptedQ = (int)$attStmt->fetchColumn();

                    if ($totalQ > 0 && $attemptedQ < $totalQ) {
                        // User has NOT finished all questions! Purge stale session exclusions for this subtopic and re-query
                        $qStmt = $db->prepare("SELECT id FROM questions WHERE subtopic_id = ?");
                        $qStmt->execute([$subtopicId]);
                        $subQIds = array_map('intval', $qStmt->fetchAll(PDO::FETCH_COLUMN));
                        $_SESSION['recent_question_ids'] = array_values(array_diff($_SESSION['recent_question_ids'] ?? [], $subQIds));

                        $question = AdaptiveEngine::getNextQuestion(
                            $userId, 
                            $subtopicId, 
                            'medium', 
                            [],
                            $language,
                            $difficulty,
                            $track
                        );
                    }
                } catch (\Throwable $e) {}
            }
        }

        if (!$question) {
            $subtopicName = '';
            $topicName = '';
            $totalQInSubtopic = 0;
            if ($subtopicId) {
                try {
                    $db = Database::getConnection();
                    $sStmt = $db->prepare("SELECT s.name as subtopic_name, t.name as topic_name FROM subtopics s JOIN topics t ON s.topic_id = t.id WHERE s.id = ?");
                    $sStmt->execute([$subtopicId]);
                    $sRow = $sStmt->fetch();
                    if ($sRow) {
                        $subtopicName = $sRow['subtopic_name'];
                        $topicName = $sRow['topic_name'];
                    }
                    $cntStmt = $db->prepare("SELECT COUNT(*) FROM questions WHERE subtopic_id = ? AND is_published = 1");
                    $cntStmt->execute([$subtopicId]);
                    $totalQInSubtopic = (int)$cntStmt->fetchColumn();
                } catch (\Throwable $e) {}
            }

            if ($subtopicId && $totalQInSubtopic === 0) {
                echo json_encode([
                    'status' => 'empty',
                    'empty_topic' => true,
                    'subtopic_id' => $subtopicId,
                    'subtopic_name' => $subtopicName,
                    'topic_name' => $topicName,
                    'message' => "Practice questions for {$subtopicName} are currently being prepared. Check back shortly!"
                ]);
                exit;
            }

            echo json_encode([
                'status' => 'completed',
                'completed' => true,
                'subtopic_id' => $subtopicId,
                'subtopic_name' => $subtopicName,
                'topic_name' => $topicName,
                'message' => 'All questions in this topic have been completed! Great job!'
            ]);
            exit;
        }

        // Add to recent session buffer (keep modest sliding window to prevent immediate repeat while avoiding starvation)
        $_SESSION['recent_question_ids'][] = (int)$question['id'];
        if (count($_SESSION['recent_question_ids']) > 30) {
            $_SESSION['recent_question_ids'] = array_slice($_SESSION['recent_question_ids'], -20);
        }

        // For Time Attack: persist seen IDs server-side to avoid repeat even across many questions
        if ($mode === 'time_attack') {
            $_SESSION['ta_seen_ids'][] = (int)$question['id'];
        }

        // Sanitize duplicate distractors for legacy questions so client always receives 4 distinct choices
        $optA = $question['option_a'];
        $optB = $question['option_b'];
        $optC = $question['option_c'];
        $optD = $question['option_d'];

        $dupDistractorFixes = [
            174 => ['D' => '80'],
            194 => ['D' => '90'],
            196 => ['B' => '40'],
            203 => ['B' => '65'],
            504 => ['D' => '12 km'],
            508 => ['D' => '12 km'],
            516 => ['D' => '10 km'],
            523 => ['D' => '12 km'],
            525 => ['D' => '12 km'],
            532 => ['D' => '12 km'],
            533 => ['D' => '12 km'],
        ];
        if (isset($dupDistractorFixes[$question['id']])) {
            foreach ($dupDistractorFixes[$question['id']] as $optKey => $newVal) {
                if ($optKey === 'A') $optA = $newVal;
                elseif ($optKey === 'B') $optB = $newVal;
                elseif ($optKey === 'C') $optC = $newVal;
                elseif ($optKey === 'D') $optD = $newVal;
            }
        }

        $rawOpts = [
            'A' => $optA,
            'B' => $optB,
            'C' => $optC,
            'D' => $optD
        ];
        $dbCorrectLetter = strtoupper(trim($question['correct_option'] ?: 'A'));

        // Server-Side Option Randomization & Per-Attempt Session Mapping
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

        $_SESSION['current_question_shuffled_correct'] = $_SESSION['current_question_shuffled_correct'] ?? [];
        $_SESSION['current_question_shuffled_correct'][$question['id']] = $shuffledCorrect;

        $_SESSION['current_question_shuffled_options'] = $_SESSION['current_question_shuffled_options'] ?? [];
        $_SESSION['current_question_shuffled_options'][$question['id']] = $shuffledOptions;
        session_write_close();

        // Parse question text and detect trivial code snippets
        $parsedQ = QuestionParser::parse($question['question_text']);
        $isTrivialCode = QuestionParser::isTrivialCodeSnippet($question['code_snippet'] ?? null, $question['question_text']);

        // Return question metadata with shuffled options
        echo json_encode([
            'status' => 'success',
            'data' => [
                'id' => $question['id'],
                'topic_name' => $question['topic_name'],
                'subtopic_name' => $question['subtopic_name'],
                'difficulty' => $question['difficulty'],
                'question_text' => $parsedQ['full_text'],
                'raw_question_text' => $question['question_text'],
                'parsed_question' => $parsedQ,
                'code_snippet' => $question['code_snippet'] ?? null,
                'is_trivial_code' => $isTrivialCode,
                'language' => $question['language'] ?? null,
                'options' => $shuffledOptions,
                'est_time_seconds' => $question['est_time_seconds'],
                'source_tag' => $parsedQ['source_tag'] ?? $question['source_tag'] ?? null,
                'company_name' => $question['company_name'] ?? null,
                'verification_status' => $question['verification_status'] ?? 'practice_style',
                'round_pattern' => $question['round_pattern'] ?? null
            ]
        ]);
        exit;
    }

    /**
     * Submit and evaluate answer attempt via AJAX
     */
    public static function submitAnswerJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $clientIp = RateLimiter::getClientIp();
        $submitIdent = $userId ? 'user:' . $userId : 'ip:' . $clientIp;

        // Individual rate limit: authenticated students get individual limits (not throttled by shared campus proxies)
        RateLimiter::enforce(
            'answer_submit', 
            $submitIdent, 
            60, 
            60, 
            'Submitting answers too quickly. Please pause a moment.'
        );
        RateLimiter::hit('answer_submit', $submitIdent);

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $questionId = (int)($input['question_id'] ?? 0);
        $selectedOption = strtoupper(trim($input['selected_option'] ?? ''));
        $confidence = strtolower(trim($input['confidence'] ?? 'sure'));
        $rawTime = $input['time_taken_seconds'] ?? null;
        $isImplausible = false;
        if ($rawTime === null || !is_numeric($rawTime)) {
            $timeTaken = 15;
        } else {
            $val = (int)$rawTime;
            if ($val < 2 || $val > 300) {
                $isImplausible = true;
            }
            $timeTaken = max(1, min(300, $val));
        }
        $mode = trim($input['mode'] ?? 'practice');

        // Anti-Exploit: Sanitize parameters & enforce strict server-side bounds
        $confidence = in_array($confidence, ['sure', 'not_sure', 'guessing']) ? $confidence : 'sure';

        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->execute([$questionId]);
        $question = $stmt->fetch();

        if (!$question) {
            echo json_encode(['status' => 'error', 'message' => 'Question not found']);
            exit;
        }

        // Shuffled Option Evaluation: Normalize casing & whitespace
        $rawCorrect = $_SESSION['current_question_shuffled_correct'][$questionId] ?? $question['correct_option'];
        $correctOption = strtoupper(trim($rawCorrect ?? 'A'));

        $shuffledOptions = $_SESSION['current_question_shuffled_options'][$questionId] ?? null;
        if ($shuffledOptions && is_array($shuffledOptions)) {
            $optionsMap = [
                'A' => trim($shuffledOptions['A'] ?? ''),
                'B' => trim($shuffledOptions['B'] ?? ''),
                'C' => trim($shuffledOptions['C'] ?? ''),
                'D' => trim($shuffledOptions['D'] ?? ''),
            ];
        } else {
            $optionsMap = [
                'A' => trim($question['option_a'] ?? ''),
                'B' => trim($question['option_b'] ?? ''),
                'C' => trim($question['option_c'] ?? ''),
                'D' => trim($question['option_d'] ?? ''),
            ];
        }
        $selectedText = $optionsMap[$selectedOption] ?? '';
        $correctText = $optionsMap[$correctOption] ?? '';
        $isCorrect = ($selectedOption === $correctOption) || ($selectedText !== '' && strtolower($selectedText) === strtolower($correctText));
        if ($isCorrect) {
            $correctOption = $selectedOption;
        }

        $cfg = GamificationEngine::getConfig();

        // 1. Audit user attempt history for this question to enforce repeat decay rules
        $prevCorrectCount = 0;
        $prevAttemptsCount = 0;
        $lastCorrectToday = false;

        if ($userId) {
            $historyStmt = $db->prepare("
                SELECT is_correct, DATE(attempted_at) as attempt_date
                FROM attempts
                WHERE user_id = ? AND question_id = ?
                ORDER BY attempted_at DESC
            ");
            $historyStmt->execute([$userId, $questionId]);
            $history = $historyStmt->fetchAll();
            $prevAttemptsCount = count($history);
            foreach ($history as $h) {
                if ((int)$h['is_correct'] === 1) {
                    $prevCorrectCount++;
                    if ($h['attempt_date'] === date('Y-m-d')) {
                        $lastCorrectToday = true;
                    }
                }
            }
        }

        // 2. Base Gamification calculation (includes anti-exploit speed floor check)
        $xpEarned = GamificationEngine::calculateXP(
            $question['difficulty'],
            $isCorrect,
            $timeTaken,
            (int)$question['est_time_seconds'],
            $confidence
        );

        $coinsEarned = GamificationEngine::calculateCoins(
            $question['difficulty'],
            $isCorrect,
            $timeTaken,
            (int)$question['est_time_seconds']
        );

        // 3. Server-Side Boss Question Evaluation (Hard question, max 2 per day)
        $isBoss = false;
        if ($userId && $isCorrect && $question['difficulty'] === 'hard') {
            $bossCheckStmt = $db->prepare("
                SELECT COUNT(*) FROM attempts
                WHERE user_id = ? AND is_boss = 1 AND is_correct = 1 AND DATE(attempted_at) = CURDATE()
            ");
            $bossCheckStmt->execute([$userId]);
            $bossTodayCount = (int)$bossCheckStmt->fetchColumn();

            $maxDailyBoss = (int)($cfg['boss']['max_daily_boss_questions'] ?? 2);
            if ($bossTodayCount < $maxDailyBoss) {
                $isBoss = true;
                $bossXpMult = (float)($cfg['boss']['xp_multiplier'] ?? 1.5);
                $bossBonusCoins = (int)($cfg['boss']['bonus_coins'] ?? 2);
                $xpEarned = (int)round($xpEarned * $bossXpMult);
                $coinsEarned += $bossBonusCoins;
            }
        }

        // 4. Repeat Decay Rules (Applies ONLY to questions already answered CORRECTLY)
        if ($isCorrect) {
            if ($lastCorrectToday || $prevCorrectCount >= 2) {
                // Solved correctly already today or 2+ times in history: 0 XP, 0 coins
                $xpEarned = 0;
                $coinsEarned = 0;
            } elseif ($prevCorrectCount === 1) {
                // Previously solved correctly once before: 25% XP, 0 coins
                $decayFactor = (float)($cfg['repeat_decay']['next_day']['xp_factor'] ?? 0.25);
                $xpEarned = (int)round($xpEarned * $decayFactor);
                $coinsEarned = 0;
            } elseif ($prevAttemptsCount > 0 && $prevCorrectCount === 0) {
                // Retrying a previously WRONG question: 50% XP, 0 coins
                $retryFactor = (float)($cfg['xp']['retry_wrong_xp_factor'] ?? 0.50);
                $xpEarned = (int)round($xpEarned * $retryFactor);
                $coinsEarned = 0;
            }
        }

        // Revision mode awards XP only, never coins
        if ($mode === 'revision') {
            $coinsEarned = 0;
        }

        // 5. Daily Practice Coin Cap (30 coins maximum per day from practice + streaks)
        if ($userId && $coinsEarned > 0) {
            $todayPracticeCoins = GamificationEngine::getTodayPracticeCoins($userId);
            $dailyCap = (int)($cfg['max_daily_practice_coins'] ?? 30);
            $availableCoins = max(0, $dailyCap - $todayPracticeCoins);
            $coinsEarned = min($coinsEarned, $availableCoins);
        }

        $xpResult = ['gained_xp' => 0, 'new_xp' => 0, 'tier_upgraded' => false];
        $streakResult = ['streak' => 0];
        $newCoinBalance = 0;
        $dailyGoalAwarded = false;

        if ($userId) {
            // Save attempt to DB with is_boss tag
            $attemptStmt = $db->prepare("
                INSERT INTO attempts (user_id, question_id, selected_option, is_correct, confidence, time_taken_seconds, xp_earned, coins_earned, mode, is_boss)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $attemptStmt->execute([
                $userId,
                $questionId,
                $selectedOption,
                $isCorrect ? 1 : 0,
                $confidence,
                $timeTaken,
                $xpEarned,
                $coinsEarned,
                $mode ?: 'practice',
                $isBoss ? 1 : 0
            ]);
            $attemptId = (int)$db->lastInsertId();

            // Record XP & Speed Coins
            if ($xpEarned !== 0) {
                $xpResult = User::addXP($userId, $xpEarned);
            }
            if ($coinsEarned > 0) {
                $desc = $isBoss ? "Boss Question Victory #{$questionId}" : "Speed Bonus on Question #{$questionId}";
                WalletService::addCoins($userId, $coinsEarned, 'speed_bonus', $desc);
            }

            // Daily Goal Evaluation: +3 coins for completing 10 questions with >= 60% accuracy (once per day)
            $goalCheckStmt = $db->prepare("
                SELECT COUNT(*) FROM coin_transactions
                WHERE user_id = ? AND transaction_type = 'admin_grant'
                  AND reference_description LIKE 'Daily Goal Bonus%'
                  AND DATE(created_at) = CURDATE()
            ");
            $goalCheckStmt->execute([$userId]);
            if ((int)$goalCheckStmt->fetchColumn() === 0) {
                $todayStatsStmt = $db->prepare("
                    SELECT COUNT(*) as total_att, SUM(is_correct) as correct_att
                    FROM attempts
                    WHERE user_id = ? AND DATE(attempted_at) = CURDATE()
                ");
                $todayStatsStmt->execute([$userId]);
                $todayStats = $todayStatsStmt->fetch();
                $totToday = (int)($todayStats['total_att'] ?? 0);
                $corToday = (int)($todayStats['correct_att'] ?? 0);

                $minGoalQs = (int)($cfg['daily_goal']['min_questions'] ?? 10);
                $minGoalAcc = (int)($cfg['daily_goal']['min_accuracy_percent'] ?? 60);

                if ($totToday >= $minGoalQs && $totToday > 0 && (($corToday / $totToday) * 100) >= $minGoalAcc) {
                    $goalReward = (int)($cfg['daily_goal']['coin_reward'] ?? 3);
                    WalletService::addCoins(
                        $userId,
                        $goalReward,
                        'admin_grant',
                        "Daily Goal Bonus: {$totToday} questions completed with " . round(($corToday / $totToday) * 100) . "% accuracy"
                    );
                    $dailyGoalAwarded = true;
                }
            }

            $newCoinBalance = WalletService::getUserBalance($userId);

            $streakResult = GamificationEngine::updateStreak($userId);
            SpacedRepetitionService::recordAttemptProgress($userId, $question['subtopic_id'], $isCorrect, $confidence);

            // Trigger Referral qualification check (awards after 20 questions completed)
            require_once __DIR__ . '/../Services/ReferralService.php';
            ReferralService::checkAndAwardReferralBonus($userId);

            // Trigger Daily Quest Progress
            require_once __DIR__ . '/../Services/QuestService.php';
            QuestService::triggerQuestProgress($userId, 'solve_count', 1);
            if ($isCorrect && $question['difficulty'] === 'hard') {
                QuestService::triggerQuestProgress($userId, 'hard_questions', 1);
            }
            if ($isCorrect && $confidence === 'sure') {
                QuestService::triggerQuestProgress($userId, 'sure_correct', 1);
            }
        } else {
            // Guest trial session increment
            $_SESSION['guest_count'] = ($_SESSION['guest_count'] ?? 0) + 1;
            $attemptId = 0;
        }

        // Update question attempt stats
        $db->prepare("UPDATE questions SET total_attempts_count = total_attempts_count + 1, total_correct_count = total_correct_count + ? WHERE id = ?")
           ->execute([$isCorrect ? 1 : 0, $questionId]);
        session_write_close();

        echo json_encode([
            'status' => 'success',
            'attempt_id' => $attemptId,
            'is_correct' => $isCorrect,
            'correct_option' => $correctOption,
            'time_taken_seconds' => $timeTaken,
            'is_implausible_time' => $isImplausible,
            'explanation' => $question['explanation'],
            'xp_earned' => $xpEarned,
            'coins_earned' => $coinsEarned,
            'is_boss' => $isBoss,
            'daily_goal_awarded' => $dailyGoalAwarded,
            'new_coin_balance' => $newCoinBalance,
            'xp_data' => $xpResult,
            'streak' => $streakResult['streak'],
            'trigger_confetti' => ($isCorrect && ($xpEarned >= 25 || $coinsEarned > 0 || $isBoss)),
            'trigger_shake' => !$isCorrect,
            'guest_remaining' => $userId ? null : (5 - ($_SESSION['guest_count'] ?? 0))
        ]);
        exit;
    }

    /**
     * Tag mistake category for a wrong answer
     */
    public static function tagMistakeJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $attemptId = (int)($input['attempt_id'] ?? 0);
        $mistakeType = trim($input['mistake_type'] ?? '');

        $validTypes = ['concept_gap', 'calculation_error', 'misread_question', 'ran_out_of_time'];
        if (!$attemptId || !in_array($mistakeType, $validTypes)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid mistake classification parameters']);
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE attempts SET mistake_type = ? WHERE id = ? " . ($userId ? "AND user_id = ?" : ""));
        $params = $userId ? [$mistakeType, $attemptId, $userId] : [$mistakeType, $attemptId];
        $stmt->execute($params);

        echo json_encode([
            'status' => 'success',
            'message' => 'Mistake category tagged successfully! Revision algorithm updated.'
        ]);
        exit;
    }

    /**
     * Explicitly Save User Practice Progress & Sync Session Attempts
     */
    public static function saveProgressJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode([
                'status' => 'guest',
                'message' => 'Guest session active! Create a free account or log in to save your progress permanently.'
            ]);
            exit;
        }

        $db = Database::getConnection();

        // Sync streak & last active timestamp
        GamificationEngine::updateStreak($userId);

        // Fetch user context statistics
        $user = User::findById($userId);
        $attemptsCount = $db->prepare("SELECT COUNT(*) FROM attempts WHERE user_id = ?");
        $attemptsCount->execute([$userId]);
        $totalAttempted = (int)$attemptsCount->fetchColumn();

        echo json_encode([
            'status' => 'success',
            'message' => "Practice progress saved successfully! {$totalAttempted} practice attempts recorded.",
            'total_attempted' => $totalAttempted,
            'xp' => $user['total_xp'] ?? 0,
            'coins' => $user['coins'] ?? 0,
            'streak' => $user['current_streak'] ?? 0
        ]);
        exit;
    }

    /**
     * Lifeline: Eliminate 1 Wrong Option for 5 Speed Coins
     */
    public static function useHintJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $rawQId = $input['question_id'] ?? null;
        if ($rawQId === null || !is_numeric($rawQId) || is_array($rawQId)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid question ID']);
            exit;
        }

        $questionId = (int)$rawQId;
        if ((float)$rawQId != $questionId || $questionId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid question ID']);
            exit;
        }

        // Reject client-side price/cost tampering
        if (isset($input['cost']) || isset($input['price']) || isset($input['amount'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Price tampering detected: lifeline cost is authoritative and managed exclusively by server configuration.'
            ]);
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT q.*, s.name as subtopic_name, s.code as subtopic_code, t.name as topic_name, t.code as topic_code
            FROM questions q
            JOIN subtopics s ON q.subtopic_id = s.id
            JOIN topics t ON s.topic_id = t.id
            WHERE q.id = ?
        ");
        $stmt->execute([$questionId]);
        $question = $stmt->fetch();

        if (!$question) {
            echo json_encode(['status' => 'error', 'message' => 'Question not found']);
            exit;
        }

        // Deduct Speed Coins for Lifeline (5 Speed Coins from config)
        $cfg = GamificationEngine::getConfig();
        $hintCost = (int)($cfg['store']['hint_cost'] ?? 5);
        $newCoinBalance = 0;

        if ($userId) {
            $currentCoins = WalletService::getUserBalance($userId);
            if ($currentCoins < $hintCost) {
                echo json_encode([
                    'status' => 'insufficient_coins',
                    'message' => "You need at least {$hintCost} Speed Coins to eliminate a wrong option! Earn coins by solving questions quickly or maintaining your daily streak.",
                    'current_coins' => $currentCoins,
                    'hint_cost' => $hintCost
                ]);
                exit;
            }

            $spent = WalletService::spendCoins($userId, $hintCost, "Eliminate Wrong Option on Question #{$questionId}");
            if (!$spent) {
                echo json_encode([
                    'status' => 'insufficient_coins',
                    'message' => 'Unable to deduct Speed Coins. Insufficient balance.',
                    'current_coins' => $currentCoins,
                    'hint_cost' => $hintCost
                ]);
                exit;
            }
            $newCoinBalance = WalletService::getUserBalance($userId);
        } else {
            // Guest trial mode: allow 1 hint free
            $newCoinBalance = 0;
        }

        // 1. Identify the ground-truth correct option and its exact text from database
        $dbCorrectLetter = strtoupper(trim($question['correct_option'] ?: 'A'));
        $dbCorrectText = trim($question['option_' . strtolower($dbCorrectLetter)] ?? '');

        // 2. Determine the active options mapping (A, B, C, D) currently shown to user
        // Priority A: Shuffled options stored in server session by getNextQuestionJSON()
        $shuffledOptions = $_SESSION['current_question_shuffled_options'][$questionId] ?? null;
        $activeCorrectLetter = $_SESSION['current_question_shuffled_correct'][$questionId] ?? null;

        // Priority B: Client passed visible_options map from DOM { "A": "...", "B": "..." }
        if ((!$shuffledOptions || !$activeCorrectLetter) && !empty($input['visible_options']) && is_array($input['visible_options'])) {
            $clientVisible = [];
            foreach (['A', 'B', 'C', 'D'] as $l) {
                if (isset($input['visible_options'][$l])) {
                    $clientVisible[$l] = trim((string)$input['visible_options'][$l]);
                }
            }
            if (count($clientVisible) === 4) {
                $shuffledOptions = $clientVisible;
                foreach ($clientVisible as $l => $txt) {
                    if (strcasecmp($txt, $dbCorrectText) === 0) {
                        $activeCorrectLetter = $l;
                        break;
                    }
                }
            }
        }

        // Priority C: Database raw options fallback
        if (!$shuffledOptions || !is_array($shuffledOptions)) {
            $shuffledOptions = [
                'A' => trim($question['option_a'] ?? ''),
                'B' => trim($question['option_b'] ?? ''),
                'C' => trim($question['option_c'] ?? ''),
                'D' => trim($question['option_d'] ?? ''),
            ];
            $activeCorrectLetter = $dbCorrectLetter;
        }

        if (!$activeCorrectLetter) {
            $activeCorrectLetter = $dbCorrectLetter;
        }

        // 3. Find INCORRECT options to eliminate
        // An option can ONLY be eliminated if:
        // - Its letter is NOT $activeCorrectLetter
        // - AND its text is NOT identical to $dbCorrectText
        // - AND its text is not empty
        $candidates = [];
        foreach (['A', 'B', 'C', 'D'] as $opt) {
            $optText = trim($shuffledOptions[$opt] ?? '');
            if ($opt !== $activeCorrectLetter && strcasecmp($optText, $dbCorrectText) !== 0 && $optText !== '') {
                $candidates[] = $opt;
            }
        }

        if (empty($candidates)) {
            // Absolute fallback: any option key not matching activeCorrectLetter
            $candidates = array_values(array_filter(['A', 'B', 'C', 'D'], fn($o) => $o !== $activeCorrectLetter));
        }

        // 4. Check if an option was already eliminated in this session for this question
        $_SESSION['current_question_eliminated_option'] = $_SESSION['current_question_eliminated_option'] ?? [];
        if (!empty($_SESSION['current_question_eliminated_option'][$questionId]) && in_array($_SESSION['current_question_eliminated_option'][$questionId], $candidates, true)) {
            $eliminatedOpt = $_SESSION['current_question_eliminated_option'][$questionId];
        } else {
            // Deterministically pick one wrong option using question ID as seed
            $seed = (int)$questionId;
            $idx = abs($seed * 31 + 7) % count($candidates);
            $eliminatedOpt = $candidates[$idx];
            $_SESSION['current_question_eliminated_option'][$questionId] = $eliminatedOpt;
        }
        session_write_close();

        $eliminatedText = trim($shuffledOptions[$eliminatedOpt] ?? '');

        echo json_encode([
            'status' => 'success',
            'message' => "Option {$eliminatedOpt} eliminated for {$hintCost} Speed Coins.",
            'hint_cost' => $hintCost,
            'eliminated_option' => $eliminatedOpt,
            'eliminated_text' => $eliminatedText,
            'new_coin_balance' => $newCoinBalance,
            'is_guest' => !$userId,
            'hint' => [
                'title' => 'Eliminate 1 Wrong Option',
                'eliminated_option' => $eliminatedOpt,
                'eliminated_text' => $eliminatedText,
                'tip' => "Option {$eliminatedOpt} is incorrect and has been crossed out. Choose between the remaining 3 options!"
            ]
        ]);
        exit;
    }

    /**
     * Server-side Quiz Session Auto-Save (CSRF + Auth Protected)
     */
    public static function saveQuizSessionJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['status' => 'unauthorized', 'message' => 'Authentication required']);
            exit;
        }

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?? $_POST;

        // CSRF Verification
        $submittedToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrf_token'] ?? null);
        if (!empty($_SESSION['csrf_token']) && $submittedToken !== null) {
            if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
                http_response_code(403);
                echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
                exit;
            }
        }

        // Server-side Input Validation
        $subtopicId = isset($input['subtopic_id']) ? (int)$input['subtopic_id'] : null;
        $questionId = isset($input['question_id']) ? (int)$input['question_id'] : null;
        $sessionType = preg_replace('/[^a-z0-9_-]/i', '', $input['session_type'] ?? 'practice');

        if ($subtopicId !== null && $subtopicId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid subtopic_id']);
            exit;
        }
        if ($questionId !== null && $questionId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid question_id']);
            exit;
        }

        $sessionData = json_encode($input['session_data'] ?? []);

        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO user_practice_sessions (user_id, session_type, subtopic_id, question_id, session_data)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                subtopic_id = VALUES(subtopic_id),
                question_id = VALUES(question_id),
                session_data = VALUES(session_data),
                updated_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([$userId, $sessionType, $subtopicId, $questionId, $sessionData]);

        echo json_encode(['status' => 'success', 'message' => 'Session auto-saved']);
        exit;
    }

    /**
     * Server-side Quiz Session Resume (Strict Isolation: User B can NEVER read User A's session)
     */
    public static function getQuizSessionJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['status' => 'unauthorized', 'message' => 'Authentication required']);
            exit;
        }

        $sessionType = preg_replace('/[^a-z0-9_-]/i', '', $_GET['session_type'] ?? 'practice');

        $db = Database::getConnection();
        // Crucial security binding: user_id is bound STRICTLY to authenticated session user_id
        $stmt = $db->prepare("
            SELECT s.*, sub.name as subtopic_name, top.name as topic_name
            FROM user_practice_sessions s
            LEFT JOIN subtopics sub ON s.subtopic_id = sub.id
            LEFT JOIN topics top ON sub.topic_id = top.id
            WHERE s.user_id = ? AND s.session_type = ?
            LIMIT 1
        ");
        $stmt->execute([$userId, $sessionType]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            echo json_encode(['status' => 'empty']);
            exit;
        }

        echo json_encode([
            'status' => 'success',
            'session' => [
                'subtopic_id' => $row['subtopic_id'],
                'question_id' => $row['question_id'],
                'session_type' => $row['session_type'],
                'topic_name' => $row['topic_name'],
                'subtopic_name' => $row['subtopic_name'],
                'updated_at' => $row['updated_at']
            ]
        ]);
        exit;
    }

    /**
     * Server-side Quiz Session Clear
     */
    public static function clearQuizSessionJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['status' => 'unauthorized', 'message' => 'Authentication required']);
            exit;
        }

        $sessionType = preg_replace('/[^a-z0-9_-]/i', '', $_GET['session_type'] ?? 'practice');
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM user_practice_sessions WHERE user_id = ? AND session_type = ?");
        $stmt->execute([$userId, $sessionType]);

        echo json_encode(['status' => 'success']);
        exit;
    }

    /**
     * Core helper to restart a practice topic (resets anti-repetition tracking)
     */
    public static function executeRestartTopic(?int $userId, int $subtopicId): void {
        $db = Database::getConnection();

        if ($userId) {
            // Ensure table exists
            try {
                $db->exec("
                    CREATE TABLE IF NOT EXISTS `user_topic_resets` (
                      `id` INT AUTO_INCREMENT PRIMARY KEY,
                      `user_id` INT NOT NULL,
                      `subtopic_id` INT NOT NULL,
                      `reset_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                      UNIQUE KEY `uniq_user_subtopic` (`user_id`, `subtopic_id`),
                      FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                      FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics`(`id`) ON DELETE CASCADE
                    ) ENGINE=InnoDB;
                ");
            } catch (\Throwable $e) {}

            // Record/update reset timestamp
            try {
                $stmt = $db->prepare("
                    INSERT INTO user_topic_resets (user_id, subtopic_id, reset_at) 
                    VALUES (?, ?, NOW()) 
                    ON DUPLICATE KEY UPDATE reset_at = NOW()
                ");
                $stmt->execute([$userId, $subtopicId]);
            } catch (\Throwable $e) {}

            // Reset mastery progress for this subtopic to Level 1
            try {
                $upStmt = $db->prepare("
                    UPDATE user_progress 
                    SET total_attempted = 0, total_correct = 0, current_mastery_level = 1 
                    WHERE user_id = ? AND subtopic_id = ?
                ");
                $upStmt->execute([$userId, $subtopicId]);
            } catch (\Throwable $e) {}

            // Clear saved practice session for this subtopic
            try {
                $db->prepare("DELETE FROM user_practice_sessions WHERE user_id = ? AND subtopic_id = ?")->execute([$userId, $subtopicId]);
            } catch (\Throwable $e) {}
        } else {
            $_SESSION['guest_count'] = 0;
        }

        // Clear session buffer question IDs belonging to this subtopic
        try {
            $qStmt = $db->prepare("SELECT id FROM questions WHERE subtopic_id = ?");
            $qStmt->execute([$subtopicId]);
            $subQIds = array_map('intval', $qStmt->fetchAll(PDO::FETCH_COLUMN));
            if (!empty($_SESSION['recent_question_ids'])) {
                $_SESSION['recent_question_ids'] = array_values(array_diff($_SESSION['recent_question_ids'], $subQIds));
            }
        } catch (\Throwable $e) {}
    }

    /**
     * AJAX endpoint to restart a practice topic
     */
    public static function restartTopicJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $subtopicId = isset($input['subtopic_id']) ? (int)$input['subtopic_id'] : (isset($_GET['subtopic_id']) ? (int)$_GET['subtopic_id'] : null);

        if (!$subtopicId) {
            echo json_encode(['status' => 'error', 'message' => 'Missing subtopic_id parameter']);
            exit;
        }

        self::executeRestartTopic($userId, $subtopicId);

        echo json_encode([
            'status' => 'success',
            'message' => 'Topic restarted successfully. You can now practice from question 1 without repeating questions.',
            'subtopic_id' => $subtopicId
        ]);
        exit;
    }
}
