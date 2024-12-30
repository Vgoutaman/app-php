<?php
require 'config.php';

// Start the session
session_start();

// Get data from POST request
$data = json_decode(file_get_contents("php://input"));

// Check if the required properties exist
if (!isset($data->username) || !isset($data->email) || !isset($data->password) || !isset($data->confirmPassword)) {
    echo json_encode(array("status" => "error", "message" => "Please fill out all fields"));
    exit();
}

$userName = $data->username;
$email = $data->email;
$password = $data->password;
$confirmPassword = $data->confirmPassword;  // This will be received but not typically stored

// Validation
if (empty($userName) || empty($email) || empty($password) || empty($confirmPassword)) {
    echo json_encode(array("status" => "error", "message" => "Please fill out all fields"));
    exit();
}

if ($password !== $confirmPassword) {
    echo json_encode(array("status" => "error", "message" => "Passwords do not match"));
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(array("status" => "error", "message" => "Please enter a valid email"));
    exit();
}

// Check if email already exists
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(array("status" => "error", "message" => "Email already exists"));
    exit();
}

// Insert the user data into the database
$sql = "INSERT INTO users (username, email, password) VALUES ('$userName', '$email', '$password')";

if ($conn->query($sql) === TRUE) {
    // After successful signup, log the user in by storing session variables
    $_SESSION['userId'] = $conn->insert_id;  // Store the user ID in session
    $_SESSION['email'] = $email;
    $_SESSION['username'] = $userName;

    echo json_encode(array("status" => "success", "message" => "Sign-up successful! You are now logged in."));
} else {
    echo json_encode(array("status" => "error", "message" => "Error: " . $conn->error));
}

$conn->close();
?>
