<?php 
include 'db.php'; 

$message = '';
$message_type = '';
$student_data = null;
$pending_fees = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id'] ?? '');

    if (isset($_POST['pay_fees'])) {
        $selected_fees = $_POST['fees_due'] ?? [];

        if (!empty($student_id) && !empty($selected_fees)) {
            $current_timestamp = date("Y-m-d H:i:s", strtotime("+3 hours"));

            $conn->begin_transaction();
            try {
                $update_stmt = $conn->prepare("UPDATE fees SET payment_status = 'Paid', payment_date = ? WHERE fee_id = ? AND student_id = ?");
                
                foreach ($selected_fees as $fee_id) {
                    $update_stmt->bind_param("sii", $current_timestamp, $fee_id, $student_id);
                    $update_stmt->execute();
                }
                
                $update_stmt->close();
                $conn->commit();
                
                $message = "Payment recorded successfully!";
                $message_type = "success";
                $student_id = '';
            } catch (Exception $e) {
                $conn->rollback();
                $message = "Error processing payment: " . $e->getMessage();
                $message_type = "danger";
            }
        } elseif (empty($selected_fees)) {
            $message = "Please select at least one fee item.";
            $message_type = "warning";
        }
    }

    if (!empty($student_id)) {
        $student_stmt = $conn->prepare("
            SELECT s.name, s.class, s.fees_applied, COUNT(f.fee_id) AS outstanding_months
            FROM students s
            LEFT JOIN fees f ON s.student_id = f.student_id AND f.payment_status = 'Pending'
            WHERE s.student_id = ? AND s.status = 'Active'
            GROUP BY s.student_id, s.name, s.class, s.fees_applied
        ");
        $student_stmt->bind_param("i", $student_id);
        $student_stmt->execute();
        $student_result = $student_stmt->get_result();

        if ($student_result->num_rows === 1) {
            $student_data = $student_result->fetch_assoc();
            $student_data['student_id'] = $student_id;
            $student_data['total_outstanding'] = $student_data['fees_applied'] * $student_data['outstanding_months'];

            $fee_stmt = $conn->prepare("SELECT fee_id, due_month, amount FROM fees WHERE student_id = ? AND payment_status = 'Pending' ORDER BY fee_id ASC");
            $fee_stmt->bind_param("i", $student_id);
            $fee_stmt->execute();
            $pending_fees = $fee_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $fee_stmt->close();
        } else {
            $message = "Active student with ID #{$student_id} was not found.";
            $message_type = "danger";
        }
        $student_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Student Fee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7fb;
            min-height: 100vh;
            font-size: 0.875rem; /* ~14px base font */
        }

        .fee-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: #ffffff;
            padding: 14px 16px;
        }

        .student-box {
            background: #f8fbff;
            border: 1px solid #dbe9ff;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .fee-item {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 6px 12px;
            background: #ffffff;
            transition: all 0.15s ease-in-out;
        }

        .fee-item:hover {
            border-color: #0d6efd;
            background-color: #f8fbff;
        }

        .amount-badge {
            background: #e7f1ff;
            color: #0d6efd;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .pay-btn {
            height: 40px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .outstanding-box {
            background: #fff3cd;
            color: #856404;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.825rem;
        }

        .section-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: #374151;
        }

        /* Input styling adjustments for smaller scale */
        .form-control-sm, .input-group-text-sm, .btn-sm-custom {
            font-size: 0.875rem;
            padding: 6px 10px;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type; ?> border-0 shadow-sm alert-dismissible fade show mb-3 py-2 px-3 small" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close py-2 px-3" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm fee-card">

                    <div class="card-header-custom text-center">
                        <h5 class="mb-0 fw-bold">Pay Student Fee</h5>
                        <p class="mb-0 opacity-75 small">Manage and clear pending fee payments</p>
                    </div>

                    <div class="card-body p-3">

                        <form method="POST">

                            <!-- Student ID Lookup -->
                            <div class="mb-3">
                                <label for="student_id" class="form-label fw-semibold mb-1 small">Student ID</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-secondary">🎓</span>
                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        id="student_id"
                                        name="student_id"
                                        placeholder="Enter student ID and press Enter"
                                        value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>"
                                        required>
                                    <button class="btn btn-outline-primary px-3 btn-sm-custom" type="submit">Lookup</button>
                                </div>
                            </div>

                            <!-- Student Info Section -->
                            <?php if ($student_data): ?>
                                <div class="mb-3">
                                    <div class="student-box">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    <?php echo htmlspecialchars($student_data['name']); ?>
                                                </div>
                                                <div class="text-muted small" style="font-size: 0.8rem;">
                                                    ID: <strong>#<?php echo htmlspecialchars($student_data['student_id']); ?></strong> | 
                                                    Class: <strong><?php echo htmlspecialchars($student_data['class']); ?></strong>
                                                </div>
                                            </div>

                                            <div class="outstanding-box">
                                                Due: Rs. <?php echo number_format($student_data['total_outstanding'], 2); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fee Checkboxes -->
                                <div class="mb-3">
                                    <label class="section-title mb-2 d-block">Select Due Fees</label>

                                    <?php if (!empty($pending_fees)): ?>
                                        <div class="d-flex flex-column gap-1">
                                            <?php foreach ($pending_fees as $fee): ?>
                                                <div class="fee-item">
                                                    <div class="form-check d-flex justify-content-between align-items-center mb-0 ps-0">
                                                        <div class="d-flex align-items-center">
                                                            <input 
                                                                class="form-check-input me-2 mt-0" 
                                                                type="checkbox" 
                                                                name="fees_due[]" 
                                                                value="<?php echo htmlspecialchars($fee['fee_id']); ?>" 
                                                                id="fee_<?php echo htmlspecialchars($fee['fee_id']); ?>">
                                                            
                                                            <label class="form-check-label fw-medium text-dark small" for="fee_<?php echo htmlspecialchars($fee['fee_id']); ?>">
                                                                <?php echo htmlspecialchars($fee['due_month']); ?>
                                                            </label>
                                                        </div>

                                                        <span class="amount-badge">
                                                            Rs. <?php echo number_format($fee['amount'], 2); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info border-0 shadow-sm mb-0 p-2 small">
                                            No outstanding fees found for this student.
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($pending_fees)): ?>
                                    <button type="submit" name="pay_fees" class="btn btn-primary w-100 pay-btn shadow-sm">
                                        Pay Selected Fees
                                    </button>
                                <?php endif; ?>

                            <?php endif; ?>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>