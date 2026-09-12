<?php
require 'core.php';

$error = '';
$saleId = null;

$classes = getClasses();
$selectedClassId = (int)($_GET['class'] ?? ($_POST['class_id'] ?? 0));
$kitItems = $selectedClassId ? getClassProducts($selectedClassId) : [];

// Complete the sale directly using the class's saved kit, no editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_sale'])) {
    $classId = (int)$_POST['class_id'];
    $kit = getClassProducts($classId);

    $items = [];
    foreach ($kit as $k) {
        $items[] = ['product_id' => $k['product_id'], 'quantity' => $k['quantity']];
    }

    if (empty($items)) {
        $error = "This class has no products assigned yet.";
    } else {
        try {
            $saleId = createSale($items, $classId);
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

$grandTotal = 0;
foreach ($kitItems as $item) {
    $grandTotal += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quick Class Sale - bkacademy</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">

    <h2 class="mb-4">Quick Class Sale</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($saleId): ?>
        <div class="alert alert-success">
            Sale completed.
            <a href="class_receipt.php?id=<?= (int)$saleId ?>" target="_blank" class="btn btn-sm btn-primary ms-2">View / Print Receipt</a>
            <a href="quick_class_sale.php" class="btn btn-sm btn-outline-secondary ms-2">New Sale</a>
        </div>
    <?php else: ?>

        <form method="get" class="row g-2 align-items-end mb-3">
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
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="text-muted mb-2">
                            This class has <?= count($kitItems) ?> item(s) assigned, total
                            <strong><?= number_format($grandTotal, 2) ?></strong>.
                        </p>
                        <ul class="list-unstyled mb-0 small text-muted">
                            <?php foreach ($kitItems as $item): ?>
                                <li><?= htmlspecialchars($item['name']) ?> &times; <?= (int)$item['quantity'] ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <form method="post">
                    <input type="hidden" name="class_id" value="<?= $selectedClassId ?>">
                    <button type="submit" name="complete_sale" value="1" class="btn btn-primary btn-lg">
                        Complete Sale
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>