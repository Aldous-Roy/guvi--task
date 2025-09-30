<?php
// DB settings
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'Preetha@ks1');
define('DB_NAME', 'internship_db');

// MongoDB settings
require_once __DIR__ . '/../../vendor/autoload.php';
function get_mongo() {
    $client = new MongoDB\Client("mongodb://127.0.0.1:27017");
    return $client->internship_db; // database
}

// Redis
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', 6379);
define('SESSION_TTL', 60 * 60 * 24); // 24 hours

// Connect to MySQL
function get_db() {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        http_response_code(500);
        exit(json_encode(['error' => 'DB connection failed']));
    }
    return $mysqli;
}

// Connect to Redis
function get_redis() {
    try {
        $redis = new Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);
        return $redis;
    } catch (Exception $e) {
        exit(json_encode(['error' => 'Redis connection failed']));
    }
}

// Helper for JSON input
function json_in() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');