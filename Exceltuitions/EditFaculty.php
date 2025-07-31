<?php
session_start();
// Include database connection and start session
include 'header.php';
include'menu.php';

$sresult = $conn->query("SELECT subject_name FROM subjects WHERE status=1 ORDER BY subject_name ASC");
if (!$sresult) {
    die("Query failed: " . $conn->error);
}

$data = [];
while ($row = $sresult->fetch_assoc()) {
    $data[] = trim($row['subject_name']);	
}



// Fetch faculty details from session or database
$faculty_id = $_SESSION['user_id'];
$query = "SELECT * FROM faculty_registration WHERE id = ? AND is_active = 'Active'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$faculty = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tname = htmlspecialchars($_POST['tname']);
    $tfname = htmlspecialchars($_POST['tfname']);
	$gender = htmlspecialchars($_POST['gender']);
    $designation = htmlspecialchars($_POST['designation']);
    $qualification = htmlspecialchars($_POST['qualification']);
	$subjects = htmlspecialchars($_POST['subjects']);
    $email = htmlspecialchars($_POST['email']);
    $phone1 = htmlspecialchars($_POST['phone1']);
    $phone2 = htmlspecialchars($_POST['phone2']);
    $address = htmlspecialchars($_POST['address']);

    // Update query
    $update_query = "UPDATE faculty SET tname = ?, tfname = ?, gender = ?, designation = ?, qualification = ?, email = ?, phone1 = ?, phone2 = ?, address = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssssssssi", $tname, $tfname, $designation, $qualification, $email, $phone1, $phone2, $address, $faculty_id);

    if ($update_stmt->execute()) {
        $success_message = "Profile updated successfully.";
    } else {
        $error_message = "Failed to update profile.";
    }
}
 $selectedSubjects = explode(',', $faculty['subjects']); // Assume comma-separated string
?>

    <?php if (!empty($success_message)) { echo "<div class='alert alert-success'>$success_message</div>"; } ?>
    <?php if (!empty($error_message)) { echo "<div class='alert alert-danger'>$error_message</div>"; } ?>

<!-- Page Title -->
<section id="hero" class="hero section">
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <div class="page-title light-background">
            <div class="container">
               <img src="assets/img/logo5.gif"  alt="Logo" style="width: 10%; height: auto; display: block; margin: 0 auto;" />
                <h1 style="text-align: center; margin-top: 10px; color:#F00">Faculty <span style="color:#3db609">Profile</span></h1>
                <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
                    <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
                        <li><a href="TDashboard" style="text-decoration: none; color: #036;" class="btn btn-warning"> <<==Back to Admin Panel</a></li>
                    </ol>
                </nav>
            </div>
            <div class="container mt-5">
                <h1 class="text-center">Edit Profile </h1>
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="card" style="background-color: transparent;">
                            <div class="card-body" style="text-align:left" >
    <form method="POST" action="EditFaculty.php">
    <ul class="list-group" >
        <div class="form-group">
            <label for="tname">Faculty Name</label>
            <input type="text" class="form-control" id="tname" name="tname" value="<?= htmlspecialchars($faculty['tname']) ?>" required>
        </div>
        <div class="form-group">
            <label for="tfname">Father's Name</label>
            <input type="text" class="form-control" id="tfname" name="tfname" value="<?= htmlspecialchars($faculty['tfname']) ?>" required>
        </div>
        <div class="form-group">
         <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                            	 <option value="">Select</option>
                        <option value="Male" <?php echo $faculty['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $faculty['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                            </select>
                            </div>
                            <div class="form-group">
            <label for="designation">Designation</label>
            <select class="form-select" id="designation" name="designation"  required>            					
                                <option value="">Select Designation</option>
                                 <?php
                        $fdesignations = ['Assistant Professor', 'Associate Professor', 'Professor', 'Lecturer', 'Teacher'];
                        foreach ($fdesignations as $designation) {
                            $selected = $faculty['designation'] === $designation ? 'selected' : '';
                            echo "<option value='$designation' $selected>$designation</option>";
                        }
                        ?>
                                
                            </select>

        </div>
        <div class="form-group">
            <label for="qualification">Qualification</label>
            <select class="form-select" id="qualification" name="qualification"  required>

                                <option value="">Select Qualification</option> 
                                 <?php
                        $fqualifications = ['PhD', 'MPhil', 'Postgraduate', 'Graduate', 'Diploma'];
                        foreach ($fqualifications as $qualification) {
                            $selected = $faculty['qualification'] === $qualification ? 'selected' : '';
                            echo "<option value='$qualification' $selected>$qualification</option>";
                        }
                        ?>                               
                                
                            </select>

        </div>
        <div class="form-control">
            <label for="subjects" class="form-label">Teaching Subjects</label>
          <select id="subjects" name="subjects[]" multiple="multiple" style="background-color:#D9ECEC;" class="form-control" required>
				<?php
                    // Assuming $data is an array of subject names
                   $facultySubjects = array_map('trim', explode(',', $faculty['subjects'])); // Normalize data
            
            foreach ($data as $subject) {
                $isSelected = in_array($subject, $facultySubjects) ? 'selected' : ''; 
                echo "<option value='" . htmlspecialchars($subject) . "' $isSelected>" . htmlspecialchars($subject) . "</option>";
            }
                ?>
            </select>



        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($faculty['email']) ?>" required>
        </div>
        <div class="form-group">
            <label for="phone1">Phone 1</label>
            <input type="text" class="form-control" id="phone1" name="phone1" value="<?= htmlspecialchars($faculty['phone1']) ?>" required>
        </div>
        <div class="form-group">
            <label for="phone2">Phone 2</label>
            <input type="text" class="form-control" id="phone2" name="phone2" value="<?= htmlspecialchars($faculty['phone2']) ?>">
        </div>
         </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="card"  style="background-color: transparent;">
                            <div class="card-body">
        <div class="form-group">
            <label for="address">Address</label>
            <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($faculty['address']) ?></textarea>
        </div>
        </div>
        </div></div>
        <div class="form-control" style="text-align:center">
        </form>
        <button type="submit" class="btn btn-primary">Update</button>

        <a href="TDashboard.php" class="btn btn-warning">Cancel</a>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        $('#subjects').select2({
            placeholder: "Select Multiple Subjects", // Placeholder text
            allowClear: true, // Allow clearing all selected options
            width: '100%' // Ensure the dropdown fits the parent container
        });
    });
</script>
