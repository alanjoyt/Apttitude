<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Services/WalletService.php';

class StoreController {

    /**
     * AJAX endpoint to unlock an item using Speed Coins
     */
    public static function unlockItemJSON(): void {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'Please log in to unlock store items!']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $unlockableId = (int)($input['unlockable_id'] ?? 0);

        if (!$unlockableId) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid store item selected']);
            exit;
        }

        $result = WalletService::unlockItem($userId, $unlockableId);
        echo json_encode($result);
        exit;
    }
}
