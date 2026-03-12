<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/User.php';

$data = getRequestData();
$required = ['email', 'password'];
$missing = validateRequiredFields($data, $required);
if ($missing !== null) {
    jsonResponse([
        'success' => false,
        'message' => 'Missing required fields: ' . implode(', ', $missing),
    ], 400);
}

$email = trim((string) $data['email']);
$password = (string) $data['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'message' => 'Invalid email address.'], 400);
}

$user = User::verifyCredentials($email, $password);
if ($user === null) {
    jsonResponse(['success' => false, 'message' => 'Invalid email or password.'], 401);
}

$_SESSION['user_id'] = (int) $user['id'];
session_regenerate_id(true);

jsonResponse(['success' => true, 'message' => 'Login successful.', 'user_id' => $user['id']]);
