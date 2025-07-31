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

// Fetch faculty schedule in DESCENDING ORDER (latest first)
$faculty_id = $_SESSION['user_id'];

$query = "SELECT id, subject, schedule_date, schedule_time, created_at 
          FROM faculty_schedule 
          WHERE faculty_id = ? 
          ORDER BY schedule_date DESC, schedule_time DESC";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Failed to prepare query: " . $conn->error);
}

$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

$current_datetime = date("Y-m-d H:i:s"); // Current date and time
?>

<!-- Page Title -->
<section id="hero" class="hero section">
    <div class="page-title light-background">
        <div class="container">
            <img src="assets/img/logo5.gif" alt="Logo" style="width: 10%; height: auto; display: block; margin: 0 auto;" />
            <h1 style="text-align: center; margin-top: 10px; color:#F00">Faculty <span style="color:#3db609">Schedule</span></h1>
            <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
                <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
                    <li><a href="TDashboard" style="text-decoration: none; color: #036;" class="btn btn-warning"> <<==Back to Dashboard</a></li>
                </ol>
            </nav>
        </div>

        <div class="container mt-5">
            <h1 class="text-center">My Scheduled Classes</h1>
            <div class="card" style="background-color: transparent;">
                <div class="card-body">
                    <?php if ($result->num_rows > 0): ?>
                        <table class="table table-striped table-bordered">
                           <thead>
                                <tr style="background-color: #3db609; color: white;">
                                    <th>S.No</th> <!-- Serial Number -->
                                   
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Scheduled At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $serial_no = 1; // Initialize serial number counter
                                while ($row = $result->fetch_assoc()): 
                                    $schedule_datetime = $row['schedule_date'] . " " . $row['schedule_time']; 
                                    $is_past_meeting = ($schedule_datetime < $current_datetime); // Check if date is past
                                ?>
                                    <tr style="<?= $is_past_meeting ? 'background-color: #f3f3f3; color: #999;' : '' ?>">
                                        <td><?= $serial_no++ ?></td> <!-- Serial number -->
                                    
                                        <td><?= htmlspecialchars($row['subject']) ?></td>
                                        <td><?= htmlspecialchars(date("d M Y", strtotime($row['schedule_date']))) ?></td>
                                        <td><?= htmlspecialchars(date("h:i A", strtotime($row['schedule_time']))) ?></td>
                                        <td><?= htmlspecialchars(date("d M Y, h:i A", strtotime($row['created_at']))) ?></td>
                                        <td>
                                            <?php if (!$is_past_meeting): ?> 
                                                <!-- Edit and Delete actions only for upcoming meetings -->
                                                <a href="edit_schedule.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                                                <a href="delete_schedule.php?id=<?= $row['id'] ?>" class="btn btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this schedule?');">Delete</a>
                                            <?php else: ?>
                                                <!-- Readonly if the meeting is in the past -->
                                             <span class="badge bg-success text-white">Completed</span>

                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center" style="color: #f00;">No scheduled classes found.</p>
                    <?php endif; ?>
                    <div class="text-center mt-4">
                        <a href="tschedule.php" class="btn btn-info">Add New Schedule</a>
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
