<?php
/**
 * core.php
 * Core database connection + functions for the bkacademy project.
 * Tables: products, sales, sale_items
 */

// Match PHP's clock to local time, so any PHP-generated dates line up
// with what MySQL stores below.
date_default_timezone_set('Asia/Karachi');

// ---------------------------------------------------------------
// DB CONNECTION
// ---------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'bkacademy');
define('DB_USER', 'root');
define('DB_PASS', '');

function getConnection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
            // Make MySQL's CURRENT_TIMESTAMP / NOW() use local (laptop) time
            // for this session, instead of the MySQL server's default (often UTC).
            $pdo->exec("SET time_zone = '+05:00'");
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}

// ---------------------------------------------------------------
// PRODUCTS
// ---------------------------------------------------------------

function getProducts() {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT * FROM products ORDER BY name ASC");
    return $stmt->fetchAll();
}

function getProduct($id) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function addProduct($name, $stock, $price) {
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        "INSERT INTO products (name, stock, price) VALUES (?, ?, ?)"
    );
    $stmt->execute([$name, $stock, $price]);
    return $pdo->lastInsertId();
}

function updateProduct($id, $name, $stock, $price) {
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        "UPDATE products SET name = ?, stock = ?, price = ? WHERE id = ?"
    );
    return $stmt->execute([$name, $stock, $price, $id]);
}

function deleteProduct($id) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * Adjust stock by a relative amount (positive to add, negative to subtract).
 * Throws if stock would go negative.
 */
function adjustStock($productId, $delta, PDO $pdo = null) {
    $ownConnection = ($pdo === null);
    $pdo = $pdo ?? getConnection();

    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        throw new Exception("Product #$productId not found.");
    }

    $newStock = $product['stock'] + $delta;
    if ($newStock < 0) {
        throw new Exception("Insufficient stock for product #$productId.");
    }

    $update = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
    $update->execute([$newStock, $productId]);

    return $newStock;
}

// ---------------------------------------------------------------
// SALES
// ---------------------------------------------------------------

/**
 * Generate a unique invoice number, e.g. INV-20260829-0001
 */
function generateInvoiceNumber() {
    $pdo = getConnection();
    $prefix = 'INV-' . date('Ymd') . '-';

    $stmt = $pdo->prepare(
        "SELECT invoice_no FROM sales WHERE invoice_no LIKE ? ORDER BY id DESC LIMIT 1"
    );
    $stmt->execute([$prefix . '%']);
    $last = $stmt->fetchColumn();

    $next = 1;
    if ($last) {
        $parts = explode('-', $last);
        $next = (int)end($parts) + 1;
    }

    return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
}

/**
 * Get all sales, most recent first.
 * Optionally filter by sale_date between $from and $to (format: 'YYYY-MM-DD').
 */
function getSales($from = null, $to = null) {
    $pdo = getConnection();

    if ($from && $to) {
        $stmt = $pdo->prepare(
            "SELECT * FROM sales WHERE sale_date BETWEEN ? AND ? ORDER BY sale_date DESC"
        );
        $stmt->execute([$from . ' 00:00:00', $to . ' 23:59:59']);
    } elseif ($from) {
        $stmt = $pdo->prepare(
            "SELECT * FROM sales WHERE sale_date >= ? ORDER BY sale_date DESC"
        );
        $stmt->execute([$from . ' 00:00:00']);
    } elseif ($to) {
        $stmt = $pdo->prepare(
            "SELECT * FROM sales WHERE sale_date <= ? ORDER BY sale_date DESC"
        );
        $stmt->execute([$to . ' 23:59:59']);
    } else {
        $stmt = $pdo->query("SELECT * FROM sales ORDER BY sale_date DESC");
    }

    return $stmt->fetchAll();
}

/**
 * Get a single sale plus its line items (with product names).
 */
function getSaleDetails($saleId) {
    $pdo = getConnection();

    $stmt = $pdo->prepare(
        "SELECT s.*, c.name AS class_name
         FROM sales s
         LEFT JOIN classes c ON c.id = s.class_id
         WHERE s.id = ?"
    );
    $stmt->execute([$saleId]);
    $sale = $stmt->fetch();

    if (!$sale) {
        return null;
    }

    $itemsStmt = $pdo->prepare(
        "SELECT si.*, p.name AS product_name
         FROM sale_items si
         JOIN products p ON p.id = si.product_id
         WHERE si.sale_id = ?"
    );
    $itemsStmt->execute([$saleId]);
    $sale['items'] = $itemsStmt->fetchAll();

    return $sale;
}

/**
 * Create a new sale with its line items in a single transaction.
 * Updates product stock and computes totals automatically.
 *
 * $items = [
 *   ['product_id' => 1, 'quantity' => 2],
 *   ['product_id' => 3, 'quantity' => 1],
 *   ...
 * ]
 *
 * Returns the new sale id.
 */
