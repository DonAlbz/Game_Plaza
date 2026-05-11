<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/UserGame.php';

$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$library = UserGame::getLibrary($userId);
jsonResponse(['success' => true, 'library' => $library]);
