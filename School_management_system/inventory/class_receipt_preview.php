<?php
require 'core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: class_sale.php');
    exit;
}

$classId = (int)($_POST['class_id'] ?? 0);
$productIds = $_POST['product_id'] ?? [];
$quantities = $_POST['quantity'] ?? [];

if (empty($productIds) || !$classId) {
    die("Nothing to preview. Please go back and select a class and items.");
}

$class = getClass($classId);

$items = [];
$grandTotal = 0;
foreach ($productIds as $pid) {
    $pid = (int)$pid;
    $qty = (int)($quantities[$pid] ?? 0);
    if ($qty <= 0) {
        continue;
    }
    $product = getProduct($pid);
    if (!$product) {
        continue;
    }
    $lineTotal = $product['price'] * $qty;
    $grandTotal += $lineTotal;
    $items[] = [
        'product_id'   => $pid,
        'product_name' => $product['name'],
        'price'        => $product['price'],
        'quantity'     => $qty,
        'total'        => $lineTotal,
    ];
}

if (empty($items)) {
    die("No valid items to preview.");
}

$copies = ['Parent\'s Copy', 'School Copy'];
$previewDate = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Preview - <?= htmlspecialchars($class['name'] ?? '') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>

.receipt-copy {
    max-width: 850px;
    margin: 0 auto;
    position: relative;
}

.copy-label {
    position: absolute;
    top: 10px;
    right: 15px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #6c757d;
    border: 1px solid #6c757d;
    border-radius: 4px;
    padding: 1px 8px;
    font-size: 12px;
}

.cut-line {
    border: none;
    border-top: 1px dashed #999;
    margin: 15px 0;
    position: relative;
}

.cut-line::after {
    content: "\2702";
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: #f8f9fa;
    padding: 0 8px;
    color: #999;
    font-size: 14px;
}

.student-name-line {
    display: inline-block;
    min-width: 250px;
    border-bottom: 1px solid #333;
    margin-left: 6px;
}

.receipt-copy table.receipt-table th,
.receipt-copy table.receipt-table td {
    font-size: 16px;
    padding: 6px 10px;
    line-height: 1.2;
    vertical-align: middle;
}

.preview-banner {
    text-align: center;
    font-weight: 700;
    letter-spacing: 2px;
    color: #dc3545;
    border: 2px dashed #dc3545;
    padding: 4px;
    margin-bottom: 10px;
    border-radius: 6px;
}

@media print {
    @page { size: A4 portrait; margin: 6mm; }
    html, body {
        width: 100%; height: auto !important; margin: 0 !important; padding: 0 !important;
        background: #fff !important;
        -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }
    .no-print { display: none !important; }
    .container { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
    .receipts-wrapper { display: block !important; width: 100% !important; min-height: 0 !important; height: auto !important; margin: 0 !important; padding: 0 !important; }
    .receipt-copy {
        width: 100% !important; max-width: 100% !important; height: auto !important; min-height: 0 !important;
        margin: 0 !important; padding: 0 !important;
        border: 1px solid #000 !important; box-shadow: none !important;
        display: block !important;
        break-inside: avoid !important; page-break-inside: avoid !important;
    }
    .receipt-copy .card-body { padding: 12px 15px !important; }
    .receipt-copy h5 { font-size: 17px !important; margin-bottom: 0 !important; }
    .copy-label { top: 6px !important; right: 8px !important; font-size: 10px !important; padding: 1px 6px !important; }
    .receipt-copy .mb-2, .receipt-copy .mb-3 { margin-bottom: 5px !important; }
    .student-name-line { min-width: 550px; }
    .receipt-copy table.receipt-table { width: 100% !important; margin-bottom: 0 !important; }
    .receipt-copy table.receipt-table th,
    .receipt-copy table.receipt-table td { font-size: 14px !important; padding: 10px 16px !important; line-height: 1.1 !important; vertical-align: middle !important; }
    .cut-line { margin: 5px 10 !important; height: 7px !important; page-break-after: avoid !important; page-break-before: avoid !important; }
    .cut-line::after { top: -1px; !important; font-size: 12px !important; padding: 10 5px !important; }
    table, tr, td, th { break-inside: avoid !important; page-break-inside: avoid !important; }
    .preview-banner { border-color: #000 !important; color: #000 !important; }
}

.receipts-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 70vh;
}
</style>
</head>
<body class="bg-light">
<div class="no-print">
    <?php include 'navbar.php'; ?>
</div>

<div class="container py-2">

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="class_sale.php?class=<?= $classId ?>" class="btn btn-outline-secondary btn-sm">&larr; Back &amp; Edit</a>
        <div>
            <button onclick="window.print()" class="btn btn-outline-primary btn-sm">Print Preview</button>
            <button type="submit" form="confirmSaleForm" class="btn btn-success btn-sm">Confirm &amp; Complete Sale</button>
        </div>
    </div>

    <div class="preview-banner no-print">
        PREVIEW ONLY &mdash; Sale not yet saved. Print if needed, then confirm to complete the sale.
    </div>

    <div class="receipts-wrapper">
        <?php foreach ($copies as $i => $copyLabel): ?>
            <div class="card receipt-copy shadow-sm">
                <span class="copy-label"><?= htmlspecialchars($copyLabel) ?></span>
                <div class="card-body">

                    <div class="text-center mb-2">
                        <h5 class="mb-0">BK Academy</h5>
                        <div class="fw-bold"><?= htmlspecialchars($class['name'] ?? '') ?></div>
                    </div>


                    <div class="mb-2 small my-3">
                        <strong>Student Name:</strong>
                        <span class="student-name-line">&nbsp;</span>
                    </div>

                    <div class="mx-auto" style="max-width: 92%;">
                        <table class="table table-bordered mb-1 receipt-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
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
                                    <th class="text-end"><?= number_format($grandTotal, 2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <hr class="my-4">

            <?php if ($i === 0): ?>
                <hr class="cut-line">
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

</div>

<!-- Hidden form that actually commits the sale, submitted via the button above -->
<form id="confirmSaleForm" method="post" action="class_sale.php" class="no-print">
    <input type="hidden" name="class_id" value="<?= $classId ?>">
    <?php foreach ($items as $item): ?>
        <input type="hidden" name="product_id[]" value="<?= (int)$item['product_id'] ?>">
        <input type="hidden" name="quantity[<?= (int)$item['product_id'] ?>]" value="<?= (int)$item['quantity'] ?>">
    <?php endforeach; ?>
    <input type="hidden" name="complete_sale" value="1">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>