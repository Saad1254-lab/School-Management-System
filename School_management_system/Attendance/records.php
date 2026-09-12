<?php
include_once("./connection.php");

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include_once("./navbar.php");

// Fetch attendance data
$sql = "SELECT attendance_records.*, teachers.name AS teacher_name 
        FROM attendance_records 
        INNER JOIN teachers ON attendance_records.teacher_id = teachers.teacher_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById("attendanceTable");
            const tr = table.getElementsByTagName("tr");
            
            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName("td");
                let found = false;
                for (let j = 0; j < td.length; j++) {
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

        function filterByDate() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const table = document.getElementById("attendanceTable");
            const tr = table.getElementsByTagName("tr");
            
            for (let i = 1; i < tr.length; i++) {
                const tdDate = tr[i].getElementsByTagName("td")[2]; // Date column index
                if (tdDate) {
                    const dateText = tdDate.textContent || tdDate.innerText;
                    const recordDate = new Date(dateText);
                    const start = new Date(startDate);
                    const end = new Date(endDate);

                    if ((startDate === "" || recordDate >= start) && (endDate === "" || recordDate <= end)) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
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
                    <h4 class="text-center">Attendance Records</h4>
                </div>
                <div class="card-body">
                    <!-- Date filter form -->
                    <form class="mb-3" onsubmit="filterByDate(); return false;">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="startDate" class="form-label">Start Date:</label>
                                <input type="date" id="startDate" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="endDate" class="form-label">End Date:</label>
                                <input type="date" id="endDate" class="form-control">
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>
                    
                    <!-- Search input field -->
                    <div class="mb-3">
                        <input type="text" id="searchInput" class="form-control" onkeyup="filterTable()" placeholder="Search for names, dates, or times..">
                    </div>

                    <table id="attendanceTable" class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Teacher Name</th>
                                <th>Date</th>
                                <th>Check-In Time</th>
                                <th>Check-Out Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$row['teacher_id']}</td>
                                            <td>{$row['teacher_name']}</td>
                                            <td>{$row['date']}</td>
                                            <td>{$row['check_in_time']}</td>
                                            <td>{$row['check_out_time']}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>No records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="mt-3 text-center">
                        <a href="index.php" class="btn btn-secondary">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php
$conn->close();
?>
