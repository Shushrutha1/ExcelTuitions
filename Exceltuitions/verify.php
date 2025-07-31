<?php
include 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if token exists
    $query = "SELECT id FROM faculty_registration WHERE token = ? AND email_verified = 0";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Database query failed: " . $conn->error);
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Update email_verified flag
        $updateQuery = "UPDATE `faculty_registration` SET `is_active` = 'Active', `email_verified` = '1' WHERE `token` = ?";
        $updateStmt = $conn->prepare($updateQuery);
        if (!$updateStmt) {
            die("Database update failed: " . $conn->error);
        }
        $updateStmt->bind_param("s", $token);

        if ($updateStmt->execute()) {
            echo '<p>Email verified successfully! You can now log in.</p>';
            echo '<a href="Faculty-Login"><<- Back</a>';
        } else {
            echo '<p>Error verifying email. Please try again.</p>';
        }
    } else {
        echo '<p>Invalid or expired token.</p>';
        echo '<a href="index"><<- Back</a>';
    }

    // Close statements
    $stmt->close();
    if (isset($updateStmt)) {
        $updateStmt->close();
    }
} else {
    echo '<p>No token provided.</p>';
    echo '<a href="index"><<- Back</a>';
}
?>

