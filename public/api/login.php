/**
 * Handles user login authentication.
 *
 * Expects a JSON payload with 'email' and 'password' fields.
 * - Retrieves the user by email from the database.
 * - Verifies the provided password against the stored password hash.
 * - On successful authentication:
 *     - Generates a random session token.
 *     - Stores the session token in Redis with a TTL (time-to-live).
 *     - Returns a JSON response with 'success' and the session 'token'.
 * - On failure:
 *     - Returns a 401 Unauthorized HTTP response with an error message.
 *
 * Dependencies:
 * - Requires 'config.php' for database and Redis connection helpers.
 * - Assumes existence of 'json_in()', 'get_db()', and 'get_redis()' functions.
 * - Uses PHP's password_verify() for password checking.
 * - Uses Redis for session management.
 */
<?php
require_once 'config.php';

$data = json_in();
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

$mysqli = get_db();
$stmt = $mysqli->prepare("SELECT id, password_hash FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($id, $hash);

if ($stmt->fetch() && password_verify($password, $hash)) {
    $token = bin2hex(random_bytes(16));
    $redis = get_redis();
    $redis->setex("session:$token", SESSION_TTL, $id);
    echo json_encode(['success' => true, 'token' => $token]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
}