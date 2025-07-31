<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: Faculty-Login");
    exit;
}

include 'header.php';
include 'config.php'; // Database connection
include 'menu.php'; // Navigation menu

$message = ''; // Feedback messages
$edit_id = null; // Initialize $edit_id
$status = 1; // Default status for active records

$sresult = $conn->query("SELECT subjects FROM faculty_registration where is_active='Active' and email_verified=1");
// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$subject = isset($_POST['subjects']) ? implode(",", $_POST['subjects']) : '';

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $total_duration = trim($_POST['total_duration']);
    $time1_from = trim($_POST['time1_from']);
    $time1_to = trim($_POST['time1_to']);
    $time2_from = trim($_POST['time2_from']);
    $time2_to = trim($_POST['time2_to']);
    $time3_from = trim($_POST['time3_from']);
    $time3_to = trim($_POST['time3_to']);
    $time4_from = trim($_POST['time4_from']);
    $time4_to = trim($_POST['time4_to']);
    $faculty_id = $_SESSION['user_id'];
    $edit_id = $_POST['edit_id'] ?? null;

    if (empty($title) || empty($description)) {
        $message = "Title and Description are required.";
    } else {
        if ($edit_id) {
            // Update Record
            $stmt = $conn->prepare("UPDATE course_tab SET title = ?, description = ?, total_duration = ?, 
                time1_from = ?, time1_to = ?, time2_from = ?, time2_to = ?, 
                time3_from = ?, time3_to = ?, time4_from = ?, time4_to = ?, faculty_id = ? WHERE subject_id = ?");
            $stmt->bind_param(
                "ssissssssssi",
                $title, $description, $total_duration,
                $time1_from, $time1_to, $time2_from, $time2_to,
                $time3_from, $time3_to, $time4_from, $time4_to,
                $faculty_id, $edit_id
            );
        } else {
            // Insert Record
            $stmt = $conn->prepare("INSERT INTO course_tab (title, description, total_duration, 
                time1_from, time1_to, time2_from, time2_to, 
                time3_from, time3_to, time4_from, time4_to, faculty_id, status) VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "ssissssssssii",
                $title, $description, $total_duration,
                $time1_from, $time1_to, $time2_from, $time2_to,
                $time3_from, $time3_to, $time4_from, $time4_to,
                $faculty_id, $status
            );
        }

        if ($stmt->execute()) {
            $message = $edit_id ? "Course updated successfully." : "Course added successfully.";
            header("Location: manage_courses.php");
            exit;
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $stmt = $conn->prepare("UPDATE course_tab SET status = 0 WHERE subject_id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Course deleted successfully.";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
    header("Location: manage_courses.php");
    exit;
}

// Handle Edit
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM course_tab WHERE subject_id = $edit_id AND status = 1");
    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
    }
}
?>


 <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet">

