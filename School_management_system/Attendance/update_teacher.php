<?php
include_once("./connection.php");

if (isset($_GET['id'])) {
    $teacher_id = $_GET['id'];

    // Fetch the current data for the selected teacher
    $sql = "SELECT * FROM teachers WHERE teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
    $stmt->close();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $joining_date = $_POST['joining_date'];

        // Update query
        $sql = "UPDATE teachers SET name = ?, joining_date = ? WHERE teacher_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $joining_date, $teacher_id);

        if ($stmt->execute()) {
             echo "<script>
                    alert('Teacher updated successfully!');
                    window.location.href = 'view_teachers.php';
                  </script>";
        } else {
            echo "Error: " . $conn->error;
        }

        $stmt->close();
        $conn->close();
    }
} else {
    header("Location: view_teachers.php?error=Invalid+Request");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Teacher</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="text-center">Update Teacher Details</h4>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Teacher Name:</label>
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo $teacher['name']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="joining_date" class="form-label">Joining Date:</label>
                            <input type="date" name="joining_date" id="joining_date" class="form-control" value="<?php echo $teacher['joining_date']; ?>" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="view_teachers.php" class="btn btn-secondary">Back to Teacher List</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>


