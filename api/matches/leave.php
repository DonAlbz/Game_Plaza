<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/Match.php';
require_once __DIR__ . '/../../models/MatchParticipant.php';

$data = getRequestData();
$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$missing = validateRequiredFields($data, ['match_id']);
if ($missing !== null) {
    jsonResponse(['success' => false, 'message' => 'Missing match id.'], 400);
}

$matchId = (int) $data['match_id'];
$match = GameMatch::findById($matchId);
if ($match === null) {
    jsonResponse(['success' => false, 'message' => 'Match not found.'], 404);
}

if (!MatchParticipant::isParticipant($matchId, $userId)) {
    jsonResponse(['success' => false, 'message' => 'You are not in this match.'], 409);
}

$left = MatchParticipant::leave($matchId, $userId);
if (!$left) {
    jsonResponse(['success' => false, 'message' => 'Unable to leave match.'], 500);
}

jsonResponse(['success' => true, 'message' => 'You have left the match.']);
