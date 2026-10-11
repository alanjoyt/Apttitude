<?php
require_once __DIR__ . '/../Services/DuelService.php';

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

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $wager = max(5, (int)($input['wager_coins'] ?? 10));

        $res = DuelService::createDuel($userId, $wager);
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
        $duelId = (int)($_GET['duel_id'] ?? 0);
        $questions = DuelService::getDuelQuestions($duelId);
        echo json_encode(['status' => 'success', 'questions' => $questions]);
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

        $res = DuelService::submitDuelResults($userId, $duelId, $score);
        echo json_encode($res);
        exit;
    }
}
