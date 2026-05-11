<?php

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name(APP_SESSION_NAME);
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $cookieParams = [
        'lifetime' => 60 * 60 * 24,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ];

    session_set_cookie_params($cookieParams);
    session_start();
}

/**
 * Ensures the user is authenticated.
 */
function requireAuth(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . APP_BASE_URL . '/views/login.php');
        exit;
    }
}

/**
 * Returns the current authenticated user ID or null.
 *
 * @return int|null
 */
function currentUserId(): ?int
{
    return !empty($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}
