<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/Match.php';
require_once __DIR__ . '/../../models/MatchParticipant.php';

$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$matches = GameMatch::all();
$enhanced = [];
foreach ($matches as $match) {
    $matchId = (int) $match['id'];
    $participantCount = GameMatch::countParticipants($matchId);
    $enhanced[] = array_merge($match, [
        'current_participants' => $participantCount,
        'is_joined' => MatchParticipant::isParticipant($matchId, $userId),
    ]);
}

jsonResponse(['success' => true, 'matches' => $enhanced]);
