<?php
// Include necessary files first
include 'header.php';
include 'menu.php';

// Ensure no output is sent before headers (no echo before header())

if (isset($_POST['create'])) {
    $schedule_date = $_POST['schedule_date'];
    $from_time = $_POST['from_time'];
    $to_time = $_POST['to_time'];
    $slot_status = $_POST['slot_status'];

    $stmt = $conn->prepare("INSERT INTO schedule (schedule_date, from_time, to_time, slot_status) 
                           VALUES (?, ?, ?, ?)");
    $stmt->execute([$schedule_date, $from_time, $to_time, $slot_status]);
    header("Location: crud.php");
}

// READ
$stmt = $conn->query("SELECT * FROM schedule");
$schedules = $stmt->fetchAll();

// UPDATE
if (isset($_GET['edit'])) {
    $schedule_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM schedule WHERE schedule_id = ?");
    $stmt->execute([$schedule_id]);
    $schedule = $stmt->fetch();
}

// UPDATE (submit)
if (isset($_POST['update'])) {
    $schedule_id = $_POST['schedule_id'];
    $schedule_date = $_POST['schedule_date'];
    $from_time = $_POST['from_time'];
    $to_time = $_POST['to_time'];
    $slot_status = $_POST['slot_status'];

    $stmt = $conn->prepare("UPDATE schedule SET schedule_date = ?, from_time = ?, to_time = ?, slot_status = ? 
                           WHERE schedule_id = ?");
    $stmt->execute([$schedule_date, $from_time, $to_time, $slot_status, $schedule_id]);
    header("Location: crud.php");
}

// DELETE
if (isset($_GET['delete'])) {
    $schedule_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM schedule WHERE schedule_id = ?");
    $stmt->execute([$schedule_id]);
    header("Location: crud.php");
}
?>


    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <section id="hero" class="hero section">
        <div class="container mt-5">
            <h1>Schedule Manager</h1>
            
            <!-- Form for adding slots -->
            <h1>Schedule Management</h1>

    <!-- CREATE FORM -->
    <h2>Create Schedule</h2>
    <form method="POST">
        <label for="schedule_date">Schedule Date:</label>
        <input type="date" name="schedule_date" required><br><br>

        <label for="from_time">From Time:</label>
        <input type="time" name="from_time" required><br><br>

        <label for="to_time">To Time:</label>
        <input type="time" name="to_time" required><br><br>

        <label for="slot_status">Slot Status:</label>
        <select name="slot_status" required>
            <option value="available">Available</option>
            <option value="booked">Booked</option>
            <option value="pending">Pending</option>
        </select><br><br>

        <button type="submit" name="create">Create</button>
    </form>

    <hr>

    <!-- UPDATE FORM -->
    <?php if (isset($schedule)): ?>
    <h2>Update Schedule</h2>
    <form method="POST">
        <input type="hidden" name="schedule_id" value="<?= $schedule['schedule_id'] ?>">

        <label for="schedule_date">Schedule Date:</label>
        <input type="date" name="schedule_date" value="<?= $schedule['schedule_date'] ?>" required><br><br>

        <label for="from_time">From Time:</label>
        <input type="time" name="from_time" value="<?= $schedule['from_time'] ?>" required><br><br>

        <label for="to_time">To Time:</label>
        <input type="time" name="to_time" value="<?= $schedule['to_time'] ?>" required><br><br>

        <label for="slot_status">Slot Status:</label>
        <select name="slot_status" required>
            <option value="available" <?= $schedule['slot_status'] == 'available' ? 'selected' : '' ?>>Available</option>
            <option value="booked" <?= $schedule['slot_status'] == 'booked' ? 'selected' : '' ?>>Booked</option>
            <option value="pending" <?= $schedule['slot_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
        </select><br><br>

        <button type="submit" name="update">Update</button>
    </form>
    <?php endif; ?>

    <hr>

    <!-- DISPLAY SCHEDULE LIST -->
    <h2>Schedule List</h2>
    <table border="1">
        <tr>
            <th>Schedule ID</th>
            <th>Schedule Date</th>
            <th>From Time</th>
            <th>To Time</th>
            <th>Slot Status</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($schedules as $schedule): ?>
        <tr>
            <td><?= $schedule['schedule_id'] ?></td>
            <td><?= $schedule['schedule_date'] ?></td>
            <td><?= $schedule['from_time'] ?></td>
            <td><?= $schedule['to_time'] ?></td>
            <td><?= $schedule['slot_status'] ?></td>
            <td><?= $schedule['created_at'] ?></td>
            <td><?= $schedule['updated_at'] ?></td>
            <td>
                <a href="?edit=<?= $schedule['schedule_id'] ?>">Edit</a> | 
                <a href="?delete=<?= $schedule['schedule_id'] ?>" onclick="return confirm('Are you sure you want to delete?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
