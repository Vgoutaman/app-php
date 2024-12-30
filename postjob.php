<?php
// Include the database connection file
require 'config.php';

// Start the session to get user information
session_start();

// Set headers for CORS (if needed)
header('Access-Control-Allow-Origin: http://10.0.0.2'); // Replace with your app's origin
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');

// Get the job details from the request (sent by the frontend)
$data = json_decode(file_get_contents("php://input"), true);

// Extract job details
$companyName = $data['Company Name'] ?? null;
$location = $data['Location'] ?? null;
$jobTitle = $data['Job Title'] ?? null;
$requiredSkills = $data['Required Skills'] ?? null;
$experienceLevel = $data['Experience Level'] ?? null;

// Validate the inputs
if (!$companyName || !$location || !$jobTitle || !$requiredSkills || !$experienceLevel) {
    echo json_encode([
        'status' => 'error',
        'message' => 'All fields are required.'
    ]);
    exit;
}

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

// Insert the job into the database along with user_id
$sql = "INSERT INTO jobs (`Company Name`, `Location`, `Job Title`, `Required Skills`, `Experience Level`, `user_id`)
        VALUES (?, ?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sssssi", $companyName, $location, $jobTitle, $requiredSkills, $experienceLevel, $userId);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Job posted successfully.'
        ]);
    } else {
        $error = $stmt->error;
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to post the job. SQL Error: ' . $error
        ]);
    }
    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to prepare the SQL statement.'
    ]);
}

$conn->close();
?>
