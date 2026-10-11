<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Services/AdaptiveEngine.php';
require_once __DIR__ . '/../Services/GamificationEngine.php';
require_once __DIR__ . '/../Services/SpacedRepetitionService.php';
require_once __DIR__ . '/../Services/WalletService.php';
require_once __DIR__ . '/../Services/QuestionParser.php';
require_once __DIR__ . '/../Models/User.php';

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

        $question = AdaptiveEngine::getNextQuestion(
            $userId, 
            $subtopicId, 
            'medium', 
            $_SESSION['recent_question_ids'],
            $language,
            $difficulty,
            $track
        );

        if (!$question) {
            echo json_encode([
                'status' => 'empty',
                'message' => 'No more unattempted questions in this category! Great job!'
            ]);
            exit;
        }

        // Add to recent session buffer
        $_SESSION['recent_question_ids'][] = (int)$question['id'];
        if (count($_SESSION['recent_question_ids']) > 50) {
            $_SESSION['recent_question_ids'] = array_slice($_SESSION['recent_question_ids'], -50);
        }

        // Store shuffled correct option mapping and options map for verification
        $_SESSION['current_question_shuffled_correct'] = $_SESSION['current_question_shuffled_correct'] ?? [];
        $_SESSION['current_question_shuffled_correct'][$question['id']] = $question['correct_option'];

        // Parse question text and detect trivial code snippets
        $parsedQ = QuestionParser::parse($question['question_text']);
        $isTrivialCode = QuestionParser::isTrivialCodeSnippet($question['code_snippet'] ?? null, $question['question_text']);

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
        // Record exact shuffled options served to the client in session
        $_SESSION['current_question_shuffled_options'] = $_SESSION['current_question_shuffled_options'] ?? [];
        $_SESSION['current_question_shuffled_options'][$question['id']] = [
            'A' => $optA,
            'B' => $optB,
            'C' => $optC,
            'D' => $optD
        ];

        // Return question metadata
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
                'options' => [
                    'A' => $optA,
                    'B' => $optB,
                    'C' => $optC,
                    'D' => $optD
                ],
                'est_time_seconds' => $question['est_time_seconds'],
                'source_tag' => $parsedQ['source_tag'] ?? $question['source_tag'] ?? null
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
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $questionId = (int)($input['question_id'] ?? 0);
        $selectedOption = strtoupper(trim($input['selected_option'] ?? ''));
        $confidence = strtolower(trim($input['confidence'] ?? 'sure'));
        $timeTaken = (int)($input['time_taken_seconds'] ?? 15);

        // Anti-Exploit: Sanitize parameters & enforce strict server-side bounds
        $confidence = in_array($confidence, ['sure', 'not_sure', 'guessing']) ? $confidence : 'sure';
        $timeTaken = max(1, min(300, $timeTaken));

        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->execute([$questionId]);
        $question = $stmt->fetch();

        if (!$question) {
            echo json_encode(['status' => 'error', 'message' => 'Question not found']);
            exit;
        }

        // Anti-Exploit: Check if user already attempted this question TODAY
        $alreadyAttemptedToday = false;
        if ($userId) {
            $alreadyStmt = $db->prepare("
                SELECT COUNT(*) FROM attempts 
                WHERE user_id = ? AND question_id = ? AND DATE(attempted_at) = CURDATE()
            ");
            $alreadyStmt->execute([$userId, $questionId]);
            if ((int)$alreadyStmt->fetchColumn() > 0) {
                $alreadyAttemptedToday = true;
            }
        }

        // Shuffled Option Evaluation: Normalize casing & whitespace
        $rawCorrect = $_SESSION['current_question_shuffled_correct'][$questionId] ?? $question['correct_option'];
        $correctOption = strtoupper(trim($rawCorrect ?? 'A'));

        // Match by option letter OR by option text content (handles duplicate distractors and exact answers)
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
        
        // Anti-Exploit: Zero out XP & Coins if solved in < 1 second (bot script speed) OR repeated on same day!
        if ($alreadyAttemptedToday || $timeTaken < 1) {
            $xpEarned = 0;
            $coinsEarned = 0;
        } else {
            $xpEarned = GamificationEngine::calculateXP(
                $question['difficulty'],
                $isCorrect,
                $timeTaken,
                $question['est_time_seconds'],
                $confidence
            );

            $coinsEarned = GamificationEngine::calculateCoins(
                $question['difficulty'],
                $isCorrect,
                $timeTaken,
                $question['est_time_seconds']
            );
        }

        $xpResult = ['gained_xp' => 0, 'new_xp' => 0, 'tier_upgraded' => false];
        $streakResult = ['streak' => 0];
        $newCoinBalance = 0;

        if ($userId) {
            // Save attempt to DB
            $attemptStmt = $db->prepare("
                INSERT INTO attempts (user_id, question_id, selected_option, is_correct, confidence, time_taken_seconds, xp_earned, coins_earned, mode)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'practice')
            ");
            $attemptStmt->execute([$userId, $questionId, $selectedOption, $isCorrect ? 1 : 0, $confidence, $timeTaken, $xpEarned, $coinsEarned]);
            $attemptId = (int)$db->lastInsertId();

            // Record XP & Speed Coins (handles positive XP gain and negative XP penalty)
            if ($xpEarned !== 0) {
                $xpResult = User::addXP($userId, $xpEarned);
            }
            if ($coinsEarned > 0) {
                WalletService::addCoins($userId, $coinsEarned, 'speed_bonus', "Speed Bonus on Question #{$questionId}");
            }
            $newCoinBalance = WalletService::getUserBalance($userId);

            $streakResult = GamificationEngine::updateStreak($userId);
            SpacedRepetitionService::recordAttemptProgress($userId, $question['subtopic_id'], $isCorrect, $confidence);

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

        echo json_encode([
            'status' => 'success',
            'attempt_id' => $attemptId,
            'is_correct' => $isCorrect,
            'correct_option' => $correctOption,
            'explanation' => $question['explanation'],
            'xp_earned' => $xpEarned,
            'coins_earned' => $coinsEarned,
            'new_coin_balance' => $newCoinBalance,
            'xp_data' => $xpResult,
            'streak' => $streakResult['streak'],
            'trigger_confetti' => ($isCorrect && ($xpEarned >= 25 || $coinsEarned > 0)),
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
     * Unlock Formula Hint using 1 Speed Coin
     */
    public static function useHintJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $questionId = (int)($input['question_id'] ?? 0);

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

        // Subtopic-tailored strategy hints
        $hintsMap = [
            'QUANT_SDT' => [
                'title' => 'Speed, Distance & Time Formula Hint',
                'formula' => 'Speed = Distance / Time. To convert km/h to m/s, multiply by 5/18. For two bodies moving in opposite directions, Relative Speed = S1 + S2. For same direction, Relative Speed = |S1 - S2|.',
                'strategy' => 'Always verify consistent units (km with hours, meters with seconds) before computing.'
            ],
            'QUANT_PERC' => [
                'title' => 'Percentages & Profit/Loss Hint',
                'formula' => 'Profit % = ((SP - CP) / CP) * 100. Loss % = ((CP - SP) / CP) * 100. Discount % is always computed on Marked Price (MP).',
                'strategy' => 'Assume a base value of 100 for unknown variables to solve percentages swiftly.'
            ],
            'QUANT_RATIO' => [
                'title' => 'Ratio & Proportion Hint',
                'formula' => 'If a : b = c : d, then a * d = b * c (Product of extremes = Product of means). Dividing X in ratio a:b gives (a / (a+b)) * X.',
                'strategy' => 'Express values using a common multiplier k (e.g. 3k, 4k) to form straightforward linear equations.'
            ],
            'LR_SEATING' => [
                'title' => 'Seating Arrangements Strategy',
                'formula' => 'In circular tables facing center: clockwise = left, anti-clockwise = right. For linear rows facing north: right is your right, left is your left.',
                'strategy' => 'Start with definite anchor clues (e.g. "X sits 3rd to left of Y") and avoid tentative branches until anchors are locked.'
            ],
            'LR_BLOOD' => [
                'title' => 'Blood Relations Strategy',
                'formula' => 'Map relationships across generation tiers: Parents/Uncles (+1), Siblings/Spouse (0), Children/Nephews (-1). Use (+) for Male, (-) for Female.',
                'strategy' => 'Decode composite statements from the end backwards (e.g. "my father\'s sister\'s daughter").'
            ],
            'LR_SERIES' => [
                'title' => 'Number & Letter Series Pattern Hint',
                'formula' => 'Check: 1) Differences between adjacent numbers. 2) Second-order differences. 3) Squares or cubes (n² ± k, n³ ± k). 4) Alternating operations (+a, ×b).',
                'strategy' => 'If terms increase gradually, look for addition. If jumping rapidly, test multiplication, squares, or primes.'
            ],
            'VERBAL_RC' => [
                'title' => 'Reading Comprehension Strategy',
                'formula' => 'The main thesis is commonly positioned in the opening or closing sentences of paragraphs. Watch out for qualifying language.',
                'strategy' => 'Eliminate choices with absolute terms ("always", "never", "all"). Rely only on direct textual evidence.'
            ],
            'VERBAL_SC' => [
                'title' => 'Sentence Correction & Grammar Hint',
                'formula' => 'Subject-Verb Agreement: Collective nouns (team, committee, family, jury) take singular verbs ("is", "was", "has") when acting as a unified whole.',
                'strategy' => 'Ignore parenthetical phrases between the subject and verb (e.g., "The captain, along with the players, is [not are] ready").'
            ],
            'DI_BAR_TABLE' => [
                'title' => 'Data Interpretation Tip',
                'formula' => 'Percentage Growth = ((Final - Initial) / Initial) * 100. Average = Total Sum / Count.',
                'strategy' => 'Round large numbers to 2 significant digits to approximate the correct answer before detailed calculation.'
            ],
        ];

        $code = $question['subtopic_code'] ?? '';
        $hintData = $hintsMap[$code] ?? [
            'title' => 'Topic Strategy & Formula Hint',
            'formula' => 'Carefully identify the given parameters vs the unknown target. Eliminate contradictory answer choices.',
            'strategy' => 'Focus on foundational formulas and core grammatical or arithmetic consistency.'
        ];

        // Deduct 1 Speed Coin if logged in
        $newCoinBalance = 0;
        if ($userId) {
            $currentCoins = WalletService::getUserBalance($userId);
            if ($currentCoins < 1) {
                echo json_encode([
                    'status' => 'insufficient_coins',
                    'message' => 'You need at least 1 Speed Coin to unlock this Formula Hint! Earn coins by solving questions quickly or maintaining your daily streak.',
                    'current_coins' => $currentCoins
                ]);
                exit;
            }

            $spent = WalletService::spendCoins($userId, 1, "Formula Hint on Question #{$questionId}");
            if (!$spent) {
                echo json_encode([
                    'status' => 'insufficient_coins',
                    'message' => 'Unable to deduct Speed Coin. Insufficient balance.',
                    'current_coins' => $currentCoins
                ]);
                exit;
            }
            $newCoinBalance = WalletService::getUserBalance($userId);
        } else {
            // Guest trial mode: allow 1 hint free
            $newCoinBalance = 0;
        }

        echo json_encode([
            'status' => 'success',
            'message' => '1 Speed Coin used for Formula Hint.',
            'new_coin_balance' => $newCoinBalance,
            'is_guest' => !$userId,
            'hint' => $hintData
        ]);
        exit;
    }
}
