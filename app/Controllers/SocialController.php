<?php
require_once __DIR__ . '/../Services/SocialService.php';

class SocialController {

    public static function addFriendJSON(): void {
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
        $email = filter_var(trim($input['email'] ?? ''), FILTER_VALIDATE_EMAIL);

        if (!$email) {
            echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address']);
            exit;
        }

        $res = SocialService::sendFriendRequest($userId, $email);
        echo json_encode($res);
        exit;
    }

    public static function acceptFriendJSON(): void {
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
        $requestId = (int)($input['request_id'] ?? 0);

        $res = SocialService::acceptFriendRequest($userId, $requestId);
        echo json_encode($res);
        exit;
    }

    public static function createSquadJSON(): void {
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
        $name = trim($input['name'] ?? '');
        $tag = trim($input['tag'] ?? '');
        $desc = trim($input['description'] ?? '');

        if (empty($name) || empty($tag)) {
            echo json_encode(['status' => 'error', 'message' => 'Squad name and tag are required']);
            exit;
        }

        $res = SocialService::createGuild($userId, $name, $tag, $desc);
        echo json_encode($res);
        exit;
    }

    public static function joinSquadJSON(): void {
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
        $squadId = (int)($input['squad_id'] ?? 0);

        $res = SocialService::joinGuild($userId, $squadId);
        echo json_encode($res);
        exit;
    }
}
