<?php
session_start();
include 'config.php'; // Database connection

// Check if the student is logged in
if (!isset($_SESSION['uname'])) {
    echo "Please log in as a student to book a schedule.";
    exit;
}

// Fetch student ID from the student_registration table using the session username
$uname = $_SESSION['uname'];
$query = "SELECT id FROM student_registration WHERE uname = ?";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $uname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $student_data = $result->fetch_assoc();
        $student_id = $student_data['id'];
    } else {
        echo "Student not found. Please contact the administration.";
        exit;
    }
} else {
    echo "Error preparing student query.";
    exit;
}

if (isset($_GET['schedule_id'])) {
    $schedule_id = intval($_GET['schedule_id']);

    // Fetch schedule details
    $query = "SELECT * FROM faculty_schedule WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $schedule_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $schedule = $result->fetch_assoc();

            // Check if the schedule is already booked
            $check_query = "SELECT * FROM student_schedule WHERE student_id = ? AND faculty_schedule_id = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("ii", $student_id, $schedule_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                echo "You have already booked this schedule.";
            } else {
                // Book the schedule
                $insert_query = "INSERT INTO student_schedule (student_id, faculty_schedule_id, subject, schedule_date, schedule_time, status, created_at) 
                                 VALUES (?, ?, ?, ?, ?, 'booked', NOW())";
                $insert_stmt = $conn->prepare($insert_query);

                if ($insert_stmt) {
                    $insert_stmt->bind_param(
                        "iisss",
                        $student_id,
                        $schedule_id,
                        $schedule['subject'],
                        $schedule['schedule_date'],
                        $schedule['schedule_time']
                    );

                    if ($insert_stmt->execute()) {
                        echo "Schedule booked successfully!";
                    } else {
                        echo "Failed to book the schedule.";
                    }
                } else {
                    echo "Error preparing booking query.";
                }
            }
        } else {
            echo "Invalid schedule ID.";
        }
    } else {
        echo "Error preparing schedule query.";
    }
} else {
    echo "Invalid request. No schedule ID provided.";
}
?>
