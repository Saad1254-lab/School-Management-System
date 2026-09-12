<?php
include_once("./connection.php");

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include_once("./navbar.php");

// Fetch teacher data
$sql = "SELECT * FROM teachers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Teachers</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById("teacherTable");
            const tr = table.getElementsByTagName("tr");
            
            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName("td");
                let found = false;
                for (let j = 0; j < td.length - 1; j++) { // Excluding Actions column
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                if (found) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="text-center">Teacher List</h4>
                </div>
                <div class="card-body">
                    <!-- Search input field -->
                    <div class="mb-3">
                        <input type="text" id="searchInput" class="form-control" onkeyup="filterTable()" placeholder="Search by name or joining date...">
                    </div>
                    <table id="teacherTable" class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Teacher ID</th>
                                <th>Teacher Name</th>
                                <th>Joining Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$row['teacher_id']}</td>
                                            <td>{$row['name']}</td>
                                            <td>{$row['joining_date']}</td>
                                            <td>
                                                <a href='update_teacher.php?id={$row['teacher_id']}' class='btn btn-warning btn-sm'>Update</a>
                                                <a href='delete_teacher.php?id={$row['teacher_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this teacher?\");'>Delete</a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No teachers found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php
$conn->close();
?>
