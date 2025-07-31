<?php

include 'header.php';
include 'menu.php';

// Fetch subjects from the database
$result = $conn->query("SELECT subject_name FROM subjects where status=1 ORDER BY subject_name ASC");

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Include database connection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tname = htmlspecialchars(trim($_POST['tname']));
    $tfname = htmlspecialchars(trim($_POST['tfname']));
    $designation = htmlspecialchars(trim($_POST['designation']));
    $qualification = htmlspecialchars(trim($_POST['qualification']));
    $gender = htmlspecialchars(trim($_POST['gender']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $phone1 = htmlspecialchars(trim($_POST['phone1']));
    $phone2 = htmlspecialchars(trim($_POST['phone2']));
    $subjects = implode(", ", $_POST['subjects']); // Convert array to string
    $address = htmlspecialchars(trim($_POST['address']));
    $aadharno = htmlspecialchars(trim($_POST['aadharno']));
    $token = bin2hex(random_bytes(16)); // Generate a unique token for email verification

    // Handle photo upload
    $photo = $_FILES['photo'];
    $photoName = $photo['name'];
    $photoTmpName = $photo['tmp_name'];
    $photoDestination = 'timages/' . uniqid() . "_" . basename($photoName);

    if (move_uploaded_file($photoTmpName, $photoDestination)) {
        // Insert data into the database
        $query = "INSERT INTO faculty_registration 
          (tname, tfname, designation, qualification, gender, email, password, phone1, phone2, subjects, photo, address, aadharno, token) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);

        if (!$stmt) {
            die("Failed to prepare query: " . $conn->error);
        }

        $stmt->bind_param(
            "ssssssssssssss", 
            $tname, 
            $tfname, 
            $designation, 
            $qualification, 
            $gender, 
            $email, 
            $password, 
            $phone1, 
            $phone2, 
            $subjects, 
            $photoDestination, 
            $address, 
            $aadharno, 
            $token
        );

        if ($stmt->execute()) {
            // Send email verification
            $verifyLink = "http://exceltuitions.com/verify.php?token=$token";
            $subject = "Email Verification";
            $message = "Hi $tname,\n\nPlease click the link below to verify your email address:\n$verifyLink\n\nThank you.";
            $headers = "From: no-reply@exceltuitions.com";

            if (mail($email, $subject, $message, $headers)) {
                echo '<script>
                        alert("Registration successful! Please check your email to activate your account.");
                        window.location.href = "index"; // Redirect to homepage
                      </script>';
            } else {
                echo '<script>
                        alert("Failed to send verification email. Please try again later.");
                        window.location.href = "Faculty-Registration"; // Redirect back to form
                      </script>';
            }
        } else {
            echo '<script>
                    alert("Database error: "' . addslashes($stmt->error) . '");
                    window.location.href = "Faculty-Registration"; // Redirect back to form
                  </script>';
        }
        $stmt->close();
    } else {
        echo '<script>
                alert("Failed to upload photo. Please try again.");
                window.location.href = "Faculty-Registration"; // Redirect back to form
              </script>';
    }
}

?>


<style>

    .error {
        color: red;
        font-size: 0.875rem;
    }

    /* Three-column layout */
    .form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .form-column {
        flex: 1;
        min-width: 250px;
    }

    @media (max-width: 768px) {
        .form-column {
            flex: 1 1 100%; /* Stack the columns on smaller screens */
        }
    }

    /* Style for image preview */
    #photoPreview {
        margin-top: 10px;
        max-width: 200px;
        max-height: 200px;
        display: none;
    }
	
</style>

