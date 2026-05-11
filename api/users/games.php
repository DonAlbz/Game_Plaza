<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/UserGame.php';

$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$targetId = (int) ($_GET['user_id'] ?? 0);
if ($targetId <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid user id.'], 400);
}

$games = UserGame::getLibrary($targetId);
$result = array_map(fn($g) => ['name' => $g['game_name'], 'genre' => $g['genre'], 'platform' => $g['platform']], $games);

jsonResponse(['success' => true, 'games' => $result]);
