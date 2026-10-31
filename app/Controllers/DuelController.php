<?php
require_once __DIR__ . '/../Services/DuelService.php';
require_once __DIR__ . '/../Services/RateLimiter.php';

class DuelController {

    public static function createDuelJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            exit;
        }

        // Authenticated student-keyed rate limit: avoids shared-proxy lockout across dorms
        RateLimiter::enforce(
            'duel_create', 
            'user:' . $userId, 
            10, 
            300, 
            'Too many duel challenges created recently. Please wait a few minutes.'
        );
        RateLimiter::hit('duel_create', 'user:' . $userId);

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $cfg = GamificationEngine::getConfig();
        $allowedWagers = $cfg['duels']['pvp_wagers'] ?? [10, 25, 50];

        $rawWager = $input['wager_coins'] ?? null;
        if ($rawWager === null || !is_numeric($rawWager) || is_array($rawWager)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error', 
                'message' => 'Invalid wager format. Please specify an integer wager amount.'
            ]);
            exit;
        }

        $wager = (int)$rawWager;
        if ((float)$rawWager != $wager) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error', 
                'message' => 'Wager must be an integer amount (no decimals allowed).'
            ]);
            exit;
        }

        if (!in_array($wager, $allowedWagers, true)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error', 
                'message' => 'Invalid wager amount. Allowed stakes are: ' . implode(', ', $allowedWagers) . ' Speed Coins.'
            ]);
            exit;
        }

        $res = DuelService::createDuel($userId, $wager);
        if (($res['status'] ?? '') === 'error') {
            http_response_code(400);
        }
        echo json_encode($res);
        exit;
    }

    public static function createBotDuelJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'Please log in to challenge the practice bot!']);
            exit;
        }

        $res = DuelService::createBotDuel($userId);
        echo json_encode($res);
        exit;
    }

    public static function joinDuelJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $duelId = (int)($input['duel_id'] ?? 0);

        $res = DuelService::joinDuel($userId, $duelId);
        echo json_encode($res);
        exit;
    }

    public static function getDuelQuestionsJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            exit;
        }

        $duelId = (int)($_GET['duel_id'] ?? 0);
        $db = Database::getConnection();
        $dStmt = $db->prepare("SELECT challenger_id, opponent_id FROM duels WHERE id = ?");
        $dStmt->execute([$duelId]);
        $duel = $dStmt->fetch(PDO::FETCH_ASSOC);

        if (!$duel) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Duel not found']);
            exit;
        }

        // IDOR Guard: Only participants can access duel questions
        if ($duel['challenger_id'] != $userId && $duel['opponent_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Forbidden: You are not a participant in this duel']);
            exit;
        }

        $questions = DuelService::getDuelQuestions($duelId);

        $_SESSION['duel_mappings'] = $_SESSION['duel_mappings'] ?? [];
        $_SESSION['duel_mappings'][$duelId] = [];

        $sanitizedQuestions = [];
        $keys = ['A', 'B', 'C', 'D'];

        foreach ($questions as $q) {
            $rawOpts = [
                'A' => $q['option_a'],
                'B' => $q['option_b'],
                'C' => $q['option_c'],
                'D' => $q['option_d']
            ];
            $dbCorrectLetter = strtoupper(trim($q['correct_option'] ?: 'A'));

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

            $_SESSION['duel_mappings'][$duelId][$q['id']] = [
                'correct_option' => $shuffledCorrect,
                'options' => $shuffledOptions
            ];

            // Crucial security fix: Never expose correct_option to client!
            $sanitizedQuestions[] = [
                'id' => (int)$q['id'],
                'question_text' => $q['question_text'],
                'code_snippet' => $q['code_snippet'] ?? null,
                'language' => $q['language'] ?? null,
                'option_a' => $shuffledOptions['A'],
                'option_b' => $shuffledOptions['B'],
                'option_c' => $shuffledOptions['C'],
                'option_d' => $shuffledOptions['D'],
                'question_order' => $q['question_order']
            ];
        }

        echo json_encode(['status' => 'success', 'questions' => $sanitizedQuestions]);
        exit;
    }

    /**
     * Check single duel question answer via server mapping
     * Provides instantaneous tamper-proof feedback without leaking answers in advance
     */
    public static function checkDuelAnswerJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $duelId = (int)($input['duel_id'] ?? 0);
        $qId = (int)($input['question_id'] ?? 0);
        $selectedOpt = strtoupper(trim((string)($input['selected_option'] ?? '')));

        $mapping = $_SESSION['duel_mappings'][$duelId][$qId] ?? null;
        if (!$mapping) {
            echo json_encode(['status' => 'error', 'message' => 'Duel question mapping not found']);
            exit;
        }

        $correctOpt = $mapping['correct_option'];
        $isCorrect = ($selectedOpt !== '' && $selectedOpt === $correctOpt);

        echo json_encode([
            'status' => 'success',
            'is_correct' => $isCorrect,
            'correct_option' => $correctOpt
        ]);
        exit;
    }

    public static function submitDuelJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $duelId = (int)($input['duel_id'] ?? 0);
        $score = (int)($input['score'] ?? 0);

        // Server-Side Verification: If client submits an answers map, evaluate against duel session mapping
        $answers = $input['answers'] ?? null;
        if (is_array($answers) && isset($_SESSION['duel_mappings'][$duelId])) {
            $verifiedScore = 0;
            foreach ($answers as $qId => $userAns) {
                $cleanAns = strtoupper(trim((string)$userAns));
                $expected = $_SESSION['duel_mappings'][$duelId][$qId]['correct_option'] ?? null;
                if ($expected && $cleanAns === $expected) {
                    $verifiedScore++;
                }
            }
            $score = $verifiedScore;
        }

        $res = DuelService::submitDuelResults($userId, $duelId, $score);
        echo json_encode($res);
        exit;
    }

    public static function forfeitDuelJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $duelId = (int)($input['duel_id'] ?? 0);

        $res = DuelService::forfeitDuel($userId, $duelId);
        echo json_encode($res);
        exit;
    }

    public static function claimAbandonmentVictoryJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $duelId = (int)($input['duel_id'] ?? 0);

        $res = DuelService::claimAbandonmentVictory($userId, $duelId);
        echo json_encode($res);
        exit;
    }
}
