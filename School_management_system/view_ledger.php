<?php
include 'db.php';
include_once("./navbar.php");

// Get student ID either from POST or GET
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['student_id'])) {
    $student_id = $_POST['student_id'];
} elseif (isset($_GET['id'])) {
    $student_id = $_GET['id'];
} else {
    $student_id = '';
}

$student_name = "";

if ($student_id) {

    // Fetch fee records
    $query = "SELECT * FROM fees WHERE student_id = ? ORDER BY fee_id ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch student
    $name_query = "SELECT * FROM students WHERE student_id = ?";
    $name_stmt = $conn->prepare($name_query);
    $name_stmt->bind_param("i", $student_id);
    $name_stmt->execute();
    $name_result = $name_stmt->get_result();

    if ($name_result->num_rows > 0) {
        $row = $name_result->fetch_assoc();
        $student_name = $row["name"];
    } else {
        echo "<p class='text-center'>No student found with this ID.</p>";
        exit;
    }

    // Total outstanding
    $total_query = "
        SELECT SUM(amount) AS total_amount 
        FROM fees 
        WHERE student_id = ? 
        AND payment_status = 'Pending'
    ";

    $total_stmt = $conn->prepare($total_query);
    $total_stmt->bind_param("i", $student_id);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result();

    $total_row = $total_result->fetch_assoc();
    $total_amount = $total_row['total_amount'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fees Records</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        th,
        td {
            font-weight: 500;
        }

        .select-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .print-selected-btn {
            display: none;
        }
    </style>
</head>

<body>

<div class="container mt-4">

    <!-- Student ID Form -->
    <form method="POST" class="row g-3 mb-4">

        <div class="col-auto">
            <input type="number"
                   name="student_id"
                   class="form-control"
                   placeholder="Enter Student ID"
                   required>
        </div>

        <div class="col-auto">
            <button type="submit" class="btn btn-primary">
                Search
            </button>
        </div>

    </form>


    <?php if ($student_id): ?>

        <h1 class="text-center">
            <?php echo htmlspecialchars($student_name); ?>
        </h1>

        <h2 class="text-center mb-4">
            Student ID:
            <?php echo htmlspecialchars($student_id); ?>
        </h2>

    <?php endif; ?>


    <?php if (!empty($student_id) && $result && $result->num_rows > 0): ?>

        <!-- Print Selected Button -->
        <div class="mb-3">

            <button type="button"
                    id="printSelectedBtn"
                    class="btn btn-success print-selected-btn"
                    onclick="printSelectedFees()">

                🖨 Print Selected

            </button>

            <button type="button"
                    class="btn btn-secondary"
                    onclick="selectAllFees()">

                Select All

            </button>

            <button type="button"
                    class="btn btn-outline-secondary"
                    onclick="unselectAllFees()">

                Unselect All

            </button>

        </div>


        <!-- Fee Form -->
        <form id="feePrintForm"
              method="POST"
              action="print_paid_voucher.php"
              target="_blank">

            <input type="hidden"
                   name="student_id"
                   value="<?php echo htmlspecialchars($student_id); ?>">


            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>Select</th>

                        <th>Payment Date</th>

                        <th>Month</th>

                        <th>Payment Status</th>

                        <th>Amount</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <?php

                        // Row color
                        $rowColor = '';

                        if ($row['payment_status'] == 'Pending') {

                            $rowColor =
                                'background-color:rgba(195, 0, 0, 0.77);';

                        } elseif ($row['payment_status'] == 'Paid') {

                            $rowColor =
                                'background-color:rgb(40, 138, 63);';
                        }

                        ?>


                        <tr style="<?php echo $rowColor; ?>">

                            <!-- Checkbox -->
                            <td class="text-white text-center">

                                <input type="checkbox"
                                       class="fee-checkbox select-checkbox"
                                       name="fee_ids[]"
                                       value="<?php echo $row['fee_id']; ?>"
                                       onchange="updatePrintButton()">

                            </td>


                            <!-- Payment Date -->
                            <td class="text-white">

                                <?php

                                echo date(
                                    "d, F, Y",
                                    strtotime($row['payment_date'])
                                );

                                echo " : ";

                                echo date(
                                    "h:i A",
                                    strtotime($row['payment_date'])
                                );

                                ?>

                            </td>


                            <!-- Month -->
                            <td class="text-white">

                                <?php
                                echo htmlspecialchars($row['due_month']);
                                ?>

                            </td>


                            <!-- Status -->
                            <td class="text-white">

                                <?php
                                echo htmlspecialchars(
                                    $row['payment_status']
                                );
                                ?>

                            </td>


                            <!-- Amount -->
                            <td class="text-white">

                                <?php
                                echo htmlspecialchars($row['amount']);
                                ?>

                            </td>


                            <!-- Actions -->
                            <td class="text-white">

                                <a href="edit_fees.php?id=<?php echo $row['fee_id']; ?>"
                                   class="btn btn-primary">

                                    Edit

                                </a>


                                <a href="delete_fees.php?id=<?php echo $row['fee_id']; ?>"
                                   class="mx-2 btn btn-dark"
                                   onclick="return confirm('Are you sure you want to delete this fee?');">

                                    Delete

                                </a>


                                <!-- Print button after Delete -->
                                <button type="button"
                                        class="btn btn-light"
                                        onclick="printSingleFee(<?php echo $row['fee_id']; ?>)">

                                    🖨 Print

                                </button>

                            </td>

                        </tr>

                    <?php endwhile; ?>


                    <!-- Total Outstanding -->
                    <tr>

                        <td colspan="4"
                            class="text-end">

                            <strong>
                                Total Outstanding
                            </strong>

                        </td>

                        <td>

                            <strong>
                                <?php
                                echo number_format(
                                    $total_amount,
                                    2
                                );
                                ?>
                            </strong>

                        </td>

                        <td></td>

                    </tr>

                </tbody>

            </table>

        </form>

    <?php elseif (!empty($student_id)): ?>

        <p class="text-center">
            No fee records found for this student.
        </p>

    <?php endif; ?>

</div>


<script>

// Update Print Selected button
function updatePrintButton() {

    const checkboxes =
        document.querySelectorAll('.fee-checkbox:checked');

    const button =
        document.getElementById('printSelectedBtn');

    if (checkboxes.length > 0) {

        button.style.display = 'inline-block';

        button.innerHTML =
            '🖨 Print Selected (' +
            checkboxes.length +
            ')';

    } else {

        button.style.display = 'none';

    }
}


// Print selected fees
function printSelectedFees() {

    const selected =
        document.querySelectorAll('.fee-checkbox:checked');

    if (selected.length === 0) {

        alert('Please select at least one fee.');

        return;
    }

    document.getElementById('feePrintForm').submit();
}


// Select all
function selectAllFees() {

    const checkboxes =
        document.querySelectorAll('.fee-checkbox');

    checkboxes.forEach(function (checkbox) {

        checkbox.checked = true;

    });

    updatePrintButton();
}


// Unselect all
function unselectAllFees() {

    const checkboxes =
        document.querySelectorAll('.fee-checkbox');

    checkboxes.forEach(function (checkbox) {

        checkbox.checked = false;

    });

    updatePrintButton();
}


// Print single fee
function printSingleFee(feeId) {

    const form =
        document.getElementById('feePrintForm');

    // Uncheck all
    const checkboxes =
        document.querySelectorAll('.fee-checkbox');

    checkboxes.forEach(function (checkbox) {

        checkbox.checked = false;

    });


    // Check selected fee
    const selected =
        document.querySelector(
            '.fee-checkbox[value="' + feeId + '"]'
        );

    if (selected) {

        selected.checked = true;

    }


    // Submit
    form.submit();

}

</script>

</body>
</html>