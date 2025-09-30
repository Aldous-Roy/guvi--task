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