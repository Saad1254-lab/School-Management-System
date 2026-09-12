<?php
include_once("./navbar.php");
session_start();

// Define a password (store in an environment variable for production use)
$correct_password = "admin54321";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];

    // Check if the password matches
    if ($password === $correct_password) {
        $_SESSION['logged_in'] = true; // Set a session variable
        header("Location: view_teachers.php"); // Redirect to the main page
        exit;
    } else {
        $error_message = "Incorrect password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Login</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)) { ?>
                        <div class="alert alert-danger">
                            <?php echo $error_message; ?>
                        </div>
                    <?php } ?>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
