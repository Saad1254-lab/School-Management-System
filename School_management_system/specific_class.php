<?php
include 'db.php'; // Database connection

// Check for database connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$totalFees = 0 ;
// Check if the form has been submitted and handle the POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchQuery = $_POST['search_query']; // Get the search query from the form input
    // Prepare the SQL query to avoid SQL injection
    $stmt = $conn->prepare("
    SELECT s.student_id, s.name, s.father_name, s.dob, s.date_of_admission, s.contact, s.fees_applied, s.class
    FROM students s
    WHERE s.status = 'Active' AND (
        s.class LIKE ? OR s.name LIKE ? OR s.student_id LIKE ?
    )
    ORDER BY s.class ASC
");

    
    // Bind the search query parameter to the prepared statement
    $searchTerm = "%" . $searchQuery . "%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm); // "s" means the parameter is a string

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
} else {
    // Default query if no search is performed
    $sql = "SELECT s.student_id, s.name, s.father_name, s.dob, s.date_of_admission, s.contact, s.fees_applied, s.class
    FROM students s
    WHERE s.status = 'Active' 
    ORDER BY s.class ASC"; // Corrected the order of WHERE and ORDER BY
    $result = $conn->query($sql);
}


///////// GET THE TOTAL FEES OF THAT SPECIFIC CLASS

// Prepare the SQL statement
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchQuery = $_POST['search_query'];
    $searchTerm = "%" . $searchQuery . "%";

    // Student search query
    $stmt = $conn->prepare("
        SELECT s.student_id, s.name, s.father_name, s.dob, s.date_of_admission, s.contact, s.fees_applied, s.class
        FROM students s
        WHERE s.status = 'Active' AND (
            CAST(s.class AS CHAR) LIKE ? OR s.name LIKE ? OR CAST(s.student_id AS CHAR) LIKE ?
        )
        ORDER BY s.class ASC
    ");
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    // Total fees query with proper casting + null safety
    $stmtTotal = $conn->prepare("
        SELECT IFNULL(SUM(fees_applied), 0) AS total_fees
        FROM students
        WHERE status = 'Active' AND (
            CAST(class AS CHAR) LIKE ? OR name LIKE ? OR CAST(student_id AS CHAR) LIKE ?
        )
    ");
    $stmtTotal->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmtTotal->execute();
    $resultTotal = $stmtTotal->get_result();

    $totalFees = 0;
    if ($resultTotal && $row = $resultTotal->fetch_assoc()) {
        $totalFees = $row['total_fees'];
    }
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

        th, td {
            font-size: 14px;
            font-weight: bold;
            padding: 2px;
            line-height: 2;
            border: 1px solid #ddd;
            text-align: center;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }
        /* Apply a light green background to odd rows */
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
    <h2 class="nop text-center mb-3">Students and Fee Status</h2>
    <div class="container-fluid nop">
  <div class="row ">
    <div class="col-12">
      <form action="specific_class.php" method="POST">
        <input type="text" class="form-control mb-2" name="search_query" placeholder="Search By Class" aria-label="Search student">
        <button type="submit" class="btn btn-primary w-100 my-2">Search</button>
      </form>
    </div>
  </div>
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
                    <th style="text-align:center;">Fees Applied</th>
                    <th style="text-align:center;" class="nop">Ledger</th>
                    <th style="text-align:center;" class="nop">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result) {
                    if ($result->num_rows > 0) {
                        $i = 1;
                        while ($row = $result->fetch_assoc()) {
                            $student_id = htmlspecialchars($row['student_id']);
                            $name = htmlspecialchars($row['name']);
                            $father_name = htmlspecialchars($row['father_name']);
                            $class = htmlspecialchars($row['class']);
                            $dob = htmlspecialchars($row['dob']);
                            $date_of_admission = htmlspecialchars($row['date_of_admission']);
                            $contact = htmlspecialchars($row['contact']);
                            $fees_applied = htmlspecialchars($row['fees_applied']);

                            echo "<tr>
                                    <td>{$i}</td>
                                    <td>{$student_id}</td>
                                    <td>{$name}</td>
                                    <td>{$father_name}</td>
                                    <td>{$class}</td>
                                    <td>{$dob}</td>
                                    <td>{$date_of_admission}</td>
                                    <td>{$contact}</td>
                                    <td>{$fees_applied}</td>
                                    <td class='nop'>
                                        <a href='view_ledger.php?id={$student_id}' class='btn btn-sm btn-primary'>Ledger</a>
                                    </td>
                                    <td class='nop'>
                                        <a href='edit_student.php?id={$student_id}' class='btn btn-sm btn-success'>Edit</a>
                                    </td>
                                </tr>";
                            $i++;
                        }
                    } else {
                        echo "<tr><td colspan='11' class='text-center'>No students found.</td></tr>";
                    }




                    
                    echo "<tr>
                    <td colspan='8' style='text-align:right; font-weight:bold;'>Total Fees</td>
                    <td style='font-weight:bold;'>" . number_format($totalFees) . "</td>
                    <td colspan='2'></td>
                  </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // For filtering the table (client-side search)
    function filterTable() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("studentsTable");
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td");
            let rowText = '';
            for (let j = 0; j < td.length; j++) {
                rowText += td[j].textContent || td[j].innerText;
            }
            if (rowText.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }

    // For print functionality
    function printTable() {
        window.print();
    }
</script>

</body>
</html>
