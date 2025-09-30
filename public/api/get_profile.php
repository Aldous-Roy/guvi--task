/**
 * Retrieves the authenticated user's profile information.
 *
 * This script performs the following steps:
 * 1. Checks for a Bearer token in the Authorization header.
 * 2. Validates the token using Redis to obtain the associated user ID.
 * 3. Fetches the user's profile from MongoDB using the user ID.
 * 4. Returns the profile data as a JSON response, including user ID, full name, age, date of birth (formatted as 'Y-m-d'), and contact information.
 *
 * Responses:
 * - 401 Unauthorized if the token is missing or invalid.
 * - JSON object with 'success' and 'profile' fields.
 *
 * Dependencies:
 * - Requires 'config.php' for Redis and MongoDB connection helpers.
 * - Assumes Redis stores session tokens as "session:{token}" => user_id.
 * - Assumes MongoDB 'profiles' collection stores user profiles with 'user_id' as integer.
 */
<?php
require_once 'config.php';

$headers = getallheaders();
$auth = $headers['Authorization'] ?? '';
if (!str_starts_with($auth, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(['error' => 'No token']);
    exit;
}

$token = substr($auth, 7);
$redis = get_redis();
$user_id = $redis->get("session:$token");
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid or expired token']);
    exit;
}

// MongoDB
$mongo = get_mongo();
$profiles = $mongo->profiles;
$profile = $profiles->findOne(['user_id' => (int)$user_id]);

if (!$profile) {
    echo json_encode(['success' => true, 'profile' => null]);
    exit;
}

// Convert MongoDB data of time to readable date string for dob
$dob = isset($profile['dob']) ? $profile['dob']->toDateTime()->format('Y-m-d') : null;

echo json_encode([
    'success' => true,
    'profile' => [
        'user_id' => $profile['user_id'],
        'full_name' => $profile['full_name'] ?? null,
        'age' => $profile['age'] ?? null,
        'dob' => $dob,
        'contact' => $profile['contact'] ?? null
    ]
]);