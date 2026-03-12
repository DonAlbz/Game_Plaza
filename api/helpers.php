<?php

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/Database.php';

/**
 * Send a JSON response and exit.
 *
 * @param array $payload
 * @param int $statusCode
 * @return void
 */
function jsonResponse(array $payload, int $statusCode = 200): void
{
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

/**
 * Read JSON payload from request body.
 *
 * @return array
 */
function getJsonInput(): array
{
    $input = file_get_contents('php://input');
    if (empty($input)) {
        return [];
    }

    $data = json_decode($input, true);
    return is_array($data) ? $data : [];
}

/**
 * Merge JSON input with POST fields for flexible requests.
 *
 * @return array
 */
function getRequestData(): array
{
    $json = getJsonInput();
    return array_merge($_POST, $json);
}

/**
 * Validate that required fields are present.
 *
 * @param array $data
 * @param array $required
 * @return array|null Returns array of missing fields or null if none.
 */
function validateRequiredFields(array $data, array $required): ?array
{
    $missing = [];
    foreach ($required as $field) {
        if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
            $missing[] = $field;
        }
    }
    return empty($missing) ? null : $missing;
}

/**
 * Returns the currently authenticated user id or null.
 *
 * @return int|null
 */
function authUserId(): ?int
{
    return currentUserId();
}
