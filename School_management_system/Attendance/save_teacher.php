<?php
include_once("./connection.php");
include_once("./navbar.php");

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_name = $_POST['teacher_name'];
    $teacher_id = $_POST['teacher_id'];
    $joining_date = $_POST['joining_date'];

    // Check if the Teacher ID is unique
    $checkQuery = "SELECT * FROM teachers WHERE teacher_id = '$teacher_id'";
    $result = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
         echo "<script>
                    alert('Teacher Already Exist!');
                    window.location.href = 'add_teacher.php';
                  </script>";
    } else {
        // Insert new teacher into the database
        $insertQuery = "INSERT INTO teachers (name, teacher_id, joining_date) VALUES ('$teacher_name', '$teacher_id', '$joining_date')";
        
        if (mysqli_query($conn, $insertQuery)) {
             echo "<script>
                    alert('Teacher added successfully!');
                    window.location.href = 'add_teacher.php';
                  </script>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}
mysqli_close($conn);
?>
