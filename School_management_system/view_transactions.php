<?php
// Include the database connection
include 'db.php';

// Initialize variables
$start_date = "";
$end_date = "";
$transactions = [];
$error_message = "";

// Process the form when the submit button is clicked
if (isset($_POST['submit'])) {
    // Get and escape the start and end dates
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);

    // Validate date format and range
    $start_date_obj = DateTime::createFromFormat('Y-m-d', $start_date);
    $end_date_obj = DateTime::createFromFormat('Y-m-d', $end_date);

    if (!$start_date_obj || !$end_date_obj) {
        $error_message = "Invalid date format. Please use YYYY-MM-DD.";
    } elseif ($start_date_obj > $end_date_obj) {
        $error_message = "Start date cannot be after end date.";
    } else {
        // SQL query with prepared statement (including due_month)
        $sql = "SELECT f.fee_id, s.student_id, s.name, f.amount, f.payment_date, f.payment_status, f.due_month
                FROM fees f
                JOIN students s ON f.student_id = s.student_id
                WHERE f.payment_date BETWEEN ? AND ? ANd payment_status = 'paid'
                ORDER BY f.payment_date ASC, f.fee_id";  // Orders by payment_date in ascending order

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        $result = $stmt->get_result();

        // Group transactions by student_id
        while ($row = $result->fetch_assoc()) {
            // Extract student details
            $student_id = $row['student_id'];
            $student_name = $row['name'];
        
            // Check if the student already exists in the transactions array
            if (!isset($transactions[$student_id])) {
                // If not, create a new entry for the student
                $transactions[$student_id] = [
                    'student_id' => $student_id,
                    'name' => $student_name,
                    'fee_ids' => [],
                    'amounts' => [],
                    'payment_dates' => [],
                    'payment_statuses' => [],
                    'due_months' => [],
                    'total_amount' => 0 // To store the sum of amounts
                ];
            }
        
            // Append the data for the current transaction
            $transactions[$student_id]['fee_ids'][] = $row['fee_id'];
            $transactions[$student_id]['amounts'][] = $row['amount'];
            $transactions[$student_id]['payment_dates'][] = substr($row['payment_date'], 0, 20); // Extracts 'YYYY-MM-DD'
            $transactions[$student_id]['payment_statuses'][] = $row['payment_status'];
            $transactions[$student_id]['due_months'][] = $row['due_month'];
        
            // Sum the amount for the current student
            $transactions[$student_id]['total_amount'] += $row['amount'];
        }
        
        // Convert associative array to indexed array for sorting
        $transactions_array = array_values($transactions);
        
        // Sort by student_id
        usort($transactions_array, function ($a, $b) {
            return $a['student_id'] - $b['student_id'];
        });
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Fee Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
        }

        .print-button-container {
            text-align: right;
            margin-bottom: 10px;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .print-area,
            .print-area * {
                visibility: visible !important;
            }

            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                font-size: 10pt;
            }

            table {
                border-collapse: collapse;
                width: 100% !important;
                /* Ensure table takes full width */
            }

            th,
            td {
                border: 1px solid black !important;
                padding: 5px;
                text-align: left;
                /* Align text to the left in table cells */
            }
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            font-size: 10px;
            padding: 1px;
            line-height: 0.2;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2>View Fee Transactions</h2>

        <form method="POST" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>" required>
                </div>
                <div class="col-md-3">
                    <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($transactions)): ?>
            <div class="print-button-container">
                <button onclick="printTable()" class="btn btn-success">Print</button>
            </div>

            <div class="table-container print-area">
                <table class="table">
                    <thead>
                        <tr>
                            <th>S. NO</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Received</th>
                            <th>Particulars</th>
                            <th>Payment Date</th>
                           
                        </tr>
                    </thead>
                    <tbody>

                    <?php            
                    
                    $serial_no = 1; 
                    ?>
                        <?php foreach ($transactions as $student): ?>
                            <tr>
                                <td><?php echo $serial_no ?></td>
                                <td><?php echo $student['student_id']; ?></td>
                                <td><?php echo $student['name']; ?></td>
                                <td><?php echo number_format($student['total_amount'], 2); ?></td>
                                
                                <td><?php echo implode(", ", $student['due_months']); ?></td>
                                <td><?php echo $student['payment_dates'][0]; ?></td>
                            </tr>
                        <?php
                        $serial_no++;
                    
                    endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>

    <script>
        function printTable() {
            window.print();
        }
    </script>

</body>

</html>
