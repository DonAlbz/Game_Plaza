<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/Database.php';

$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$pdo = Database::getConnection();
$stmt = $pdo->prepare(
    'SELECT u.id, u.username, u.bio, u.skill_level, u.availability, p.preferred_genres, p.preferred_playstyle,
            (SELECT COUNT(*) FROM likes_follows f2 WHERE f2.followee_id = u.id AND f2.action_type = \'Follow\') AS followers_count
     FROM likes_follows lf
     JOIN users u ON lf.followee_id = u.id
     LEFT JOIN user_preferences p ON u.id = p.user_id
     WHERE lf.follower_id = :user_id AND lf.action_type = :action'
);
$stmt->execute(['user_id' => $userId, 'action' => 'Follow']);
$followed = $stmt->fetchAll();

jsonResponse(['success' => true, 'following' => $followed]);
