<?php
session_start();
if(!isset($_SESSION['uid']) || empty($_SESSION['uid'])){
  header("location:AdminLogin");
  exit;
}
?>

<?php
include'header.php';
include'menu.php';



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
                <li><a href="AdminPanel" style="text-decoration: none; color: #036;">Main Content</a></li>

            </ol>
        </nav>
    </div>
    <!--  Page Title  -->
    
    <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
            <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
                <li><a href="Subject" style="text-decoration: none; " class="btn btn-warning">Subject</a></li>
                <li><a href="Courses" style="text-decoration: none; ;" class="btn btn-success">Courses</a></li>
                <li><a href="AdminPanel" style="text-decoration: none; " class="btn btn-info">Feedback</a></li>
                <li><a href="AdminPanel" style="text-decoration: none; " class="btn btn-primary">Change Password</a></li>
                </ol><br><br>
                <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">
                <li><a href="Subject" style="text-decoration: none; " class="btn btn-dark">Subject</a></li>
                <li><a href="AdminPanel" style="text-decoration: none; ;" class="btn btn-light">Courses</a></li>
                <li><a href="AdminPanel" style="text-decoration: none; " class="btn btn-outline-primary">Feedback</a></li>
                <li><a href="AdminPanel" style="text-decoration: none; " class="btn btn-secondary">Change Password</a></li>

            </ol>
            
            </ol><br><br>
            <nav class="breadcrumbs" style="text-align: center; margin-bottom: 20px;">
                <ol style="list-style: none; padding: 0; display: inline-flex; gap: 10px;">                
                <li><a href="AdminLogout" style="text-decoration: none; " class="btn btn-danger">Logout</a></li>

            </ol>
        </nav>

<?php
include'footer.php';
?>
