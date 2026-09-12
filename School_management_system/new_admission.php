<?php
include 'db.php';
include 'navbar.php';

// Check for database connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to safely fetch all active students
function fetchActiveStudents($conn)
{
    $sql = "SELECT s.student_id, s.name, s.father_name, s.dob, s.date_of_admission, s.contact, s.fees_applied, s.class
            FROM students s
            WHERE s.status = 'InActive' 
            ORDER BY s.class ASC, s.student_id ASC";

    $result = $conn->query($sql);

    $students = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    return $students;
}

$active_students = fetchActiveStudents($conn);
$conn->close();

// Calculate the total number of active students
$total_students = count($active_students);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧑‍🎓 Active Student Roster</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* --- Custom UI Styles --- */
        body {
            background-color: #f8f9fa;
        }

        .container-fluid {
            padding-top: 20px;
        }

        /* Control Card Styling */
        .controls-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
        }

        /* Table Styling */
        .table-custom {
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        .table-custom thead th {
            background-color: #17a2b8;
            color: white;
            font-weight: 600;
            vertical-align: middle;
            text-align: center;
            font-size: 0.9rem;
        }

        .table-custom tbody tr:hover {
            background-color: #e9ecef;
        }

        /* Class Group Header Styling */
        .class-header-row td {
            background-color: #343a40 !important;
            color: #ffffff;
            font-size: 1.5rem !important;
            font-weight: bold;
            text-align: center;
            padding: 10px 0;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Student Row Data */
        .table-custom td {
            font-size: 14px;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }



        /* Action buttons grouping */
        .action-group .btn {
            margin: 2px;
        }

        /* The manually injected header row, visible only in print */
        .print-repeat-header {
            display: none;
            /* Hide in screen view */
        }

        /* --- Print Styles --- */
        @media print {
            body {
                background-color: white;
            }

            /* Hides elements not needed for print */
            .nop,
            .navbar,
            .controls-card,
            .total-students-info {
                display: none !important;
            }

            /* Class, DOA, Fees Applied, and Actions columns will be hidden on print due to the 'nop' class */

            .table-custom thead {
                display: table-header-group;
            }

            /* Class header styling for print */
            .class-header-row td {
                /* Note: We now hide the class column, so colspan should be reduced by 2 for pure print-only efficiency, 
                   but keeping it at '10' for simplicity if the table structure is complex. Let's adjust to 8 (10 total columns - 2 hidden 'nop' columns in this row). 
                   Actually, let's keep it at 10 as colspan is for the merged header row. */
                background-color: #dee2e6 !important;
                color: #000 !important;
                font-size: 1.1rem !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                border-bottom: 2px solid #adb5bd !important;
                page-break-after: avoid;
            }

            /* The manually injected header row, visible only in print */
            .print-repeat-header {
                display: table-row;
                /* Show in print view */
            }

            .table-custom,
            .table-custom td,
            .table-custom th {
                border-color: #adb5bd !important;
            }

            .table-custom td,
            .table-custom th {
                font-size: 8px;
                padding: 1px 3px;
            }
        }
    </style>
</head>

<body>

    <?php
    // include 'navbar.php'; 
    ?>

    <div class="container-fluid">


        <div class="row nop justify-content-center mb-3">
            <div class="col-lg-8 col-md-10">
                <div class="p-3 bg-info text-white rounded total-students-info text-center shadow-sm">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i> Total New Admissions Students:
                        <span class="badge bg-light text-info fs-5"><?php echo $total_students; ?></span>
                    </h5>
                </div>
            </div>
        </div>

        <div class="row nop justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="controls-card mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8 mb-3 mb-md-0">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                                <input type="text" id="searchInput" class="form-control" placeholder="Search by name, ID, or class..." onkeyup="filterTable()">
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-custom table-hover" id="studentsTable">
                <thead class="nop">
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Father Name</th>
                        <th class='nop'>Class</th>
                        <th>DOB</th>
                        <th class="nop">DOA</th>
                        <th>Contact</th>
                        <th class='nop'>Fees Applied</th>
                        <th class="nop">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1; // Class-level serial number counter
                    $lastClass = ""; // Track previous class
                    $total_fees = 0;

                    // Column headers HTML snippet to be repeated after class headers
                    $column_headers_html = "
                        <tr class='print-repeat-header table-secondary'>
                            <th class='text-center'>#</th>
                            <th class='text-center' >ID</th>
                            <th class='text-center' >Name</th>
                            <th class='text-center' >Father Name</th>
                            <th class='nop'>Class</th>
                            <th class='text-center' >DOB</th>
                            <th class='nop'>DOA</th> <th class='text-center'>Contact</th>
                            <th  class='nop'>Fees Applied</th>
                            <th  class='nop'>Actions</th>
                        </tr>
                    ";

                    if (!empty($active_students)) {
                        foreach ($active_students as $row) {
                            $student_id = htmlspecialchars($row['student_id']);
                            $name = htmlspecialchars($row['name']);
                            $father_name = htmlspecialchars($row['father_name']);
                            $class = htmlspecialchars($row['class']);
                            $dob = htmlspecialchars($row['dob']);
                            $date_of_admission = htmlspecialchars($row['date_of_admission']);
                            $contact = htmlspecialchars($row['contact']);
                            $fees_applied = htmlspecialchars($row['fees_applied']);
                            $total_fees += (float)$row['fees_applied'];

                            // --- Class Group Header ---
                            if ($class !== $lastClass) {
                                // Reset the serial number counter
                                $i = 1;

                                // Output the Class Header row
                                echo "<tr class='class-header-row'>
                                        <td colspan='10'>
                                            <i class='fas fa-graduation-cap me-2'></i> CLASS: {$class} 
                                        </td>
                                    </tr>";

                                // Output the repeating TH row immediately after the class header (visible only on print)
                                echo $column_headers_html;

                                $lastClass = $class; // Update last class
                            }
                            // -------------------------

                            echo "<tr>
                                <td>{$i}</td>
                                <td class='text-center'>{$student_id}</td>
                                <td  class='text-center'>{$name}</td>
                                <td  class='text-center' >{$father_name}</td>
                                <td class='nop text-center'>{$class}</td> <td>{$dob}</td>
                                <td class='nop text-center'>{$date_of_admission}</td> <td>{$contact}</td>
                                <td class='nop text-center'>{$fees_applied}</td>
                                <td class='nop action-group text-cen'>
                                    <a href='view_ledger.php?id={$student_id}' class='btn btn-sm btn-primary' title='View Ledger'><i class='fas fa-receipt'></i></a>
                                    <a href='edit_student.php?id={$student_id}' class='btn btn-sm btn-warning text-dark' title='Edit Student'><i class='fas fa-edit'></i></a>
                                </td>
                            </tr>";
                          

                            $i++;
                        }
                          echo "
                          <tr class='table-success fw-bold nop'>
                     <td colspan='8' class='text-end'>GRAND TOTAL FEES</td>
                       <td class='nop text-center'>Rs. " . number_format($total_fees) . "</td>
                             <td></td>
                                    </tr>";
                    } else {
                        echo "<tr id='no-students-row'><td colspan='10' class='text-center p-4 text-muted'>No active students found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript for search functionality (Updated for robust header removal)
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase().trim();
            const table = document.getElementById('studentsTable');
            const rows = table.getElementsByTagName('tr');

            let currentHeader = null;
            let currentRepeatHeader = null;
            let headerHasVisibleRows = false;

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];

                if (row.parentElement.tagName === 'THEAD') continue;

                if (row.classList.contains('print-repeat-header')) {
                    row.style.display = 'none';
                    continue;
                }

                if (row.classList.contains('class-header-row')) {
                    if (currentHeader) {
                        currentHeader.style.display = headerHasVisibleRows ? '' : 'none';
                    }
                    currentHeader = row;
                    headerHasVisibleRows = false;
                    currentHeader.style.display = '';
                    continue;
                }

                const columns = row.getElementsByTagName('td');
                if (columns.length < 5) {
                    row.style.display = 'none';
                    continue;
                }

                let match = false;

                // 🔥 SEARCH IN ID, Name, Father, Class, AND Contact (index 7)
                const searchableColumns = [1, 2, 3, 4, 7];

                for (let col of searchableColumns) {
                    if (columns[col] && columns[col].textContent.toLowerCase().includes(filter)) {
                        match = true;
                        break;
                    }
                }

                if (match) {
                    row.style.display = '';
                    headerHasVisibleRows = true;
                    if (currentHeader) currentHeader.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }

            if (currentHeader) {
                currentHeader.style.display = headerHasVisibleRows ? '' : 'none';
            }

            if (filter === '') {
                for (let i = 0; i < rows.length; i++) {
                    rows[i].style.display = '';
                }
            }

            const noStudentsRow = document.getElementById('no-students-row');
            if (noStudentsRow) {
                let totalVisibleStudents = 0;
                for (let i = 0; i < rows.length; i++) {
                    if (!rows[i].classList.contains('class-header-row') &&
                        !rows[i].classList.contains('print-repeat-header') &&
                        rows[i].style.display !== 'none' &&
                        rows[i].id !== 'no-students-row' &&
                        rows[i].parentElement.tagName !== 'THEAD') {
                        totalVisibleStudents++;
                    }
                }
                noStudentsRow.style.display = (totalVisibleStudents === 0 && filter !== '') ? '' : 'none';
            }
        }
    </script>
</body>

</html>