<style>
/* Reduce table text size normally */
#printTable {
    font-size: 12px;
}

/* Even smaller when printing */
@media print {
    #printTable {
        font-size: 8px;
    }

    th, td {
        padding: 2px !important;
    }
}
</style>


<?php
include 'db.php';
include 'navbar.php';



// Fetch unique due months from fees table
function fetchDueMonths($conn)
{
    $sql = "SELECT DISTINCT due_month FROM fees WHERE payment_status = 'Paid' ORDER BY payment_date";
    $result = $conn->query($sql);

    $months = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $months[] = htmlspecialchars($row['due_month']);
        }
    }
    return $months;
}

// Fetch students with pending fees for selected months
function fetchStudentsByMonths($conn, $months)
{
    if (empty($months)) return [];

    $placeholders = implode(',', array_fill(0, count($months), '?'));
    $types = str_repeat('s', count($months));

    $sql = "SELECT s.student_id, s.name, s.father_name, s.contact, s.class, f.amount, f.due_month
            FROM students s
            INNER JOIN fees f ON s.student_id = f.student_id
            WHERE f.payment_status = 'Paid' 
            AND s.status = 'Active'
            AND f.due_month IN ($placeholders)
            ORDER BY s.class, f.due_month";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param($types, ...$months);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];

    while ($row = $result->fetch_assoc()) {
        $students[] = [
            'student_id' => $row['student_id'],
            'name' => $row['name'],
            'father_name' => $row['father_name'],
            'contact' => $row['contact'],
            'class' => $row['class'],
            'due_month' => $row['due_month'],
            'amount' => $row['amount']
        ];
    }

    return $students;
}

$selected_months = isset($_POST['months']) ? $_POST['months'] : [];
$students = fetchStudentsByMonths($conn, $selected_months);
$due_months = fetchDueMonths($conn);
?>

<div class="container mt-4">
    <form method="POST" class="mb-4">
        <div class="card">
            <div class="card-header">
                <strong>Select Due Months</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($due_months as $month): ?>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="months[]" value="<?= $month ?>" <?= in_array($month, $selected_months) ? 'checked' : '' ?>>
                                <label class="form-check-label"><?= $month ?></label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Generate Table</button>
            </div>
        </div>
    </form>

    <?php if (!empty($students)): ?>


           <div class="mb-3 text-end">
        <button onclick="printTable()" class="btn btn-success">Print</button>
    </div>

        
        <table id="printTable" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>S. No</th>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Father Name</th>
                    <th>Contact</th>
                    <th>Class</th>
                    <th>Due Month</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php $index = 1; ?>
                <?php $totalAmount = 0; ?>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= $index ?></td>
                        <td><?= htmlspecialchars($student['student_id']) ?></td>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                        <td><?= htmlspecialchars($student['father_name']) ?></td>
                        <td><?= htmlspecialchars($student['contact']) ?></td>
                        <td><?= htmlspecialchars($student['class']) ?></td>
                        <td><?= htmlspecialchars($student['due_month']) ?></td>
                        <td><?= number_format($student['amount'], 2) ?></td>
                    </tr>
                    <?php
                    $totalAmount += $student['amount'];
                    $index++;
                    ?>
                <?php endforeach; ?>

                <tr class="table-warning fw-bold">
                    <td colspan="7" class="text-end">Total</td>
                    <td><?= number_format($totalAmount, 2) ?></td>
                </tr>
            </tbody>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="alert alert-warning">No records found for selected months.</div>
    <?php endif; ?>
</div>

<script>
function printTable() {
    var printContents = document.getElementById("printTable").outerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
</script>