<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/Database.php';
require_once __DIR__ . '/../../models/Social.php';

$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$followers = [];
$pdo = Database::getConnection();
$stmt = $pdo->prepare(
    'SELECT u.id, u.username, u.skill_level, u.availability, u.bio
     FROM likes_follows lf
     JOIN users u ON lf.follower_id = u.id
     WHERE lf.followee_id = :user_id AND lf.action_type = :action'
);
$stmt->execute(['user_id' => $userId, 'action' => 'Follow']);
$followers = $stmt->fetchAll();

jsonResponse(['success' => true, 'followers' => $followers]);
