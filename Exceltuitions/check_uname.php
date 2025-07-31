<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = trim($_POST['uname']);
    
    $sql = "SELECT id FROM student_registration WHERE uname = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $uname);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "exists", "message" => "Username already exists."]);
    } else {
        echo json_encode(["status" => "available", "message" => "Username is available."]);
    }

    $stmt->close();
}
?>
