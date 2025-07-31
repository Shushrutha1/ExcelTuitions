<?php
session_start();

// Include required files
include 'header.php';
include 'menu.php';
include 'config.php'; // Database connection

// Check if student is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: Student-Login");
    exit;
}

$booking_message = ""; // To display success/error messages

// Handle search request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_schedule'])) {
    $subject = trim($_POST['subject']);
    $date = $_POST['schedule_date'];

    // Query to get available schedules
    $query = "SELECT fs.id, fs.faculty_id, fs.subject, fs.schedule_date, fs.schedule_time, fs.created_at 
              FROM faculty_schedule fs
              WHERE fs.subject = ? AND fs.schedule_date = ? AND NOT EXISTS (
                  SELECT 1 FROM student_schedule ss WHERE ss.faculty_schedule_id = fs.id AND ss.status = 'booked'
              )";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $subject, $date);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Handle booking request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_schedule'])) {
    $student_id = $_SESSION['user_id']; // Logged-in student's ID
    $schedule_id = intval($_POST['schedule_id']);

    // Fetch schedule details
    $query = "SELECT * FROM faculty_schedule WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $schedule = $result->fetch_assoc();

        // Check if student already booked this schedule
        $check_query = "SELECT * FROM student_schedule WHERE student_id = ? AND faculty_schedule_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ii", $student_id, $schedule_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $booking_message = "<div class='alert alert-warning text-center'>You have already booked this schedule!</div>";
        } else {
            // Insert booking into student_schedule
            $insert_query = "INSERT INTO student_schedule (student_id, faculty_schedule_id, subject, schedule_date, schedule_time, status, created_at) 
                             VALUES (?, ?, ?, ?, ?, 'booked', NOW())";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param(
                "iisss",
                $student_id,
                $schedule_id,
                $schedule['subject'],
                $schedule['schedule_date'],
                $schedule['schedule_time']
            );

            if ($insert_stmt->execute()) {
                $booking_message = "<div class='alert alert-success text-center'>Schedule booked successfully!</div>";
            } else {
                $booking_message = "<div class='alert alert-danger text-center'>Booking failed. Please try again!</div>";
            }
        }
    } else {
        $booking_message = "<div class='alert alert-danger text-center'>Invalid schedule selected.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Schedule</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-section {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .btn-schedule {
            background-color: #28a745;
            color: #ffffff;
        }
        .btn-schedule:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <section id="hero" class="hero section">
        <div class="container mt-5">
            <img src="assets/img/logo5.gif" alt="Logo" style="width: 10%; height: auto; display: block; margin: 0 auto;">
            <h1 class="text-center mt-4 text-danger">Student <span class="text-success">Schedule</span></h1>
            <nav class="breadcrumbs text-center">
                <a href="dashboard" class="btn btn-warning"><<== Back to Dashboard</a>
            </nav>
        </div>

        <!-- Search Form -->
        <div class="container mt-4">
            <div class="form-section">
                <h3 class="text-center text-success mb-4">Search Available Schedules</h3>
                <form method="POST" id="scheduleForm">
                    <div class="form-group">
                        <label for="subject"><b>Enter Subject:</b></label>
                        <input type="text" id="subject" name="subject" class="form-control" placeholder="Enter Subject Name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="schedule_date"><b>Select Date:</b></label>
                        <input type="date" id="schedule_date" name="schedule_date" class="form-control" required>
                    </div>

                    <button type="submit" name="search_schedule" class="btn btn-schedule btn-block">Search</button>
                </form>
            </div>

            <!-- Display Booking Messages -->
            <?= $booking_message; ?>

            <!-- Display Available Schedules -->
            <?php if (isset($result) && $result->num_rows > 0): ?>
                <div class="mt-4">
                    <h3 class="text-center text-success">Available Schedules</h3>
                    <table class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Faculty ID</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['faculty_id']) ?></td>
                                    <td><?= htmlspecialchars($row['subject']) ?></td>
                                    <td><?= htmlspecialchars(date("d M Y", strtotime($row['schedule_date']))) ?></td>
                                    <td><?= htmlspecialchars(date("h:i A", strtotime($row['schedule_time']))) ?></td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="schedule_id" value="<?= $row['id'] ?>">
                                            <button type="submit" name="book_schedule" class="btn btn-success btn-block">Book This Schedule</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <nav class="breadcrumbs text-center">
                    <a href="view_book_s.php" class="btn btn-warning">View Your Schedules</a>
                </nav>
                </div>
            <?php elseif (isset($result)): ?>
                <div class="alert alert-warning text-center">No schedules available for the entered subject and date.</div>
                <nav class="breadcrumbs text-center">
                    <a href="view_book_s.php" class="btn btn-warning">View Your Schedules</a>
                </nav>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <!-- Include JS and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.5/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        document.getElementById('schedule_date').setAttribute('min', new Date().toISOString().split('T')[0]);
    </script>

</body>
</html>  
