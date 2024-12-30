<?php
session_start();

if (isset($_SESSION['userId'])) {
    // User is logged in
    echo "Welcome " . $_SESSION['username'];  // Display the username
} else {
    // User is not logged in
    echo "Please log in to access this page.";
}
?>
