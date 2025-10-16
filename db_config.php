<?php
// --- DATABASE CONFIGURATION ---
// Replace these with your actual database credentials.
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');      // Your database username (default for XAMPP is 'root')
define('DB_PASSWORD', '');          // Your database password (default for XAMPP is empty)
define('DB_NAME', 'todo_app_db');   // The name of your database
// --- ATTEMPT TO CONNECT TO MYSQL DATABASE ---
// Create connection using mysqli
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
// Check connection
if ($conn->connect_error) {
    // If connection fails, stop the script and display an error.
    // In a production environment, you would log this error instead of displaying it.
    header('Content-Type: application/json');
    http_response_code(500);
    die(json_encode([
        'message' => 'Database connection failed.',
        'error' => $conn->connect_error
    ]));
}
// Set character set to utf8mb4 for full Unicode support
$conn->set_charset("utf8mb4");
?>
