<?php
session_start();

// Check if the user is already logged in, redirect if true
if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirect to the home page or dashboard
    exit();
}

// Define the correct password (hashed for security)
$correct_password = password_hash('16@aleem', PASSWORD_DEFAULT);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize user input
    $password = trim($_POST['password']);

    // Validate the password
    if (password_verify($password, $correct_password)) {
        $_SESSION['user_id'] = uniqid();
        header('Location: index.php');
        exit();
    } else {
        $error_message = "Invalid password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: url("Photo.jpg") no-repeat center center / cover;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            color: #fff;
        }

        .login-card h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .login-card label {
            font-weight: 500;
        }

        .login-card .form-control {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 6px;
            border: none;
        }

        .login-card .form-control:focus {
            box-shadow: 0 0 0 2px #0d6efd;
        }

        .login-card button {
            padding: 10px;
            font-weight: 600;
        }

        .alert {
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <div class="login-card">

        <h2>Login</h2>

        <?php if (isset($error_message)) : ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password" 
                    name="password" 
                    required
                >
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    Login
                </button>
            </div>
        </form>

    </div>

</body>
</html>