<?php
require 'config.php';

// Start the session
session_start();

// Get data from POST request
$data = json_decode(file_get_contents("php://input"));

// Check if the required properties exist
if (!isset($data->email) || !isset($data->password)) {
    echo json_encode(array("status" => "error", "message" => "Please fill out all fields"));
    exit();
}

$email = $data->email;
$password = $data->password;

// Validation
if (empty($email) || empty($password)) {
    echo json_encode(array("status" => "error", "message" => "Please fill out all fields"));
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(array("status" => "error", "message" => "Please enter a valid email"));
    exit();
}

// Sanitize email and password
$email = $conn->real_escape_string($email);
$password = $conn->real_escape_string($password);

// Check if the email exists in the database
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verify password (direct comparison)
    if ($user['password'] === $password) {
        // Store user information in session
        $_SESSION['userId'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['username'] = $user['username'];

        echo json_encode(array(
            "status" => "success",
            "message" => "Login successful",
            "user" => array(
                "id" => $user['id'],
                "email" => $user['email'],
                "username" => $user['username']
            )
        ));
    } else {
        echo json_encode(array("status" => "error", "message" => "Incorrect password"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Email not found"));
}

$stmt->close();
$conn->close();
?>
