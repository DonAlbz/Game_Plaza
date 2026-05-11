<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/Match.php';
require_once __DIR__ . '/../../models/MatchParticipant.php';

$data = getRequestData();
$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$required = ['match_id'];
$missing = validateRequiredFields($data, $required);
if ($missing !== null) {
    jsonResponse(['success' => false, 'message' => 'Missing match id.'], 400);
}

$matchId = (int) $data['match_id'];
$match = GameMatch::findById($matchId);
if ($match === null) {
    jsonResponse(['success' => false, 'message' => 'Match not found.'], 404);
}

if (MatchParticipant::isParticipant($matchId, $userId)) {
    jsonResponse(['success' => false, 'message' => 'You are already in this match.'], 409);
}

$currentCount = GameMatch::countParticipants($matchId);
if ($currentCount >= $match['max_participants']) {
    jsonResponse(['success' => false, 'message' => 'Match is already full.'], 400);
}

$joined = MatchParticipant::join($matchId, $userId);
if (!$joined) {
    jsonResponse(['success' => false, 'message' => 'Unable to join match.'], 500);
}

jsonResponse(['success' => true, 'message' => 'You have joined the match.']);
