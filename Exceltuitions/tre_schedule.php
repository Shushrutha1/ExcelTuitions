<?php
session_start();

// Check if faculty is logged in
if (!isset($_SESSION['uname']) || empty($_SESSION['uname'])) {
    header("Location: Faculty-Login");
    exit;
}

// Include required files
include 'header.php';
include 'menu.php'; // Ensure no output occurs before this
include 'config.php'; // Database connection

// Fetch class data if ID is provided
$class_id = isset($_GET['class_id']) ? $_GET['class_id'] : '';

if ($class_id) {
    $query = "SELECT subject, schedule_date, schedule_time FROM faculty_schedule WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Failed to prepare query: " . $conn->error);
    }
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $schedule = $result->fetch_assoc();
} else {
	echo'<script>window.location.href = "faculty_schedule.php";</script>' ;
}
?>

<section id="hero" class="hero section">
    <div class="page-title light-background">
        <div class="container">
            <img src="assets/img/logo5.gif" alt="Logo" style="width: 10%; height: auto; display: block; margin: 0 auto;" />
            <h1 style="text-align: center; margin-top: 10px; color:#F00">Faculty <span style="color:#3db609">Schedule</span></h1>
            <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
                <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
                    <li><a href="faculty_schedule.php" style="text-decoration: none; color: #036;" class="btn btn-warning"> <<==Back to Schedule</a></li>
                </ol>
            </nav>
        </div>

        <div class="container mt-5">
            <h1 class="text-center">Reschedule Class</h1>
            <form id="rescheduleForm">
                <input type="hidden" id="class_id" name="class_id" value="<?= $schedule['id'] ?>">
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control" value="<?= htmlspecialchars($schedule['subject']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" class="form-control" value="<?= htmlspecialchars($schedule['schedule_date']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="time">Time</label>
                    <input type="time" id="time" name="time" class="form-control" value="<?= htmlspecialchars($schedule['schedule_time']) ?>" required>
                </div>
                <button type="submit" class="btn btn-success btn-block">Update Schedule</button>
            </form>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.5/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
document.getElementById('rescheduleForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent the form from submitting normally

    var class_id = document.getElementById('class_id').value;
    var subject = document.getElementById('subject').value.trim();
    var schedule_date = document.getElementById('date').value.trim();
    var schedule_time = document.getElementById('time').value.trim();

    // Validate inputs
    if (!subject || !schedule_date || !schedule_time) {
        alert('All fields are required.');
        return;
    }

    // AJAX request to server
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_schedule.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    var data = `class_id=${encodeURIComponent(class_id)}&subject=${encodeURIComponent(subject)}&date=${encodeURIComponent(schedule_date)}&time=${encodeURIComponent(schedule_time)}`;

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert(xhr.responseText); // Display success message
        } else if (xhr.readyState === 4) {
            alert('Failed to reschedule class.');
        }
    };

    xhr.send(data);
});
</script>

</body>
</html>
