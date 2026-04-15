<?php
/**
 * Database Configuration
 * Furniture Shop - E-commerce Platform
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'furniture_shop');

// Create database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set charset to UTF8
mysqli_set_charset($conn, 'utf8mb4');

/**
 * Helper Functions
 */

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Redirect function
function redirect($url) {
    header("Location: $url");
    exit();
}

// Escape string for SQL
function escape($conn, $string) {
    return mysqli_real_escape_string($conn, $string);
}

// Format price
function formatPrice($price) {
    return '€' . number_format($price, 2, '.', ',');
}

// Get current timestamp
function getCurrentTimestamp() {
    return date('Y-m-d H:i:s');
}

/**
 * Session Configuration
 */

// Set session cookie parameters for security
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

// In production, enable HTTPS-only cookies
// ini_set('session.cookie_secure', 1);

/**
 * Error Reporting
 * Change based on environment
 */

// Development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Production (uncomment for live site)
// error_reporting(0);
// ini_set('display_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', '/path/to/error.log');

/**
 * Application Settings
 */

// Site URL
define('SITE_URL', 'http://localhost/furniture_shop');

// Upload directory
define('UPLOAD_DIR', __DIR__ . '/images/');

// Max file upload size (in bytes)
define('MAX_FILE_SIZE', 5242880); // 5MB

// Allowed image extensions
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Items per page for pagination
define('ITEMS_PER_PAGE', 12);

// Currency
define('CURRENCY', '€');
define('CURRENCY_CODE', 'EUR');

/**
 * Email Configuration (if using PHPMailer)
 */

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com'); // Change this
define('SMTP_PASSWORD', 'your-app-password'); // Change this (use App Password)
define('SMTP_FROM_EMAIL', 'your-email@gmail.com'); // Change this
define('SMTP_FROM_NAME', 'Мебели Онлайн');

/**
 * Stripe Configuration (if using Stripe payments)
 */

define('STRIPE_PUBLISHABLE_KEY', 'pk_test_your_key_here'); // Change this
define('STRIPE_SECRET_KEY', 'sk_test_your_key_here'); // Change this

/**
 * Admin Default Credentials
 * 
 * Username: momos1607
 * Password: password (change after first login!)
 * 
 * You can change password in database using:
 * UPDATE users SET password = '$2y$10$YOUR_NEW_HASH' WHERE username = 'momos1607';
 */

?>
