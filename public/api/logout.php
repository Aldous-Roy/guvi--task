/**
 * Handles user logout by invalidating the session token.
 *
 * This script expects an 'Authorization' header with a Bearer token.
 * If a valid Bearer token is provided, it deletes the corresponding session from Redis.
 * Responds with a JSON object indicating success regardless of token validity.
 *
 * Dependencies:
 * - Requires 'config.php' for configuration and Redis connection.
 *
 * Response:
 * - JSON: { "success": true }
 */
<?php
require_once 'config.php';

$headers = getallheaders();
$auth = $headers['Authorization'] ?? '';
if (!str_starts_with($auth, 'Bearer ')) { echo json_encode(['success'=>true]); exit; }

$token = substr($auth, 7);
$redis = get_redis();
$redis->del("session:$token");

echo json_encode(['success'=>true]);