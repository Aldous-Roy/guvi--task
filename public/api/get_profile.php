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

// Convert MongoDB\BSON\UTCDateTime to readable date string for dob
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