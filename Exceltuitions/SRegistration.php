<?php
include("config.php");
// Fetch subjects from the database
$result = $conn->query("SELECT subject_name FROM subjects where status=1 ORDER BY subject_name ASC");

if (!$result) {
    die("Query failed: " . $conn->error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = trim($_POST['uname']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $sname = trim($_POST['sname']);
    $fname = trim($_POST['fname']);
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $category = trim($_POST['category']);
    $education = trim($_POST['education']);
    $address = trim($_POST['address']);
    $gender = trim($_POST['gender']);
    $syllabus = trim($_POST['syllabus']);
    $subject = isset($_POST['subjects']) ? implode(",", $_POST['subjects']) : '';
    $token = bin2hex(random_bytes(16)); // Generate unique token
    $photo = $_FILES['photo'];

    // Check if `uname` already exists
    $checkSql = "SELECT id FROM student_registration WHERE uname = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $uname);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script>alert('Username already exists. Please choose a different username.');</script>";
        $checkStmt->close();
    } else {
        $checkStmt->close();

        // Photo upload logic
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['photo']['type'], $allowed_types)) {
                echo "<script>alert('Error: Only JPG, PNG, or GIF files are allowed.');</script>";
            } elseif ($_FILES['photo']['size'] > 1 * 1024 * 1024) {
                echo "<script>alert('Error: Image size must be less than 1MB.');</script>";
            } else {
                $target_dir = "simages/";
                $photo_name = basename($_FILES['photo']['name']);
                $target_file = $target_dir . uniqid() . "_" . $photo_name;

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                    $photo = $target_file;
                } else {
                    echo "<script>alert('Error uploading photo.');</script>";
                }
            }
        } else {
            echo "<script>alert('No photo uploaded or there was an error.');</script>";
        }

        // Check if required fields are not empty
        if ($education != '' && $address != '' && $photo != '') {
            $sql = "INSERT INTO student_registration (uname, password, sname, fname, mobile, email, category, education, syllabus, subject, address, gender, photo, token, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Inactive', NOW())";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssssssss", $uname, $password, $sname, $fname, $mobile, $email, $category, $education, $syllabus, $subject, $address, $gender, $photo, $token);

            // Execute the statement
            if ($stmt->execute()) {
                $activation_link = "http://exceltuitions.com/activate.php?token=$token";
                $subject = "Exceltuitions Account Activation";
                $message = "Hi $sname,\n\nPlease click the link below to activate your account:\n$activation_link\n\nThank you!";
                $headers = "From: no-reply@exceltuitions.com";

                if (mail($email, $subject, $message, $headers)) {
                    echo '<script>alert("Registration successful! Please check your email to activate your account."); window.location.href = "https://exceltuitions.com";</script>';
                } else {
                    echo '<script>alert("Registration successful, but failed to send the activation email.");</script>';
                }
            } else {
                echo "Error: " . $conn->error;
            }

            $stmt->close();
        } else {
            echo "<script>alert('Please fill in all required fields and upload a photo.');</script>";
        }
    }
}



?>


<?php
include("header.php");
include("menu.php");
?>
 <style>
        .error {
            color: red;
            font-size: 14px;
        }
        .success {
            color: green;
            font-size: 14px;
        }
    </style>

    <section id="hero" class="hero section">

        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
<link href="newstyle.css" rel="stylesheet">
  <link href="modalstyle.css" rel="stylesheet">
