<?php
// Database connection + site constants
if (session_status() === PHP_SESSION_NONE) session_start();

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'uganda_agri_weather');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('BASE_URL', '/uganda-weather-platform/public');
define('SITE_NAME', 'Uganda Agri-Weather Platform');

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die("Database connection failed. Make sure MySQL is running in XAMPP and the 'uganda_agri_weather' database has been imported. (" . $e->getMessage() . ")");
}
