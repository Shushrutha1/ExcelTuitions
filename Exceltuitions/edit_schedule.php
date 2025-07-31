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

// Check if ID is set
if (isset($_GET['id'])) {
    $schedule_id = intval($_GET['id']);

    // Fetch the current schedule details
    $query = "SELECT subject, schedule_date, schedule_time FROM faculty_schedule WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $schedule = $result->fetch_assoc();
    } else {
        echo "No schedule found with the provided ID.";
        exit;
    }
} else {
    echo "Invalid request. Schedule ID not provided.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = $_POST['subject'];
    $schedule_date = $_POST['schedule_date'];
    $schedule_time = $_POST['schedule_time'];

    // Update schedule in the database with updated `created_at`
    $update_query = "UPDATE faculty_schedule 
                     SET subject = ?, schedule_date = ?, schedule_time = ?, created_at = NOW() 
                     WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssi", $subject, $schedule_date, $schedule_time, $schedule_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Schedule updated successfully!'); window.location.href='view_schedule.php';</script>";
    } else {
        echo "Failed to update schedule: " . $conn->error;
    }
}
?>

<!-- Page Title -->
<section id="hero" class="hero section">
    <div class="container mt-5">
        <div class="container">
            <img src="assets/img/logo5.gif" alt="Logo" style="width: 10%; height: auto; display: block; margin: 0 auto;" />
            <h2 class="text-center mb-4" style="color:#F00">Edit Your <span style="color:#3db609">Schedule</span></h2>
            <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
                <h4 style="color:#039">Editing Schedule for Subject: <span style="color:#093"><?= htmlspecialchars($schedule['subject']) ?></span></h4>
                <hr>
                <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
                    <li><a href="TDashboard" style="text-decoration: none; color: #036;" class="btn btn-warning"> <<== Back to Admin Panel</a></li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card" style="background-color: transparent;">
                    <div class="card-body">
                        <form method="POST" id="editScheduleForm">
                            <div class="form-group mb-3">
                                <label for="subject" style="font-weight:bold;">Subject</label>
                                <input type="text" id="subject" name="subject" class="form-control" value="<?= htmlspecialchars($schedule['subject']) ?>" required readonly="readonly">
                            </div>

                            <div class="form-group mb-3">
                                <label for="schedule_date" style="font-weight:bold;">Date</label>
                                <input type="date" id="schedule_date" name="schedule_date" class="form-control" value="<?= htmlspecialchars($schedule['schedule_date']) ?>" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="schedule_time" style="font-weight:bold;">Time</label>
                                <input type="time" id="schedule_time" name="schedule_time" class="form-control" value="<?= htmlspecialchars($schedule['schedule_time']) ?>" required>
                            </div>

                            <button type="submit" class="btn btn-success btn-block">Update Schedule</button>
                            <a href="view_schedule.php" class="btn btn-secondary btn-block">Cancel</a>
                        </form>
                    </div>
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

<script>
    // Ensure the selected date and time are not in the past
    document.getElementById('editScheduleForm').addEventListener('submit', function(event) {
        const dateInput = document.getElementById('schedule_date');
        const timeInput = document.getElementById('schedule_time');

        const selectedDate = new Date(dateInput.value + 'T' + timeInput.value);
        const now = new Date();

        if (selectedDate < now) {
            alert('You cannot schedule a class in the past. Please select a valid date and time.');
            event.preventDefault();
        }
    });

    // Set minimum date for the date input to today
    document.getElementById('schedule_date').setAttribute('min', new Date().toISOString().split('T')[0]);
</script>