function createSale(array $items, $classId = null) {
    if (empty($items)) {
        throw new Exception("Cannot create a sale with no items.");
    }

    $pdo = getConnection();
    $pdo->beginTransaction();

    try {
        $invoiceNo = generateInvoiceNumber();
        $grandTotal = 0;
        $lineData = [];

        // Validate items, lock product rows, compute line totals
        foreach ($items as $item) {
            $productId = (int)$item['product_id'];
            $quantity  = (int)$item['quantity'];

            if ($quantity <= 0) {
                throw new Exception("Quantity must be greater than zero.");
            }

            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? FOR UPDATE");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if (!$product) {
                throw new Exception("Product #$productId not found.");
            }
            if ($product['stock'] < $quantity) {
                throw new Exception("Insufficient stock for '{$product['name']}'.");
            }

            $unitPrice  = $product['price'];
            $lineTotal  = round($unitPrice * $quantity, 2);
            $grandTotal += $lineTotal;

            $lineData[] = [
                'product_id' => $productId,
                'quantity'   => $quantity,
                'price'      => $unitPrice,
                'total'      => $lineTotal,
            ];
        }

        // Insert sale header
        $saleStmt = $pdo->prepare(
            "INSERT INTO sales (invoice_no, total, sale_date, class_id) VALUES (?, ?, NOW(), ?)"
        );
        $saleStmt->execute([$invoiceNo, $grandTotal, $classId ?: null]);
        $saleId = $pdo->lastInsertId();

        // Insert line items + decrement stock
        $itemStmt = $pdo->prepare(
            "INSERT INTO sale_items (sale_id, product_id, quantity, price, total)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stockStmt = $pdo->prepare(
            "UPDATE products SET stock = stock - ? WHERE id = ?"
        );

        foreach ($lineData as $line) {
            $itemStmt->execute([
                $saleId,
                $line['product_id'],
                $line['quantity'],
                $line['price'],
                $line['total'],
            ]);
            $stockStmt->execute([$line['quantity'], $line['product_id']]);
        }

        $pdo->commit();
        return $saleId;

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Simple sales summary between two dates (inclusive), e.g. for a dashboard.
 * $from / $to format: 'YYYY-MM-DD'
 */
function getSalesSummary($from, $to) {
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) AS total_sales, COALESCE(SUM(total), 0) AS total_revenue
         FROM sales
         WHERE sale_date BETWEEN ? AND ?"
    );
    $stmt->execute([$from . ' 00:00:00', $to . ' 23:59:59']);
    return $stmt->fetch();
}

/**
 * Today's sales summary, using MySQL's own CURDATE()/NOW() instead of PHP's
 * date() — avoids mismatches when the PHP and MySQL server timezones differ.
 */
function getTodaySummary() {
    $pdo = getConnection();
    $stmt = $pdo->query(
        "SELECT COUNT(*) AS total_sales, COALESCE(SUM(total), 0) AS total_revenue
         FROM sales
         WHERE DATE(sale_date) = CURDATE()"
    );
    return $stmt->fetch();
}

/**
 * Current month-to-date sales summary, using MySQL's own clock.
 */
function getMonthSummary() {
    $pdo = getConnection();
    $stmt = $pdo->query(
        "SELECT COUNT(*) AS total_sales, COALESCE(SUM(total), 0) AS total_revenue
         FROM sales
         WHERE YEAR(sale_date) = YEAR(CURDATE()) AND MONTH(sale_date) = MONTH(CURDATE())"
    );
    return $stmt->fetch();
}

// ---------------------------------------------------------------
// CLASSES (product kits per class, e.g. "Grade-4 Rose")
// ---------------------------------------------------------------

function getClasses() {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT * FROM classes ORDER BY name ASC");
    return $stmt->fetchAll();
}

function getClass($id) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function addClass($name) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("INSERT INTO classes (name) VALUES (?)");
    $stmt->execute([$name]);
    return $pdo->lastInsertId();
}

function updateClass($id, $name) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("UPDATE classes SET name = ? WHERE id = ?");
    return $stmt->execute([$name, $id]);
}

function deleteClass($id) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * Get the product list assigned to a class (its "kit"), each with the
 * preset quantity and the product's current price/name/stock.
 */
function getClassProducts($classId) {
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        "SELECT cp.product_id, cp.quantity, p.name, p.price, p.stock
         FROM class_products cp
         JOIN products p ON p.id = cp.product_id
         WHERE cp.class_id = ?
         ORDER BY p.name ASC"
    );
    $stmt->execute([$classId]);
    return $stmt->fetchAll();
}

/**
 * Replace the entire product list for a class in one go.
 * $productQuantities = [product_id => quantity, ...]
 */
function setClassProducts($classId, array $productQuantities) {
    $pdo = getConnection();
    $pdo->beginTransaction();

    try {
        $del = $pdo->prepare("DELETE FROM class_products WHERE class_id = ?");
        $del->execute([$classId]);

        $ins = $pdo->prepare(
            "INSERT INTO class_products (class_id, product_id, quantity) VALUES (?, ?, ?)"
        );

        foreach ($productQuantities as $productId => $quantity) {
            $quantity = (int)$quantity;
            if ($quantity > 0) {
                $ins->execute([$classId, (int)$productId, $quantity]);
            }
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}