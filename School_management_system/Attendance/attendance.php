<?php

include_once("./connection.php");
include_once("./navbar.php");

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_id = $_POST['teacher_id'];
    $action = $_POST['action'];

    // Current date and time
    $current_time = date('Y-m-d H:i:s');

    // Add 10 hours to the current time
    $time = date('Y-m-d H:i:s', strtotime('+10 hours', strtotime($current_time)));

    // Extract the new date after adding 10 hours
    $date = date('Y-m-d', strtotime('+10 hours', strtotime($current_time)));

    if ($action == 'check_in') {
        // Check if already checked in
        $sql = "SELECT * FROM attendance_records WHERE teacher_id = '$teacher_id' AND date = '$date'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "You have already checked in today.";
        } else {
            // Insert Check-In record with modified time and date
            $sql = "INSERT INTO attendance_records (teacher_id, check_in_time, date) VALUES ('$teacher_id', '$time', '$date')";
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Checked in successfully!'); window.location.href = 'index.php';</script>";
            } else {
                echo "Error: " . $conn->error;
            }
        }
    } elseif ($action == 'check_out') {
        // Update Check-Out record with modified time and date
        $sql = "UPDATE attendance_records SET check_out_time = '$time' WHERE teacher_id = '$teacher_id' AND date = '$date'";
        if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
            echo "<script>alert('Checked Out successfully!'); window.location.href = 'index.php';</script>";
        } else {
            echo "Error: Either you haven't checked in today or something went wrong.";
        }
    }
}
$conn->close();
?>
<br><a href="index.php">Back</a>