<!-- Page Title -->
    <div class="page-title light-background">
      <div class="container">
        <h1>Student Registration</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index">Home</a></li>
            <li class="current">Student Registration</li>
          </ol>
        </nav>
      </div>
      <form method="POST" enctype="multipart/form-data" style="color:#036">

       
        <div class="form-container">
            

            <div class="form-group">
                <label for="sname" style="text-align:left">Student Name</label>
                <input type="text" id="sname" name="sname" placeholder="Enter your name" required>
            </div>

            <div class="form-group">
                <label for="fname" style="text-align:left">Father's Name</label>
                <input type="text" id="fname" name="fname" placeholder="Enter your father's name" required>
            </div>

            <div class="form-group">
                <label for="mobile" style="text-align:left">Mobile</label>
                <input type="text" id="mobile" name="mobile" pattern="\d{10}" placeholder="Enter your mobile number" required>
            </div>

            <div class="form-group" style="text-align:left">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="category" style="text-align:left">Category</label>
                <!-- <input type="text" id="category" name="category" placeholder="Enter your category"> -->
                <select id="category" name="category" >
                <option value="">Select Category</option>
                <option value="OC">OC</option>
                <option value="BC-A">BC-A</option>
                <option value="BC-B">BC-B</option>
                <option value="BC-C">BC-C</option>
                <option value="BC-D">BC-D</option>
                <option value="BC-E">BC-E</option>
                <option value="SC">SC</option>
                <option value="ST">ST</option>
                </select>
            </div>
            
              <div class="form-group">
                <label for="gender" style="text-align:left">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="">Select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
             <div class="form-group">
                <label for="category" style="text-align:left">Syllabus</label>
                <!-- <input type="text" id="category" name="category" placeholder="Enter your category"> -->
                <select id="syllabus" name="syllabus" >
                <option value="">Select Syllabus</option>
                <option value="CBSE">CBSE</option>
                <option value="SSC">SSC</option>
                <option value="ICSE">ICSE</option>
               
                </select>
            </div>
              <div class="form-group">
                <label for="category" style="text-align:left">Subject</label>
                <!-- <input type="text" id="category" name="category" placeholder="Enter your category"> -->
               <select class="form-control" id="subjects" name="subjects[]" multiple="multiple" style="background-color:#BFB" required>
               <!--  <option value="Maths">Maths</option>
                <option value="Physics">Physics</option>
                <option value="Chemistry">Chemistry</option>
                <option value="Botany">Botany</option>
                <option value="Zoology">Zoology</option>
                <option value="English">English</option>
                <option value="Hindi">Hindi</option>
                <option value="Sanskrit">Sanskrit</option> -->
                 <?php
        // Populate the dropdown
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['subject_name']) . '">' . htmlspecialchars($row['subject_name']) . '</option>';
        }
        ?>
            </select>
            </div>

            <div class="form-group">
                <label for="education" style="text-align:left">Education</label>
                <!-- <input type="text" id="education" name="education" placeholder="Enter your education"> -->
                <select id="education" name="education">
                <option value="">Select Education</option>
                <option value="5th">5th Class</option>
                <option value="6th">6th Class</option>
                <option value="7th">7th Class</option>
                <option value="8th">8th Class</option>
                <option value="9th">9th Class</option>
                <option value="10th">10th Class</option>
                <option value="Inter-I">Inter-I Year</option>
                <option value="Inter-II">Inter-II Year</option>
                <option value="UG-I Year">Degree/UG/B-Tech-I Year</option>
                <option value="UG-II Year">Degree/UG/B-Tech-II Year</option>
                <option value="UG-III Year">Degree/UG/B-Tech-III Year</option>
                <option value="UG-IV Year">Degree/UG/B-Tech-IV Year</option>

                </select>
                
            </div>

            <div class="form-group">
                <label for="address" style="text-align:left">Address</label>
                <textarea id="address" name="address" rows="3" placeholder="Enter your address"></textarea>
            </div>
			<div class="form-group">
                <label for="uname" style="text-align:left">User ID</label>
                <input type="text" id="uname" name="uname" placeholder="Enter your User ID" required>
                <div id="uname-message"></div>
            </div>

            <div class="form-group">
                <label for="password" style="text-align:left">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

    <label for="photo" style="text-align:left">Photo</label>
    <input type="file" id="photo" name="photo" accept="image/*" onchange="previewImage(event)">

</div>
<div class="form-group">
    <div id="photo-preview" style="margin-top: 10px;">
        <img id="preview" src="#" alt="Image Preview" style="max-width: 200px; display: none;">
    </div>
</div>

<button type="submit" class="btn" style="width:20%; height:50px;">Register</button>
</form>

</div></div></section>
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
	
	
	document.getElementById("facultyForm").onsubmit = function() {
    var email = document.getElementById("email").value;
    var phone1 = document.getElementById("phone1").value;
    var aadharno = document.getElementById("aadharno").value;

    // Example: Check email validity
    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    if (!email.match(emailPattern)) {
        document.getElementById("emailError").innerHTML = "Please enter a valid email.";
        return false;  // Prevent form submission
    }

    // Check phone number length
    if (phone1.length !== 10) {
        document.getElementById("phone1Error").innerHTML = "Phone number must be 10 digits.";
        return false;
    }

    // Check Aadhar number length
    if (aadharno.length !== 12) {
        document.getElementById("aadharnoError").innerHTML = "Aadhar number must be 12 digits.";
        return false;
    }

    return true;
};



	
</script>


<?php
include("footer.php");
?>
<script>
   $(document).ready(function () {
            $('#subjects').select2({
                placeholder: "Select Multiple Subjects", // Placeholder text
                allowClear: true, // Allow clearing all selected options
                width: '100%' // Ensure the dropdown fits the parent container
            });
        });
   
</script>
<script>
        $(document).ready(function() {
            $('#uname').on('input', function() {
                const uname = $(this).val().trim();

                if (uname.length > 0) {
                    $.ajax({
                        url: 'check_uname.php',
                        type: 'POST',
                        data: { uname: uname },
                        dataType: 'json',
                        success: function(response) {
                            const messageElement = $('#uname-message');
                            if (response.status === 'exists') {
                                messageElement.html(response.message).addClass('error').removeClass('success');
                            } else {
                                messageElement.html(response.message).addClass('success').removeClass('error');
                            }
                        },
                        error: function() {
                            console.error('An error occurred while checking the username.');
                        }
                    });
                } else {
                    $('#uname-message').html('').removeClass('error success');
                }
            });
        });
    </script>