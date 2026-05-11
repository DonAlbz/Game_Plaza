<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/UserPreference.php';

$data = getRequestData();
$required = ['username', 'email', 'password'];
$missing = validateRequiredFields($data, $required);
if ($missing !== null) {
    jsonResponse([
        'success' => false,
        'message' => 'Missing required fields: ' . implode(', ', $missing),
    ], 400);
}

$username = trim((string) $data['username']);
$email = trim((string) $data['email']);
$password = (string) $data['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'message' => 'Invalid email address.'], 400);
}

if (strlen($username) < 3 || strlen($username) > 30) {
    jsonResponse(['success' => false, 'message' => 'Username must be between 3 and 30 characters.'], 400);
}

if (strlen($password) < 6) {
    jsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters.'], 400);
}

if (User::exists($email, $username)) {
    jsonResponse(['success' => false, 'message' => 'Email or username already registered.'], 409);
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$userId = User::create($username, $email, $passwordHash);

if ($userId === false) {
    jsonResponse(['success' => false, 'message' => 'Unable to create account. Please try again later.'], 500);
}

UserPreference::createDefaults($userId);

$_SESSION['user_id'] = $userId;
session_regenerate_id(true);

jsonResponse(['success' => true, 'message' => 'Registration successful.', 'user_id' => $userId]);
