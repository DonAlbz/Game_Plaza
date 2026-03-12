<?php

require_once __DIR__ . '/Database.php';

class GameMatch
{
    /**
     * Create a new match record.
     *
     * @param int $creatorId
     * @param int $gameId
     * @param string $name
     * @param string $matchType
     * @param int $maxParticipants
     * @param string $scheduledTime
     * @param string $description
     * @return int|false
     */
    public static function create(int $creatorId, int $gameId, string $name, string $matchType, int $maxParticipants, string $scheduledTime, string $description)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO matches (creator_id, game_id, name, match_type, max_participants, scheduled_time, description) VALUES (:creator_id, :game_id, :name, :match_type, :max_participants, :scheduled_time, :description)'
        );
        $result = $stmt->execute([
            'creator_id' => $creatorId,
            'game_id' => $gameId,
            'name' => $name,
            'match_type' => $matchType,
            'max_participants' => $maxParticipants,
            'scheduled_time' => $scheduledTime,
            'description' => $description,
        ]);

        return $result ? (int) $pdo->lastInsertId() : false;
    }

    /**
     * Find all public matches.
     *
     * @return array
     */
    public static function all(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query(
            'SELECT m.*, g.name AS game_name, u.username AS creator_username
             FROM matches m
             JOIN games g ON m.game_id = g.id
             JOIN users u ON m.creator_id = u.id
             ORDER BY m.scheduled_time ASC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Find match by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT m.*, g.name AS game_name, u.username AS creator_username
             FROM matches m
             JOIN games g ON m.game_id = g.id
             JOIN users u ON m.creator_id = u.id
             WHERE m.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $match = $stmt->fetch();
        return $match ?: null;
    }

    /**
     * Get the number of participants for a match.
     *
     * @param int $matchId
     * @return int
     */
    public static function countParticipants(int $matchId): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM match_participants WHERE match_id = :match_id');
        $stmt->execute(['match_id' => $matchId]);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }
}
