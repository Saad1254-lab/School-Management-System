<?php
// Include database connection
include 'db.php';

if (isset($_GET['id'])) {
    $fee_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Fetch fee details based on fee_id
    $sql = "SELECT * FROM fees WHERE fee_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $fee_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $fee = $result->fetch_assoc();
            $amount = $fee['amount'];
            $due_month = $fee['due_month'];
            $payment_status = $fee['payment_status'];
            $student_id = $fee['student_id'];  // Save student_id for redirect
        } else {
            $error_message = "Fee record not found.";
        }
        $stmt->close();
    } else {
        $error_message = "Error preparing statement: " . $conn->error;
    }
}

if (isset($_POST['submit'])) {
    // Get form input values and sanitize them
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $due_month = mysqli_real_escape_string($conn, $_POST['due_month']);
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);

    // Update query
    $sql = "UPDATE fees 
            SET amount = ?, due_month = ?, payment_status = ? 
            WHERE fee_id = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("dssi", $amount, $due_month, $payment_status, $fee_id);

        if ($stmt->execute()) {
            $success_message = "Fee record updated successfully!";
            header("Location: view_ledger.php?id=" . $student_id);
        } else {
            $error_message = "Error executing query: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Error preparing statement: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Fee Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include './navbar.php'; ?>
<div class="container mt-5">
    <h2 class="text-center mb-4">Edit Fee Record</h2>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php elseif (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($fee)): ?>
        <form action="" method="POST">
            <input type="hidden" name="fee_id" value="<?= htmlspecialchars($fee['fee_id']); ?>">
            <input type="hidden" name="student_id" value="<?= htmlspecialchars($fee['student_id']); ?>">

            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" step="0.01" name="amount" class="form-control" value="<?= htmlspecialchars($fee['amount']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="due_month" class="form-label">Due Month</label>
                <input type="text" name="due_month" class="form-control" value="<?= htmlspecialchars($fee['due_month']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="payment_status" class="form-label">Payment Status</label>
                <select name="payment_status" class="form-select" required>
                    <option value="Pending" <?= $fee['payment_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Paid" <?= $fee['payment_status'] == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                </select>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Update Fee</button>
        </form>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>
