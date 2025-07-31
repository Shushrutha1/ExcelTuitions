<?php
session_start();

// Check if faculty is logged in
if (!isset($_SESSION['uname']) || empty($_SESSION['uname'])) {
    header("Location: Faculty-Login");
    exit;
}

// Include database connection
include 'config.php';

// Check if ID is set
if (isset($_GET['id'])) {
    $schedule_id = intval($_GET['id']);

    // Delete query
    $delete_query = "DELETE FROM faculty_schedule WHERE id = ?";
    $stmt = $conn->prepare($delete_query);

    if (!$stmt) {
        die("Failed to prepare query: " . $conn->error);
    }

    $stmt->bind_param("i", $schedule_id);

    if ($stmt->execute()) {
        echo "<script>alert('Schedule deleted successfully!'); window.location.href='view_schedule.php';</script>";
    } else {
        echo "<script>alert('Failed to delete schedule. Please try again.'); window.location.href='view_schedule.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request. No schedule ID provided.'); window.location.href='view_schedule.php';</script>";
}
?>
