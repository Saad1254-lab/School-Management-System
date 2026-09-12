<?php
    session_start();


/*
|--------------------------------------------------------------------------
| PASSWORD PROTECTION
|--------------------------------------------------------------------------
*/

// Change this password
$correct_password = "12345";

// If logout requested
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();

    // Start a fresh session
    session_start();

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Check password submission
if (isset($_POST['page_password'])) {

    if ($_POST['page_password'] === $correct_password) {

        $_SESSION['generate_fees_access'] = true;

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    } else {
        $password_error = "Incorrect password!";
    }
}

// If not authenticated, show password page
if (!isset($_SESSION['generate_fees_access']) || $_SESSION['generate_fees_access'] !== true) {
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Required</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container">

        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">

            <div class="col-md-5">

                <div class="card shadow">

                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">🔒 Restricted Access</h4>
                    </div>

                    <div class="card-body p-4">

                        <p class="text-center text-muted">
                            Please enter the password to access this page.
                        </p>

                        <?php if (isset($password_error)) { ?>
                            <div class="alert alert-danger text-center">
                                <?php echo htmlspecialchars($password_error); ?>
                            </div>
                        <?php } ?>

                        <form method="POST">

                            <div class="mb-3">

                                <label class="form-label">
                                    Password
                                </label>

                                <input
                                    type="password"
                                    name="page_password"
                                    class="form-control"
                                    placeholder="Enter password"
                                    required
                                    autofocus
                                >

                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary w-100">
                                Access Page
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>
</html>

<?php
    exit;
}


// ==========================================================================
// USER IS AUTHENTICATED - LOAD DATABASE
// ==========================================================================

include 'db.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Generate Fees</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>




<div class="container mt-5">

    <!-- Logout button -->

    <div class="text-end mb-3">

        <a
            href="?logout=1"
            class="btn btn-danger"
            onclick="return confirm('Are you sure you want to leave this page?');">
            Logout / Exit
        </a>

    </div>


    <h2 class="text-center mb-4">
        Generate Fees for New Month
    </h2>


    <form method="POST" class="border p-4 rounded shadow-sm">

        <div class="mb-3">

            <label for="month" class="form-label">
                Enter Month and Year
            </label>

            <input
                type="text"
                class="form-control"
                id="month"
                name="month"
                placeholder="e.g., March 2024"
                required>

        </div>

        <button
            type="submit"
            name="generate_fees"
            class="btn btn-primary w-100">

            Generate Fees

        </button>

    </form>


<?php

