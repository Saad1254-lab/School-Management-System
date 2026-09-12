<?php
require 'core.php';

$error = '';
$success = '';
$lastSaleId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productIds = $_POST['product_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    $items = [];
    foreach ($productIds as $i => $pid) {
        $qty = (int)($quantities[$i] ?? 0);
        if ($pid !== '' && $qty > 0) {
            $items[] = ['product_id' => (int)$pid, 'quantity' => $qty];
        }
    }

    if (empty($items)) {
        $error = "Add at least one product with a quantity.";
    } else {
        try {
            $lastSaleId = createSale($items);
            $success = "Sale completed successfully.";
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

$products = getProducts();
$saleDetails = $lastSaleId ? getSaleDetails($lastSaleId) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>New Sale - bkacademy</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">

    <h2 class="mb-4">New Sale</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success && $saleDetails): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?><br>
            Invoice: <strong><?= htmlspecialchars($saleDetails['invoice_no']) ?></strong>
            &mdash; Total: <strong><?= number_format($saleDetails['total'], 2) ?></strong>
            &mdash; <a href="receipt.php?id=<?= (int)$saleDetails['id'] ?>" target="_blank">View Receipt</a>
        </div>
    <?php endif; ?>

    <form method="post">
        <table class="table table-bordered bg-white" id="items-table">
            <thead>
                <tr>
                    <th style="width:50%">Product</th>
                    <th style="width:20%">Quantity</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="product_id[]" class="form-select" required>
                            <option value="">-- select product --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= (int)$p['id'] ?>">
                                    <?= htmlspecialchars($p['name']) ?>
                                    (stock: <?= (int)$p['stock'] ?>, price: <?= number_format($p['price'], 2) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <input type="number" name="quantity[]" class="form-control" min="1" value="1" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-row">Remove</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" id="add-row" class="btn btn-outline-secondary btn-sm mb-3">+ Add Item</button>
        <br>
        <button type="submit" class="btn btn-primary">Complete Sale</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('add-row').addEventListener('click', function () {
    const tbody = document.querySelector('#items-table tbody');
    const row = tbody.rows[0].cloneNode(true);
    row.querySelectorAll('select, input').forEach(el => {
        if (el.tagName === 'SELECT') el.selectedIndex = 0;
        if (el.tagName === 'INPUT') el.value = 1;
    });
    tbody.appendChild(row);
});

document.querySelector('#items-table').addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        const tbody = document.querySelector('#items-table tbody');
        if (tbody.rows.length > 1) {
            e.target.closest('tr').remove();
        }
    }
});
</script>
</body>
</html>