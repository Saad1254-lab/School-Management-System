<?php

include 'db.php';



// Check selected fees
if (
    $_SERVER["REQUEST_METHOD"] !== "POST" ||
    empty($_POST['fee_ids'])
) {
    die("No fee selected.");
}


// Convert IDs to integers
$fee_ids = array_map('intval', $_POST['fee_ids']);


// Remove duplicate IDs
$fee_ids = array_unique($fee_ids);


// Make sure IDs exist
if (count($fee_ids) === 0) {
    die("No valid fee selected.");
}


// Create ?,?,? placeholders
$placeholders = implode(
    ',',
    array_fill(0, count($fee_ids), '?')
);


// Prepare query
$sql = "
    SELECT
        f.*,
        s.name,
        s.father_name,
        s.student_id
    FROM fees f
    INNER JOIN students s
        ON s.student_id = f.student_id
    WHERE f.fee_id IN ($placeholders)
    ORDER BY f.fee_id ASC
";


$stmt = $conn->prepare($sql);


// Bind parameters dynamically
$types = str_repeat('i', count($fee_ids));

$stmt->bind_param(
    $types,
    ...$fee_ids
);

$stmt->execute();

$result = $stmt->get_result();


// Make sure records exist
if ($result->num_rows === 0) {
    die("Selected fee records not found.");
}


// Store records
$fees = [];

while ($row = $result->fetch_assoc()) {

    $fees[] = $row;

}


// Student information
$student_name = $fees[0]['name'];
$father_name = $fees[0]['father_name'];
$student_id = $fees[0]['student_id'];


// Determine PAID stamp
//
// IMPORTANT:
// PAID stamp appears ONLY when ALL selected fees
// are Paid.
//
// Example:
//
// Fee 1 = Paid
// Fee 2 = Paid
// => PAID stamp
//
// Fee 1 = Paid
// Fee 2 = Pending
// => NO PAID stamp

$allPaid = true;

foreach ($fees as $fee) {

    if ($fee['payment_status'] !== 'Paid') {

        $allPaid = false;

        break;
    }
}


// Calculate total
$total_amount = 0;

foreach ($fees as $fee) {

    $total_amount += (float)$fee['amount'];

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Fee Voucher</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }


        .voucher {

            width: 90%;
            margin: 30px auto;

            border: 2px solid #000;

            padding: 25px;

            position: relative;

        }


        .header {

            text-align: center;

            margin-bottom: 20px;

        }


        .header h1 {

            margin: 0;

            font-size: 28px;

        }


        .header h2 {

            margin: 5px 0;

            font-size: 20px;

        }


        .student-info {

            display: flex;

            justify-content: space-between;

            margin-bottom: 20px;

            font-size: 16px;

        }


        table {

            width: 100%;

            border-collapse: collapse;

        }


        th,
        td {

            border: 1px solid #000;

            padding: 10px;

            text-align: center;

        }


        th {

            background: #eee;

        }


        .total {

            text-align: right;

            font-size: 20px;

            font-weight: bold;

            margin-top: 15px;

        }


        /* PAID STAMP */

        .paid-stamp {

            position: absolute;

            top: 130px;

            right: 80px;

            border: 6px solid #008000;

            color: #008000;

            font-size: 38px;

            font-weight: bold;

            padding: 8px 20px;

            transform: rotate(-15deg);

            opacity: 0.75;

            letter-spacing: 3px;

        }


        .footer {

            margin-top: 50px;

            display: flex;

            justify-content: space-between;

        }


        @media print {

            .no-print {

                display: none !important;

            }


            .voucher {

                width: 90%;

                margin: 0 auto;

                border: 2px solid #000;

            }

        }

    </style>

</head>


<body>


<div class="voucher">


    <?php if ($allPaid): ?>

        <!-- PAID STAMP -->

        <div class="paid-stamp">

            PAID

        </div>

    <?php endif; ?>


    <div class="header">

        <h1>
            Baitul Khair Academy
        </h1>

        <h2>
            FEE VOUCHER
        </h2>

    </div>


    <div class="student-info">

        <div>

            <strong>Student ID:</strong>

            <?php
            echo htmlspecialchars($student_id);
            ?>

        </div>


        <div>

            <strong>Student Name:</strong>

            <?php
            echo htmlspecialchars($student_name);
            ?>

        </div>


        <div>

            <strong>Father Name:</strong>

            <?php
            echo htmlspecialchars($father_name);
            ?>

        </div>

    </div>


    <table>

        <thead>

            <tr>

                <th>#</th>

                <th>Month</th>

                <th>Payment Date</th>

                <th>Status</th>

                <th>Amount</th>

            </tr>

        </thead>


        <tbody>

            <?php

            $counter = 1;

            foreach ($fees as $fee):

            ?>

                <tr>

                    <td>
                        <?php echo $counter++; ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars(
                            $fee['due_month']
                        );
                        ?>
                    </td>


                    <td>

                        <?php

                        echo date(
                            "d, F, Y",
                            strtotime(
                                $fee['payment_date']
                            )
                        );

                        ?>

                    </td>


                    <td>

                        <?php
                        echo htmlspecialchars(
                            $fee['payment_status']
                        );
                        ?>

                    </td>


                    <td>

                        <?php
                        echo number_format(
                            $fee['amount'],
                            2
                        );
                        ?>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>


    <div class="total">

        Total Amount:
        Rs. <?php echo number_format($total_amount, 2); ?>

    </div>


    <div class="footer">

        <div>
            ______________________
            <br>
            Parent Signature
        </div>


        <div>
            ______________________
            <br>
            School Stamp
        </div>

    </div>


    <div class="no-print"
         style="text-align:center; margin-top:30px;">

        <button class="btn btn-secondary" onclick="window.print()">
            Print Voucher
        </button>

    </div>


</div>


</body>

</html>