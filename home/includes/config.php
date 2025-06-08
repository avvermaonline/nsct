<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create sessions directory if it doesn't exist
$sessionPath = dirname(__DIR__) . '/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}

// Set session save path
session_save_path($sessionPath);

// Session configuration
$session_options = [
    'cookie_httponly' => 1,
    'use_only_cookies' => 1,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'gc_maxlifetime' => 3600, // 1 hour
    'cookie_lifetime' => 0,    // Until browser closes
    'cookie_samesite' => 'Lax'
];

foreach ($session_options as $key => $value) {
    ini_set("session.$key", $value);
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'nsct');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Include database connection
require_once __DIR__ . '/database.php';

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Global functions
function redirect($url) {
    header("Location: $url");
    exit;
}

function sanitize($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// Security headers
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
if(isset($_SERVER['HTTPS'])) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}
