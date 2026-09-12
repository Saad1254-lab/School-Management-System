<?php
require 'core.php';

$error = '';
$saleId = null;

$classes = getClasses();
$selectedClassId = (int)($_GET['class'] ?? ($_POST['class_id'] ?? 0));
$kitItems = $selectedClassId ? getClassProducts($selectedClassId) : [];

// Complete the sale
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_sale'])) {
    $productIds = $_POST['product_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    $items = [];
    foreach ($productIds as $pid) {
        $qty = (int)($quantities[$pid] ?? 0);
        if ($qty > 0) {
            $items[] = ['product_id' => (int)$pid, 'quantity' => $qty];
        }
    }

    if (empty($items)) {
        $error = "This class has no products selected.";
    } else {
        try {
            $saleId = createSale($items, $selectedClassId);
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Class Sale - bkacademy</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">

    <h2 class="mb-4">Class Sale</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($saleId): ?>
        <div class="alert alert-success">
            Sale confirmed and saved for this class.
            <a href="class_receipt.php?id=<?= (int)$saleId ?>" target="_blank" class="btn btn-sm btn-primary ms-2">View / Print Final Receipt</a>
            <a href="class_sale.php" class="btn btn-sm btn-outline-secondary ms-2">New Class Sale</a>
        </div>
    <?php else: ?>

        <form method="get" class="row g-2 align-items-end mb-4">
            <div class="col-auto">
                <label class="form-label mb-0">Class</label>
                <select name="class" class="form-select" onchange="this.form.submit()">
                    <option value="">-- select class --</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= $c['id'] == $selectedClassId ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if ($selectedClassId): ?>
            <?php if (empty($kitItems)): ?>
                <div class="alert alert-warning">
                    No products assigned to this class yet.
                    <a href="classes.php?class=<?= $selectedClassId ?>">Assign products</a> first.
                </div>
            <?php else: ?>
                <form method="post" action="class_receipt_preview.php">
                    <input type="hidden" name="class_id" value="<?= $selectedClassId ?>">
                    <table class="table table-bordered bg-white">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th style="width:15%">Price</th>
                                <th style="width:15%">Quantity</th>
                                <th style="width:15%">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $grandTotal = 0; ?>
                            <?php foreach ($kitItems as $item): ?>
                                <?php $lineTotal = $item['price'] * $item['quantity']; $grandTotal += $lineTotal; ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($item['name']) ?>
                                        <input type="hidden" name="product_id[]" value="<?= (int)$item['product_id'] ?>">
                                    </td>
                                    <td><?= number_format($item['price'], 2) ?></td>
                                    <td>
                                        <input type="number" min="1"
                                               name="quantity[<?= (int)$item['product_id'] ?>]"
                                               class="form-control form-control-sm"
                                               value="<?= (int)$item['quantity'] ?>">
                                    </td>
                                    <td><?= number_format($lineTotal, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th><?= number_format($grandTotal, 2) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                    <button type="submit" class="btn btn-primary">Preview &amp; Print Receipt</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>