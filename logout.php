<?php
// Start the session and include necessary files

session_start();

// Ensure proper headers are sent
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Allow all origins (adjust as needed)

// Include database configuration if required
require_once 'config.php'; // Uncomment if you need database connection

// Destroy the session
if (session_id()) {
    session_unset();    // Clear session variables
    session_destroy();  // Destroy session data
    echo json_encode(array("status" => "success", "message" => "Logged out successfully"));
    exit();
} else {
    echo json_encode(array("status" => "error", "message" => "No active session found"));
    exit();
}
?>
