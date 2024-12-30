<?php
// Include the database connection file
require 'config.php';

// Start the session to get user information
session_start();

// Set headers for CORS
header('Access-Control-Allow-Origin: http://10.0.0.2'); // Replace with your app's origin
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');

// Check if user is logged in and user_id exists in session
if (!isset($_SESSION['userId'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in.'
    ]);
    exit;
}

// Get the logged-in user's ID from the session
$userId = $_SESSION['userId'];

// Fetch jobs posted by the logged-in user
$sql = "SELECT `id`, `Company Name` AS company_name, `Location` AS job_location, `Job Title` AS title, 
        `Required Skills` AS skills, `Experience Level` AS level 
        FROM `jobs` 
        WHERE `user_id` = ?"; // Filter jobs by user_id

// Prepare and execute the query
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $userId); // Bind user_id parameter to the query
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $jobs = [];
            while ($row = $result->fetch_assoc()) {
                $jobs[] = $row;
            }
            echo json_encode(['status' => 'success', 'data' => $jobs]);
        } else {
            echo json_encode(['status' => 'success', 'data' => []]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'SQL query failed: ' . $stmt->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to prepare SQL statement.'
    ]);
}

// Close the database connection
$conn->close();
?>
