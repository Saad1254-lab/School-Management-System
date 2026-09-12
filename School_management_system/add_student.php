<?php
// Include database connection
include 'db.php';

// Generate next student_id
$sql = "SELECT MAX(student_id) AS max_id FROM students";
$result = $conn->query($sql);
$new_student_id = 1;

if ($result && $row = $result->fetch_assoc()) {
    $new_student_id = $row['max_id'] + 1;
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

    // SQL query
    $sql = "INSERT INTO students 
    (student_id, gr_no, father_cnic, name, father_name, dob, date_of_admission, fees_applied, contact, address, class, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "isssssssssss",
            $student_id,
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
            $status
        );

        if ($stmt->execute()) {
            $success_message = "New student added successfully!";
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

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Student</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body>

    <?php include './navbar.php'; ?>

    <div class="container mt-5">

        <h2 class="text-center mb-4">
            Add New Student
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


        <form method="POST" class="border p-4 rounded shadow-sm">

            <!-- Student ID -->
            <div class="mb-3">

                <label for="student_id" class="form-label">
                    Student ID
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="student_id"
                    name="student_id"
                    value="<?= $new_student_id ?>"
                    placeholder="Enter student ID"
                    required>

            </div>


            <!-- GR No -->
            <div class="mb-3">

                <label for="gr_no" class="form-label">
                    GR No
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="gr_no"
                    name="gr_no"
                    placeholder="Enter GR No"
                    required>

            </div>


            <!-- Father CNIC -->
            <div class="mb-3">

                <label for="father_cnic" class="form-label">
                    Father CNIC
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="father_cnic"
                    name="father_cnic"
                    placeholder="Enter Father CNIC (e.g., 42101-1234567-1)"
                    maxlength="15">

            </div>


            <!-- Student Name -->
            <div class="mb-3">

                <label for="name" class="form-label">
                    Student Name
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="name"
                    name="name"
                    placeholder="Enter student name"
                    required>

            </div>


            <!-- Father Name -->
            <div class="mb-3">

                <label for="father_name" class="form-label">
                    Father Name
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="father_name"
                    name="father_name"
                    placeholder="Enter father's name"
                    required>

            </div>


            <!-- Class -->
            <div class="mb-3">

                <label for="class" class="form-label">
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

                    <!-- Pre School -->

                    <option value="Prek-1 Lilly">
                        Prek-1 Lilly
                    </option>

                    <option value="Prek-1 Rose">
                        Prek-1 Rose
                    </option>

                    <option value="Prek-2 Lilly">
                        Prek-2 Lilly
                    </option>

                    <option value="Prek-2 Rose">
                        Prek-2 Rose
                    </option>

                    <option value="KG Lilly">
                        KG Lilly
                    </option>

                    <option value="KG Rose">
                        KG Rose
                    </option>


                    <!-- Primary -->

                    <option value="Grade-1 Lilly">
                        Grade-1 Lilly
                    </option>

                    <option value="Grade-1 Rose">
                        Grade-1 Rose
                    </option>

                    <option value="Grade-2 Rose">
                        Grade-2 Rose
                    </option>

                    <option value="Grade-3 Rose">
                        Grade-3 Rose
                    </option>

                    <option value="Grade-4 Rose">
                        Grade-4 Rose
                    </option>

                    <option value="Grade-5 Rose">
                        Grade-5 Rose
                    </option>


                    <!-- Secondary -->

                    <option value="Grade-6 Rose">
                        Grade-6 Rose
                    </option>

                    <option value="Grade-7 Rose">
                        Grade-7 Rose
                    </option>

                    <option value="Grade-8 Rose">
                        Grade-8 Rose
                    </option>

                    <option value="Grade-9 Rose">
                        Grade-9 Rose
                    </option>

                    <option value="Grade-10 Rose">
                        Grade-10 Rose
                    </option>


                    <option value="RCL">
                        RCL
                    </option>

                </select>

            </div>


            <!-- Date of Birth -->
            <div class="mb-3">

                <label for="dob" class="form-label">
                    Date of Birth
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="dob"
                    name="dob"
                    placeholder="Enter date of birth (e.g., DD-MM-YYYY)"
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
                    value="<?= date('d M, Y') ?>"
                    placeholder="Enter date of admission (e.g., DD-MM-YYYY)"
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
                    placeholder="Enter fee amount"
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
                    placeholder="Enter contact number"
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
                    placeholder="Enter address"
                    required></textarea>

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
                        selected>

                        Active

                    </option>

                    <option value="Withdrawn">
                        Withdrawn
                    </option>

                    <option value="InActive">
                        InActive
                    </option>

                </select>

            </div>


            <!-- Submit -->
            <button
                type="submit"
                name="submit"
                class="btn btn-primary w-100">

                Add Student

            </button>

        </form>

    </div>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>