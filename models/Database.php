<?php

require_once __DIR__ . '/../config/db.php';

class Database
{
    /** @var PDO|null */
    private static $instance = null;

    /**
     * Returns the shared PDO instance.
     *
     * @return PDO
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            self::$instance = getPDO();
        }

        return self::$instance;
    }
}
