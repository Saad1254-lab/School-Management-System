<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Fee Vouchers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .voucher {
            border: 1px solid #000;
            page-break-before: always;
            /* Ensure each voucher starts on a new page */
        }

        .voucher h3 {
            text-align: center;
        }

        .voucher .section {
            margin-top: 20px;
            padding: 10px;
        }

        .voucher .section h4 {
            text-align: center;
            margin-bottom: 10px;
        }

        .voucher table {
            width: 100%;
            border-collapse: collapse;
        }

        .voucher table th,
        .voucher table td {
            border: 1px solid #ddd;
            padding: 2px;
            text-align: left;
        }

        .voucher table th {
            background-color: #f2f2f2;
        }

        @media print {
            @page {
                size: A4;
                margin: 0.5cm;
                margin-left: -190px;
            }

            body * {
                visibility: hidden;
            }

            .voucher,
            .voucher * {
                visibility: visible;
            }

            .voucher {
                width: 730px;
                margin: 0;
                margin-top: 30px;
            }

            .voucher .btn {
                display: none;
            }
        }

        .logo {
            height: 40px;
            width: 500px;
            display: block;
            margin: 0 auto;
        }

        .des {
            font-size: 12px;
            margin: 0;
            padding-bottom: 0px;
        }
    </style>
</head>

<body>
<?php
include 'db.php';
include './navbar.php';

// Fetch all students from the database
$sql = "SELECT students.student_id, students.name, students.father_name, students.class, GROUP_CONCAT(fees.due_month) AS due_months, SUM(fees.amount) AS total_amount 
        FROM students
        INNER JOIN fees ON students.student_id = fees.student_id 
        WHERE fees.payment_status = 'Pending' && students.status = 'Active'
        GROUP BY students.student_id
        ORDER BY students.class";

$stmt = $conn->prepare($sql);
$stmt->execute();
$students_result = $stmt->get_result();
?>

