<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/UserPreference.php';
require_once __DIR__ . '/../../models/UserGame.php';
require_once __DIR__ . '/../../models/Social.php';

$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$currentUser = User::findById($userId);
if ($currentUser === null) {
    jsonResponse(['success' => false, 'message' => 'User not found.'], 404);
}

$currentPref = UserPreference::findByUserId($userId);
$userGames = UserGame::getLibrary($userId);
$currentGameIds = array_column($userGames, 'game_id');
$currentGenres = array_map('trim', explode(',', $currentPref['preferred_genres'] ?? ''));
$followedIds = Social::getFollowingIds($userId);

$pdo = Database::getConnection();
$stmt = $pdo->prepare(
    'SELECT u.id, u.username, u.bio, u.skill_level, u.availability, p.preferred_genres, p.preferred_playstyle, p.competitive_preference, p.team_size_preference
     FROM users u
     LEFT JOIN user_preferences p ON u.id = p.user_id
     WHERE u.id != :current_user'
);
$stmt->execute(['current_user' => $userId]);
$otherUsers = $stmt->fetchAll();

$suggestions = [];
foreach ($otherUsers as $other) {
    $otherLibrary = UserGame::getLibrary((int) $other['id']);
    $otherGameIds = array_column($otherLibrary, 'game_id');
    $sharedGameIds = array_intersect($currentGameIds, $otherGameIds);
    $sharedGames = count($sharedGameIds);
    $sharedGameNames = array_values(array_map(
        fn($g) => $g['game_name'],
        array_filter($otherLibrary, fn($g) => in_array($g['game_id'], $sharedGameIds))
    ));

    $genreScore = 0;
    $otherGenres = array_map('trim', explode(',', $other['preferred_genres'] ?? ''));
    foreach ($currentGenres as $genre) {
        if ($genre !== '' && in_array($genre, $otherGenres, true)) {
            $genreScore++;
        }
    }

    $skillBonus = $other['skill_level'] === $currentUser['skill_level'] ? 1 : 0;
    $availabilityBonus = stripos($other['availability'], $currentUser['availability']) !== false || stripos($currentUser['availability'], (string) $other['availability']) !== false ? 1 : 0;
    $followBonus = in_array((int) $other['id'], $followedIds, true) ? 5 : 0;
    $compatibility = ($sharedGames * 4) + ($genreScore * 2) + $skillBonus + $availabilityBonus + $followBonus;

    $suggestions[] = [
        'id' => $other['id'],
        'is_followed' => in_array((int) $other['id'], $followedIds, true),
        'username' => $other['username'],
        'bio' => $other['bio'],
        'skill_level' => $other['skill_level'],
        'availability' => $other['availability'],
        'preferred_genres' => $other['preferred_genres'],
        'preferred_playstyle' => $other['preferred_playstyle'],
        'compatibility_score' => $compatibility,
        'shared_games' => $sharedGames,
        'shared_game_names' => $sharedGameNames,
        'genre_match' => $genreScore,
    ];
}

usort($suggestions, function ($a, $b) {
    return $b['compatibility_score'] <=> $a['compatibility_score'];
});

jsonResponse(['success' => true, 'suggestions' => array_slice($suggestions, 0, 12)]);
