<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch student data from the database
include 'header.php';
include 'menu.php';
$sts='Active';
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM student_registration WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <!-- Include Bootstrap CSS -->

    <!-- Include Google Fonts -->

    <style>
        body {
           /* background: linear-gradient(135deg, #1f4037, #99f2c8);*/
            color: #fff;
            font-family: 'Orbitron', sans-serif;
            margin: 0;
            padding: 0;
        }
        .dashboard-header {
            text-align: center;
            padding: 20px 0;
        }
        .dashboard-container {
            margin: 20px auto;
            max-width: 1200px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .card {
		   background-image: linear-gradient(to bottom, #01494f, #146870, #278893, #3aaab8, #4dcddf);
            border: none;
            border-radius: 10px;
            width: 100%;
            max-width: 350px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .card h4 {
            margin-bottom: 10px;
            color: #99f2c8;
        }
        .card p {
            font-size: 14px;
            color: #bbb;
        }
        .logout-btn {
            background: #ff6b6b;
            border: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        .logout-btn:hover {
            background: #ff3b3b;
        }
        .welcome-section {
            margin-bottom: 20px;
        }
        .welcome-section h2 {
            font-size: 2rem;
        }
		.timeline-container {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            width: 90%;
            margin: 0 auto;
        }
        .timeline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ff9800;
            border-radius: 25px;
            padding: 10px 20px;
            position: relative;
            width: 100%;
            overflow: hidden;
        }
        .timeline::before {
            content: '';
            position: absolute;
            height: 4px;
            background-color: white;
            top: 50%;
            left: 10%;
            right: 10%;
            transform: translateY(-50%);
            z-index: 1;
        }
        .month {
            position: relative;
            text-align: center;
            flex: 1;
            z-index: 2;
        }
        .month span {
            font-size: 12px;
            color: white;
            display: block;
            margin-top: 5px;
        }
        .dot {
            width: 14px;
            height: 14px;
            background-color: white;
            border-radius: 50%;
            margin: 0 auto;
            position: relative;
            z-index: 3;
            cursor: pointer;
        }
        .dot.active {
            background-color: #4caf50;
        }
        .tooltip {
            position: absolute;
            bottom: 35px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4caf50;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
        }
        .dot:hover .tooltip {
            visibility: visible;
            opacity: 1;
        }
		
    </style>
</head>
<body>
<section id="hero" class="hero section">
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
<div class="page-title light-background">
    <div class="container">
      <img 
    src="<?php echo $student['photo'] ?>" 
    alt="Logo" 
    style="width: 10%; height: auto; margin: 0 auto; border: 3px solid #036;"
/>


        <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
            <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
                <li><a href="index" style="text-decoration: none; color: #036;">Home</a></li>
                <li style="color: #555;">Student Login Page</li>
            </ol>
        </nav>
    </div>


    <div class="container welcome-section text-center">
        <h2> Hello,<span style="color:#0C3"><?php echo htmlspecialchars($student['sname']); ?></span>!</h2>
        <p>Your academic journey, personalized for you.</p>
        <button class="logout-btn" onClick="window.location.href='logout.php'">Logout</button>
    </div>

    <div class="dashboard-container">
        <!-- Profile Card -->
        <div class="card">
            <h4>Profile</h4>
                        <div style="text-align:left">
            <p style="color:#FF6"><strong><span style="color:#FFF">Name: </span></strong> <?php echo htmlspecialchars($student['sname']); ?></p>
            <p style="color:#9FF"><strong><span style="color:#FFF">Father Name: </span></strong> <?php echo htmlspecialchars($student['fname']); ?></p>
            <p style="color:#FCF"><strong><span style="color:#FFF">E-Mail: </span></strong> <?php echo htmlspecialchars($student['email']); ?></p>
            <p style="color:#FF6"><strong><span style="color:#FFF">Category: </span></strong> <?php echo htmlspecialchars($student['category']); ?></p>
            <p style="text-align:right"> <a href="Edit-S-Profile" class="btn" style="background-color:#FF6">Edit Profile</a>
            </p>
            </div>

        </div>

        <!-- Notifications Card -->
        <div class="card">
    <h4 style="color:#FF9">Notifications</h4>
    <marquee direction="up" scrollamount="2" style="height: 100px; overflow: hidden; color: #CF0;">
        <ul>
            <li>New Assignment Due: Dec 10, 2024</li>
            <li>Semester Exam: Jan 15, 2025</li>
            <li>Project Submission: Dec 20, 2024</li>
            <li>Holiday Announcement: Dec 25, 2024</li>
        </ul>
    </marquee>
</div>


        <!-- Academic Progress Card -->
        <div class="card">
            <h4>Academic Progress</h4>
            <div style="text-align:left">
            <p style="color:#FF6"><strong><span style="color:#FFF">Education: </span></strong> <?php echo htmlspecialchars($student['education']); ?></p>
            <p style="color:#FF6"><strong><span style="color:#FFF">Syllabus: </span></strong> <?php echo htmlspecialchars($student['syllabus']); ?></p>
            <p style="color:#FF6"><strong><span style="color:#FFF">Subject: </span></strong> <?php echo htmlspecialchars($student['subject']); ?></p>
            </div>
        </div>

        <!-- Upcoming Events Card -->
        <div class="card">
            <h4 style="color:#0F3">Upcoming Events</h4>
            <p style="color:#FFF">Seminar on AI: Dec 12, 2024</p>
            <p style="color:#FFF">Hackathon Registration Ends: Dec 20, 2024</p>
        </div>
          <!-- Schedule Classes for tuition -->
        <div class="card">
            <h4 style="color:#FCF">Schedule Classes</h4>
            <p style="color:#3FF">Seminar on AI: Dec 12, 2024</p>
            <p  style="color:#3FF">Hackathon Registration Ends: Dec 20, 2024</p>
           
           <nav class="breadcrumbs text-center">
                    <a href="sschedule.php" class="btn btn-warning">Schedule a meeting</a>
                </nav>
           
        </div>
        
         <div class="card">
            <h4 style="color:#FCF">View your Scheduled Classes</h4>
            <p style="color:#3FF">Seems like you have missed some classes?</p>
            <p  style="color:#3FF">Check it here </p>
           
           <nav class="breadcrumbs text-center">
                    <a href="view_book_s.php" class="btn btn-warning">View your Schedules</a>
                </nav>
           
        </div>
    </div>
   
<?php include'footer.php'; ?>
    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
