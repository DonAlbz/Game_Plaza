<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/UserGame.php';
require_once __DIR__ . '/../../models/Game.php';

$data = getRequestData();
$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$required = ['game_id', 'platform'];
$missing = validateRequiredFields($data, $required);
if ($missing !== null) {
    jsonResponse(['success' => false, 'message' => 'Missing required fields: ' . implode(', ', $missing)], 400);
}

$gameId = (int) $data['game_id'];
$platform = trim((string) $data['platform']);

$game = Game::findById($gameId);
if ($game === null) {
    jsonResponse(['success' => false, 'message' => 'Game not found.'], 404);
}

if (UserGame::ownsGame($userId, $gameId)) {
    jsonResponse(['success' => false, 'message' => 'Game already in your library.'], 409);
}

$added = UserGame::addGame($userId, $gameId, $platform);
if (!$added) {
    jsonResponse(['success' => false, 'message' => 'Unable to add game.'], 500);
}

jsonResponse(['success' => true, 'message' => 'Game added to library.']);
