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
        .no-gutters { /* Custom style to remove table cell padding */
            margin-right: 0;
            margin-left: 0;
        }

        .no-gutters th,
        .no-gutters td {
            padding-right: 0.5rem; /* Reduced padding if needed */
            padding-left: 0.5rem;
        }

        .search-bar {
            margin-bottom: 20px;
            width: 100%; /* Make the container take full width */
        }

        #searchInput {
            width: 100%; /* Set the input width to 100% */
        }
         table {
            border-collapse: collapse;
            width: 100%;
        }
        th,td{
              font-size: 14px; /* Small font size */
              font-weight: bold;
            padding: 2px; /* Minimal padding */
            line-height: 2; /* Compact spacing */
            border: 1px solid #ddd; /* Optional: Cell borders */
            text-align: center;
        }
     table {
    border-collapse: collapse;
    width: 100%;
  }
  .table-striped tbody tr:nth-of-type(odd) {
    background-color: lightgreen; /* Light green shade */
}
  @media print {
            @page {
                size: A4 landscape;

            }

            .nop {
                display: none;
            }
            th,
        td {
            font-size: 10px;

        }
        

        }
  

    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container-fluid mt-5">
    <h2 class="nop text-center mb-3">Withdrawn Students</h2>

  

    <!-- Search input -->
    <div class="search-bar nop">
        <input type="text" id="searchInput" class="form-control" placeholder="Search by name, ID, or class" onkeyup="filterTable()">
    </div>
    <div class="print-button nop">
            <button class="btn btn-primary my-2" onclick="printTable()">Print</button>
        </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped no-gutters" id="studentsTable">
            <thead class="table-dark">
                <tr>
                    <th style="text-align:center;">S No</th>
                    <th style="text-align:center;">St ID</th>
                    <th style="text-align:center;">Name</th>
                    <th style="text-align:center;">Father Name</th>
                    <th style="text-align:center;">Class</th>
                    <th style="text-align:center;">DOB</th>
                    <th style="text-align:center;">DOA</th>
                    <th style="text-align:center;">Contact</th>
                    <th style="text-align:center;">Withdraw Date</th>
                    <th style="text-align:center;">Fees Applied</th>
                    <th style="text-align:center;" class="nop">Ledger</th>
                    <th style="text-align:center;" class="nop">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
         $sql = "SELECT s.student_id, s.name, s.father_name, s.dob, s.date_of_admission, s.contact, s.fees_applied, s.class, s.withdraw_date
         FROM students s
         WHERE s.status = 'Withdrawn' 
         ORDER BY s.class ASC"; // Corrected the order of WHERE and ORDER BY
 

                $result = $conn->query($sql);

               if ($result) {
    if ($result->num_rows > 0) {
        $i = 1; // Initialize serial number
        while ($row = $result->fetch_assoc()) { // Fixed the syntax for while loop
            $student_id = htmlspecialchars($row['student_id']);
            $name = htmlspecialchars($row['name']);
            $father_name = htmlspecialchars($row['father_name']);
            $class = htmlspecialchars($row['class']);
            $dob = htmlspecialchars($row['dob']);
            $date_of_admission = htmlspecialchars($row['date_of_admission']);
            $contact = htmlspecialchars($row['contact']);
            $fees_applied = htmlspecialchars($row['fees_applied']);
            $withdraw_date = htmlspecialchars($row['withdraw_date']);

            echo "<tr>
                    <td>{$i}</td>
                    <td>{$student_id}</td>
                    <td>{$name}</td>
                    <td>{$father_name}</td>
                    <td>{$class}</td>
                    <td>{$dob}</td>
                    <td>{$date_of_admission}</td>
                    <td>{$contact}</td>
                    <td>{$withdraw_date}</td>
                    <td>{$fees_applied}</td>
                    <td class='nop'>
                        <a href='view_ledger.php?id={$student_id}' class='btn btn-sm btn-primary'>Ledger</a>
                    </td>
                    <td class='nop'>
                        <a href='edit_student.php?id={$student_id}' class='btn btn-sm btn-success'>Edit</a>
                    </td>
                </tr>";

            $i++; // Increment serial number
        }
    } else {
        echo "<tr><td colspan='11' class='text-center'>No students found.</td></tr>";
    }
} else {
    echo "<tr><td colspan='11' class='text-center'>Error retrieving data: " . $conn->error . "</td></tr>";
}



                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // JavaScript for search functionality
    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('studentsTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const columns = rows[i].getElementsByTagName('td');
            let match = false;

            for (let j = 0; j < columns.length - 1; j++) {  // Excluding the 'Actions' column
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
