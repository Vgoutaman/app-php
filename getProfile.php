<?php
// Database connection (adjust accordingly)
// Database connection (adjust accordingly)
require 'config.php';

// Check if 'user_id' is passed in the request
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Prepare SQL query to fetch candidate profile based on 'user_id'
    $stmt = $conn->prepare("SELECT * FROM candidateprofile WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);  // Bind the 'user_id' as integer
    $stmt->execute();
    $result = $stmt->get_result();

    // If candidate exists, check if profile is saved
    if ($result->num_rows > 0) {
        $profile = $result->fetch_assoc();

        // Check if the profile is saved by checking savedprofile table
        $checkSavedStmt = $conn->prepare("SELECT * FROM savedprofile WHERE user_id = ?");
        $checkSavedStmt->bind_param("i", $user_id);
        $checkSavedStmt->execute();
        $savedResult = $checkSavedStmt->get_result();

        // Add 'isSaved' field based on whether the profile is saved
        if ($savedResult->num_rows > 0) {
            $profile['isSaved'] = true;
        } else {
            $profile['isSaved'] = false;
        }

        echo json_encode($profile);  // Send profile data along with 'isSaved' as JSON response
    } else {
        echo json_encode(["message" => "Candidate not found"]);
    }
} else {
    // If 'user_id' is missing, return an error
    echo json_encode(["message" => "user_id is missing"]);
}

?>