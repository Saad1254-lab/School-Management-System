<?php
include 'db.php';

// Check for database connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>


<?php
 include_once("./navbar.php");



 
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class-wise Age Groups</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-primary bg-gradient">

<div class="container py-5">

    <div class="text-center mb-4">
        <h1 class="display-4 fw-bold text-white">
            Class-wise Age Groups
        </h1>
    </div>

    <div class="card shadow-lg border-0 rounded-4">

        <div class="card-body p-0">

            <table class="table table-hover table-striped table-bordered align-middle text-center mb-0">

                <thead class="table-primary">
                    <tr>
                        <th class="py-3 fs-5">Class</th>
                        <th class="py-3 fs-5">Age Group</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td class="fw-semibold">PreK-1</td>
                        <td>3 – 4 Years</td>
                    </tr>

                    <tr>
                        <td class="fw-semibold">PreK-2</td>
                        <td>4 – 5 Years</td>
                    </tr>

                    <tr>
                        <td class="fw-semibold">KG</td>
                        <td>5 – 6 Years</td>
                    </tr>

                    <tr>
                        <td class="fw-semibold">Grade-1</td>
                        <td>6 – 7 Years</td>
                    </tr>

                    <tr>
                        <td class="fw-semibold">Grade-2</td>
                        <td>7 – 8 Years</td>
                    </tr>

                    <tr>
                        <td class="fw-semibold">Grade-3</td>
                        <td>8 – 9 Years</td>
                    </tr>

                    <tr>
                        <td class="fw-semibold">Grade-4</td>
                        <td>9 – 10 Years</td>
                    </tr>

                    <tr>
                        <td class="fw-semibold">Grade-5</td>
                        <td>10 – 11 Years</td>
                    </tr>

                    <tr>
                        <td class="fw-semibold">Grade-6</td>
                        <td>11 – 12 Years</td>
                    </tr>

                    <tr>
                        <td class="fw-semibold">Grade-7</td>
                        <td>12 – 13 Years</td>
                    </tr>

                    <tr>
                        <td class="fw-semibold">Grade-8</td>
                        <td>13 – 14 Years</td>
                    </tr>

                    <tr>
                        <td class="fw-semibold">Grade-9</td>
                        <td>14 – 15 Years</td>
                    </tr>

                    <tr>
                        <td class="fw-semibold">Grade-10</td>
                        <td>15 – 16 Years</td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>