<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/UserGame.php';

$data = getRequestData();
$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$required = ['game_id'];
$missing = validateRequiredFields($data, $required);
if ($missing !== null) {
    jsonResponse(['success' => false, 'message' => 'Missing required fields: ' . implode(', ', $missing)], 400);
}

$gameId = (int) $data['game_id'];
$removed = UserGame::removeGame($userId, $gameId);
if (!$removed) {
    jsonResponse(['success' => false, 'message' => 'Unable to remove game.'], 500);
}

jsonResponse(['success' => true, 'message' => 'Game removed from library.']);
