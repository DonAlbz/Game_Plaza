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
    jsonResponse(['success' => false, 'message' => 'You cannot unfollow yourself.'], 400);
}

if (!Social::exists($userId, $followeeId, 'Follow')) {
    jsonResponse(['success' => false, 'message' => 'You are not following this player.'], 409);
}

$deleted = Social::unfollow($userId, $followeeId);
if (!$deleted) {
    jsonResponse(['success' => false, 'message' => 'Unable to unfollow user.'], 500);
}

jsonResponse(['success' => true, 'message' => 'Player unfollowed successfully.']);
