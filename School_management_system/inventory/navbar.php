```php
<?php
// navbar.php
// Shared navigation bar. Include this after opening <body>.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================================
// LOGOUT
// ==========================================================

if (isset($_GET['logout'])) {

    // Remove all session variables
    $_SESSION = [];

    // Delete session cookie
    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Destroy complete session
    session_destroy();

    // Redirect to parent folder index.php
    header("Location: ../index.php");
    exit;
}


// ==========================================================
// NAVIGATION
// ==========================================================

$currentPage = basename($_SERVER['PHP_SELF']);

function navActive($page, $current) {
    return $page === $current ? 'active' : '';
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">

    <div class="container">

        <a class="navbar-brand" href="index.php">
            BK Academy
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navMenu"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a
                        class="nav-link <?= navActive('index.php', $currentPage) ?>"
                        href="index.php"
                    >
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link <?= navActive('products.php', $currentPage) ?>"
                        href="products.php"
                    >
                        Products
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link <?= navActive('classes.php', $currentPage) ?>"
                        href="classes.php"
                    >
                        Classes
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link <?= navActive('class_sale.php', $currentPage) ?>"
                        href="class_sale.php"
                    >
                        Class Sale
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link <?= navActive('pos.php', $currentPage) ?>"
                        href="pos.php"
                    >
                        New Sale
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link <?= navActive('sales.php', $currentPage) ?>"
                        href="sales.php"
                    >
                        Sales History
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link <?= navActive('quick_class_sale.php', $currentPage) ?>"
                        href="quick_class_sale.php"
                    >
                        Quick Class Sale
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item ms-lg-2">

                    <a
                        href="?logout=1"
                        class="btn btn-danger btn-sm px-3"
                        onclick="return confirm('Are you sure you want to logout?');"
                    >
                        Logout
                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>
```
