<?php

require_once __DIR__ . '/Database.php';

class User
{
    /**
     * Find a user by email address.
     *
     * @param string $email
     * @return array|null
     */
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find a user by username.
     *
     * @param string $username
     * @return array|null
     */
    public static function findByUsername(string $username): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Create a new user record.
     *
     * @param string $username
     * @param string $email
     * @param string $passwordHash
     * @return int|false
     */
    public static function create(string $username, string $email, string $passwordHash)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)'
        );
        $result = $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);

        return $result ? (int) $pdo->lastInsertId() : false;
    }

    /**
     * Verify login credentials.
     *
     * @param string $email
     * @param string $password
     * @return array|null
     */
    public static function verifyCredentials(string $email, string $password): ?array
    {
        $user = self::findByEmail($email);
        if ($user === null) {
            return null;
        }

        if (password_verify($password, $user['password_hash'])) {
            return $user;
        }

        return null;
    }

    /**
     * Check if email or username is already registered.
     *
     * @param string $email
     * @param string $username
     * @return bool
     */
    public static function exists(string $email, string $username): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email OR username = :username LIMIT 1');
        $stmt->execute(['email' => $email, 'username' => $username]);
        return (bool) $stmt->fetch();
    }

    /**
     * Update a user's profile fields.
     *
     * @param int $userId
     * @param string $bio
     * @param string $skillLevel
     * @param string $availability
     * @return bool
     */
    public static function updateProfile(int $userId, string $bio, string $skillLevel, string $availability): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE users SET bio = :bio, skill_level = :skill_level, availability = :availability, updated_at = CURRENT_TIMESTAMP WHERE id = :id'
        );
        return $stmt->execute([
            'bio' => $bio,
            'skill_level' => $skillLevel,
            'availability' => $availability,
            'id' => $userId,
        ]);
    }
}
