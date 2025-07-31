<?php
session_start();
include 'header.php';
include 'config.php';
include 'menu.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uname = htmlspecialchars(trim($_POST['uid']));
    $password = htmlspecialchars(trim($_POST['pwd']));
	$sts = 1;
    // Query to check if user exists
    $sql = "SELECT * FROM admin WHERE uid = ? AND status = ?";
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
        if (password_verify($password, $user['pwd'])) {
            // Set session variables
            $_SESSION['uid'] = $user['id'];
            $_SESSION['uid'] = $user['uid'];
            

            // Insert login record into user_logins
            $loginTime = date("Y-m-d H:i:s");
            $insertLogin = "INSERT INTO admin_logins (username, login_time) VALUES (?, ?)";
            $loginStmt = $conn->prepare($insertLogin);
            if ($loginStmt) {
               
                $loginStmt->bind_param("ss", $uname, $loginTime);
                $loginStmt->execute();
                $loginStmt->close();
            } else {
                die("Failed to insert login record: " . $conn->error);
            }

//            echo "<script>alert('Admin Login successful!'); window.location.href='AdminPanel';< /script>";
		echo "<script>window.location.href='AdminPanel';</script>";
        } else {
            echo "<script>alert('Invalid Admin password.');</script>";
        }
    } else {
        echo "<script>alert('Invalid Admin ID or Password.');</script>";
    }

    $stmt->close();
}
?>


<!-- Page Title -->
<section id="hero" class="hero section">
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
<div class="page-title light-background">
    <div class="container">
        <img src="assets/img/logo5.gif" alt="Logo" style="width: 10%; height: auto; display: block; margin: 0 auto;" />
        <h1 style="text-align: center; margin-top: 10px; color:#F00">Admin <span style="color:#3db609"> Login</span></h1>
        <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
            <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
                <li><a href="AdminLogin" style="text-decoration: none; color: #F00;">Admin Panel</a></li>
                <li style="color: #555;">Login Page</li>
            </ol>
        </nav>
    </div>
    <form method="POST" style="max-width: 400px; margin: 0 auto; background: #FC6; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <div class="form-group" style="text-align:left">
            <label for="uid" style="color: #003;">Admin ID</label><br>
            <input type="text" id="uid" name="uid" placeholder="Enter your User ID" required style="width: 100%; padding: 10px; margin-top: 5px; background-color: #f9f9f9; border: 1px solid #ccc; border-radius: 5px;">
        </div>
        <br>
        <div class="form-group" style="text-align:left">
            <label for="pwd" style="color: #003;">Admin Password</label><br>
            <input type="password" id="pwd" name="pwd" placeholder="Enter your password" required style="width: 100%; padding: 10px; margin-top: 5px; background-color: #f9f9f9; border: 1px solid #ccc; border-radius: 5px;">
        </div>
        <br>
        <button type="submit" class="btn-submit" style="width: 100%; padding: 10px; background-color: #036; color: #fff; border: none; border-radius: 5px; font-size: 16px;">Login</button>
        
    </form>
</div>
</div>
</section>
<?php include 'footer.php'; ?>
