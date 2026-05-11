<?php

require_once __DIR__ . '/Database.php';

class Social
{
    /**
     * Follow another user.
     *
     * @param int $followerId
     * @param int $followeeId
     * @return bool
     */
    public static function follow(int $followerId, int $followeeId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO likes_follows (follower_id, followee_id, action_type) VALUES (:follower_id, :followee_id, :action_type)'
        );
        return $stmt->execute([
            'follower_id' => $followerId,
            'followee_id' => $followeeId,
            'action_type' => 'Follow',
        ]);
    }

    /**
     * Like another user.
     *
     * @param int $followerId
     * @param int $followeeId
     * @return bool
     */
    public static function like(int $followerId, int $followeeId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO likes_follows (follower_id, followee_id, action_type) VALUES (:follower_id, :followee_id, :action_type)'
        );
        return $stmt->execute([
            'follower_id' => $followerId,
            'followee_id' => $followeeId,
            'action_type' => 'Like',
        ]);
    }

    /**
     * Check if an action already exists.
     *
     * @param int $followerId
     * @param int $followeeId
     * @return bool
     */
    public static function exists(int $followerId, int $followeeId, string $actionType): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id FROM likes_follows WHERE follower_id = :follower_id AND followee_id = :followee_id AND action_type = :action_type LIMIT 1');
        $stmt->execute(['follower_id' => $followerId, 'followee_id' => $followeeId, 'action_type' => $actionType]);
        return (bool) $stmt->fetch();
    }

    /**
     * Get the number of followers for a user.
     *
     * @param int $userId
     * @return int
     */
    public static function countFollowers(int $userId): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM likes_follows WHERE followee_id = :followee_id AND action_type = :action');
        $stmt->execute(['followee_id' => $userId, 'action' => 'Follow']);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Get the IDs of users followed by the given user.
     *
     * @param int $userId
     * @return array<int>
     */
    public static function unfollow(int $followerId, int $followeeId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM likes_follows WHERE follower_id = :follower_id AND followee_id = :followee_id AND action_type = :action_type');
        return $stmt->execute(['follower_id' => $followerId, 'followee_id' => $followeeId, 'action_type' => 'Follow']);
    }

    public static function getFollowingIds(int $userId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT followee_id FROM likes_follows WHERE follower_id = :follower_id AND action_type = :action'
        );
        $stmt->execute(['follower_id' => $userId, 'action' => 'Follow']);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }
}
