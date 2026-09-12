
<?php
// Start the session
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Clear the localStorage item (optional)
?>

<?php
// Redirect to the login page
  echo "<script>
                localStorage.clear();
                    alert('Logout successfully!');
                    window.location.href = '../index.php';
                  </script>";
exit;
?>
