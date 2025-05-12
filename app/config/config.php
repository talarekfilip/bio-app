<?php
session_start();

// Database configuration
// TODO: Replace with your database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application configuration
define('APP_NAME', 'Portfolio Generator');
define('APP_URL', 'http://localhost/portfolio-generator-v1');
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Create required directories if they don't exist
$directories = [
    UPLOAD_DIR,
    __DIR__ . '/../logs'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Set default timezone
date_default_timezone_set('UTC');

// Security headers
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' https://kit.fontawesome.com; style-src \'self\' \'unsafe-inline\' https://fonts.googleapis.com; font-src \'self\' https://fonts.gstatic.com; img-src \'self\' data:;');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS'])); 