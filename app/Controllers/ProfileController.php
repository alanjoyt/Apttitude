<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/User.php';

class ProfileController {

    /**
     * Update user profile name, bio, and avatar icon
     */
    public static function updateProfileJSON(): void {
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
        $bio = trim($input['bio'] ?? 'Apttitudee Scholar');
        $avatarIcon = trim($input['avatar_icon'] ?? 'bi-person-circle');

        if (empty($name)) {
            echo json_encode(['status' => 'error', 'message' => 'Name cannot be empty']);
            exit;
        }

        // Allowed avatar icon list
        $allowedAvatars = [
            'bi-person-circle', 'bi-robot', 'bi-lightning-charge-fill', 
            'bi-controller', 'bi-rocket-takeoff-fill', 'bi-shield-shaded', 
            'bi-gem', 'bi-fire', 'bi-award-fill', 'bi-cpu-fill'
        ];
        if (!in_array($avatarIcon, $allowedAvatars)) {
            $avatarIcon = 'bi-person-circle';
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET name = ?, bio = ?, avatar_icon = ? WHERE id = ?");
        $stmt->execute([$name, $bio, $avatarIcon, $userId]);

        $_SESSION['user_name'] = $name;

        echo json_encode([
            'status' => 'success',
            'message' => 'Profile updated successfully!',
            'user' => [
                'name' => $name,
                'bio' => $bio,
                'avatar_icon' => $avatarIcon
            ]
        ]);
        exit;
    }

    /**
     * Change user password with current password verification
     */
    public static function changePasswordJSON(): void {
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
        $oldPassword = $input['old_password'] ?? '';
        $newPassword = $input['new_password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';

        if (empty($oldPassword) || empty($newPassword)) {
            echo json_encode(['status' => 'error', 'message' => 'Please fill in all password fields']);
            exit;
        }

        if (strlen($newPassword) < 6) {
            echo json_encode(['status' => 'error', 'message' => 'New password must be at least 6 characters long']);
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            echo json_encode(['status' => 'error', 'message' => 'New password and confirmation do not match']);
            exit;
        }

        $user = User::findById($userId);
        if (!$user || !password_verify($oldPassword, $user['password_hash'])) {
            echo json_encode(['status' => 'error', 'message' => 'Incorrect current password']);
            exit;
        }

        // Hash and update new password
        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$newHash, $userId]);

        echo json_encode(['status' => 'success', 'message' => 'Password changed successfully!']);
        exit;
    }
}
