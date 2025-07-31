<?php
session_start();

// Check if faculty is logged in
if (!isset($_SESSION['uname']) || empty($_SESSION['uname'])) {
    header("Location: Faculty-Login");
    exit;
}

// Include required files
include 'header.php';
include 'menu.php';
include 'config.php'; // Database connection

$faculty_id = $_SESSION['user_id'];

// Default values for form fields
$class_id = $subject = $schedule_date = $schedule_time = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $subject = trim($_POST['subject']);
    $schedule_date = trim($_POST['date']);
    $schedule_time = trim($_POST['time']);

    // Validate inputs
    if (empty($subject) || empty($schedule_date) || empty($schedule_time)) {
        $error = "All fields are required.";
    } else {
        // Combine date and time to validate
        $scheduledDateTime = strtotime("$schedule_date $schedule_time");
        if (!$scheduledDateTime) {
            $error = "Invalid date or time.";
        } elseif ($scheduledDateTime < time()) {
            $error = "You cannot reschedule a class in the past.";
        } else {
            // Convert time to 12-hour format
            $schedule_time_12hr = date("h:i A", $scheduledDateTime);

            // Update query
            $query = "UPDATE faculty_schedule SET subject = ?, schedule_date = ?, schedule_time = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssi", $subject, $schedule_date, $schedule_time_12hr, $class_id);

            if ($stmt->execute()) {
                $success = "Class rescheduled successfully!";
            } else {
                $error = "Failed to reschedule class: " . $stmt->error;
            }

            $stmt->close();
        }
    }
} else {
    // Fetch existing schedule for form
    $class_id = $_GET['id'];
    $query = "SELECT subject, schedule_date, schedule_time FROM faculty_schedule WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $schedule = $result->fetch_assoc();

    if ($schedule) {
        $subject = $schedule['subject'];
        $schedule_date = $schedule['schedule_date'];
        $schedule_time = $schedule['schedule_time'];
    } else {
        $error = "Class not found.";
    }
    $stmt->close();
}
?>

<!-- Page Title -->
<section id="hero" class="hero section">
    <div class="page-title light-background">
        <div class="container">
            <img src="assets/img/logo5.gif" alt="Logo" style="width: 10%; height: auto; display: block; margin: 0 auto;" />
            <h1 style="text-align: center; margin-top: 10px; color:#F00">Reschedule Class</h1>
            <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
                <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
                    <li><a href="TDashboard" style="text-decoration: none; color: #036;" class="btn btn-warning"> <<==Back to Dashboard</a></li>
                </ol>
            </nav>
        </div>

        <div class="container mt-5">
            <h2 class="text-center">Edit Scheduled Class</h2>
            <div class="card" style="background-color: transparent;">
                <div class="card-body">
                    <?php if (isset($success)) echo "<p class='text-center text-success'>$success</p>"; ?>
                    <?php if (isset($error)) echo "<p class='text-center text-danger'>$error</p>"; ?>

                    <form action="tschedule.php" method="POST">
                        <input type="hidden" name="class_id" value="<?= htmlspecialchars($class_id) ?>" />

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" class="form-control" value="<?= htmlspecialchars($subject) ?>" required />
                        </div>

                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" id="date" name="date" class="form-control" value="<?= htmlspecialchars($schedule_date) ?>" required />
                        </div>

                        <div class="form-group">
                            <label for="time">Time</label>
                            <input type="time" id="time" name="time" class="form-control" value="<?= htmlspecialchars($schedule_time) ?>" required />
                        </div>

                        <button type="submit" class="btn btn-success btn-block">Update Schedule</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<!-- Include JS and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.5/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
