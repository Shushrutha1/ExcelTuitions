<?php
// Database connection
include("config.php");
// Check for the token
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verify the token
    $sql = "SELECT id FROM student_registration WHERE token = ? AND status = 'Inactive'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Activate the account
        $update_sql = "UPDATE student_registration SET status = 'Active', token = NULL WHERE token = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("s", $token);

        if ($update_stmt->execute()) {
      		echo '<script>alert("Account activated successfully!"); window.location.href = "https://exceltuitions.com/Login";</script>';
        } else {
			echo '<script>alert("Failed to activate account"); window.location.href = "https://exceltuitions.com/Login";</script>';

        }
    } else {
		echo '<script>alert("Invalid or expired Link"); window.location.href = "https://exceltuitions.com/Login";</script>';
    }

    $stmt->close();
}

$conn->close();
?>
