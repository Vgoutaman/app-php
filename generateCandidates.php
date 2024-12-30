<?php
// Include the database configuration file
require 'config.php';

// Set headers for CORS (if needed)
header('Access-Control-Allow-Origin: http://10.0.0.2'); // Replace with your app's origin
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');

// Get the job details from the request (sent by the frontend)
$data = json_decode(file_get_contents("php://input"), true);

// Extract job details
$companyName = $data['companyName'] ?? null;
$location = $data['location'] ?? null;
$jobTitle = $data['jobTitle'] ?? null;
$requiredSkills = $data['requiredSkills'] ?? null;
$experienceLevel = $data['experienceLevel'] ?? null;

// Validate the inputs
if (!$companyName || !$location || !$jobTitle || !$requiredSkills || !$experienceLevel) {
    echo json_encode([
        'status' => 'error',
        'message' => 'All fields are required.'
    ]);
    exit;
}

// Prepare the SQL query to match candidates based on job details
$sql = "
    SELECT * FROM candidateprofile 
    WHERE FIND_IN_SET(?, skills) > 0 
    AND experience >= ?
    AND (`Job Title` = ? OR ? IS NULL)
    AND (location = ? OR ? IS NULL)
";

// Prepare and bind the statement
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ssssss", $requiredSkills, $experienceLevel, $jobTitle, $jobTitle, $location, $location);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $candidates = [];
        while ($row = $result->fetch_assoc()) {
            $candidates[] = $row;
        }
        
        // Return the matched candidates as JSON
        echo json_encode([
            'status' => 'success',
            'candidates' => $candidates
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to retrieve candidates.'
        ]);
    }
    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error preparing statement: ' . $conn->error
    ]);
}

$conn->close();
?>