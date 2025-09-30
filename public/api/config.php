/**
 * Configuration file for database and cache connections.
 *
 * - Defines constants for MySQL, MongoDB, and Redis connection settings.
 * - Provides helper functions to connect to MySQL (`get_db()`), MongoDB (`get_mongo()`), and Redis (`get_redis()`).
 * - Includes a helper function (`json_in()`) to parse JSON input from HTTP requests.
 * - Sets HTTP headers for JSON content type and CORS.
 *
 * MySQL:
 *   - DB_HOST: Hostname for MySQL server.
 *   - DB_USER: Username for MySQL.
 *   - DB_PASS: Password for MySQL.
 *   - DB_NAME: Database name.
 *
 * MongoDB:
 *   - Uses Composer autoload for MongoDB client.
 *   - `get_mongo()` returns a MongoDB database instance.
 *
 * Redis:
 *   - REDIS_HOST: Hostname for Redis server.
 *   - REDIS_PORT: Port for Redis server.
 *   - SESSION_TTL: Session time-to-live in seconds.
 *
 * Functions:
 *   - get_db(): Returns a MySQLi connection or outputs error as JSON.
 *   - get_mongo(): Returns a MongoDB database instance.
 *   - get_redis(): Returns a Redis connection or outputs error as JSON.
 *   - json_in(): Decodes JSON input from HTTP request body.
 *
 * HTTP Headers:
 *   - Sets Content-Type to application/json.
 *   - Allows CORS from any origin and specific headers.
 */
<?php
// DB settings
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'Preetha@ks1'); //replace with your MySQL root password
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

// Headers with CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');