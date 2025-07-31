<?php
session_start();
require_once 'config.php'; // Include your database connection file

if (isset($_SESSION['user_id'])) {
    $uname = $_SESSION['user_id'];

    // Update the logout time in the user_logins table
    $logoutTime = date("Y-m-d H:i:s");
    $updateLogout = "UPDATE faculty_logins SET logout_time = ? WHERE username = ? AND logout_time IS NULL ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($updateLogout);
    if ($stmt) {
        $stmt->bind_param("ss", $logoutTime, $uname);
        $stmt->execute();
        $stmt->close();
    }
}

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect the user
header("Location: index");
exit();
