<?php

require_once __DIR__ . '/Database.php';

class MatchParticipant
{
    /**
     * Join a match.
     *
     * @param int $matchId
     * @param int $userId
     * @return bool
     */
    public static function join(int $matchId, int $userId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO match_participants (match_id, user_id, status) VALUES (:match_id, :user_id, :status)'
        );
        return $stmt->execute([
            'match_id' => $matchId,
            'user_id' => $userId,
            'status' => 'Confirmed',
        ]);
    }

    /**
     * Check if a user already joined the match.
     *
     * @param int $matchId
     * @param int $userId
     * @return bool
     */
    public static function isParticipant(int $matchId, int $userId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id FROM match_participants WHERE match_id = :match_id AND user_id = :user_id LIMIT 1');
        $stmt->execute(['match_id' => $matchId, 'user_id' => $userId]);
        return (bool) $stmt->fetch();
    }

    /**
     * Leave a match.
     *
     * @param int $matchId
     * @param int $userId
     * @return bool
     */
    public static function leave(int $matchId, int $userId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM match_participants WHERE match_id = :match_id AND user_id = :user_id');
        return $stmt->execute(['match_id' => $matchId, 'user_id' => $userId]);
    }

    /**
     * Get participants for a match.
     *
     * @param int $matchId
     * @return array
     */
    public static function getParticipants(int $matchId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT u.username, u.skill_level, mp.joined_at
             FROM match_participants mp
             JOIN users u ON mp.user_id = u.id
             WHERE mp.match_id = :match_id
             ORDER BY mp.joined_at ASC'
        );
        $stmt->execute(['match_id' => $matchId]);
        return $stmt->fetchAll();
    }
}
