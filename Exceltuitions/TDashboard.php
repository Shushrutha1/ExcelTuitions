<?php
session_start();

// Check if faculty is logged in
if (!isset($_SESSION['uname']) || empty($_SESSION['uname'])) {
    header("location: Faculty-Login");
    exit;
}

// Include required files
include 'header.php';
include 'menu.php';
include 'config.php'; // Database connection

// Fetch faculty details
$faculty_id = $_SESSION['user_id']; // Ensure the session variable matches the correct key

$query = "SELECT * FROM faculty_registration WHERE id = ? AND is_active = 'Active'";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Failed to prepare query: " . $conn->error);
}

$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $faculty = $result->fetch_assoc();
} else {
    die("Faculty record not found or inactive.");
}

$stmt->close();
?>

<!-- Page Title -->
<section id="hero" class="hero section">
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <div class="page-title light-background">
            <div class="container">
               <img src="assets/img/logo5.gif"  alt="Logo" style="width: 10%; height: auto; display: block; margin: 0 auto;" />
                <h1 style="text-align: center; margin-top: 10px; color:#F00">Faculty <span style="color:#3db609">Profile</span></h1>
                <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
                 <h4 style="color:#039">Welcome, <span style="color:#093"> <?= htmlspecialchars($faculty['tname']) ?></span></h4>
                                <hr>
                  <!--  <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
                        <li><a href="TDashboard" style="text-decoration: none; color: #036;" class="btn btn-warning"> <<==Back to Admin Panel</a></li>
                    </ol>-->
                </nav>
            </div>

            <div class="container mt-5">
                <h1 class="text-center">Faculty Dashboard</h1>
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="card" style="background-color: transparent;">
                            <div class="card-body" >
                      <div style="text-align:center"><img src="<?= $faculty['photo'] ?>" alt="Logo" style="width: 20%; height: auto; display: block; margin: 0 auto;" /></div>
                               
                                <h5>Faculty Information</h5>
                                <ul class="list-group" >
                                    <li class="list-group-item"><strong>Name:</strong> <?= htmlspecialchars($faculty['tname']) ?> <?= htmlspecialchars($faculty['tfname']) ?></li>
                                    <li class="list-group-item"><strong>Father Name:</strong> <?= htmlspecialchars($faculty['tfname']) ?></li>
                                    <li class="list-group-item"><strong>Designation:</strong> <?= htmlspecialchars($faculty['designation']) ?></li>
                                    <li class="list-group-item"><strong>Qualification:</strong> <?= htmlspecialchars($faculty['qualification']) ?></li>
                                    <li class="list-group-item"><strong>Gender:</strong> <?= htmlspecialchars($faculty['gender']) ?></li>
                                    <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($faculty['email']) ?></li>
                                    <li class="list-group-item"><strong>Phone 1:</strong> <?= htmlspecialchars($faculty['phone1']) ?></li>
                                    <li class="list-group-item"><strong>Phone 2:</strong> <?= htmlspecialchars($faculty['phone2']) ?></li>
                                    <li class="list-group-item"><strong>Subjects:</strong> <?= htmlspecialchars($faculty['subjects']) ?></li>
                                    <li class="list-group-item"><strong>Address:</strong> <?= htmlspecialchars($faculty['address']) ?></li>
                                    <li class="list-group-item"><strong>Aadhar No:</strong> <?= htmlspecialchars($faculty['aadharno']) ?></li>
                                    <li class="list-group-item"><strong>Status:</strong> <?= $faculty['is_active'] ? 'Active' : 'Inactive' ?></li>
                                    <li class="list-group-item"><strong>Account Created:</strong> <?= htmlspecialchars($faculty['created_at']) ?></li>
                                    <li class="list-group-item"><strong>Email Verified:</strong> <?= $faculty['email_verified'] ? 'Yes' : 'No' ?></li>
                                    <br /><div style="text-align:center"> <a href="EditFaculty" class="btn btn-info">Edit Profile</a></div>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="card"  style="background-color: transparent;">
                            <div class="card-body">
                                <h4>Faculty Schedules </h4>
                                <hr>
                                <ul class="list-group">

                                    <a href="tschedule.php" class="btn btn-info">Schedule Date & Time</a><br />
                                    <a href="view_schedule.php" class="btn btn-success">My Schedules</a><br />
                                     <a href="view_book_t.php" class="btn btn-info">View your Booked Schedules</a>

                                    
                                </ul>
                                <hr>
                                <a href="logout.php" class="btn btn-danger">Logout</a>
                            </div>
                        </div>
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
