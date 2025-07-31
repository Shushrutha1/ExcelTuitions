<?php
session_start();



// Include required files
include 'header.php';
include 'menu.php';
include 'config.php'; // Database connection

// Fetch the schedules that the faculty has been booked for, including student names
$query = "SELECT ss.id, ss.student_id, sr.sname, ss.schedule_date, ss.schedule_time, ss.status, ss.created_at, fs.subject 
          FROM student_schedule ss
          JOIN faculty_schedule fs ON ss.faculty_schedule_id = fs.id
          JOIN student_registration sr ON ss.student_id = sr.id
          WHERE ss.status = 'booked'";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$current_datetime = date("Y-m-d H:i:s"); // Current date and time
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Faculty Meetings</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-custom {
            background-color: #28a745;
            color: white;
        }

        .btn-custom:hover {
            background-color: #218838;
        }

        .text-success {
            color: #28a745 !important;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }

        .custom-table thead tr {
            background-color: #3db609;
            color: #ffffff;
            text-align: center;
            font-weight: bold;
        }

        .custom-table th, .custom-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        .custom-table tbody tr {
            border-bottom: 1px solid #ddd;
        }

        .custom-table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        .custom-table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .custom-table tbody tr:last-of-type {
            border-bottom: 2px solid #3db609;
        }

        .completed {
            background-color: #f3f3f3;
            color: #999;
        }

        .badge-completed {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Page Title -->
    <section id="hero" class="hero section">
        <div class="page-title light-background">
            <div class="container">
                <img src="assets/img/logo5.gif" alt="Logo" style="width: 10%; height: auto; display: block; margin: 0 auto;" />
                <h1 style="text-align: center; margin-top: 10px; color:#F00">Faculty <span style="color:#3db609">Meetings</span></h1>
                <nav class="breadcrumbs text-center">
                    <a href="TDashboard" class="btn btn-warning"><<== Back to Dashboard</a>
                </nav>
            </div>
        </div>
    </section>

    <h3 class="text-center mt-4">Your Booked Students</h3>
    <div class="container">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Booking Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $serial_number = 1;
                    $meetings = [];

                    // Fetch all the records into an array
                    while ($row = $result->fetch_assoc()) {
                        $row['schedule_datetime'] = $row['schedule_date'] . ' ' . $row['schedule_time'];
                        $meetings[] = $row;
                    }

                    // Separate upcoming and completed meetings
                    $upcoming_meetings = [];
                    $completed_meetings = [];

                    foreach ($meetings as $meeting) {
                        if (strtotime($meeting['schedule_datetime']) >= strtotime($current_datetime)) {
                            $upcoming_meetings[] = $meeting; // Upcoming meetings
                        } else {
                            $completed_meetings[] = $meeting; // Completed meetings
                        }
                    }

                    // Sort upcoming meetings by datetime
                    usort($upcoming_meetings, function($a, $b) {
                        return strtotime($a['schedule_datetime']) - strtotime($b['schedule_datetime']);
                    });

                    // Display upcoming meetings
                    foreach ($upcoming_meetings as $row):
                    ?>
                        <tr>
                            <td><?= $serial_number++ ?></td>
                            <td><?= htmlspecialchars($row['student_id']) ?></td> <!-- Display Student ID -->
                            <td><?= htmlspecialchars($row['sname']) ?></td> <!-- Display Student Name -->
                            <td><?= htmlspecialchars($row['subject']) ?></td>
                            <td><?= htmlspecialchars(date("d M Y", strtotime($row['schedule_date']))) ?></td>
                            <td><?= htmlspecialchars(date("h:i A", strtotime($row['schedule_time']))) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Display completed meetings at the bottom -->
                    <?php foreach ($completed_meetings as $row): ?>
                        <tr class="completed">
                            <td><?= $serial_number++ ?></td>
                            <td><?= htmlspecialchars($row['student_id']) ?></td> <!-- Display Student ID -->
                            <td><?= htmlspecialchars($row['sname']) ?></td> <!-- Display Student Name -->
                            <td><?= htmlspecialchars($row['subject']) ?></td>
                            <td><?= htmlspecialchars(date("d M Y", strtotime($row['schedule_date']))) ?></td>
                            <td><?= htmlspecialchars(date("h:i A", strtotime($row['schedule_time']))) ?></td>
                            <td><span class="badge badge-completed">Completed</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Include JS and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.5/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
