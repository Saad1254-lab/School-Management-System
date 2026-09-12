<?php  
include_once("./connection.php");
include_once("./navbar.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
   


</head>
<script>
    window.onload = function() {
        // Function to check if 5 minutes have passed
        function canRequestPassword() {
            const lastRequestTime = localStorage.getItem('lastRequestTime');
            if (!lastRequestTime) return true;

            const currentTime = new Date().getTime();
            const fiveMinutes = 5 * 60 * 1000; // 5 minutes in milliseconds

            return (currentTime - lastRequestTime > fiveMinutes);
        }

        // Display a Google alert to the user
        if (canRequestPassword()) {
            localStorage.setItem('lastRequestTime', new Date().getTime());

            alert('This page is protected. Please enter the password to access.');

            var password = prompt("Enter the password:");

            if (password !== "BKA03219") { 
                alert('Incorrect password! Access denied.');
                window.location.href = 'https://www.example.com'; 
            }
        } else {
            
        }
    };
</script>


</script>
<body>



<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="text-center">Teacher Attendance Management</h4>
                </div>
                <div class="card-body">
                    <form action="attendance.php" method="POST">
                        <div class="mb-3">
                            <label for="teacher_id" class="form-label">Teacher ID:</label>
                            <input type="text" name="teacher_id" id="teacher_id" class="form-control" placeholder="Enter your Teacher ID" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="check_in" class="btn btn-success">Check In</button>
                            <button type="submit" name="action" value="check_out" class="btn btn-danger">Check Out</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="records.php" class="btn btn-secondary">View Attendance Records</a>
            </div>
        </div>
    </div>
</div>



</body>
</html>
