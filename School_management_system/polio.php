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
    <title>Students and Fee Status</title>
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
        <h2 class="nop text-center mb-3">Active Students</h2>

        <!-- Search input -->
        <div class="search-bar nop">
            <input type="text" id="searchInput" class="form-control" style="border: 1px solid #000;" placeholder="Search by name, ID, or class" onkeyup="filterTable()">
        </div>
        <div class="print-button nop">
            <button class="btn btn-primary my-2" onclick="printTable()">Print</button>
        </div>

        <div class="table-responsive">
            <h3 class="text-center mb-3">For Polio</h3>
            <table class="table table-bordered table-striped no-gutters" id="studentsTable">
                <thead class="table-dark">
                    <tr>
                        <th style="text-align:center;">S No</th>
                        <th style="text-align:center;">St ID</th>
                        <th style="text-align:center;">Name</th>
                        <th style="text-align:center;">Father Name</th>
                        <th style="text-align:center;" class='nop'>Class</th>
                        <th style="text-align:center;">DOB</th>
                        <th class='nop' style="text-align:center;">DOA</th>
                        <th style="text-align:center;" class='nop'>Contact</th>
                        <th style="text-align:center;">Age</th>
                        <th style="text-align:center;" class="nop">Ledger</th>
                        <th style="text-align:center;" class="nop">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT s.student_id, s.name, s.father_name, s.dob, s.date_of_admission, s.contact, s.fees_applied, s.class
                            FROM students s
                            WHERE s.status = 'Active' 
                            ORDER BY s.class ASC";

                    $result = $conn->query($sql);

                    if ($result) {
                        if ($result->num_rows > 0) {
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

                                // --- Calculate age ---
                                $dobDate = DateTime::createFromFormat('d M, Y', $dob);
                                $today = new DateTime();
                                $ageInterval = $today->diff($dobDate);

                                // Add 1 extra month for accuracy
                                $months = $ageInterval->m + 1;
                                if ($months > 11) {
                                    $months = 0;
                                    $years = $ageInterval->y + 1;
                                } else {
                                    $years = $ageInterval->y;
                                }
                                $age = $years + ($months / 12); // age in decimal
                                // -------------------

                                // --- Only display students under 5 years ---
                                if ($age < 5) {
                                    // When class changes, add full-width class row
                                    if ($class !== $lastClass) {
                                        echo "<tr>
                    <td colspan='12' style='background:#e9ecef; 
                        font-weight:bold; 
                        font-size:22px; 
                        padding:10px; 
                        text-align:center; 
                        letter-spacing:1px;'>{$class}</td>
                  </tr>";
                                        $lastClass = $class;
                                    }

                                    echo "<tr>
                <td>{$i}</td>
                <td>{$student_id}</td>
                <td>{$name}</td>
                <td>{$father_name}</td>
                <td class='nop'>{$class}</td>
                <td>{$dob}</td>
                <td class='nop'>{$date_of_admission}</td>
                <td class='nop'>{$contact}</td>
                <td>" . floor($age) . "." . $months . "</td>
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
                            echo "<tr><td colspan='12' class='text-center'>No students found.</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='12' class='text-center'>Error retrieving data: " . $conn->error . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('studentsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const columns = rows[i].getElementsByTagName('td');
                let match = false;

                for (let j = 0; j < columns.length - 1; j++) {
                    if (columns[j].textContent.toLowerCase().includes(filter)) {
                        match = true;
                        break;
                    }
                }

                rows[i].style.display = match ? '' : 'none';
            }
        }

        function printTable() {
            window.print();
        }
    </script>
</body>

</html>