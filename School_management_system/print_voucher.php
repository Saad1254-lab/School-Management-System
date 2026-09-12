<?php
include 'db.php';




// --- Determine student_id (deny both being set at once) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id_form'])) {

    $student_id = $_POST['student_id_form'];

    $issue_date = "01 " . date('M') . ", " . date('Y');
    $due_date   = "10 " . date('M') . ", " . date('Y');

}
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['student_id'])) {

    $student_id = $_GET['student_id'];
    $issue_date = $_GET['issue_date'] ?? '';
    $due_date   = $_GET['due_date'] ?? '';

}
else {

    $student_id = null;
    $issue_date = '';
    $due_date   = '';

}

// Fetch student + dues only if ID is provided
if ($student_id) {
    // Fetch student details
    $sql = "SELECT * FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();

    // Fetch dues
    $sql = "SELECT due_month, amount 
            FROM fees 
            WHERE student_id = ? AND payment_status = 'Pending' 
            ORDER BY fee_id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $dues_result = $stmt->get_result();
    $dues = [];

    while ($row = $dues_result->fetch_assoc()) {
        $dues[] = $row;
    }
}

$currentMonth = date('M');   // Feb
$currentYear  = date('Y');   // 2026
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Fee Voucher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .voucher {
            border: 1px solid #000;
            padding: 20px;
            margin-top: 10px;
            page-break-before: always;
            
            /* Ensure each voucher starts on a new page */
        }
        .head{
            margin-top: 8px
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
            margin-bottom: 15px;
        }

        .voucher table {
            width: 100%;
            border-collapse: collapse;
            /* Remove border between cells */
        }

        .voucher table th,
        .voucher table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .voucher table th {
            background-color: #f2f2f2;
            /* Light gray background for table headers */
        }

        /* Page Break for printing */
        @media print {

            @media print {
            @page {
                size: A4;
                margin: 0.5cm;
              
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

            body * {
                visibility: hidden;
            }

            .voucher,
            .voucher * {
                visibility: visible;
            }

            .voucher {
                position: absolute;
                top: 30px;
                left: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .voucher .btn {
                display: none;
            }

            /* Ensure each copy starts on a new page */
            .voucher .page-break {
                page-break-before: always;
            }
        }
       

        .logo {
            height: 30px;
            /* Adjust height as needed */
            width: 500px;
            /* Adjust width as needed */
            display: block;
            margin: 0 auto;
            /* Center the logo horizontally */
        }

        .des {
            font-size: 12px;
            margin: 0;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <form method="POST" class="row g-3 mb-4">
            <div class="col-auto">
                <input type="number" name="student_id_form" class="form-control" placeholder="Enter Student ID" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
        <h2 class="text-center mb-4">Print Fee Voucher for Single Student</h2>

        <form method="GET" action="print_voucher.php">
            <div class="mb-3 " id="voucherForm">
                <label for="student_id" class="form-label">Enter Student ID</label>
                <input type="text" name="student_id" id="student_id" class="form-control" required>
                <label for="issue_date" class="form-label">Enter Issue Date</label>
                <input type="text" name="issue_date" id="issue_date" value="01 <?= date('M') ?>, <?= date('Y') ?>" class="form-control" required>
                <label for="due_date" class="form-label">Enter Due Date</label>
                <input type="text" name="due_date" id="due_date" value="10 <?= date('M') ?>, <?= date('Y') ?>" class="form-control" required>
            </div>
            <button id="voucherForm2" type="submit" class="btn btn-primary">Generate Voucher</button>
        </form>
        <h2 id="printAll"  class="text-center mb-4">Print All Stundent Voucher</h2>
        <form method="POST" action="./print_all.php">
            <div class="mb-3 " id="voucherForm3">             
                <label for="issue_date" class="form-label">Enter Issue Date</label>
                <input type="text" name="issue_date" id="issue_date" class="form-control" value="01 <?= date('M') ?>, <?= date('Y') ?>" required>
                <label for="due_date" class="form-label">Enter Due Date</label>
                <input type="text" name="due_date" id="due_date" class="form-control" value="10 <?= date('M') ?>, <?= date('Y') ?>" required>
            </div>
            <button id="voucherForm4" type="submit" class="btn btn-primary">Generate Voucher</button>
        </form>
        <br>
       

        <?php if (isset($student) && !empty($student)): ?>
            <button class="btn btn-success" onclick="window.print()">Print Voucher</button>
            <style>
                .frm {
                    display: hidden;
                }
            </style>
            <div class="voucher">
                <div class="section" style="border-bottom: 2px solid #000;">
                
                <div  style="border: 3px solid black; width: 100%;  padding: 10px;"> 
                    <img class="logo" src="./logo.jpg">
                </div>
                 
                    <h4 class="head" style="margin: 0; padding: 0;" >School Copy</h4>
                    <table style="font-size: 12px; width: 100%; table-layout: fixed;">
                        <ul style="list-style-type: none; margin: 0; padding: 0;">
                           <li class="text-end fw-bold" style="margin: 0; padding: 0px; font-size: 12px;">
                              Issue Date: <span style="font-weight: normal;"><?php print ($issue_date); ?></span>
                            </li>

                            <li class="text-end fw-bold" style="margin: 0; padding: 0px; font-size: 12px; ">
                                Due Date: <span style="font-weight: normal;"><?php print ($due_date); ?></span>
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
                            <td style="border: none; text-align: right; padding: 2px 5px;" class="fw-bold">Father's Name
                            </td>
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
                                <th style="text-align: center; padding: 5px; border:2px solid black">Particulars</th>
                                <th style="text-align: center; padding: 5px; border:2px solid black">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dues as $due): ?>
                                <tr>
                                    <td style="text-align:center; padding: 5px; border:2px solid black" class="fw-bold">
                                        <?php echo htmlspecialchars($due['due_month']); ?>
                                    </td>
                                    <td style="text-align:center; padding: 5px; border:2px solid black">
                                        <?php echo number_format($due['amount'], 2); ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                            <tr>
                                <td style="text-align:center; padding: 5px; border:2px solid black" class="fw-bold">Total Amount</td>
                                <td style="text-align:center; padding: 5px; border:2px solid black" class="fw-bold">
                                    <?php echo number_format(array_sum(array_column($dues, 'amount')), 2); ?>
                                </td>
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

                <div class="section" ;>
                     <div  style="border: 3px solid black; width: 100%;  padding: 10px;"> 
                    <img class="logo" src="./logo.jpg">
                </div>
            
                    <h4 class="head" style="margin: 0; padding: 0;" >Parents Copy</h4>
                    <table style="border: none; font-size: 12px; width: 100%; table-layout: fixed;">
                        <ul style="list-style-type: none; margin: 0; padding: 0;">
                            <li class="text-end fw-bold" style="margin: 0; padding: 0px;  font-size: 12px;">
                                Issue Date: <span style="font-weight: normal;"><?php print ($issue_date); ?></span>
                            </li>
                            <li class="text-end fw-bold" style="margin: 0; padding: 0px;  font-size: 12px;">
                                Due Date: <span style="font-weight: normal;"><?php print ($due_date); ?></span>
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
                            <td style="border: none; text-align: right; padding: 2px 5px;" class="fw-bold">Father's Name
                            </td>
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
                                <th style="text-align: center; padding: 5px;  border:2px solid black">Particulars</th>
                                <th style="text-align: center; padding: 5px; border:2px solid black">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dues as $due): ?>
                                <tr>
                                    <td style="text-align:center; padding: 5px; border:2px solid black" class="fw-bold">
                                        <?php echo htmlspecialchars($due['due_month']); ?>
                                    </td>
                                    <td style="text-align:center; padding: 5px; border:2px solid black">
                                        <?php echo number_format($due['amount'], 2); ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                            <tr>
                                <td style="text-align:center; padding: 5px; border:2px solid black" class="fw-bold">Total Amount</td>
                                <td style="text-align:center; padding: 5px; border:2px solid black" class="fw-bold">
                                    <?php echo number_format(array_sum(array_column($dues, 'amount')), 2); ?>
                                </td>
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
        </div>
        <script>
            // Hide the form when the voucher is generated
            document.getElementById('voucherForm').style.display = 'none';
            document.getElementById('voucherForm2').style.display = 'none';
            document.getElementById('voucherForm3').style.display = 'none';
            document.getElementById('voucherForm4').style.display = 'none';
            document.getElementById('printAll').style.display = 'none';
        </script>


    <?php endif; ?>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>