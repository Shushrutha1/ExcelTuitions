<?php
session_start();
include 'header.php';
include 'menu.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first.'); window.location.href='login';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Database connection (replace with your connection code)


// Fetch existing user data
$query = "SELECT * FROM student_registration WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('User not found.'); window.location.href='dashboard';</script>";
    exit;
}

$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sname = trim($_POST['sname']);
    $fname = trim($_POST['fname']);
    $phone = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $category = $_POST['category'];
    $gender = $_POST['gender'];
    $syllabus = $_POST['syllabus'];
    $subjects = isset($_POST['subject']) ? implode(",", $_POST['subject']) : '';
    $education = $_POST['education'];
    $address = trim($_POST['address']);
    $photoDestination = $user['photo']; // Default to existing photo
    $errors = [];

    // Validation
    if (empty($sname) || empty($phone) || empty($email)) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    // Handle file upload
    if (!empty($_FILES['photo']['name'])) {
        $photo = $_FILES['photo'];
        $photoName = $photo['name'];
        $photoTmpName = $photo['tmp_name'];
        $photoDestination = 'simages/' . $photoName;
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExt = strtolower(pathinfo($photoName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowed)) {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        } elseif ($photo['error'] === 0 && !move_uploaded_file($photoTmpName, $photoDestination)) {
            $errors[] = "Failed to upload photo.";
        }
    }

    // If no errors, update the database
    if (empty($errors)) {
        $updateQuery = "UPDATE student_registration SET 
            sname = ?, fname = ?, mobile = ?, email = ?, 
            category = ?, gender = ?, syllabus = ?, 
            subject = ?, education = ?, address = ?, 
            photo = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
       $stmt->bind_param(
    "sssssssssssi", 
    $sname, 
    $fname, 
    $phone, 
    $email, 
    $category, 
    $gender, 
    $syllabus, 
    $subjects,  // Corrected variable name
    $education, 
    $address, 
    $photoDestination, 
    $user_id
);


        if ($stmt->execute()) {
            echo "<script>alert('Profile updated successfully!'); window.location.href='dashboard';</script>";
        } else {
            echo "<script>alert('Failed to update profile. Please try again.');</script>";
        }
    } else {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
}
?>

<!-- Page Title -->
<section id="hero" class="hero section">
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
<div class="page-title light-background">
    <div class="container">
        <img src="assets/img/logo5.gif" alt="Logo" style="width: 10%; height: auto; display: block; margin: 0 auto;" />
        <h1 style="text-align: center; margin-top: 10px;">Student <span style="color:#3db609">Login</span> Page</h1>
        <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
            <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
                <li><a href="index" style="text-decoration: none; color: #036;">Home</a></li>
                <li style="color: #555;">Login Page</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-5">
    <form method="POST" enctype="multipart/form-data" >
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="sname">Student Name</label>
                    <input type="text" id="sname" style="background-color:#D9ECEC;" name="sname" placeholder="Enter your name" value="<?php echo htmlspecialchars($user['sname']); ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="mobile">Mobile</label>
                    <input type="text" id="mobile" style="background-color:#D9ECEC;" name="mobile" pattern="\d{10}" placeholder="Enter your mobile number" value="<?php echo htmlspecialchars($user['mobile']); ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" class="form-control" style="background-color:#D9ECEC;">
                        <option value="">Select Category</option >
                        <?php
                        $categories = ['OC', 'BC-A', 'BC-B', 'BC-C', 'BC-D', 'BC-E', 'SC', 'ST'];
                        foreach ($categories as $category) {
                            $selected = $user['category'] === $category ? 'selected' : '';
                            echo "<option value='$category' $selected>$category</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="syllabus">Syllabus</label>
                    <select id="syllabus" name="syllabus" class="form-control" style="background-color:#D9ECEC;">
                        <option value="">Select Syllabus</option>
                        <?php
                        $syllabi = ['CBSE', 'SSC', 'ICSE'];
                        foreach ($syllabi as $syllabus) {
                            $selected = $user['syllabus'] === $syllabus ? 'selected' : '';
                            echo "<option value='$syllabus' $selected>$syllabus</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="fname">Father's Name</label>
                    <input type="text" id="fname" name="fname" style="background-color:#D9ECEC;" placeholder="Enter your father's name" value="<?php echo htmlspecialchars($user['fname']); ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" style="background-color:#D9ECEC;" placeholder="Enter your email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" style="background-color:#D9ECEC;" class="form-control" required>
                        <option value="">Select</option>
                        <option value="Male" <?php echo $user['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $user['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="subjects">Subjects</label>
                    <select id="subject" name="subject[]" multiple="multiple" style="background-color:#D9ECEC;" class="form-control" required>
                        <?php
                        $availableSubjects = ['Maths', 'Physics', 'Chemistry', 'Botany', 'Zoology', 'English', 'Hindi', 'Sanskrit'];
                        $userSubjects = explode(',', $user['subject']);
                        foreach ($availableSubjects as $subject) {
                            $selected = in_array($subject, $userSubjects) ? 'selected' : '';
                            echo "<option value='$subject' $selected>$subject</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="education">Education</label>
                    <select id="education" name="education" class="form-control" style="background-color:#D9ECEC;">
                        <option value="">Select Education</option>
                        <?php
                        $educations = ['5th', '6th', '7th', '8th', '9th', '10th', 'Inter-I', 'Inter-II', 'UG-I Year', 'UG-II Year', 'UG-III Year', 'UG-IV Year'];
                        foreach ($educations as $education) {
                            $selected = $user['education'] === $education ? 'selected' : '';
                            echo "<option value='$education' $selected>$education</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3" style="background-color:#D9ECEC;" placeholder="Enter your address" class="form-control"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                
                                <div class="form-group">
                    <div id="photo-preview" style="margin-top: 10px;">
                        <img id="preview" src="<?php echo htmlspecialchars($user['photo']); ?>" alt="Image Preview" style="max-width: 200px;">
                    </div>
                </div>
                <div class="form-group">
                    <label for="photo">Photo</label>
                    <input type="file" id="photo" name="photo" style="background-color:#D9ECEC;" accept="image/*" onchange="previewImage(event)" class="form-control">
                </div></div>
<div style="text-align:center">
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 20px;">Update</button>
                </div>

        </div>
    </form>
</div>

</section>
<?php include 'footer.php'; ?>




<script>
   $(document).ready(function () {
            $('#subject').select2({
                placeholder: "Select Multiple Subjects", // Placeholder text
                allowClear: true, // Allow clearing all selected options
                width: '100%' // Ensure the dropdown fits the parent container
            });
        });
   
</script>
<script>

 // Check phone number length
    if (phone1.length !== 10) {
        document.getElementById("phone1Error").innerHTML = "Phone number must be 10 digits.";
        return false;
    }
	</script>
    <script>
	function previewImage(event) {
        const file = event.target.files[0];
        const maxSize = 1 * 1024 * 1024; // 1MB in bytes
        const preview = document.getElementById('preview');

        // Validate file size
        if (file.size > maxSize) {
            alert("Error: Image size must be less than 1MB.");
            event.target.value = ""; // Clear the input field
            preview.style.display = "none"; // Hide the preview
            return;
        }

        // Display image preview
        const reader = new FileReader();
        reader.onload = function () {
            preview.src = reader.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(file);
    }
	</script>