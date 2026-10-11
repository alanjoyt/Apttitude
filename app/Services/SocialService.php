<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/User.php';

class SocialService {

    /**
     * Get user's accepted friends
     */
    public static function getFriends(int $userId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT f.id as friendship_id, u.id as user_id, u.name, u.tier, u.total_xp, u.current_streak, f.status
            FROM friendships f
            JOIN users u ON (f.friend_id = u.id AND f.user_id = ?) OR (f.user_id = u.id AND f.friend_id = ?)
            WHERE u.id != ? AND f.status = 'accepted'
            ORDER BY u.total_xp DESC
        ");
        $stmt->execute([$userId, $userId, $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get pending friend requests for user
     */
    public static function getPendingRequests(int $userId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT f.id as request_id, u.id as user_id, u.name, u.tier, u.total_xp
            FROM friendships f
            JOIN users u ON f.user_id = u.id
            WHERE f.friend_id = ? AND f.status = 'pending'
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Send friend request by email or user_id
     */
    public static function sendFriendRequest(int $userId, string $friendEmail): array {
        $db = Database::getConnection();
        $friend = User::findByEmail($friendEmail);

        if (!$friend) {
            return ['status' => 'error', 'message' => 'User with this email not found'];
        }

        if ($friend['id'] == $userId) {
            return ['status' => 'error', 'message' => 'You cannot add yourself as a friend!'];
        }

        // Check existing request
        $stmt = $db->prepare("SELECT * FROM friendships WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
        $stmt->execute([$userId, $friend['id'], $friend['id'], $userId]);
        if ($stmt->fetch()) {
            return ['status' => 'error', 'message' => 'Friendship or request already exists!'];
        }

        $db->prepare("INSERT INTO friendships (user_id, friend_id, status) VALUES (?, ?, 'pending')")
           ->execute([$userId, $friend['id']]);

        return ['status' => 'success', 'message' => 'Friend request sent to ' . $friend['name'] . '!'];
    }

    /**
     * Accept friend request
     */
    public static function acceptFriendRequest(int $userId, int $requestId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE friendships SET status = 'accepted' WHERE id = ? AND friend_id = ?");
        $stmt->execute([$requestId, $userId]);
        return ['status' => 'success', 'message' => 'Friend request accepted!'];
    }

    /**
     * Get user's Study Squad / Guild
     */
    public static function getUserGuild(int $userId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT g.*, gm.role, gm.contributed_xp
            FROM guilds g
            JOIN guild_members gm ON g.id = gm.guild_id
            WHERE gm.user_id = ?
        ");
        $stmt->execute([$userId]);
        $guild = $stmt->fetch();

        if (!$guild) return null;

        // Fetch roster
        $membersStmt = $db->prepare("
            SELECT u.id, u.name, u.tier, u.total_xp, gm.role, gm.contributed_xp
            FROM guild_members gm
            JOIN users u ON gm.user_id = u.id
            WHERE gm.guild_id = ?
            ORDER BY gm.contributed_xp DESC
        ");
        $membersStmt->execute([$guild['id']]);
        $guild['members'] = $membersStmt->fetchAll();

        return $guild;
    }

    /**
     * Create new Study Squad / Guild
     */
    public static function createGuild(int $userId, string $name, string $tag, string $description): array {
        $db = Database::getConnection();

        // Check existing guild membership
        if (self::getUserGuild($userId)) {
            return ['status' => 'error', 'message' => 'You are already a member of a Study Squad! Leave your current squad first.'];
        }

        try {
            $stmt = $db->prepare("INSERT INTO guilds (name, tag, description, leader_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, strtoupper($tag), $description, $userId]);
            $guildId = (int)$db->lastInsertId();

            $db->prepare("INSERT INTO guild_members (guild_id, user_id, role) VALUES (?, ?, 'leader')")
               ->execute([$guildId, $userId]);

            return ['status' => 'success', 'message' => "Study Squad '{$name}' created successfully!"];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Squad name or tag already taken!'];
        }
    }

    /**
     * Join existing Study Squad
     */
    public static function joinGuild(int $userId, int $guildId): array {
        $db = Database::getConnection();

        if (self::getUserGuild($userId)) {
            return ['status' => 'error', 'message' => 'You are already a member of a Study Squad!'];
        }

        $stmt = $db->prepare("SELECT * FROM guilds WHERE id = ?");
        $stmt->execute([$guildId]);
        if (!$stmt->fetch()) {
            return ['status' => 'error', 'message' => 'Study Squad not found!'];
        }

        $db->prepare("INSERT INTO guild_members (guild_id, user_id, role) VALUES (?, ?, 'member')")
           ->execute([$guildId, $userId]);

        return ['status' => 'success', 'message' => 'Joined Study Squad successfully!'];
    }
}
