<?php
header('Content-Type: application/json');

// Include the config file for database connection
include 'config.php';

// Start the session to get the logged-in user information
session_start();

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Get the logged-in user's ID from the session
$user_id = $_SESSION['userId'];

// Check if a candidateId is provided in the request
// Check if a candidateId is provided in the request
$candidateId = isset($_GET['candidateId']) ? intval($_GET['candidateId']) : null;

if ($candidateId) {
    // Fetch the profile for the specified candidate
    $sql = "SELECT * FROM candidateprofile WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $candidateId);
} else {
    // Fetch the profile for the logged-in user
    $sql = "SELECT * FROM candidateprofile WHERE user_id = ? ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}


$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $profile = $result->fetch_assoc();
    echo json_encode($profile);
} else {
    echo json_encode(['error' => 'No profile data found']);
}

$stmt->close();
$conn->close();
?>
