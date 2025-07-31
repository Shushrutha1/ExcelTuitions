<?php
include "header.php";



session_start();
require 'config.php'; // Include your database connection file
// Set timezone to Indian Standard Time
date_default_timezone_set('Asia/Kolkata');

// Function to get client IP address
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uname = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $sts = "Active";

    // Query to check if user exists
    $sql = "SELECT * FROM student_registration WHERE uname = ? AND status = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $uname, $sts);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['uname'] = $user['uname'];
            $_SESSION['sname'] = $user['sname'];

            // Capture login time and IP address
            $loginTime = date("Y-m-d H:i:s");
            $ipAddress = getClientIP();

            // Insert login record into user_logins
            $insertLogin = "INSERT INTO user_logins (username, login_time, ip_address) VALUES (?, ?, ?)";
            $loginStmt = $conn->prepare($insertLogin);
            if ($loginStmt) {
                $loginStmt->bind_param("sss", $uname, $loginTime, $ipAddress);
                $loginStmt->execute();
                $loginStmt->close();
            } else {
                die("Failed to insert login record: " . $conn->error);
            }

            echo "<script>alert('Login successful!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('Invalid User ID or Password.');</script>";
    }

    $stmt->close();
}


?>


<?php
include"menu.php"; ?>

<?php 
 include_once"hero.php";
?>

<!-- Bootstrap CSS -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}
.modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    width: 400px;
}
.close-btn {
    background: red;
    color: #fff;
    border: none;
    font-size: 20px;
    cursor: pointer;
}

/* Modal Styles */
/* Modal Background */
.modal {
  display: none; /* Hidden by default */
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5); /* Dark overlay */
  z-index: 1000;
}

/* Modal Content Box */
.modal-content {
  position: relative;
/*   background: linear-gradient(135deg, #CFF, #FFF); */
  background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(219,255,227,1) 0%, rgba(255,255,255,1) 55%);
  padding: 20px;
  width: 420px;
  max-width: 90%;
  margin: 100px auto;
  border-radius: 12px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  color: #ffffff;
}

/* Close Button 
.close {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 24px;
  cursor: pointer;
  color: #fff;
}

.close:hover {
  color: #ff6666;
}

*/
/* Close Button - Red Color */
.close {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 24px;
  cursor: pointer;
  color: #ff0000; /* Red Color */
  background-color: transparent;
  border: none;
}

.close:hover {
  color: #cc0000; /* Darker Red on Hover */
}

/* Form Styles */


.input-group {
  margin-bottom: 20px;
}

.input-group label {
  display: block;
  font-size: 14px;
  margin-bottom: 6px;
  color: #ffffff;
}

.input-group input {
  width: 100%;
  padding: 10px;
  border-radius: 6px;
  border: none;
  font-size: 14px;
}

/* Button Styles */
.btn-open {
  padding: 10px 20px;
  font-size: 16px;
  background: linear-gradient(135deg, #32a852, #3a86ff);
  border: none;
  border-radius: 8px;
  color: #ffffff;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-open:hover {
  background: linear-gradient(135deg, #3a86ff, #32a852);
}

.btn-submit {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  background: #0074cc;
  border: none;
  border-radius: 8px;
  color: #ffffff;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-submit:hover {
  background: #005fa3;
}

</style>
<div id="loginModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3><img src="assets/img/logo5.gif" width="25%" height="25%" />Student Login</h3>
    <form id="loginForm" method="POST">
        <div class="input-group">
            <label for="username" style="color:#003">User ID</label>
            <input type="text" id="username" style="background-color:#CFC" name="username" placeholder="Enter your User ID" required>
        </div>
        <div class="input-group">
            <label for="password" style="color:#003">Password</label>
            <input type="password" id="password" name="password" style="background-color:#CFC" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn-submit">Login</button>
        <div class="input-group" style="display: flex; justify-content: space-between; margin-top: 15px;">
            <a href="SRegistration.php" style="color:#006;">New Registration</a>
            <a href="forgot_password.php" style="color:#006;">Forgot Password</a>
        </div>
    </form>
  </div>
</div>

<!--  student registration -->
<!-- Modal Structure -->
<div id="studentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Student Registration</h2>
            <button class="close-btn">&times;</button>
        </div>
        <form id="registrationForm">
            <div class="input-group">
                <label for="sname">Student Name</label>
                <input type="text" id="sname" name="sname" required>
            </div>
            <div class="input-group">
                <label for="fname">Father's Name</label>
                <input type="text" id="fname" name="fname" required>
            </div>
            <div class="input-group">
                <label for="mobile">Mobile</label>
                <input type="text" id="mobile" name="mobile" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="General">General</option>
                    <option value="OBC">OBC</option>
                    <option value="SC">SC</option>
                    <option value="ST">ST</option>
                </select>
            </div>
            <div class="input-group">
                <label for="education">Education</label>
                <input type="text" id="education" name="education" required>
            </div>
            <div class="input-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="input-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Submit</button>
        </form>
    </div>
</div>

<!--  student registration -->
<script>
/* // Get the modal, button, form, and close elements
const modal = document.getElementById("loginModal");
const openModalButton = document.getElementById("openModalButton");
const closeButton = document.querySelector(".close");
const loginForm = document.getElementById("loginForm");

// Open the modal when the button is clicked and clear form data
openModalButton.onclick = function () {
  loginForm.reset(); // Clear the form data
  modal.style.display = "block";
};

// Close the modal when the close button is clicked
closeButton.onclick = function () {
  modal.style.display = "none";
};

// Close the modal when clicking outside the modal content
window.onclick = function (event) {
  if (event.target === modal) {
    modal.style.display = "none";
  }
};
document.addEventListener("keydown", function (event) {
  if (event.key === "Escape" || event.key === "Esc") {
    modal.style.display = "none";
  }
});*/
</script>

<script>
    // Get the modal, buttons, form, and close elements
    const modal = document.getElementById("loginModal");
    const openModalButtons = document.querySelectorAll(".openModalButton");
    const closeButton = document.querySelector(".close");
    const loginForm = document.getElementById("loginForm");

    // Add click event listeners to all buttons with the class "openModalButton"
    openModalButtons.forEach((button) => {
      button.onclick = function (event) {
        event.preventDefault(); // Prevent default anchor behavior
        loginForm.reset(); // Clear the form data
        modal.style.display = "block";
      };
    });

    // Close the modal when the close button is clicked
    closeButton.onclick = function () {
      modal.style.display = "none";
    };

    // Close the modal when clicking outside the modal content
    window.onclick = function (event) {
      if (event.target === modal) {
        modal.style.display = "none";
      }
    };

    // Close the modal when pressing the "Escape" key
    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" || event.key === "Esc") {
        modal.style.display = "none";
      }
    });
  </script>

<?php
include"footer.php";
?>