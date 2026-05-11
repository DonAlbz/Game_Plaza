<?php

require_once __DIR__ . '/../../api/helpers.php';

$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$pdo = Database::getConnection();
$stmt = $pdo->query('SELECT preferred_genres FROM user_preferences WHERE preferred_genres IS NOT NULL AND preferred_genres != \'\'');
$rows = $stmt->fetchAll();

$genres = [];
foreach ($rows as $row) {
    foreach (array_map('trim', explode(',', $row['preferred_genres'])) as $genre) {
        if ($genre !== '') {
            $genres[$genre] = true;
        }
    }
}

$genres = array_keys($genres);
sort($genres);

jsonResponse(['success' => true, 'genres' => $genres]);
