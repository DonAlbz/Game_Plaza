<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/UserPreference.php';

$data = getRequestData();
$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$fields = [
    'bio' => trim((string) ($data['bio'] ?? '')),
    'skill_level' => trim((string) ($data['skill_level'] ?? 'Beginner')),
    'availability' => trim((string) ($data['availability'] ?? '')),
    'preferred_genres' => trim((string) ($data['preferred_genres'] ?? '')),
    'preferred_playstyle' => trim((string) ($data['preferred_playstyle'] ?? '')),
    'competitive_preference' => trim((string) ($data['competitive_preference'] ?? 'Mixed')),
    'team_size_preference' => trim((string) ($data['team_size_preference'] ?? 'Squad')),
];

$validSkills = ['Beginner', 'Intermediate', 'Advanced', 'Pro'];
$validCompetitive = ['Casual', 'Competitive', 'Mixed'];
$validTeamSizes = ['Solo', 'Duo', 'Squad', 'Large'];

if (!in_array($fields['skill_level'], $validSkills, true)) {
    jsonResponse(['success' => false, 'message' => 'Invalid skill level.'], 400);
}

if (!in_array($fields['competitive_preference'], $validCompetitive, true)) {
    jsonResponse(['success' => false, 'message' => 'Invalid competitive preference.'], 400);
}

if (!in_array($fields['team_size_preference'], $validTeamSizes, true)) {
    jsonResponse(['success' => false, 'message' => 'Invalid team size preference.'], 400);
}

$updated = User::updateProfile($userId, $fields['bio'], $fields['skill_level'], $fields['availability']);
if (!$updated) {
    jsonResponse(['success' => false, 'message' => 'Unable to update profile.'], 500);
}

$prefUpdated = UserPreference::updatePreferences(
    $userId,
    $fields['preferred_genres'],
    $fields['preferred_playstyle'],
    $fields['competitive_preference'],
    $fields['team_size_preference']
);

if (!$prefUpdated) {
    jsonResponse(['success' => false, 'message' => 'Unable to update preferences.'], 500);
}

jsonResponse(['success' => true, 'message' => 'Profile updated successfully.']);