<div class="container">
    <button class="btn btn-primary my-3" onclick="window.print()">Print All Vouchers</button>
    
    <?php while ($student = $students_result->fetch_assoc()): ?>
        <div class="voucher">
            <div class="section" style="border-bottom: 2px solid #000;">
                <div style="border: 3px solid black; width: 100%; padding: 10px;"> 
                    <img class="logo" src="./logo.jpg">
                </div>
                <h4 class="head" style="margin: 0; padding: 0;">School Copy</h4>
                <table style="font-size: 12px; width: 100%; table-layout: fixed;">
                    <ul style="list-style-type: none; margin: 0; padding: 0;">
                        <li class="text-end fw-bold" style="margin: 0; padding: 0; font-size: 12px;">
                            Issue Date: <span style="font-weight: normal;"><?php print ($_POST["issue_date"]); ?></span>
                        </li>
                        <li class="text-end fw-bold" style="margin: 0; padding: 0; font-size: 12px;">
                            Due Date: <span style="font-weight: normal;"><?php print ($_POST["due_date"]); ?></span>
                        </li>
                    </ul>
                    <br>

                    <tr style="margin: 0; padding: 0;">
                        <td style="border: none; text-align: right; padding: 2px 5px;" class="fw-bold">Student Name</td>
                        <td style="border: none; border-bottom: 1px solid #000; text-align: center; padding: 2px 5px;">
                            <?php echo $student['name']; ?>
                        </td>
                        <td style="border: none; text-align: right; padding: 2px 5px;" class="fw-bold">Student ID</td>
                        <td style="border: none; border-bottom: 1px solid #000; text-align: center; padding: 2px 5px;">
                            <?php echo $student['student_id']; ?>
                        </td>
                    </tr>
                    <tr style="margin: 0; padding: 0;">
                        <td style="border: none; text-align: right; padding: 2px 5px;" class="fw-bold">Father's Name</td>
                        <td style="border: none; border-bottom: 1px solid #000; text-align: center; padding: 2px 5px;">
                            <?php echo $student['father_name']; ?>
                        </td>
                        <td style="border: none; text-align: right; padding: 2px 5px;" class="fw-bold">Class</td>
                        <td style="border: none; border-bottom: 1px solid #000; text-align: center; padding: 2px 5px;">
                            <?php echo $student['class']; ?>
                        </td>
                    </tr>
                </table>

                <br>

                <table style="width: 100%; font-size: 12px; table-layout: fixed;">
                    <thead>
                        <tr>
                            <th style="text-align: center; padding: 3px; border:2px solid black">Particulars</th>
                            <th style="text-align: center; padding: 3px; border:2px solid black">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch dues details again for each student
                        $sql_dues = "SELECT fees.due_month, fees.amount 
                                     FROM fees 
                                     WHERE fees.student_id = ? AND fees.payment_status = 'Pending' 
                                     GROUP BY fees.due_month 
                                     ORDER BY fees.fee_id";
                        $stmt_dues = $conn->prepare($sql_dues);
                        $stmt_dues->bind_param('i', $student['student_id']);
                        $stmt_dues->execute();
                        $dues_result = $stmt_dues->get_result();

                        while ($row = $dues_result->fetch_assoc()): ?>
                            <tr>
                                <td style="padding: 3px; border:2px solid black; text-align: center;"><strong><?php echo $row['due_month']; ?></strong></td>
                                <td style="padding: 3px; border:2px solid black; text-align: center;"><?php echo number_format($row['amount'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        <tr>
                            <td style="text-align: center; padding: 3px; border: 2px solid black;" colspan="1"><strong>Total Amount</strong></td>
                            <td style="text-align: center; padding: 3px; border: 2px solid black;"><strong><?php echo number_format($student['total_amount'], 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                 <ul>
                        
                            <li class="des">For Online Payment send Amount on Meezan bank Account # (01300103267438) Title
                                (BAITUL KHAIR ACADEMY)
                        </li>
                        <li class="des">Share payment screenshot along with Student's GR NO on 03359999625
                        </li>
                        <li class="des">We confirm receiving accordingly.
                        </li>
                    </ul>
            </div>
            <div class="section" style="border-bottom: 2px solid #000;">
                <div style="border: 3px solid black; width: 100%; padding: 10px;"> 
                    <img class="logo" src="./logo.jpg">
                </div>
                <h4 class="head" style="margin: 0; padding: 0;">Parents Copy</h4>
                <table style="font-size: 12px; width: 100%; table-layout: fixed;">
                    <ul style="list-style-type: none; margin: 0; padding: 0;">
                        <li class="text-end fw-bold" style="margin: 0; padding: 0; font-size: 12px;">
                            Issue Date: <span style="font-weight: normal;"><?php print ($_POST["issue_date"]); ?></span>
                        </li>
                        <li class="text-end fw-bold" style="margin: 0; padding: 0; font-size: 12px;">
                            Due Date: <span style="font-weight: normal;"><?php print ($_POST["due_date"]); ?></span>
                        </li>
                    </ul>
                    <br>

                    <tr style="margin: 0; padding: 0;">
                        <td style="border: none; text-align: right; padding: 2px 5px;" class="fw-bold">Student Name</td>
                        <td style="border: none; border-bottom: 1px solid #000; text-align: center; padding: 2px 5px;">
                            <?php echo $student['name']; ?>
                        </td>
                        <td style="border: none; text-align: right; padding: 2px 5px;" class="fw-bold">Student ID</td>
                        <td style="border: none; border-bottom: 1px solid #000; text-align: center; padding: 2px 5px;">
                            <?php echo $student['student_id']; ?>
                        </td>
                    </tr>
                    <tr style="margin: 0; padding: 0;">
                        <td style="border: none; text-align: right; padding: 2px 5px;" class="fw-bold">Father's Name</td>
                        <td style="border: none; border-bottom: 1px solid #000; text-align: center; padding: 2px 5px;">
                            <?php echo $student['father_name']; ?>
                        </td>
                        <td style="border: none; text-align: right; padding: 2px 5px;" class="fw-bold">Class</td>
                        <td style="border: none; border-bottom: 1px solid #000; text-align: center; padding: 2px 5px;">
                            <?php echo $student['class']; ?>
                        </td>
                    </tr>
                </table>

                <br>

                <table style="width: 100%; font-size: 12px; table-layout: fixed;">
                    <thead>
                        <tr>
                            <th style="text-align: center; padding: 3px; border:2px solid black">Particulars</th>
                            <th style="text-align: center; padding: 3px; border:2px solid black">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch dues details again for each student
                        $sql_dues = "SELECT fees.due_month, fees.amount 
                                     FROM fees 
                                     WHERE fees.student_id = ? AND fees.payment_status = 'Pending' 
                                     GROUP BY fees.due_month 
                                     ORDER BY fees.fee_id";
                        $stmt_dues = $conn->prepare($sql_dues);
                        $stmt_dues->bind_param('i', $student['student_id']);
                        $stmt_dues->execute();
                        $dues_result = $stmt_dues->get_result();

                        while ($row = $dues_result->fetch_assoc()): ?>
                            <tr>
                                <td style="padding: 3px; border:2px solid black; text-align: center;"><strong><?php echo $row['due_month']; ?></strong></td>
                                <td style="padding: 3px; border:2px solid black; text-align: center;"><?php echo number_format($row['amount'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        <tr>
                            <td style="text-align: center; padding: 3px; border: 2px solid black;" colspan="1"><strong>Total Amount</strong></td>
                            <td style="text-align: center; padding: 3px; border: 2px solid black;"><strong><?php echo number_format($student['total_amount'], 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                 <ul>
                        
                            <li class="des">For Online Payment send Amount on Meezan bank Account # (01300103267438) Title
                                (BAITUL KHAIR ACADEMY)
                        </li>
                        <li class="des">Share payment screenshot along with Student's GR NO on 03359999625
                        </li>
                        <li class="des">We confirm receiving accordingly.
                        </li>
                    </ul>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
