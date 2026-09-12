<?php
require 'core.php';

$error = '';
$success = '';
$editProduct = null;

// Handle add / update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $id    = $_POST['id'] ?? null;

    if ($name === '' || $price < 0 || $stock < 0) {
        $error = "Please enter a valid name, stock and price.";
    } else {
        try {
            if ($id) {
                updateProduct((int)$id, $name, $stock, $price);
                $success = "Product updated.";
            } else {
                addProduct($name, $stock, $price);
                $success = "Product added.";
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    try {
        deleteProduct((int)$_GET['delete']);
        $success = "Product deleted.";
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle edit (load into form)
if (isset($_GET['edit'])) {
    $editProduct = getProduct((int)$_GET['edit']);
}

$products = getProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Products - bkacademy</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">

    <h2 class="mb-4">Products</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= $editProduct ? 'Edit Product' : 'Add Product' ?></h5>
                    <form method="post">
                        <?php if ($editProduct): ?>
                            <input type="hidden" name="id" value="<?= (int)$editProduct['id'] ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required
                                   value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" min="0" required
                                   value="<?= htmlspecialchars($editProduct['stock'] ?? 0) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" min="0" required
                                   value="<?= htmlspecialchars($editProduct['price'] ?? 0) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <?= $editProduct ? 'Update' : 'Add' ?> Product
                        </button>
                        <?php if ($editProduct): ?>
                            <a href="products.php" class="btn btn-link">Cancel</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <table class="table table-bordered bg-white">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= (int)$p['id'] ?></td>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= (int)$p['stock'] ?></td>
                            <td><?= number_format($p['price'], 2) ?></td>
                            <td>
                                <a href="products.php?edit=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="products.php?delete=<?= (int)$p['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this product?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="5" class="text-center text-muted">No products yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>