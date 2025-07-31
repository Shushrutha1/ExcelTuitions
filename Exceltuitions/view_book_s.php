<?php
session_start();



// Include required files
include 'header.php';
include 'menu.php';
include 'config.php'; // Database connection

// Fetch booked schedules, ordered by upcoming first and completed last
$query = "SELECT ss.id, fs.faculty_id, fs.faculty_name, ss.schedule_date, ss.schedule_time, ss.status, ss.created_at, fs.subject 
          FROM student_schedule ss
          JOIN faculty_schedule fs ON ss.faculty_schedule_id = fs.id
          WHERE ss.status = 'booked'
          ORDER BY ss.schedule_date ASC, ss.schedule_time ASC"; // Upcoming first

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$current_datetime = date("Y-m-d H:i:s"); // Current date and time
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Meetings</title>
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
            text-align: center;
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

        .readonly {
            background-color: #ddd !important;
            color: #6c757d !important;
            pointer-events: none;
            font-weight: bold;
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
                <h1 style="text-align: center; margin-top: 10px; color:#F00">Students <span style="color:#3db609">Meetings</span></h1>
                <nav class="breadcrumbs text-center">
                    <a href="dashboard.php" class="btn btn-warning"><<== Back to Dashboard</a>
                </nav>
            </div>
        </div>
    </section>

    <h3 class="text-center mt-4">Your Booked Meetings</h3>
    <div class="container">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Faculty ID</th>
                        <th>Faculty Name</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Booking Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $serial_number = 1;
                    $upcoming_meetings = [];
                    $completed_meetings = [];

                    // Separate meetings into upcoming and completed
                    while ($row = $result->fetch_assoc()) {
                        $schedule_datetime = $row['schedule_date'] . ' ' . $row['schedule_time'];
                        $is_past = (strtotime($schedule_datetime) < strtotime($current_datetime)); // Check if past event

                        if ($is_past) {
                            $completed_meetings[] = $row;
                        } else {
                            $upcoming_meetings[] = $row;
                        }
                    }

                    // Display upcoming meetings first
                    foreach ($upcoming_meetings as $row):
                    ?>
                        <tr>
                            <td><?= $serial_number++ ?></td>
                            <td><?= htmlspecialchars($row['faculty_id']) ?></td>
                            <td><?= htmlspecialchars($row['faculty_name']) ?></td>
                            <td><?= htmlspecialchars($row['subject']) ?></td>
                            <td><?= htmlspecialchars(date("d M Y", strtotime($row['schedule_date']))) ?></td>
                            <td><?= htmlspecialchars(date("h:i A", strtotime($row['schedule_time']))) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Display completed meetings -->
                    <?php foreach ($completed_meetings as $row): ?>
                        <tr class="completed">
                            <td><?= $serial_number++ ?></td>
                            <td><?= htmlspecialchars($row['faculty_id']) ?></td>
                            <td><?= htmlspecialchars($row['faculty_name']) ?></td>
                            <td><?= htmlspecialchars($row['subject']) ?></td>
                            <td><?= htmlspecialchars(date("d M Y", strtotime($row['schedule_date']))) ?></td>
                            <td><?= htmlspecialchars(date("h:i A", strtotime($row['schedule_time']))) ?></td>
                            <td><span class="badge badge-completed">Completed</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <nav class="breadcrumbs text-center">
                <a href="sschedule.php" class="btn btn-warning">Schedule a New Meeting</a>
            </nav>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Include JS and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.5/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
