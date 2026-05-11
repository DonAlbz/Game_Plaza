<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/Social.php';

$data = getRequestData();
$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$required = ['user_id'];
$missing = validateRequiredFields($data, $required);
if ($missing !== null) {
    jsonResponse(['success' => false, 'message' => 'Missing required fields: ' . implode(', ', $missing)], 400);
}

$followeeId = (int) $data['user_id'];
if ($followeeId === $userId) {
    jsonResponse(['success' => false, 'message' => 'You cannot like yourself.'], 400);
}

if (Social::exists($userId, $followeeId)) {
    jsonResponse(['success' => false, 'message' => 'You already liked or followed this player.'], 409);
}

$created = Social::like($userId, $followeeId);
if (!$created) {
    jsonResponse(['success' => false, 'message' => 'Unable to like user.'], 500);
}

jsonResponse(['success' => true, 'message' => 'Player liked successfully.']);
