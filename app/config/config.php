<?php
/**
 * JeevanDaan Configuration
 */

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'jeevandaan');
define('DB_PORT', 3306);

// App Settings
define('APP_NAME', 'Jeevandaan');
define('APP_URL', 'http://localhost/jeevandaan/public');
define('APP_ROOT', dirname(dirname(__DIR__)));

// Google OAuth (Replace with your credentials)
define('GOOGLE_CLIENT_ID', 'YOUR-CLIENT-ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR-SECRET-KEY');
define('GOOGLE_REDIRECT_URI', APP_URL . '/auth/google-callback');

// Upload Settings - NOW IN PUBLIC FOLDER
define('UPLOAD_PATH', APP_ROOT . '/public/uploads/');
define('UPLOAD_URL', APP_URL . '/uploads/');  // New: URL for browser access
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png']);

// Donation Rules
define('MIN_DONATION_AGE', 18);
define('MAX_DONATION_AGE', 65);
define('MIN_WEIGHT_KG', 45);
define('DONATION_INTERVAL_DAYS', 90);

// Timezone
date_default_timezone_set('Asia/Kathmandu');

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
