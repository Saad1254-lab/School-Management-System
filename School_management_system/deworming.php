<?php
include 'db.php';

// Check for database connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Above 5 Years</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .no-gutters {
            margin-right: 0;
            margin-left: 0;
        }

        .no-gutters th,
        .no-gutters td {
            padding-right: 0.5rem;
            padding-left: 0.5rem;
        }

        .search-bar {
            margin-bottom: 20px;
            width: 100%;
        }

        #searchInput {
            width: 100%;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            font-size: 13px;
            font-weight: bold;
            padding: 2px;
            line-height: 2;
            border: 1px solid #ddd;
            text-align: center;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: lightgreen;
        }

        @media print {
            @page {
                size: A4 portrait;
            }

            .nop {
                display: none;
            }

            th,
            td {
                font-size: 9px;
                padding: 0.5px 1px;
                line-height: 0.1;
            }

            thead {
                display: table-row-group !important;
            }
        }
    </style>
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container-fluid mt-5">

   

    <!-- Search -->
    <div class="search-bar nop">
        <input type="text" id="searchInput" class="form-control"
               style="border:1px solid #000;"
               placeholder="Search by name, ID, or class"
               onkeyup="filterTable()">
    </div>

    <div class="print-button nop">
        <button class="btn btn-primary my-2" onclick="printTable()">Print</button>
    </div>

    <div class="table-responsive">
        <h3 class="text-center mb-3">For Deworming Tablets (Age: Above 5 )</h3>

        <table class="table table-bordered table-striped no-gutters" id="studentsTable">

            <thead class="table-dark">
            <tr>
                <th>S No</th>
                <th>St ID</th>
                <th>Name</th>
                <th>Father Name</th>
                <th class="nop">Class</th>
                <th class="nop">DOB</th>
                <th class="nop">DOA</th>
                <th >Contact</th>
                <th>Age</th>
                <th class="nop">Ledger</th>
                <th class="nop">Actions</th>
            </tr>
            </thead>

            <tbody>
            <?php
            $sql = "SELECT student_id, name, father_name, dob, date_of_admission, contact, class
                    FROM students
                    WHERE status = 'Active'
                    ORDER BY class ASC";

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {

                $i = 1;
                $lastClass = "";

                while ($row = $result->fetch_assoc()) {

                    $student_id = htmlspecialchars($row['student_id']);
                    $name = htmlspecialchars($row['name']);
                    $father_name = htmlspecialchars($row['father_name']);
                    $class = htmlspecialchars($row['class']);
                    $dob = htmlspecialchars($row['dob']);
                    $date_of_admission = htmlspecialchars($row['date_of_admission']);
                    $contact = htmlspecialchars($row['contact']);

                    // AGE CALCULATION
                    $dobDate = DateTime::createFromFormat('d M, Y', $dob);
                    if (!$dobDate) continue;

                    $today = new DateTime();
                    $diff = $today->diff($dobDate);

                    $months = $diff->m + 1;
                    $years = $diff->y;

                    if ($months > 11) {
                        $months = 0;
                        $years++;
                    }

                    $age = $years + ($months / 12);

                    // ✅ SHOW ONLY AGE GREATER THAN 5
                    if ($age > 5 ) {

                        if ($class !== $lastClass) {
                            echo "<tr>
                                <td colspan='11'
                                    style='background:#e9ecef;font-weight:bold;
                                    font-size:22px;padding:10px;text-align:center;'>
                                    {$class}
                                </td>
                            </tr>";
                            $lastClass = $class;
                        }

                        echo "<tr>
                            <td>{$i}</td>
                            <td>{$student_id}</td>
                            <td>{$name}</td>
                            <td>{$father_name}</td>
                            <td class='nop'>{$class}</td>
                            <td class='nop'>{$dob}</td>
                            <td class='nop'>{$date_of_admission}</td>
                            <td>{$contact}</td>
                            <td>{$years}.{$months}</td>
                            <td class='nop'>
                                <a href='view_ledger.php?id={$student_id}' class='btn btn-sm btn-primary'>Ledger</a>
                            </td>
                            <td class='nop'>
                                <a href='edit_student.php?id={$student_id}' class='btn btn-sm btn-success'>Edit</a>
                            </td>
                        </tr>";

                        $i++;
                    }
                }

            } else {
                echo "<tr><td colspan='11' class='text-center'>No students found</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function filterTable() {
        let input = document.getElementById("searchInput").value.toLowerCase();
        let rows = document.querySelectorAll("#studentsTable tbody tr");

        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
        });
    }

    function printTable() {
        window.print();
    }
</script>

</body>
</html>
