<?php
ob_start(); // Start output buffering
session_start();
if(!isset($_SESSION['uid']) || empty($_SESSION['uid'])){
  header("location:AdminPanel");
  exit;
}
?>

<?php
include 'header.php';
include 'config.php';
include 'menu.php';

$subject_name = '';
$edit_id = null; // Initialize $edit_id to avoid undefined variable error
$message = '';   // Initialize $message for displaying success/error messages

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_name = trim($_POST['subject_name']);
    $edit_id = $_POST['edit_id'] ?? null;

    if (empty($subject_name)) {
        $message = "Subject name cannot be empty.";
    } else {
        // Check for duplicate entry
        $stmt = $conn->prepare("SELECT id FROM subjects WHERE subject_name = ? AND status = 1" . ($edit_id ? " AND id != ?" : ""));
        if ($edit_id) {
            $stmt->bind_param("si", $subject_name, $edit_id);
        } else {
            $stmt->bind_param("s", $subject_name);
        }
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            //$message = "Duplicate entry: Subject name already exists.";
			echo '<script>alert("Duplicate entry: Subject name already exists.");</script>';
        } else {
            // Insert or Update record
            if ($edit_id) {
                // Update existing record
                $sts = 1;
                $stmt = $conn->prepare("UPDATE subjects SET subject_name = ? WHERE id = ? AND status = ?");
                $stmt->bind_param("sis", $subject_name, $edit_id, $sts);
            } else {
                // Insert new record
                $stmt = $conn->prepare("INSERT INTO subjects (subject_name, status) VALUES (?, 1)");
                $stmt->bind_param("s", $subject_name);
            }

            if ($stmt->execute()) {
                $message = $edit_id ? "Subject updated successfully." : "Subject added successfully.";
                header("Location: Subject"); // Redirect to prevent form resubmission
                exit;
            } else {
                $message = "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $stmt = $conn->prepare("UPDATE subjects SET status = 0 WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Subject deleted successfully.";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
    header("Location: Subject");
    exit;
}

// Handle Edit
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM subjects WHERE id = $edit_id AND status = 1");
    $subject = $result->fetch_assoc();
    $subject_name = $subject['subject_name'];
}

?>


<!-- Page Title -->
<section id="hero" class="hero section">
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
<div class="page-title light-background">
    <div class="container">
        <img src="assets/img/logo5.gif" alt="Logo" style="width: 10%; height: auto; display: block; margin: 0 auto;" />
        <h1 style="text-align: center; margin-top: 10px; color:#F00">Admin <span style="color:#3db609">Panel</span> </h1>
        <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
            <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
                <li><a href="AdminPanel" style="text-decoration: none; color: #036;" class="btn btn-warning"> <<==Back to Admin Panel</a></li>

            </ol>
        </nav>
    </div>
    <!--  Page Title  -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">



    <div class="container mt-5">
    <h1 class="text-center">Subject Management</h1>

   <!-- Add/Edit Form -->
<form method="POST" action="">
    <input type="hidden" name="edit_id" value="<?= htmlspecialchars($edit_id ?? '') ?>">

    <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
        <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
            <label for="subjectName" class="form-label">Subject_Name</label>
            <input type="text" name="subject_name" class="form-control" placeholder="Enter Subject Name" 
                   value="<?= htmlspecialchars($subject_name ?? '') ?>" required>
            <button type="submit" class="btn btn-primary"><?= $edit_id ? 'Update' : 'Add' ?>_Subject</button>
        </ol>
    </nav>
</form>


    <!-- Success/Error Message -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-info mt-3"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Subjects Table -->
    <table id="subjectsTable" class="table table-bordered mt-4">
    <thead>
        <tr>
            <th>ID</th>
            <th>Subject Name</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Initialize the counter outside the loop
        $i = 1;

        $result = $conn->query("SELECT * FROM subjects WHERE status = 1 ORDER BY id DESC");
        while ($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?= $i; ?></td> <!-- Display the serial number -->
                <td><?= htmlspecialchars($row['subject_name']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" 
                       onclick="return confirm('Are you sure you want to delete this subject?');">Delete</a>
                </td>
            </tr>
        <?php
            // Increment the counter after each iteration
            $i++;
        endwhile;
        ?>
    </tbody>
</table>


</div>
</section>
<?php
include'footer.php';
?>
 <!-- Include jQuery and DataTables JS -->
 <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#subjectsTable').DataTable({
        dom: 'Bfrltip',
        responsive: true, // Adds responsive behavior
        scrollX: true,    // Allows horizontal scrolling
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]     // Adds horizontal scrolling for large tables
    });
});