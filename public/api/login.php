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