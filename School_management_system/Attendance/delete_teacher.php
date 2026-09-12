<?php
include_once("./connection.php");

if (isset($_GET['id'])) {
    $teacher_id = $_GET['id'];

    // First, delete from the attendance_records table where teacher_id matches
    $delete_attendance_sql = "DELETE FROM attendance_records WHERE teacher_id = ?";
    $stmt_attendance = $conn->prepare($delete_attendance_sql);
    $stmt_attendance->bind_param("i", $teacher_id);

    if ($stmt_attendance->execute()) {
        // Now, delete from the teachers table
        $delete_teacher_sql = "DELETE FROM teachers WHERE teacher_id = ?";
        $stmt_teacher = $conn->prepare($delete_teacher_sql);
        $stmt_teacher->bind_param("i", $teacher_id);

        if ($stmt_teacher->execute()) {
            header("Location: view_teachers.php?message=Teacher+Deleted+Successfully");
        } else {
            echo "Error: " . $conn->error;
        }

        $stmt_teacher->close();
    } else {
        echo "Error deleting attendance: " . $conn->error;
    }

    $stmt_attendance->close();
    $conn->close();
} else {
    header("Location: view_teachers.php?error=Invalid+Request");
}
?>
