<?php
require 'core.php';

$error = '';
$success = '';

// Add a new class
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_class'])) {
    $name = trim($_POST['class_name'] ?? '');
    if ($name === '') {
        $error = "Class name cannot be empty.";
    } else {
        try {
            addClass($name);
            $success = "Class added.";
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Delete a class
if (isset($_GET['delete'])) {
    try {
        deleteClass((int)$_GET['delete']);
        $success = "Class deleted.";
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Save product list for a class
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_products'])) {
    $classId = (int)$_POST['class_id'];
    $selectedProducts = $_POST['product_id'] ?? [];   // product ids that were checked
    $quantities = $_POST['quantity'] ?? [];            // product_id => quantity

    $productQuantities = [];
    foreach ($selectedProducts as $pid) {
        $productQuantities[$pid] = $quantities[$pid] ?? 1;
    }

    try {
        setClassProducts($classId, $productQuantities);
        $success = "Product list saved for class.";
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

$classes = getClasses();
$allProducts = getProducts();

// Which class are we currently editing the product list for?
$selectedClassId = (int)($_GET['class'] ?? ($_POST['class_id'] ?? ($classes[0]['id'] ?? 0)));
$assignedProducts = $selectedClassId ? getClassProducts($selectedClassId) : [];
$assignedMap = [];
foreach ($assignedProducts as $ap) {
    $assignedMap[$ap['product_id']] = $ap['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Classes - bkacademy</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">

    <h2 class="mb-4">Classes</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Add Class</h5>
                    <form method="post" class="d-flex gap-2">
                        <input type="text" name="class_name" class="form-control" placeholder="e.g. Grade-4 Rose" required>
                        <button type="submit" name="add_class" value="1" class="btn btn-primary">Add</button>
                    </form>
                </div>
            </div>

            <div class="list-group">
                <?php foreach ($classes as $c): ?>
                    <a href="classes.php?class=<?= (int)$c['id'] ?>"
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= $c['id'] == $selectedClassId ? 'active' : '' ?>">
                        <?= htmlspecialchars($c['name']) ?>
                        <a href="classes.php?delete=<?= (int)$c['id'] ?>"
                           class="btn btn-sm <?= $c['id'] == $selectedClassId ? 'btn-light' : 'btn-outline-danger' ?>"
                           onclick="return confirm('Delete this class and its product list?');">Delete</a>
                    </a>
                <?php endforeach; ?>
                <?php if (empty($classes)): ?>
                    <div class="text-muted p-2">No classes yet. Add one above.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-8">
            <?php if ($selectedClassId): ?>
                <?php $currentClass = getClass($selectedClassId); ?>
                <div class="card">
                    <div class="card-header">
                        Products for: <strong><?= htmlspecialchars($currentClass['name'] ?? '') ?></strong>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="class_id" value="<?= $selectedClassId ?>">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th style="width:10%"></th>
                                        <th>Product</th>
                                        <th style="width:20%">Price</th>
                                        <th style="width:20%">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allProducts as $p): ?>
                                        <?php $checked = isset($assignedMap[$p['id']]); ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input product-check"
                                                       name="product_id[]" value="<?= (int)$p['id'] ?>"
                                                       <?= $checked ? 'checked' : '' ?>>
                                            </td>
                                            <td><?= htmlspecialchars($p['name']) ?></td>
                                            <td><?= number_format($p['price'], 2) ?></td>
                                            <td>
                                                <input type="number" min="1"
                                                       name="quantity[<?= (int)$p['id'] ?>]"
                                                       class="form-control form-control-sm"
                                                       value="<?= $checked ? (int)$assignedMap[$p['id']] : 1 ?>">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($allProducts)): ?>
                                        <tr><td colspan="4" class="text-center text-muted">No products yet. Add some first.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <button type="submit" name="save_products" value="1" class="btn btn-primary">Save Product List</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-muted">Add a class to start assigning products.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>