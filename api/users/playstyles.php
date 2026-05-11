<?php

require_once __DIR__ . '/../../api/helpers.php';

$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$pdo = Database::getConnection();
$stmt = $pdo->query('SELECT preferred_playstyle FROM user_preferences WHERE preferred_playstyle IS NOT NULL AND preferred_playstyle != \'\'');
$rows = $stmt->fetchAll();

$values = [];
foreach ($rows as $row) {
    $val = trim($row['preferred_playstyle']);
    if ($val !== '') {
        $values[$val] = true;
    }
}

$defaults = ['Aggressive', 'Casual', 'Supportive', 'Tactical', 'Hybrid'];
foreach ($defaults as $d) {
    $values[$d] = true;
}

$values = array_keys($values);
sort($values);

jsonResponse(['success' => true, 'playstyles' => $values]);
