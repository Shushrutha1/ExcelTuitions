<?php 
include("config.php");
// Fetch user data
$sql = "SELECT * FROM student_registration WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found!");
}

$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = trim($_POST['uname']);
    $sname = trim($_POST['sname']);
    $fname = trim($_POST['fname']);
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $category = trim($_POST['category']);
    $education = trim($_POST['education']);
    $address = trim($_POST['address']);
    $gender = trim($_POST['gender']);

    // Update user data
    $update_sql = "UPDATE student_registration 
                   SET uname = ?, sname = ?, fname = ?, mobile = ?, email = ?, category = ?, education = ?, address = ?, gender = ? 
                   WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssssssi", $uname, $sname, $fname, $mobile, $email, $category, $education, $address, $gender, $id);

    if ($update_stmt->execute()) {
        echo "Registration details updated successfully!";
        // Redirect or show a success message
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
include("header.php");
include("menu.php");
?>

<form method="POST">
        <h2>Edit Registration</h2>

        <div class="form-group">
            <label for="uname">User ID</label>
            <input type="text" id="uname" name="uname" value="<?= htmlspecialchars($user['uname']) ?>" required>
        </div>

        <div class="form-group">
            <label for="sname">Student Name</label>
            <input type="text" id="sname" name="sname" value="<?= htmlspecialchars($user['sname']) ?>" required>
        </div>

        <div class="form-group">
            <label for="fname">Father's Name</label>
            <input type="text" id="fname" name="fname" value="<?= htmlspecialchars($user['fname']) ?>" required>
        </div>

        <div class="form-group">
            <label for="mobile">Mobile</label>
            <input type="text" id="mobile" name="mobile" value="<?= htmlspecialchars($user['mobile']) ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" id="category" name="category" value="<?= htmlspecialchars($user['category']) ?>">
        </div>

        <div class="form-group">
            <label for="education">Education</label>
            <input type="text" id="education" name="education" value="<?= htmlspecialchars($user['education']) ?>">
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="3"><?= htmlspecialchars($user['address']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="">Select</option>
                <option value="Male" <?= $user['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $user['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
            </select>
        </div>

        <button type="submit" class="btn-submit">Update</button>
    </form>
    
