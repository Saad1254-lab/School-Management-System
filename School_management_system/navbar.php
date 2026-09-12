<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Output the JavaScript alert first
    echo "<script>alert('Please login first.'); window.location.href = 'login.php';</script>";
    exit(); // Make sure the script execution stops here after redirecting

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom hover styles for nav items */
        .navbar-nav .nav-item .nav-link {
            transition: all 0.3s ease;
            font-size: 13px;
        }

        .navbar-nav .nav-item:hover .nav-link {
            color: #fff !important;
            /* Change text color on hover */
            background-color: #28a745 !important;
            /* Change background color on hover */
            border-radius: 4px;
            /* Optional: add border radius */
        }

        /* Optional: Add a custom underline effect */
        .navbar-nav .nav-item:hover .nav-link {
            text-decoration: underline;
        }

        /* Additional styles for better navbar look */
        .navbar-nav .nav-item .nav-link {
            color: #ffffff;
            /* Default text color */
        }

        .navbar-nav .nav-item .nav-link.active {
            color: #ffffff;
            /* Color for the active page */
            font-weight: bold;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success nxt4">
        <div class="container">
            <a class="navbar-brand nop" href="index.php">Bk Academy</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_student.php">Add Student</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pay_fee.php">Pay Fee</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_students.php">View Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./view_transactions.php">View Fee Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./generate_fee.php">Generate Fees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./outstanding.php">Out Standings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./selected_outstanding.php">S Out</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="./print_voucher.php?student_id=16560
                             &issue_date=01%20<?= date('M Y') ?>
                              &due_date=10%20<?= date('M Y') ?>">
                            Print
                        </a>

                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="inventory">Inventory</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="withdrawn_students.php">Withdrawn Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="specific_class.php">Specific Class</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./logout.php">Log Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>