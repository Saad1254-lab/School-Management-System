<style>
/* Reduce table text size normally */
#printTable, #summaryTable {
    font-size: 12px;
}

/* Even smaller when printing */
@media print {
    #printTable, #summaryTable {
        font-size: 8px;
    }

    th, td {
        padding: 2px !important;
    }

    .no-print {
        display: none !important;
    }
}
</style>

<?php
include 'db.php';
include 'navbar.php';

/**
 * Fetch a month-by-month summary of ALL received (Paid) fees,
 * grouped by the actual payment_date (not due_month).
 * This answers "all months data according to data".
 */
function fetchMonthlySummary($conn)
{
    $sql = "SELECT DATE_FORMAT(payment_date, '%Y-%m') AS ym,
                   DATE_FORMAT(payment_date, '%M %Y') AS label,
                   COUNT(*) AS total_count,
                   SUM(amount) AS total_amount
            FROM fees
            WHERE payment_status = 'Paid' AND payment_date IS NOT NULL
            GROUP BY ym, label
            ORDER BY ym DESC";

    $result = $conn->query($sql);
    $summary = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $summary[] = $row;
        }
    }
    return $summary;
}

/**
 * Fetch every student/fee record whose payment was actually
 * RECEIVED (payment_date) within a given year-month, e.g. "2026-08".
 */
function fetchFeesReceivedForMonth($conn, $yearMonth)
{
    if (empty($yearMonth)) return [];

    $sql = "SELECT s.student_id, s.name, s.father_name, s.contact, s.class,
                   f.amount, f.due_month, f.payment_date
            FROM students s
            INNER JOIN fees f ON s.student_id = f.student_id
            WHERE f.payment_status = 'Paid'
              AND s.status = 'Active'
              AND DATE_FORMAT(f.payment_date, '%Y-%m') = ?
            ORDER BY s.class, f.payment_date";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param('s', $yearMonth);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    return $students;
}

// ---- Handle request ----
// Accept either a combined "YYYY-MM" value, or separate month+year selects.
$selected_ym = '';
if (!empty($_POST['year_month'])) {
    $selected_ym = $_POST['year_month']; // e.g. "2026-08"
} elseif (!empty($_POST['month']) && !empty($_POST['year'])) {
    $selected_ym = $_POST['year'] . '-' . str_pad($_POST['month'], 2, '0', STR_PAD_LEFT);
}

$monthly_summary = fetchMonthlySummary($conn);
$students = $selected_ym ? fetchFeesReceivedForMonth($conn, $selected_ym) : [];

$totalAmount = 0;
foreach ($students as $s) {
    $totalAmount += $s['amount'];
}
$totalCount = count($students);

// Human-readable label for the selected month, e.g. "August 2026"
$selected_label = $selected_ym ? date('F Y', strtotime($selected_ym . '-01')) : '';
?>

<div class="container mt-4">

    <!-- ===================== Month picker ===================== -->
    <form method="POST" class="mb-4">
        <div class="card">
            <div class="card-header">
                <strong>View Fees Received For a Month</strong>
            </div>
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Month</label>
                        <select name="month" class="form-select" required>
                            <option value="">-- Select Month --</option>
                            <?php
                            $monthNames = [
                                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                            ];
                            $selMonth = isset($_POST['month']) ? (int)$_POST['month'] : (int)date('n');
                            foreach ($monthNames as $num => $label):
                            ?>
                                <option value="<?= $num ?>" <?= $selMonth === $num ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control"
                               value="<?= htmlspecialchars($_POST['year'] ?? date('Y')) ?>"
                               min="2000" max="2100" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Show Fees Received</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- ===================== Selected month results ===================== -->
    <?php if ($selected_ym): ?>

        <?php if (!empty($students)): ?>
            <div class="card mb-3">
                <div class="card-body d-flex flex-wrap gap-4">
                    <div><strong>Month:</strong> <?= htmlspecialchars($selected_label) ?></div>
                    <div><strong>Fees Received:</strong> <?= $totalCount ?></div>
                    <div><strong>Total Amount:</strong> <?= number_format($totalAmount, 2) ?></div>
                </div>
            </div>

            <div class="mb-3 text-end no-print">
                <button onclick="printTable('printTable')" class="btn btn-success">Print</button>
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
                        <th>Payment Date</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index = 1; ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= $index++ ?></td>
                            <td><?= htmlspecialchars($student['student_id']) ?></td>
                            <td><?= htmlspecialchars($student['name']) ?></td>
                            <td><?= htmlspecialchars($student['father_name']) ?></td>
                            <td><?= htmlspecialchars($student['contact']) ?></td>
                            <td><?= htmlspecialchars($student['class']) ?></td>
                            <td><?= htmlspecialchars($student['due_month']) ?></td>
                            <td><?= htmlspecialchars($student['payment_date']) ?></td>
                            <td><?= number_format($student['amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-warning fw-bold">
                        <td colspan="8" class="text-end">Total</td>
                        <td><?= number_format($totalAmount, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning">
                No fees received for <?= htmlspecialchars($selected_label) ?>.
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <!-- ===================== All months summary ===================== -->
    <?php if (!empty($monthly_summary)): ?>
        <hr class="my-4">
        <h5>All Months Summary (Fees Received)</h5>

        <div class="mb-3 text-end no-print">
            <button onclick="printTable('summaryTable')" class="btn btn-success">Print Summary</button>
        </div>

        <table id="summaryTable" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>S. No</th>
                    <th>Month</th>
                    <th>Fees Received (Count)</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; $grandTotal = 0; $grandCount = 0; ?>
                <?php foreach ($monthly_summary as $row): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['label']) ?></td>
                        <td><?= (int)$row['total_count'] ?></td>
                        <td><?= number_format($row['total_amount'], 2) ?></td>
                    </tr>
                    <?php
                    $grandTotal += $row['total_amount'];
                    $grandCount += $row['total_count'];
                    ?>
                <?php endforeach; ?>
                <tr class="table-warning fw-bold">
                    <td colspan="2" class="text-end">Grand Total</td>
                    <td><?= $grandCount ?></td>
                    <td><?= number_format($grandTotal, 2) ?></td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<script>
function printTable(tableId) {
    var printContents = document.getElementById(tableId).outerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
</script>