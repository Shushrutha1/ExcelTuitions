<?php include("config.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>ExcelTuitions.com</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons 
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">-->

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">  
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">

  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- =======================================================
  * Template Name: iLanding
  * Template URL: https://bootstrapmade.com/ilanding-bootstrap-landing-page-template/
  * Updated: Oct 28 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
<style>
/* Container Styling */

/* Animation for .etsitename */
.etsitename {


    color: #00ff00; 
    text-shadow: 0 0 2px #00ff00, 0 0 3px #00ff00;
    animation: colorChange 5s infinite alternate;
}


@keyframes colorChange {
    0% {
        color: #00ff00; 
        text-shadow: 0 0 2px #00ff00, 0 0 3px #00ff00;
    }
    25% {
        color: #ff0000; 
        text-shadow: 0 0 2px #ff0000, 0 0 3px #ff0000;
    }
    50% {
        color: #1e90ff; 
        text-shadow: 0 0 2px #1e90ff, 0 0 3px #1e90ff;
    }
    75% {
        color: #ffff00; 
        text-shadow: 0 0 2px #ffff00, 0 0 3px #ffff00;
    }
    100% {
        color: #ff00ff; 
        text-shadow: 0 0 2px #ff00ff, 0 0 3px #ff00ff;
    }
} 
.etsitenameimg {
    width: 150px;
    height: auto;
    filter: hue-rotate(90deg) brightness(1.5) contrast(1.2);
    transition: filter 0.5s ease;
}

.etsitename:hover {
    filter: hue-rotate(270deg);
}


</style>
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="index" class="logo d-flex align-items-center me-auto me-xl-0">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="assets/img/logo3.gif" alt=""> 
        <h1><strong><span style="color:#042844">Excel</span><span style="color:#3db609">Tuitions.com</span></strong></h1>
      </a>
