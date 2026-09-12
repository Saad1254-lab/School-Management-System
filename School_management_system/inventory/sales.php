<?php
require 'core.php';

$fromDate = $_GET['from'] ?? '';
$toDate   = $_GET['to'] ?? '';

$sales = getSales($fromDate ?: null, $toDate ?: null);
$grandTotal = array_sum(array_column($sales, 'total'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales History - bkacademy</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">

    <h2 class="mb-4">Sales History</h2>

    <form method="get" class="row g-2 align-items-end mb-4">
        <div class="col-auto">
            <label class="form-label mb-0">From</label>
            <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($fromDate) ?>">
        </div>
        <div class="col-auto">
            <label class="form-label mb-0">To</label>
            <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($toDate) ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
            <?php if ($fromDate || $toDate): ?>
                <a href="sales.php" class="btn btn-outline-secondary">Clear</a>
            <?php endif; ?>
        </div>
    </form>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>ID</th>
                <th>Invoice #</th>
                <th>Date</th>
                <th>Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sales as $s): ?>
                <tr>
                    <td><?= (int)$s['id'] ?></td>
                    <td><?= htmlspecialchars($s['invoice_no']) ?></td>
                    <td><?= htmlspecialchars($s['sale_date']) ?></td>
                    <td><?= number_format($s['total'], 2) ?></td>
                    <td>
                        <a href="receipt.php?id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-primary" target="_blank">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($sales)): ?>
                <tr><td colspan="5" class="text-center text-muted">No sales yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-end">
        <div class="card">
            <div class="card-body py-2 px-4">
                <span class="text-muted">Total of all sales:</span>
                <strong class="fs-5 ms-2"><?= number_format($grandTotal, 2) ?></strong>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>