// ==========================================================================
// GENERATE FEES FOR ALL STUDENTS
// ==========================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_fees'])) {

    $due_month = $_POST['month'];

    // Get all active students
    $students_stmt = $conn->prepare("
        SELECT student_id, name, fees_applied
        FROM students
        WHERE status = 'Active'
    ");

    $students_stmt->execute();

    $students_result = $students_stmt->get_result();


    if ($students_result->num_rows > 0) {

        $insert_stmt = $conn->prepare("
            INSERT INTO fees
            (student_id, amount, due_month)
            VALUES (?, ?, ?)
        ");

        $insert_stmt->bind_param(
            "ids",
            $student_id,
            $fees_applied,
            $due_month
        );

        $success = true;


        while ($student_row = $students_result->fetch_assoc()) {

            $student_id = $student_row['student_id'];
            $student_name = $student_row['name'];
            $fees_applied = $student_row['fees_applied'];


            // Check if already generated
            $check_fee_stmt = $conn->prepare("
                SELECT 1
                FROM fees
                WHERE student_id = ?
                AND due_month = ?
                LIMIT 1
            ");

            $check_fee_stmt->bind_param(
                "is",
                $student_id,
                $due_month
            );

            $check_fee_stmt->execute();

            $check_fee_stmt->store_result();


            if ($check_fee_stmt->num_rows > 0) {

                echo "
                <div class='alert alert-info mt-3'>

                    Skipping student:
                    <strong>" . htmlspecialchars($student_name) . "</strong>

                    (Student ID:
                    <strong>" . htmlspecialchars($student_id) . "</strong>)

                    — Fees already generated for
                    <strong>" . htmlspecialchars($due_month) . "</strong>.

                </div>
                ";

                $check_fee_stmt->close();

                continue;
            }


            // Insert fee
            if (!$insert_stmt->execute()) {

                $success = false;

                break;
            }


            $check_fee_stmt->close();
        }


        $insert_stmt->close();


        if ($success) {

            echo "
            <div class='alert alert-success mt-3'>

                Fees for
                <strong>" . htmlspecialchars($due_month) . "</strong>

                have been successfully generated for all students.

            </div>
            ";

        } else {

            echo "
            <div class='alert alert-danger mt-3'>

                An error occurred while generating fees.
                Please try again.

            </div>
            ";
        }


    } else {

        echo "
        <div class='alert alert-info mt-3'>

            No students found in the database.

        </div>
        ";
    }
}

?>


<!-- ======================================================================
     SPECIFIC STUDENT
====================================================================== -->

<h2 class="text-center mb-4 mt-5">
    Generate Fees for Specific Student
</h2>


<form method="POST" class="border p-4 rounded shadow-sm">

    <div class="mb-3">

        <label for="student_id" class="form-label">
            Student ID
        </label>

        <input
            type="text"
            class="form-control"
            id="student_id"
            name="student_id"
            placeholder="Enter Student ID"
            required>

    </div>


    <div class="mb-3">

        <label for="month_specific" class="form-label">
            Enter Month and Year
        </label>

        <input
            type="text"
            class="form-control"
            id="month_specific"
            name="month_specific"
            placeholder="e.g., March 2024"
            required>

    </div>


    <button
        type="submit"
        name="generate_fees_specific"
        class="btn btn-primary w-100">

        Generate Fees for Student

    </button>

</form>


<?php

// ==========================================================================
// GENERATE FEE FOR SPECIFIC STUDENT
// ==========================================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['generate_fees_specific'])
) {

    $student_id = $_POST['student_id'];

    $due_month_specific = $_POST['month_specific'];


    // Check existing fee
    $check_stmt_specific = $conn->prepare("
        SELECT 1
        FROM fees
        WHERE student_id = ?
        AND due_month = ?
        LIMIT 1
    ");

    $check_stmt_specific->bind_param(
        "is",
        $student_id,
        $due_month_specific
    );

    $check_stmt_specific->execute();

    $check_stmt_specific->store_result();


    if ($check_stmt_specific->num_rows > 0) {

        echo "
        <div class='alert alert-warning mt-3'>

            Fees for student ID
            <strong>" . htmlspecialchars($student_id) . "</strong>

            in
            <strong>" . htmlspecialchars($due_month_specific) . "</strong>

            have already been generated.

        </div>
        ";

        $check_stmt_specific->close();


    } else {

        $check_stmt_specific->close();


        // Get student
        $student_fee_stmt = $conn->prepare("
            SELECT name, fees_applied
            FROM students
            WHERE student_id = ?
        ");

        $student_fee_stmt->bind_param(
            "i",
            $student_id
        );

        $student_fee_stmt->execute();

        $student_fee_stmt->store_result();

        $student_fee_stmt->bind_result(
            $student_name,
            $fees_applied
        );

        $student_fee_stmt->fetch();


        if ($student_fee_stmt->num_rows > 0) {

            // Insert fee
            $insert_stmt_specific = $conn->prepare("
                INSERT INTO fees
                (student_id, amount, due_month)
                VALUES (?, ?, ?)
            ");

            $insert_stmt_specific->bind_param(
                "ids",
                $student_id,
                $fees_applied,
                $due_month_specific
            );


            if ($insert_stmt_specific->execute()) {

                echo "
                <div class='alert alert-success mt-3'>

                    Fees for
                    <strong>" . htmlspecialchars($student_name) . "</strong>

                    (Student ID:
                    <strong>" . htmlspecialchars($student_id) . "</strong>)

                    for
                    <strong>" . htmlspecialchars($due_month_specific) . "</strong>

                    have been successfully generated.

                </div>
                ";

            } else {

                echo "
                <div class='alert alert-danger mt-3'>

                    An error occurred while generating fees
                    for this student. Please try again.

                </div>
                ";
            }


            $insert_stmt_specific->close();


        } else {

            echo "
            <div class='alert alert-info mt-3'>

                Student ID
                <strong>" . htmlspecialchars($student_id) . "</strong>
                not found.

            </div>
            ";
        }


        $student_fee_stmt->close();
    }
}

?>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>