<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['uname']) || empty($_SESSION['uname'])) {
    header("Location: Faculty-Login");
    exit;
}

// Include database connection
include 'config.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_id = $_SESSION['user_id']; // Faculty ID from session
    $subject = trim($_POST['subject']);
    $schedule_date = trim($_POST['date']);
    $schedule_time = trim($_POST['time']);

    // Validate inputs
    if (empty($subject) || empty($schedule_date) || empty($schedule_time)) {
        die("All fields are required.");
    }

    // Combine date and time to validate against current date and time
    $scheduledDateTime = strtotime("$schedule_date $schedule_time");
    if (!$scheduledDateTime) {
        die("Invalid date or time.");
    }

    // Ensure the scheduled time is not in the past
    if ($scheduledDateTime < time()) {
        die("You cannot schedule a class in the past.");
    }

    // Convert time to 12-hour format for storage
    $schedule_time_12hr = date("h:i A", $scheduledDateTime);

    // Fetch faculty name (tname) from the faculty_registration table
    $query = "SELECT tname FROM faculty_registration WHERE id = ? AND is_active = 'Active'";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Failed to prepare query: " . $conn->error);
    }

    // Bind the faculty_id to the prepared statement
    $stmt->bind_param("i", $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $faculty = $result->fetch_assoc();
        $faculty_name = $faculty['tname']; // Get the faculty name (tname) from the result
    } else {
        die("Faculty not found or inactive.");
    }

    $stmt->close();

    // Insert schedule data into faculty_schedule table
    $query = "INSERT INTO faculty_schedule (faculty_id, faculty_name, subject, schedule_date, schedule_time, created_at) 
              VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Failed to prepare query: " . $conn->error);
    }

    // Bind the parameters for the insert query
    $stmt->bind_param("issss", $faculty_id, $faculty_name, $subject, $schedule_date, $schedule_time_12hr);

    if ($stmt->execute()) {
        echo "Class scheduled successfully!";
        header("Location: tschedule"); // Redirect to the dashboard
        exit;
    } else {
        die("Failed to schedule class: " . $stmt->error);
    }

    $stmt->close();
} else {
    die("Invalid request.");
}
?>
