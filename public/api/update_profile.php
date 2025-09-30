/**
 * Updates the user's profile information in the MongoDB database.
 *
 * This script performs the following steps:
 * 1. Retrieves the Authorization token from the request headers.
 * 2. Validates the token using Redis to obtain the associated user ID.
 * 3. Parses the incoming JSON payload for profile fields: full_name, age, dob, and contact.
 * 4. Updates the user's profile in the MongoDB 'profiles' collection, setting the provided fields.
 *    - If 'dob' is provided, it is converted to a MongoDB UTCDateTime.
 *    - The 'updated_at' field is set to the current time.
 *    - If the profile does not exist, it is created (upsert).
 * 5. Returns a JSON response indicating success or an error if authentication fails.
 *
 * Requirements:
 * - Requires 'config.php' for database and Redis connection helpers.
 * - Expects a valid Bearer token in the 'Authorization' header.
 * - Expects a JSON payload with optional 'full_name', 'age', 'dob', and 'contact' fields.
 *
 * Response:
 * - On success: { "success": true, "message": "Profile updated" }
 * - On failure: Appropriate HTTP status code and error message.
 */
<?php
require_once 'config.php';

$headers = getallheaders();
$auth = $headers['Authorization'] ?? '';
if (!str_starts_with($auth, 'Bearer ')) { http_response_code(401); exit(json_encode(['error'=>'No token'])); }
$token = substr($auth, 7);
$redis = get_redis();
$user_id = $redis->get("session:$token");
if (!$user_id) { http_response_code(401); exit(json_encode(['error'=>'Invalid token'])); }

$data = json_in();
$full_name = $data['full_name'] ?? null;
$age = $data['age'] ?? null;
$dob = $data['dob'] ?? null;
$contact = $data['contact'] ?? null;

// MongoDB
$mongo = get_mongo();
$profiles = $mongo->profiles;

$updateData = [
    'full_name' => $full_name,
    'age' => $age,
    'dob' => $dob ? new MongoDB\BSON\UTCDateTime(strtotime($dob) * 1000) : null,
    'contact' => $contact,
    'updated_at' => new MongoDB\BSON\UTCDateTime()
];

$profiles->updateOne(
    ['user_id' => (int)$user_id],
    ['$set' => $updateData],
    ['upsert' => true]
);

echo json_encode(['success' => true, 'message' => 'Profile updated']);