<!-- Page Title -->
<section id="hero" class="hero section">
    <div class="container">
        <h1 style="text-align: center; margin-top: 10px; color:#F00">
            Admin <span style="color:#3db609">Panel</span>
        </h1>
        <nav class="breadcrumbs text-center mb-4">
            <a href="AdminPanel" class="btn btn-warning"><<== Back to Admin Panel</a>
        </nav>

        <?php if ($message): ?>
            <div class="alert alert-info text-center"><?= $message ?></div>
        <?php endif; ?>

        <form id="courseForm" method="post" action="manage_courses.php" class="row g-3">
            <input type="hidden" name="edit_id" value="<?= $edit_id ?? '' ?>">
			<div class="col-md-4">
             <label for="subjects" class="form-label">Teaching Subjects</label>
            <select class="form-control" id="subjects" name="subjects[]" multiple="multiple" style="background-color:#BFB" required>
          
                 <?php
        // Populate the dropdown
        while ($row = $sresult->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['subjects']) . '">' . htmlspecialchars($row['subjects']) . '</option>';
        }
        ?>
            </select>
        </div>

            <!-- Title -->
            <div class="col-md-4">
                <label for="title" class="form-label">Title:</label>
                <input type="text" id="title" name="title" class="form-control" value="<?= $course['title'] ?? '' ?>" required>
            </div>

            <!-- Description -->
            <div class="col-md-4">
                <label for="description" class="form-label">Description:</label>
                <textarea id="description" name="description" class="form-control" required><?= $course['description'] ?? '' ?></textarea>
            </div>

            <!-- Total Duration -->
            <div class="col-md-4">
                <label for="total_duration" class="form-label">Total Duration:</label>
                <input type="text" id="total_duration" name="total_duration" class="form-control" value="<?= $course['total_duration'] ?? '' ?>">
            </div>

            <!-- Time 1 -->
            <div class="col-md-4">
                <label for="time1" class="form-label">Time 1 (From-To):</label>
                <div class="d-flex">
                    <input type="time" id="time1_from" name="time1_from" class="form-control" value="<?= $course['time1_from'] ?? '' ?>" required>
                    <input type="time" id="time1_to" name="time1_to" class="form-control ms-2" value="<?= $course['time1_to'] ?? '' ?>" required>
                </div>
            </div>

            <!-- Time 2 -->
            <div class="col-md-4">
                <label for="time2" class="form-label">Time 2 (From-To):</label>
                <div class="d-flex">
                    <input type="time" id="time2_from" name="time2_from" class="form-control" value="<?= $course['time2_from'] ?? '' ?>">
                    <input type="time" id="time2_to" name="time2_to" class="form-control ms-2" value="<?= $course['time2_to'] ?? '' ?>">
                </div>
            </div>

            <!-- Time 3 -->
            <div class="col-md-4">
                <label for="time3" class="form-label">Time 3 (From-To):</label>
                <div class="d-flex">
                    <input type="time" id="time3_from" name="time3_from" class="form-control" value="<?= $course['time3_from'] ?? '' ?>">
                    <input type="time" id="time3_to" name="time3_to" class="form-control ms-2" value="<?= $course['time3_to'] ?? '' ?>">
                </div>
            </div>

            <!-- Time 4 -->
            <div class="col-md-4">
                <label for="time4" class="form-label">Time 4 (From-To):</label>
                <div class="d-flex">
                    <input type="time" id="time4_from" name="time4_from" class="form-control" value="<?= $course['time4_from'] ?? '' ?>">
                    <input type="time" id="time4_to" name="time4_to" class="form-control ms-2" value="<?= $course['time4_to'] ?? '' ?>">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</section>

<script>
    // Add event listeners to the time fields
    document.getElementById("time1_from").addEventListener("change", validateTime1);
    document.getElementById("time1_to").addEventListener("change", validateTime1);

    function validateTime1() {
        const time1From = document.getElementById("time1_from").value;
        const time1To = document.getElementById("time1_to").value;

        // Validate the time dynamically
        if (time1From && time1To && time1From >= time1To) {
            alert("Time 1: 'From' time must be enter grater than 'To' time.");
            document.getElementById("time1_to").value = ""; // Clear the invalid value
        }
    }
	</script>
    <script>
	
	    // Add event listeners to the time fields
    document.getElementById("time2_from").addEventListener("change", validatetime2);
    document.getElementById("time2_to").addEventListener("change", validatetime2);

    function validatetime2() {
        const time2From = document.getElementById("time2_from").value;
        const time2To = document.getElementById("time2_to").value;

        // Validate the time dynamically
        if (time2From && time2To && time2From >= time2To) {
            alert("Time 1: 'From' time must be enter grater than 'To' time.");
            document.getElementById("time2_to").value = ""; // Clear the invalid value
        }
    }
		</script>
    <script>
	    // Add event listeners to the time fields
    document.getElementById("time3_from").addEventListener("change", validatetime3);
    document.getElementById("time3_to").addEventListener("change", validatetime3);

    function validatetime3() {
        const time3From = document.getElementById("time3_from").value;
        const time3To = document.getElementById("time3_to").value;

        // Validate the time dynamically
        if (time3From && time3To && time3From >= time3To) {
            alert("Time 1: 'From' time must be enter grater than 'To' time.");
            document.getElementById("time3_to").value = ""; // Clear the invalid value
        }
    }
		</script>
    <script>
	
	    // Add event listeners to the time fields
    document.getElementById("time4_from").addEventListener("change", validatetime4);
    document.getElementById("time4_to").addEventListener("change", validatetime4);

    function validatetime4() {
        const time4From = document.getElementById("time4_from").value;
        const time4To = document.getElementById("time4_to").value;

        // Validate the time dynamically
        if (time4From && time4To && time4From >= time4To) {
            alert("Time 1: 'From' time must be enter grater than 'To' time.");
            document.getElementById("time4_to").value = ""; // Clear the invalid value
        }
    }
</script>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#courseTable').DataTable();
});
</script>
    
    
    <?php
include'footer.php';
?>