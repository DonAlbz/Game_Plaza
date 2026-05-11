<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/UserPreference.php';
require_once __DIR__ . '/../../models/Social.php';

$data = getRequestData();
$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$query = trim((string) ($data['q'] ?? ''));
$filterGenre = trim((string) ($data['genre'] ?? ''));
$pdo = Database::getConnection();

$sql = 'SELECT u.id, u.username, u.bio, u.skill_level, u.availability, p.preferred_genres, p.preferred_playstyle
        FROM users u
        LEFT JOIN user_preferences p ON u.id = p.user_id
        WHERE u.id != :current_user';
$params = ['current_user' => $userId];

if ($query !== '') {
    $sql .= ' AND (u.username LIKE :query1 OR u.bio LIKE :query2 OR p.preferred_genres LIKE :query3)';
    $params['query1'] = '%' . $query . '%';
    $params['query2'] = '%' . $query . '%';
    $params['query3'] = '%' . $query . '%';
}

if ($filterGenre !== '') {
    $sql .= ' AND p.preferred_genres LIKE :genre';
    $params['genre'] = '%' . $filterGenre . '%';
}

$sql .= ' ORDER BY u.username ASC LIMIT 24';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

foreach ($users as &$user) {
    $user['followers_count'] = Social::countFollowers((int) $user['id']);
    $user['canFollow'] = !Social::exists($userId, (int) $user['id'], 'Follow');
}

jsonResponse(['success' => true, 'users' => $users]);
