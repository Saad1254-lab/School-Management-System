<?php
session_start();

if (isset($_SESSION['user_id'])) {
    // User is logged in, show the confirmation dialog
    echo "<script>
        var confirmLogout = confirm('Are you sure you want to logout?');
        if (confirmLogout) {
            // If user confirms, redirect to logout.php to handle PHP session destruction
            window.location.href = 'end.php';
        } else {
            // If user cancels, redirect back to home page or wherever you'd like
            window.location.href = 'index.php';
        }
    </script>";
} else {
    // If user is not logged in, redirect to login page
    header('Location: login.php');
    exit();
}
?>
