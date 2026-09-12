<?php
include 'db.php';

// Fetch outstanding students
function fetchOutstandingStudents($conn)
{
    $sql = "SELECT s.student_id, s.name, s.father_name, s.contact, s.class, f.amount, f.due_month, f.fee_id
    FROM students s
    INNER JOIN fees f ON s.student_id = f.student_id
    WHERE f.payment_status = 'Pending' AND s.status = 'Active'
    ORDER BY s.class ASC, s.name ASC, f.fee_id ASC";

    $stmt = $conn->prepare($sql);
    if (!$stmt)
        die("SQL error: " . $conn->error);

    $stmt->execute();
    $result = $stmt->get_result();
    $students = [];

    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['student_id']);

        if (!isset($students[$id])) {
            $students[$id] = [
                'student_id' => $id,
                'name' => htmlspecialchars($row['name']),
                'father_name' => htmlspecialchars($row['father_name']),
                'contact' => htmlspecialchars($row['contact']),
                'class' => htmlspecialchars($row['class']),
                'due_months' => [],
                'total_amount' => 0
            ];
        }

        $students[$id]['due_months'][] = [
            'fee_id' => $row['fee_id'],
            'due_month' => htmlspecialchars($row['due_month']),
            'amount' => $row['amount']
        ];
        $students[$id]['total_amount'] += $row['amount'];
    }

    return $students;
}

$outstanding_students = fetchOutstandingStudents($conn);
$conn->close();

// Total outstanding
$total_outstanding = array_sum(array_column($outstanding_students, 'total_amount'));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outstanding Students</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f6fa;
            font-size: 13px;
        }

        .page-title {
            font-size: 20px;
            font-weight: 600;
            color: #2f3542;
        }

        .search-bar input {
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 8px;
        }

        .custom-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            font-size: 12px;
        }

        .custom-table thead th {
            background: #1e3d7b;
            color: white;
            font-size: 12px;
            padding: 8px;
        }

        .custom-table tbody td {
            padding: 6px;
            font-size: 14.5px;
        }

        .badge-month {
            padding: 3px 6px;
            background: #1c7ed6;
            color: white;
            font-size: 10px;
            border-radius: 6px;
            margin-right: 5px;
            margin-bottom: 3px;
            display: inline-block;
        }

        .amount-high {
            background: #ff6b81;
            color: white;
            font-weight: bold;
        }

        .btn {
            font-size: 12px !important;
            padding: 4px 10px !important;
        }

        .badge-class {
            font-size: 10px;
            padding: 3px 6px;
        }

        .highlight-red {
            background-color: #FA8072 !important;
        }

        @media print {

            .nop,
            .navbar {
                display: none !important;
            }

            @page {
                size: A4 landscape;
            }

            th,
            td {
                font-size: 9px !important;
            }
        }
    </style>

    <script>
        function filterTable() {
            let value = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#studentsTable tbody tr");

            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(value) ? "" : "none";
            });
        }
    </script>

</head>

<body>

    <?php include './navbar.php'; ?>

    <div class="container-fluid py-3">

        <h2 class="text-center page-title nop">📘 Outstanding Students</h2>

        <div class="row my-3 nop">
            <div class="col-md-4 mx-auto">
                <input type="text" id="searchInput" class="form-control" placeholder="Search Student Name, ID, Class..."
                    onkeyup="filterTable()">
            </div>
        </div>

        <div class="text-center mb-3 nop">
            <button class="btn btn-primary" onclick="window.print()">🖨 Print</button>
        </div>

        <div class="table-responsive custom-table">
            <table class="table table-bordered" id="studentsTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Father's Name</th>
                        <th>Class</th>
                        <th>Contact</th>
                        <th>Total Amount</th>
                        <th>Due Months</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($outstanding_students)): ?>
                        <?php $serial = 1;
                        foreach ($outstanding_students as $student): ?>

                            <?php
                            $rowClass = "";
                            if ($student['total_amount'] > 5500) {
                                $rowClass = "highlight-red";
                            }
                            ?>

                            <tr class="<?php echo $rowClass; ?>">
                                <td><?= $serial++; ?></td>
                                <td><?= $student['student_id']; ?></td>
                                <td><?= $student['name']; ?></td>
                                <td><?= $student['father_name']; ?></td>
                                <td><span class="badge bg-secondary badge-class"><?= $student['class']; ?></span></td>
                                <td><?= $student['contact']; ?></td>

                                <td class="<?= $student['total_amount'] > 4800 ? 'amount-high' : '' ?>">
                                    <?= number_format($student['total_amount'], 2); ?>
                                </td>

                                <td>
                                    <?php
                                    usort($student['due_months'], fn($a, $b) => $a['fee_id'] - $b['fee_id']);
                                    foreach ($student['due_months'] as $due) {
                                        echo "<span class='badge-month'>{$due['due_month']}</span>";
                                    }
                                    ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                        <tr class="table-info fw-bold">
                            <td colspan="6" class="text-end">TOTAL OUTSTANDING</td>
                            <td colspan="2" class="text-danger text-center">
                                Rs. <?= number_format($total_outstanding, 2); ?>
                            </td>
                        </tr>

                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center p-3 text-muted">No Outstanding Fees 😊</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>

    </div>

</body>

</html>