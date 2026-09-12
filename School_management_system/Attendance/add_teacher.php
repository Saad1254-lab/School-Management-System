<?php
include_once("./navbar.php"); 


session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Teacher</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="text-center">Add New Teacher</h4>
                </div>
                <div class="card-body">
                    <form action="save_teacher.php" method="POST">
                        <div class="mb-3">
                            <label for="teacher_name" class="form-label">Teacher Name:</label>
                            <input type="text" name="teacher_name" id="teacher_name" class="form-control" placeholder="Enter Teacher Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="teacher_id" class="form-label">Teacher ID:</label>
                            <input type="text" name="teacher_id" id="teacher_id" class="form-control" placeholder="Enter Teacher ID" required>
                        </div>
                        <div class="mb-3">
                            <label for="joining_date" class="form-label">Joining Date:</label>
                            <input type="date" name="joining_date" id="joining_date" class="form-control" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Add Teacher</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
