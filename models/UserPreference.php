<?php

require_once __DIR__ . '/Database.php';

class UserPreference
{
    /**
     * Get preferences by user id.
     *
     * @param int $userId
     * @return array|null
     */
    public static function findByUserId(int $userId): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM user_preferences WHERE user_id = :user_id LIMIT 1');
        $stmt->execute(['user_id' => $userId]);
        $pref = $stmt->fetch();
        return $pref ?: null;
    }

    /**
     * Create default preferences for a new user.
     *
     * @param int $userId
     * @return bool
     */
    public static function createDefaults(int $userId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO user_preferences (user_id, preferred_genres, preferred_playstyle, competitive_preference, team_size_preference) VALUES (:user_id, :genres, :playstyle, :competitive, :team_size)'
        );
        return $stmt->execute([
            'user_id' => $userId,
            'genres' => 'Action, Adventure, Strategy',
            'playstyle' => 'Balanced',
            'competitive' => 'Mixed',
            'team_size' => 'Squad',
        ]);
    }

    /**
     * Update preferences for a user.
     *
     * @param int $userId
     * @param string $genres
     * @param string $playstyle
     * @param string $competitive
     * @param string $teamSize
     * @return bool
     */
    public static function updatePreferences(int $userId, string $genres, string $playstyle, string $competitive, string $teamSize): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO user_preferences (user_id, preferred_genres, preferred_playstyle, competitive_preference, team_size_preference)
             VALUES (:user_id, :genres, :playstyle, :competitive, :team_size)
             ON DUPLICATE KEY UPDATE preferred_genres = VALUES(preferred_genres), preferred_playstyle = VALUES(preferred_playstyle), competitive_preference = VALUES(competitive_preference), team_size_preference = VALUES(team_size_preference)'
        );

        return $stmt->execute([
            'user_id' => $userId,
            'genres' => $genres,
            'playstyle' => $playstyle,
            'competitive' => $competitive,
            'team_size' => $teamSize,
        ]);
    }
}
