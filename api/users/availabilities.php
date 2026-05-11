<?php

require_once __DIR__ . '/../../api/helpers.php';

$userId = authUserId();
if ($userId === null) {
    jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
}

$pdo = Database::getConnection();
$stmt = $pdo->query('SELECT availability FROM users WHERE availability IS NOT NULL AND availability != \'\'');
$rows = $stmt->fetchAll();

$values = [];
foreach ($rows as $row) {
    foreach (array_map('trim', explode(',', $row['availability'])) as $val) {
        if ($val !== '') {
            $values[$val] = true;
        }
    }
}

$defaults = ['Mornings', 'Afternoons', 'Evenings', 'Nights', 'Weekdays', 'Weekends'];
foreach ($defaults as $d) {
    $values[$d] = true;
}

$values = array_keys($values);
sort($values);

jsonResponse(['success' => true, 'availabilities' => $values]);
