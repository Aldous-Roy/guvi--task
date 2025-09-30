<?php
require_once 'config.php';

$headers = getallheaders();
$auth = $headers['Authorization'] ?? '';
if (!str_starts_with($auth, 'Bearer ')) { echo json_encode(['success'=>true]); exit; }

$token = substr($auth, 7);
$redis = get_redis();
$redis->del("session:$token");

echo json_encode(['success'=>true]);