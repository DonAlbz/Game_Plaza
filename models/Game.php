<?php

require_once __DIR__ . '/Database.php';

class Game
{
    /**
     * Get all games from the catalog.
     *
     * @return array
     */
    public static function all(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT * FROM games ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    /**
     * Find a game by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM games WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $game = $stmt->fetch();
        return $game ?: null;
    }
}
