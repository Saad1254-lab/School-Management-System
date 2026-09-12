<?php
require 'core.php';

if (!isset($_GET['id'])) {
    die("No invoice specified.");
}

$sale = getSaleDetails((int)$_GET['id']);

if (!$sale) {
    die("Invoice not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receipt - <?= htmlspecialchars($sale['invoice_no']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    @media print {
        @page {
            margin: 5mm;
        }
        .no-print { display: none !important; }
        body {
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .container {
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
        }
        .receipt-box {
            max-width: 100% !important;
            margin: 0 !important;
        }
        .card.receipt-box {
            border: none !important;
            box-shadow: none !important;
        }
        .card-body {
            padding: 0 !important;
        }
        .mb-4, .mb-3 { margin-bottom: 0.5rem !important; }
        .mt-4 { margin-top: 0.5rem !important; }
        table { font-size: 12px; }
        h4 { font-size: 16px; margin-bottom: 0.15rem !important; }
    }
    .receipt-box {
        max-width: 650px;
        margin: 0 auto;
    }
    .paid-stamp {
        position: absolute;
        top: 15px;
        right: 25px;
        border: 3px solid #dc3545;
        color: #dc3545;
        font-weight: 700;
        font-size: 22px;
        text-transform: uppercase;
        letter-spacing: 2px;
        padding: 4px 14px;
        border-radius: 8px;
        transform: rotate(-15deg);
        opacity: 0.75;
        font-family: 'Courier New', monospace;
        pointer-events: none;
    }
    .card.receipt-box {
        position: relative;
        overflow: hidden;
    }
</style>
</head>
<body class="bg-light">
<div class="no-print">
    <?php include 'navbar.php'; ?>
</div>

<div class="container py-2">
    <div class="receipt-box">

        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <a href="sales.php" class="btn btn-outline-secondary btn-sm">&larr; Back to Sales History</a>
            <button onclick="window.print()" class="btn btn-primary btn-sm">Print Receipt</button>
        </div>

        <div class="card receipt-box shadow-sm">
            <div class="paid-stamp">Paid</div>
            <div class="card-body">

                <div class="text-center mb-4">
                    <h4 class="mb-0">BK Academy</h4>
                    <div class="text-muted">Sales Receipt</div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Invoice #:</strong><br>
                        <?= htmlspecialchars($sale['invoice_no']) ?>
                    </div>
                    <div class="col-6 text-end">
                        <strong>Date:</strong><br>
                        <?= htmlspecialchars($sale['sale_date']) ?>
                    </div>
                </div>

                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sale['items'] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td class="text-end"><?= (int)$item['quantity'] ?></td>
                                <td class="text-end"><?= number_format($item['price'], 2) ?></td>
                                <td class="text-end"><?= number_format($item['total'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Grand Total</th>
                            <th class="text-end"><?= number_format($sale['total'], 2) ?></th>
                        </tr>
                    </tfoot>
                </table>

                <div class="text-center text-muted mt-4">
                    Thank you for your purchase!
                </div>

            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" class="no-print"></script>
</body>
</html>