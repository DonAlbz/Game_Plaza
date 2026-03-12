<?php

require_once __DIR__ . '/../../api/helpers.php';
require_once __DIR__ . '/../../models/Game.php';

$games = Game::all();
jsonResponse(['success' => true, 'games' => $games]);
