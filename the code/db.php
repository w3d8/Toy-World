<?php
// Define database connection constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'toyworld_db');

// Error reporting for development (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Create connection using mysqli
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to ensure proper encoding
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    // Log error (in production, you should log to a file instead of displaying)
    die("Database connection error: " . $e->getMessage());
}