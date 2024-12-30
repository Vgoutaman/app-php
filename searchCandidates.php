<?php
header('Content-Type: application/json');

// Include the database connection
include 'config.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the raw input data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log the incoming data for debugging
error_log("Incoming Data: " . json_encode($data));

// Prepare and sanitize input data
$jobTitle = $conn->real_escape_string($data['Job Title']);
$requiredSkills = $conn->real_escape_string($data['Required Skills']); // Skills as plain text
$location = $conn->real_escape_string($data['Location']);
$experienceLevel = $conn->real_escape_string($data['Experience Level']);

// Log the sanitized data (optional)
error_log("Sanitized Data: Job Title: $jobTitle, Skills: $requiredSkills, Location: $location, Experience Level: $experienceLevel");

// SQL query to search for candidates based on job details (Location, Job Title, Skills, Experience)
$sql = "SELECT * FROM candidateprofile 
        WHERE `Job Title` LIKE '%$jobTitle%' 
        AND `Skills` LIKE '%$requiredSkills%' 
        AND `Location` LIKE '%$location%' 
        AND `Experience` LIKE '%$experienceLevel%'";

// Log the query for debugging
error_log("SQL Query: " . $sql);

$result = $conn->query($sql);

// Check if there are matching candidates
if ($result->num_rows > 0) {
    $candidates = [];
    while($row = $result->fetch_assoc()) {
        $candidates[] = $row; // Collect candidates
    }
    echo json_encode(['status' => 'success', 'candidates' => $candidates]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No candidates found']);
}

$conn->close();
?>
