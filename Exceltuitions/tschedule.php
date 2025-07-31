<?php
session_start();

// Check if faculty is logged in
if (!isset($_SESSION['uname']) || empty($_SESSION['uname'])) {
    header("location: Faculty-Login");
    exit;
}

// Include required files
include 'header.php';
include 'menu.php';
include 'config.php'; // Database connection

// Fetch faculty details
$faculty_id = $_SESSION['user_id']; // Ensure the session variable matches the correct key

$query = "SELECT * FROM faculty_registration WHERE id = ? AND is_active = 'Active'";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Failed to prepare query: " . $conn->error);
}

$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $faculty = $result->fetch_assoc();
    $subjects = explode(',', $faculty['subjects']); // Parse subjects
} else {
    die("Faculty record not found or inactive.");
}

$stmt->close();
?>

<!-- Custom CSS -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
    }

    .schedule-form {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .form-group label {
        font-weight: bold;
    }

    .btn-schedule {
        background-color: #28a745;
        color: #ffffff;
    }

    .btn-schedule:hover {
        background-color: #218838;
    }

    .flatpickr-wrapper {
        display: block;
    }
</style>

<!-- Page Title -->
<section id="hero" class="hero section">
    <div class="container mt-5"><div class="container">
               <img src="assets/img/logo5.gif"  alt="Logo" style="width: 10%; height: auto; display: block; margin: 0 auto;" />
                <h2 class="text-center mb-4" style="color:#F00">Schedule Your <span style="color:#3db609">Classes</span></h2>
                <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
                 <h4 style="color:#039">Welcome, <span style="color:#093"> <?= htmlspecialchars($faculty['tname']) ?></span></h4>
                                <hr>
                   <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
                        <li><a href="TDashboard"  style="text-decoration: none; color: #036;" class="btn btn-warning"> <<==Back to Admin Panel</a></li>
                    </ol>
                </nav>
            </div>
       
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card" style="background-color: transparent;">
                    <div class="card-body">
                       
                        <form method="POST" action="save_schedule.php" id="scheduleForm">
                            <div class="form-group mb-3">
                                <label for="subject" style="font-weight:bold;">Select Subject:</label>
                                <select id="subject" name="subject" class="form-control" required>
                                    <option value="" disabled selected>Select a subject</option>
                                    <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= htmlspecialchars(trim($subject)) ?>"><?= htmlspecialchars(trim($subject)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="date" style="font-weight:bold;">Select Date:</label>
                                <input type="date" id="date" name="date" class="form-control" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="time" style="font-weight:bold;">Select Time:</label>
                                <input type="time" id="time" name="time" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-success btn-block">Schedule Class</button>
                             
                     <button>  <a href="view_schedule.php" class="btn btn-info">View Schedules</a></button>
                   
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
    document.getElementById('scheduleForm').addEventListener('submit', function(event) {
        const dateInput = document.getElementById('date');
        const timeInput = document.getElementById('time');

        const selectedDate = new Date(dateInput.value + 'T' + timeInput.value);
        const now = new Date();

        if (selectedDate < now ) {
            alert('You cannot schedule a class in the past. Please select a valid date and time.');
            event.preventDefault();
        }
		else
		{
			alert('Scheduled successfully');
		}
    });

    // Set minimum date for the date input to today
    document.getElementById('date').setAttribute('min', new Date().toISOString().split('T')[0]);
</script>
