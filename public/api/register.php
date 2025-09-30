<?php
require_once 'config.php';

$data = json_in();
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$full_name = $data['full_name'] ?? null;

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email or password']);
    exit;
}

$mysqli = get_db();
$stmt = $mysqli->prepare("INSERT INTO users (email, password_hash, full_name) VALUES (?, ?, ?)");
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt->bind_param("sss", $email, $hash, $full_name);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(409);
    echo json_encode(['error' => 'Email already registered']);
}