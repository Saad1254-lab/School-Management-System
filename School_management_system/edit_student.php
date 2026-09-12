<?php
// Include database connection
include 'db.php';

if (isset($_GET['id'])) {

    $student_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Fetch student details
    $sql = "SELECT * FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $student_id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $student = $result->fetch_assoc();

        } else {

            $error_message = "Student not found.";
        }

        $stmt->close();

    } else {

        $error_message = "Error preparing statement: " . $conn->error;
    }
}


if (isset($_POST['submit'])) {

    // Get form input values
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $gr_no = mysqli_real_escape_string($conn, $_POST['gr_no']);
    $father_cnic = mysqli_real_escape_string($conn, $_POST['father_cnic']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $date_of_admission = mysqli_real_escape_string($conn, $_POST['date_of_admission']);
    $fees_applied = mysqli_real_escape_string($conn, $_POST['fees_applied']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);


    // Update query
    if ($status === 'Withdrawn') {

        $sql = "UPDATE students
                SET gr_no = ?,
                    father_cnic = ?,
                    name = ?,
                    father_name = ?,
                    dob = ?,
                    date_of_admission = ?,
                    fees_applied = ?,
                    contact = ?,
                    address = ?,
                    class = ?,
                    status = ?,
                    withdraw_date = CURRENT_TIMESTAMP
                WHERE student_id = ?";

    } else {

        $sql = "UPDATE students
                SET gr_no = ?,
                    father_cnic = ?,
                    name = ?,
                    father_name = ?,
                    dob = ?,
                    date_of_admission = ?,
                    fees_applied = ?,
                    contact = ?,
                    address = ?,
                    class = ?,
                    status = ?,
                    withdraw_date = NULL
                WHERE student_id = ?";
    }


    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "sssssssssssi",
            $gr_no,
            $father_cnic,
            $name,
            $father_name,
            $dob,
            $date_of_admission,
            $fees_applied,
            $contact,
            $address,
            $class,
            $status,
            $student_id
        );


        if ($stmt->execute()) {

            $success_message = "Student details updated successfully!";


            // Refresh student data after update
            $sql_fetch = "SELECT * FROM students WHERE student_id = ?";
            $stmt_fetch = $conn->prepare($sql_fetch);

            if ($stmt_fetch) {

                $stmt_fetch->bind_param("i", $student_id);
                $stmt_fetch->execute();

                $result_fetch = $stmt_fetch->get_result();

                if ($result_fetch->num_rows > 0) {

                    $student = $result_fetch->fetch_assoc();
                }

                $stmt_fetch->close();
            }

        } else {

            $error_message = "Error executing query: " . $stmt->error;
        }

        $stmt->close();

    } else {

        $error_message = "Error preparing statement: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Edit Student</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>


<body>

    <?php include './navbar.php'; ?>


    <div class="container mt-5">

        <h2 class="text-center mb-4">
            Edit Student
        </h2>


        <?php if (isset($success_message)): ?>

            <div class="alert alert-success">

                <?php echo htmlspecialchars($success_message); ?>

            </div>

        <?php elseif (isset($error_message)): ?>

            <div class="alert alert-danger">

                <?php echo htmlspecialchars($error_message); ?>

            </div>

        <?php endif; ?>


        <?php if (isset($student)): ?>

            <form
                action=""
                method="POST"
                class="border p-4 rounded shadow-sm">


                <!-- Student ID -->
                <div class="mb-3">

                    <label
                        for="student_id"
                        class="form-label">

                        Student ID

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="student_id"
                        name="student_id"
                        value="<?= htmlspecialchars($student['student_id']); ?>"
                        readonly>

                </div>


                <!-- GR No -->
                <div class="mb-3">

                    <label
                        for="gr_no"
                        class="form-label">

                        GR No

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="gr_no"
                        name="gr_no"
                        value="<?= htmlspecialchars($student['gr_no'] ?? ''); ?>"
                        placeholder="Enter GR No"
                        required>

                </div>


                <!-- Father CNIC -->
                <div class="mb-3">

                    <label
                        for="father_cnic"
                        class="form-label">

                        Father CNIC

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="father_cnic"
                        name="father_cnic"
                        value="<?= htmlspecialchars($student['father_cnic'] ?? ''); ?>"
                        placeholder="Enter Father CNIC (e.g., 42101-1234567-1)"
                        maxlength="15">

                </div>


                <!-- Student Name -->
                <div class="mb-3">

                    <label
                        for="name"
                        class="form-label">

                        Student Name

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="name"
                        name="name"
                        value="<?= htmlspecialchars($student['name']); ?>"
                        required>

                </div>


                <!-- Father Name -->
                <div class="mb-3">

                    <label
                        for="father_name"
                        class="form-label">

                        Father's Name

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="father_name"
                        name="father_name"
                        value="<?= htmlspecialchars($student['father_name']); ?>"
                        required>

                </div>


                <!-- Class -->
                <div class="mb-3">

                    <label
                        for="class"
                        class="form-label">

                        Class

                    </label>

                    <select
                        class="form-control"
                        id="class"
                        name="class"
                        required>

                        <option value="">
                            Select Class
                        </option>


                        <option
                            value="Prek-1 Lilly"
                            <?= $student['class'] == 'Prek-1 Lilly' ? 'selected' : '' ?>>

                            Prek-1 Lilly

                        </option>


                        <option
                            value="Prek-1 Rose"
                            <?= $student['class'] == 'Prek-1 Rose' ? 'selected' : '' ?>>

                            Prek-1 Rose

                        </option>


                        <option
                            value="Prek-2 Lilly"
                            <?= $student['class'] == 'Prek-2 Lilly' ? 'selected' : '' ?>>

                            Prek-2 Lilly

                        </option>


                        <option
                            value="Prek-2 Rose"
                            <?= $student['class'] == 'Prek-2 Rose' ? 'selected' : '' ?>>

                            Prek-2 Rose

                        </option>


                        <option
                            value="KG Lilly"
                            <?= $student['class'] == 'KG Lilly' ? 'selected' : '' ?>>

                            KG Lilly

                        </option>


                        <option
                            value="KG Rose"
                            <?= $student['class'] == 'KG Rose' ? 'selected' : '' ?>>

                            KG Rose

                        </option>


                        <option
                            value="Grade-1 Lilly"
                            <?= $student['class'] == 'Grade-1 Lilly' ? 'selected' : '' ?>>

                            Grade-1 Lilly

                        </option>


                        <option
                            value="Grade-1 Rose"
                            <?= $student['class'] == 'Grade-1 Rose' ? 'selected' : '' ?>>

                            Grade-1 Rose

                        </option>


                        <option
                            value="Grade-2 Rose"
                            <?= $student['class'] == 'Grade-2 Rose' ? 'selected' : '' ?>>

                            Grade-2 Rose

                        </option>


                        <option
                            value="Grade-3 Rose"
                            <?= $student['class'] == 'Grade-3 Rose' ? 'selected' : '' ?>>

                            Grade-3 Rose

                        </option>


                        <option
                            value="Grade-4 Rose"
                            <?= $student['class'] == 'Grade-4 Rose' ? 'selected' : '' ?>>

                            Grade-4 Rose

                        </option>


                        <option
                            value="Grade-5 Rose"
                            <?= $student['class'] == 'Grade-5 Rose' ? 'selected' : '' ?>>

                            Grade-5 Rose

                        </option>


                        <option
                            value="Grade-6 Rose"
                            <?= $student['class'] == 'Grade-6 Rose' ? 'selected' : '' ?>>

                            Grade-6 Rose

                        </option>


                        <option
                            value="Grade-7 Rose"
                            <?= $student['class'] == 'Grade-7 Rose' ? 'selected' : '' ?>>

                            Grade-7 Rose

                        </option>


                        <option
                            value="Grade-8 Rose"
                            <?= $student['class'] == 'Grade-8 Rose' ? 'selected' : '' ?>>

                            Grade-8 Rose

                        </option>


                        <option
                            value="Grade-9 Rose"
                            <?= $student['class'] == 'Grade-9 Rose' ? 'selected' : '' ?>>

                            Grade-9 Rose

                        </option>


                        <option
                            value="Grade-10 Rose"
                            <?= $student['class'] == 'Grade-10 Rose' ? 'selected' : '' ?>>

                            Grade-10 Rose

                        </option>


                        <option
                            value="RCL"
                            <?= $student['class'] == 'RCL' ? 'selected' : '' ?>>

                            RCL

                        </option>

                    </select>

                </div>


                <!-- Date of Birth -->
                <div class="mb-3">

                    <label
                        for="dob"
                        class="form-label">

                        Date of Birth

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="dob"
                        name="dob"
                        value="<?= htmlspecialchars($student['dob']); ?>"
                        required>

                </div>


                <!-- Date of Admission -->
                <div class="mb-3">

                    <label
                        for="date_of_admission"
                        class="form-label">

                        Date of Admission

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="date_of_admission"
                        name="date_of_admission"
                        value="<?= htmlspecialchars($student['date_of_admission']); ?>"
                        required>

                </div>


                <!-- Fees Applied -->
                <div class="mb-3">

                    <label
                        for="fees_applied"
                        class="form-label">

                        Fees Applied

                    </label>

                    <input
                        type="number"
                        class="form-control"
                        id="fees_applied"
                        name="fees_applied"
                        value="<?= htmlspecialchars($student['fees_applied']); ?>"
                        required>

                </div>


                <!-- Contact -->
                <div class="mb-3">

                    <label
                        for="contact"
                        class="form-label">

                        Contact Number

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="contact"
                        name="contact"
                        value="<?= htmlspecialchars($student['contact']); ?>"
                        required>

                </div>


                <!-- Address -->
                <div class="mb-3">

                    <label
                        for="address"
                        class="form-label">

                        Address

                    </label>

                    <textarea
                        class="form-control"
                        id="address"
                        name="address"
                        rows="3"
                        required><?= htmlspecialchars($student['address']); ?></textarea>

                </div>


                <!-- Status -->
                <div class="mb-3">

                    <label
                        for="status"
                        class="form-label">

                        Status

                    </label>

                    <select
                        name="status"
                        id="status"
                        class="form-select"
                        required>

                        <option
                            value="Active"
                            <?= $student['status'] == 'Active' ? 'selected' : ''; ?>>

                            Active

                        </option>


                        <option
                            value="Withdrawn"
                            <?= $student['status'] == 'Withdrawn' ? 'selected' : ''; ?>>

                            Withdrawn

                        </option>


                        <option
                            value="InActive"
                            <?= $student['status'] == 'InActive' ? 'selected' : ''; ?>>

                            InActive

                        </option>

                    </select>

                </div>


                <!-- Update Button -->
                <button
                    type="submit"
                    name="submit"
                    class="btn btn-primary">

                    Update Student

                </button>

            </form>

        <?php endif; ?>

    </div>


    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js">
    </script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js">
    </script>

</body>

</html>