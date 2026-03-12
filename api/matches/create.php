<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/Match.php';
require_once __DIR__ . '/../../models/Game.php';

$data = getRequestData();
$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$required = ['name', 'game_id', 'match_type', 'max_participants', 'scheduled_time', 'description'];
$missing = validateRequiredFields($data, $required);
if ($missing !== null) {
    jsonResponse(['success' => false, 'message' => 'Missing fields: ' . implode(', ', $missing)], 400);
}

$gameId = (int) $data['game_id'];
$game = Game::findById($gameId);
if ($game === null) {
    jsonResponse(['success' => false, 'message' => 'Selected game does not exist.'], 404);
}

$maxParticipants = max(2, min(12, (int) $data['max_participants']));
$scheduledTime = trim((string) $data['scheduled_time']);
$timestamp = strtotime($scheduledTime);
if ($timestamp === false) {
    jsonResponse(['success' => false, 'message' => 'Invalid scheduled time format.'], 400);
}
$scheduledTime = date('Y-m-d H:i:s', $timestamp);

$matchId = false;
try {
    $matchId = GameMatch::create(
        $userId,
        $gameId,
        trim((string) $data['name']),
        trim((string) $data['match_type']),
        $maxParticipants,
        $scheduledTime,
        trim((string) $data['description'])
    );
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Unable to create match.'], 500);
}

if ($matchId === false) {
    jsonResponse(['success' => false, 'message' => 'Unable to create match.'], 500);
}

jsonResponse(['success' => true, 'message' => 'Match created successfully.', 'match_id' => $matchId]);
