<?php
session_start();
include 'header.php';
include 'config.php';
include 'menu.php';
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

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $sts = "Active";

    try {
        // Query to check if the user exists
        $sql = "SELECT * FROM faculty_registration WHERE email = ? AND is_active = ? and email_verified=1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Statement preparation failed: " . $conn->error);
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
                $_SESSION['uname'] = $user['email'];
                $_SESSION['tname'] = $user['tname'];

                // Log the login activity
                $loginTime = date("Y-m-d H:i:s");
                $ipAddress = getClientIP();
                $insertLogin = "INSERT INTO user_logins (username, login_time, ip_address) VALUES (?, ?, ?)";
                $loginStmt = $conn->prepare($insertLogin);
                if (!$loginStmt) {
                    throw new Exception("Failed to insert login record: " . $conn->error);
                }

                $loginStmt->bind_param("sss", $uname, $loginTime, $ipAddress);
                $loginStmt->execute();
                $loginStmt->close();

                // Redirect to dashboard
                echo "<script>alert('Login successful!'); window.location.href='TDashboard.php';</script>";
                exit();
            } else {
                echo "<script>alert('Invalid password. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Invalid email or account is inactive.');</script>";
        }

        $stmt->close();
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo "<script>alert('An error occurred. Please try again later.');</script>";
    }
}
?>


<!-- Page Content -->
<section id="hero" class="hero section">
    <div class="page-title light-background">
        <div class="container">
            <img src="assets/img/logo5.gif" alt="Logo" style="width: 10%; height: auto; display: block; margin: 0 auto;" />
            <h1 style="text-align: center; margin-top: 10px;">
                <span style="color:#3db609">Faculty</span> Login <span style="color:#3db609">Page</span>
            </h1>
            <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
                <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
                    <li><a href="index" style="text-decoration: none; color: #036;">Home</a></li>
                    <li style="color: #555;">Login Page</li>
                </ol>
            </nav>
        </div>
        <form method="POST" style="max-width: 400px; margin: 0 auto; background: #79EF76; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <div class="form-group" style="text-align:left;">
                <label for="email" style="color: #003;">Email</label><br>
                <input type="email" id="email" name="email" placeholder="Enter your email" required style="width: 100%; padding: 10px; margin-top: 5px; background-color: #f9f9f9; border: 1px solid #ccc; border-radius: 5px;">
            </div>
            <br>
            <div class="form-group" style="text-align:left;">
                <label for="password" style="color: #003;">Password</label><br>
                <input type="password" id="password" name="password" placeholder="Enter your password" required style="width: 100%; padding: 10px; margin-top: 5px; background-color: #f9f9f9; border: 1px solid #ccc; border-radius: 5px;">
            </div>
            <br>
            <button type="submit" class="btn-submit" style="width: 100%; padding: 10px; background-color: #036; color: #fff; border: none; border-radius: 5px; font-size: 16px;">Login</button>
            <div class="input-group" style="display: flex; justify-content: space-between; margin-top: 15px;">
                <a href="Faculty-Registration.php" style="color: #006; text-decoration: none;">New Registration</a>
                <a href="forgot_password.php" style="color: #006; text-decoration: none;">Forgot Password</a>
            </div>
        </form>
    </div>
</section>

<?php include 'footer.php'; ?>

