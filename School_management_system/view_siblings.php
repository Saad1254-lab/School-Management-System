<?php
include_once("db.php");
include_once("navbar.php");
?>

<!-- Include Bootstrap CSS (if not already included in navbar.php) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container-fluid">
    <?php
    if (isset($_GET["fname"])) {
        $father_name = $_GET["fname"];
        
        $student_name = $_GET["Sname"];
        
        // Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM students WHERE father_name Like ? AND status = 'Active'");
        $stmt->bind_param("s", $father_name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo '<div class="card shadow">';
echo '<div class="card-header bg-primary text-white text-center"><h4>Siblings for ' . htmlspecialchars($student_name) . '</h4></div>';
            echo '<div class="card-body">';
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered table-striped">';
            echo '<thead class="table-dark">
                    <tr>
                        <th>S No</th>
                        <th>St ID</th>
                        <th>Name</th>
                        <th>Father Name</th>
                        <th>Class</th>
                        <th>DOB</th>
                        <th>DOA</th>
                        <th>Contact</th>
                        <th>Fees Applied</th>
                        <th>Ledger</th>
                        <th>Siblings</th>
                        <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>';

            $sno = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $sno++ . "</td>";
                echo "<td>" . htmlspecialchars($row["student_id"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["father_name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["class"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["dob"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["date_of_admission"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["contact"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["fees_applied"]) . "</td>";
                  echo "   <td class='nop'>
                    <a href='view_ledger.php?id={$row["student_id"]}' class='btn btn-sm btn-primary'>Ledger</a>
                    </td>
                  <td class='nop'>
        <a href='view_siblings.php?fname=" . urlencode($row["father_name"]) . "&contact=" . urlencode($row["contact"]) . "' class='btn btn-sm btn-primary'>View</a>
      </td>
    
                
                    <td class='nop'>
                        <a href='edit_student.php?id={$row["student_id"]}' class='btn btn-sm btn-success'>Edit</a>
       
                    </td>";
            }

            echo '</tbody></table></div></div></div>';
        } else {
            echo '<div class="alert alert-warning">No student found with father\'s name: <strong>' . htmlspecialchars($father_name) . '</strong></div>';
        }

        $stmt->close();
    } else {
        echo '<div class="alert alert-danger">No father name provided.</div>';
    }
    ?>
</div>
