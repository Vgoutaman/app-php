<?php
header('Content-Type: application/json');

// Include the config file for database connection
include 'config.php';

try {
    // Fetch all profiles from the savedprofile table
    $sql = "SELECT 
                `Name` AS name,
                `Location` AS location,
                `Job Title` AS job_title,
                `Portfolio` AS portfolio,
                `Skills` AS skills,
                `Experience` AS experience,
                `Job History` AS job_history,
                `Social Media` AS social_media
            FROM `savedprofile`";

    // Execute the query
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $profiles = [];
        while ($row = $result->fetch_assoc()) {
            // Decode JSON fields like Skills, if needed
            $row['skills'] = json_decode($row['skills'], true);
            $profiles[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $profiles]);
    } else {
        echo json_encode(['status' => 'success', 'data' => []]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error fetching profiles: ' . $e->getMessage()
    ]);
}

// Close the database connection
$conn->close();
?>
