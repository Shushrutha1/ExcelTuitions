<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav id="navmenu" class="navmenu">
    <ul>
        <li><a href="index">Home</a></li>
        <li><a href="index#about">About</a></li>
        <li class="dropdown">
            <a href="#"><span>Slot Booking</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
                <li><a href="#">For Student</a></li>
                <li><a href="#">For Teacher</a></li>
                <hr />
                <li><a href="#">Find a Teacher</a></li>
                <li><a href="#">Find a Class</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#"><span>Administration</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
                <li><a href="#features">Courses/Subjects</a></li>
                <li><a href="SRegistration">Student Registration</a></li>
                <li><a href="Faculty-Registration">Faculty Registration</a></li>
                <li><a href="Faculty-Login">Faculty Login</a></li>
                <li><a href="#">Admissions</a></li>
                <li><a href="#">Quick Links</a></li>
            </ul>
        </li>
        <li><a href="#services">Gallery</a></li>
        <li><strong>
            <?php if (isset($_SESSION['user_id'])): ?>
                 <a class="btn-getstarted" style="background-color:#F00" href="logout" onclick="window.location.href='logout'"><strong>Logout</strong></a> 
               <!-- <button class="btn-getstarted" style="background-color:#F00" onclick="window.location.href='logout.php'">Logout</button> -->
            <?php else: ?>
                <a href="Login" style="color:#fff" class="openModalButton btn-getstarted">Login</a>
            <?php endif; ?>
        </strong></li>
        <li><a href="#contact">Contact</a></li>
        
    </ul>
    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
</nav>

     <!--  <a class="btn-getstarted" href="index.html#about">Get Started</a> -->

    </div>
  </header>

  <main class="main">