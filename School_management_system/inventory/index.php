```php
<?php
session_start();
require 'core.php';

/*
|--------------------------------------------------------------------------
| DASHBOARD PASSWORD PROTECTION
|--------------------------------------------------------------------------
| Change this password to whatever you want.
*/
$dashboardPassword = '1234';

// Logout / lock dashboard
if (isset($_GET['lock'])) {
    unset($_SESSION['dashboard_authenticated']);
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Handle password submission
$passwordError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dashboard_password'])) {

    $enteredPassword = $_POST['dashboard_password'] ?? '';

    if (hash_equals($dashboardPassword, $enteredPassword)) {

        $_SESSION['dashboard_authenticated'] = true;

        // Prevent form resubmission
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit;

    } else {
        $passwordError = 'Incorrect password.';
    }
}

// Check authentication
$isAuthenticated = !empty($_SESSION['dashboard_authenticated']);


/*
|--------------------------------------------------------------------------
| DASHBOARD DATA
|--------------------------------------------------------------------------
| Only load dashboard data after authentication.
*/
if ($isAuthenticated) {

    $products = getProducts();
    $totalProducts = count($products);

    $lowStock = array_filter(
        $products,
        fn($p) => $p['stock'] <= 5
    );

    $todaySummary = getTodaySummary();

    $monthSummary = getMonthSummary();

    $recentSales = array_slice(getSales(), 0, 5);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard - bkacademy</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

<style>

body {
    background: #f8f9fa;
}

/* Password screen */
.password-screen {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.password-card {
    width: 100%;
    max-width: 400px;
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.10);
}

.password-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: #0d6efd;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 30px;
}

</style>

</head>

<body>

<?php if (!$isAuthenticated): ?>

<!-- ==========================================================
     PASSWORD SCREEN
========================================================== -->

<div class="password-screen">

    <div class="card password-card">

        <div class="card-body p-4">

            <div class="password-icon">
                🔒
            </div>

            <h4 class="text-center mb-2">
                Dashboard Locked
            </h4>

            <p class="text-center text-muted mb-4">
                Enter password to access the dashboard.
            </p>

            <?php if ($passwordError): ?>

                <div class="alert alert-danger text-center">
                    <?= htmlspecialchars($passwordError) ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">

                    <label class="form-label">
                        Password
                    </label>

                    <input
                        type="password"
                        name="dashboard_password"
                        class="form-control form-control-lg"
                        placeholder="Enter password"
                        required
                        autofocus
                    >

                </div>

                <button
                    type="submit"
                    class="btn btn-primary btn-lg w-100"
                >
                    Unlock Dashboard
                </button>

            </form>

        </div>

    </div>

</div>


<?php else: ?>

<!-- ==========================================================
     DASHBOARD
========================================================== -->

<?php include 'navbar.php'; ?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="mb-0">
            Dashboard
        </h2>

        <a
            href="?lock=1"
            class="btn btn-outline-danger btn-sm"
        >
            🔒 Lock Dashboard
        </a>

    </div>


    <!-- ======================================================
         QUICK STATS
    ======================================================= -->

    <div class="row g-3 mb-4">

        <div class="col-md-3">

            <div class="card text-bg-primary h-100">

                <div class="card-body">

                    <div class="text-uppercase small">
                        Today's Sales
                    </div>

                    <div class="fs-3">
                        <?= number_format(
                            $todaySummary['total_revenue'],
                            2
                        ) ?>
                    </div>

                    <div class="small">
                        <?= (int)$todaySummary['total_sales'] ?>
                        transactions
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card text-bg-success h-100">

                <div class="card-body">

                    <div class="text-uppercase small">
                        This Month
                    </div>

                    <div class="fs-3">
                        <?= number_format(
                            $monthSummary['total_revenue'],
                            2
                        ) ?>
                    </div>

                    <div class="small">
                        <?= (int)$monthSummary['total_sales'] ?>
                        transactions
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card text-bg-secondary h-100">

                <div class="card-body">

                    <div class="text-uppercase small">
                        Total Products
                    </div>

                    <div class="fs-3">
                        <?= $totalProducts ?>
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card
                <?= count($lowStock)
                    ? 'text-bg-danger'
                    : 'text-bg-secondary' ?>
                h-100">

                <div class="card-body">

                    <div class="text-uppercase small">
                        Low Stock (&le;5)
                    </div>

                    <div class="fs-3">
                        <?= count($lowStock) ?>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ======================================================
         RECENT SALES + LOW STOCK
    ======================================================= -->

    <div class="row g-3">

        <!-- Recent Sales -->

        <div class="col-md-7">

            <div class="card">

                <div class="card-header
                    d-flex
                    justify-content-between
                    align-items-center">

                    <span>
                        Recent Sales
                    </span>

                    <a
                        href="sales.php"
                        class="btn btn-sm btn-outline-primary"
                    >
                        View all
                    </a>

                </div>


                <table class="table mb-0">

                    <thead>

                        <tr>

                            <th>
                                Invoice #
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Total
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($recentSales as $s): ?>

                            <tr>

                                <td>

                                    <a
                                        href="receipt.php?id=<?= (int)$s['id'] ?>"
                                        target="_blank"
                                    >
                                        <?= htmlspecialchars(
                                            $s['invoice_no']
                                        ) ?>
                                    </a>

                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $s['sale_date']
                                    ) ?>
                                </td>


                                <td>

                                    <?= number_format(
                                        $s['total'],
                                        2
                                    ) ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                        <?php if (empty($recentSales)): ?>

                            <tr>

                                <td
                                    colspan="3"
                                    class="text-center text-muted"
                                >
                                    No sales yet.
                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>


        <!-- Low Stock -->

        <div class="col-md-5">

            <div class="card">

                <div class="card-header
                    d-flex
                    justify-content-between
                    align-items-center">

                    <span>
                        Low Stock Products
                    </span>

                    <a
                        href="products.php"
                        class="btn btn-sm btn-outline-primary"
                    >
                        Manage
                    </a>

                </div>


                <table class="table mb-0">

                    <thead>

                        <tr>

                            <th>
                                Name
                            </th>

                            <th>
                                Stock
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($lowStock as $p): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars(
                                        $p['name']
                                    ) ?>
                                </td>

                                <td>

                                    <span class="badge bg-danger">

                                        <?= (int)$p['stock'] ?>

                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                        <?php if (empty($lowStock)): ?>

                            <tr>

                                <td
                                    colspan="2"
                                    class="text-center text-muted"
                                >
                                    All stocked up.
                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- ======================================================
         BUTTONS
    ======================================================= -->

    <div class="mt-4">

        <a
            href="pos.php"
            class="btn btn-primary"
        >
            + New Sale
        </a>


        <a
            href="products.php"
            class="btn btn-outline-secondary"
        >
            Manage Products
        </a>

    </div>

</div>


<?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
```
