<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/UserPreference.php';
require_once __DIR__ . '/../../models/UserGame.php';

$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$user = User::findById($userId);
if ($user === null) {
    jsonResponse(['success' => false, 'message' => 'User not found.'], 404);
}

$preferences = UserPreference::findByUserId($userId);
$library = UserGame::getLibrary($userId);

$json = [
    'success' => true,
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'bio' => $user['bio'],
        'skill_level' => $user['skill_level'],
        'availability' => $user['availability'],
        'profile_created_at' => $user['profile_created_at'],
        'updated_at' => $user['updated_at'],
    ],
    'preferences' => $preferences,
    'library' => $library,
];

jsonResponse($json);
