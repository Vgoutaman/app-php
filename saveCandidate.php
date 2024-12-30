<?php
header('Content-Type: application/json');

// Include the config file for database connection
include 'config.php';

// Get the raw input data
// Get the raw input data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log the incoming data for debugging
error_log("Incoming Data: " . json_encode($data));

if (!$data) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Prepare and sanitize input data
$name = $conn->real_escape_string($data['name']);
$location = $conn->real_escape_string($data['location']);
$job_title = $conn->real_escape_string($data['job_title']);
$portfolio = $conn->real_escape_string($data['portfolio']);
$skills = $conn->real_escape_string(json_encode($data['skills'])); // Store skills as JSON
$experience = $conn->real_escape_string($data['experience']);
$job_history = $conn->real_escape_string($data['job_history']);
$social_media = $conn->real_escape_string($data['social_media']);
$user_id = $conn->real_escape_string($data['user_id']); // Capture the user_id

// Log the final SQL data for debugging
error_log("Prepared Data - Name: $name, Skills: $skills, User ID: $user_id");

// Insert profile data into the `savedprofile` table
$sql = "INSERT INTO savedprofile (user_id, Name, Location, `Job Title`, Portfolio, Skills, Experience, `Job History`, `Social Media`)
        VALUES ('$user_id', '$name', '$location', '$job_title', '$portfolio', '$skills', '$experience', '$job_history', '$social_media')";

// Log the query
error_log("SQL Query: " . $sql);

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => 'Profile saved successfully']);
} else {
    error_log('MySQL Error: ' . $conn->error);  // Log MySQL error
    echo json_encode(['error' => 'Error saving profile: ' . $conn->error]);
}

$conn->close();

?>