<section id="hero" class="hero section">
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container">
        <h1>Faculty Registration Page</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index">Home</a></li>
            <li class="current">Faculty Registration</li>
          </ol>
        </nav>
      </div>
        <div class="container mt-5">

            <form id="facultyForm" enctype="multipart/form-data" method="POST" style="text-align:left; color:#039">
                <div class="form-row">
                    <!-- Column 1 -->
                    <div class="form-column">
                        <div class="mb-3">
                            <label for="tname" class="form-label">Teacher Name</label>
                            <input type="text" class="form-control" id="tname" name="tname" style="background-color:#BFB" placeholder="Enter Faculty Name" required>
                        </div>

                        <div class="mb-3">
                            <label for="tfname" class="form-label">Teacher's Father Name</label>
                            <input type="text" class="form-control" id="tfname" name="tfname" placeholder="Enter Faculty Father Name" style="background-color:#BFB" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" style="background-color:#BFB" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="designation" class="form-label">Designation</label>
                            <select class="form-select" id="designation" name="designation" style="background-color:#BFB" required>
                                <option value="">Select Designation</option>
                                <option value="Assistant Professor">Assistant Professor</option>
                                <option value="Associate Professor">Associate Professor</option>
                                <option value="Professor">Professor</option>
                                <option value="Lecturer">Lecturer</option>
                                <option value="Teacher">Teacher</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="qualification" class="form-label">Qualification</label>
                            <select class="form-select" id="qualification" name="qualification" style="background-color:#BFB" required>
                                <option value="">Select Qualification</option>
                                <option value="PhD">PhD</option>
                                <option value="MPhil">MPhil</option>
                                <option value="Postgraduate">Postgraduate</option>
                                <option value="Graduate">Graduate</option>
                                <option value="Diploma">Diploma</option>
                            </select>
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div class="form-column">
                        

                        <div class="mb-3">
                            <label for="email" class="form-label">E-Mmail - ID</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter a valid E-Mail ID" style="background-color:#BFB" required>
                            <div class="error" id="emailError"></div>
                        </div>
                        <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                <input type="password" id="password" class="form-control" name="password" placeholder="Enter your password" style="background-color:#BFB" required>
                </div>

                        <div class="mb-3">
                            <label for="phone1" class="form-label">Phone 1</label>
                            <input type="text" class="form-control" id="phone1" name="phone1" pattern="\d{10}" placeholder="Enter Phone Number" style="background-color:#BFB" required>
                            <div class="error" id="phone1Error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="phone2" class="form-label">Phone 2</label>
                            <input type="text" class="form-control" id="phone2" name="phone2" pattern="\d{10}" style="background-color:#BFB" placeholder="Enter Alternate Phone Number">
                        </div>

                       <div class="mb-3">
            <label for="subjects" class="form-label">Teaching Subjects</label>
            <select class="form-control" id="subjects" name="subjects[]" multiple="multiple" style="background-color:#BFB" required>
           <!--     <option value="Maths">Maths</option>
                <option value="Physics">Physics</option>
                <option value="Chemistry">Chemistry</option>
                <option value="Botany">Botany</option>
                <option value="Zoology">Zoology</option>
                <option value="English">English</option>
                <option value="Hindi">Hindi</option>
                <option value="Sanskrit">Sanskrit</option>-->
                 <?php
        // Populate the dropdown
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['subject_name']) . '">' . htmlspecialchars($row['subject_name']) . '</option>';
        }
        ?>
            </select>
        </div>
                    </div>
                    <!-- Column 3 -->
                    <div class="form-column">
                        <div class="mb-3">
                            <label for="photo" class="form-label">Photo (Less than 1MB)</label>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*" style="background-color:#BFB" onchange="previewImage(event)" required>
                            <img id="photoPreview" src="" alt="Image Preview" />
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" style="background-color:#BFB" placeholder="Enter Address" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="aadharno" class="form-label">Aadhar Number</label>
                            <input type="text" class="form-control" id="aadharno" name="aadharno" pattern="\d{12}" style="background-color:#BFB" placeholder="Enter Faculty Aadhar No" required>
                        </div>

                        <button type="submit" class="btn btn-primary" >Register</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
include 'footer.php';
?>

<script>
    // Image preview functionality
    document.getElementById('photo').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const preview = document.getElementById('photoPreview');
        
        // Check if file is selected and is an image
        if (file && file.type.startsWith('image')) {
            const reader = new FileReader();
            reader.onload = function () {
                preview.src = reader.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
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
<script>
   $(document).ready(function () {
            $('#subjects').select2({
                placeholder: "Select Multiple Subjects", // Placeholder text
                allowClear: true, // Allow clearing all selected options
                width: '100%' // Ensure the dropdown fits the parent container
            });
        });
</script>


