<?php

// Database configuration

	$dbhost = 'localhost';

	$dbuser = 'root';

	$dbpass = '';

		$conn = mysqli_connect($dbhost, $dbuser, $dbpass);

        if ($conn->connect_error) {

	    die("Connection failed: " . $conn->connect_error);

		} 

			mysqli_select_db($conn,'exceltuitions' );

			$conn->set_charset("utf8");



?>