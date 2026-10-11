<?php
require_once __DIR__ . '/../Services/QuestService.php';

class QuestController {

    public static function getDailyQuestsJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            exit;
        }

        $quests = QuestService::getUserDailyQuests($userId);
        echo json_encode(['status' => 'success', 'quests' => $quests]);
        exit;
    }

    public static function claimQuestJSON(): void {
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
        $questId = (int)($input['quest_id'] ?? 0);

        if ($questId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Quest ID']);
            exit;
        }

        $result = QuestService::claimQuestReward($userId, $questId);
        echo json_encode($result);
        exit;
    }
}
