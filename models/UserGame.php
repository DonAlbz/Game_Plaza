<?php

require_once __DIR__ . '/Database.php';

class UserGame
{
    /**
     * Get a user's game library with platform metadata.
     *
     * @param int $userId
     * @return array
     */
    public static function getLibrary(int $userId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT ug.*, g.name AS game_name, g.genre, g.developer
             FROM user_games ug
             INNER JOIN games g ON ug.game_id = g.id
             WHERE ug.user_id = :user_id
             ORDER BY g.name ASC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Add a game to user library.
     *
     * @param int $userId
     * @param int $gameId
     * @param string $platform
     * @return bool
     */
    public static function addGame(int $userId, int $gameId, string $platform): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO user_games (user_id, game_id, platform) VALUES (:user_id, :game_id, :platform)'
        );
        return $stmt->execute([
            'user_id' => $userId,
            'game_id' => $gameId,
            'platform' => $platform,
        ]);
    }

    /**
     * Remove a game from user library.
     *
     * @param int $userId
     * @param int $gameId
     * @return bool
     */
    public static function removeGame(int $userId, int $gameId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM user_games WHERE user_id = :user_id AND game_id = :game_id');
        return $stmt->execute(['user_id' => $userId, 'game_id' => $gameId]);
    }

    /**
     * Check if a user already owns a game.
     *
     * @param int $userId
     * @param int $gameId
     * @return bool
     */
    public static function ownsGame(int $userId, int $gameId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id FROM user_games WHERE user_id = :user_id AND game_id = :game_id LIMIT 1');
        $stmt->execute(['user_id' => $userId, 'game_id' => $gameId]);
        return (bool) $stmt->fetch();
    }
}
