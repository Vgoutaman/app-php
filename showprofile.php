<?php
header('Content-Type: application/json');

// Include the config file for database connection
include 'config.php';

// Get the candidate ID from the request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['candidate_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Candidate ID is required']);
    exit;
}

$candidate_id = $data['candidate_id'];

// Fetch candidate details from the candidateprofile table
$sql = "SELECT * FROM candidateprofile WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if candidate was found
if ($result->num_rows > 0) {
    $candidate = $result->fetch_assoc();
    echo json_encode(['status' => 'success', 'candidate' => $candidate]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Candidate not found']);
}

$stmt->close();
$conn->close();
?>
