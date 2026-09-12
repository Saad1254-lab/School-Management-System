<?php
require 'core.php';

if (!isset($_GET['id'])) {
    die("No invoice specified.");
}

$sale = getSaleDetails((int)$_GET['id']);

if (!$sale) {
    die("Invoice not found.");
}

$copies = ['Parent\'s Copy', 'School Copy'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<title>Class Receipt - <?= htmlspecialchars($sale['invoice_no']) ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

/* =====================================================
   NORMAL SCREEN
===================================================== */

.receipt-copy {
    max-width: 850px;
    margin: 0 auto;
    position: relative;
}

.copy-label {
    position: absolute;
    top: 10px;
    right: 15px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #6c757d;
    border: 1px solid #6c757d;
    border-radius: 4px;
    padding: 1px 8px;
    font-size: 12px;
}

.cut-line {
    border: none;
    border-top: 1px dashed #999;
    margin: 15px 0;
    position: relative;
}

.cut-line::after {
    content: "\2702";
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: #f8f9fa;
    padding: 0 8px;
    color: #999;
    font-size: 14px;
}

.student-name-line {
    display: inline-block;
    min-width: 250px;
    border-bottom: 1px solid #333;
    margin-left: 6px;
}


/* =====================================================
   TABLE
===================================================== */

.receipt-copy table.receipt-table th,
.receipt-copy table.receipt-table td {
    font-size: 16px;
    padding: 6px 10px;
    line-height: 1.2;
    vertical-align: middle;
}


/* =====================================================
   PRINT
===================================================== */

@media print {

    /* A4 page */
    @page {
        size: A4 portrait;
        margin: 6mm;
    }

    html,
    body {
        width: 100%;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;

        background: #fff !important;

        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Hide buttons/navbar */
    .no-print {
        display: none !important;
    }

    .container {
        width: 100% !important;
        max-width: 100% !important;

        margin: 0 !important;
        padding: 0 !important;
    }


    /* =================================================
       RECEIPTS WRAPPER
    ================================================= */

    .receipts-wrapper {

        display: block !important;

        width: 100% !important;

        min-height: 0 !important;
        height: auto !important;

        margin: 0 !important;
        padding: 0 !important;
    }


    /* =================================================
       EACH RECEIPT
    ================================================= */

    .receipt-copy {

        width: 100% !important;
        max-width: 100% !important;

        height: auto !important;
        min-height: 0 !important;

        margin: 0 !important;

        padding: 0 !important;

        border: 1px solid #000 !important;
        box-shadow: none !important;

        display: block !important;

        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }


    /* =================================================
       CARD BODY
    ================================================= */

    .receipt-copy .card-body {

        padding: 12px 15px !important;

    }


    /* =================================================
       HEADER
    ================================================= */

    .receipt-copy h5 {

        font-size: 17px !important;
        margin-bottom: 0 !important;

    }


    /* =================================================
       COPY LABEL
    ================================================= */

    .copy-label {

        top: 6px !important;
        right: 8px !important;

        font-size: 10px !important;

        padding: 1px 6px !important;
    }


    /* =================================================
       GENERAL SPACING
    ================================================= */

    .receipt-copy .mb-2 {
        margin-bottom: 5px !important;
    }

    .receipt-copy .mb-3 {
        margin-bottom: 5px !important;
    }


    /* =================================================
       STUDENT NAME
    ================================================= */

    .student-name-line {

        min-width: 220px;

    }


    /* =================================================
       TABLE
    ================================================= */

    .receipt-copy table.receipt-table {

        width: 100% !important;

        margin-bottom: 0 !important;

    }

    .receipt-copy table.receipt-table th,
    .receipt-copy table.receipt-table td {

        font-size: 14px !important;

        padding: 10px 16px !important;

        line-height: 1.1 !important;

        vertical-align: middle !important;
    }


    /* =================================================
       CUT LINE
    ================================================= */

    .cut-line {

        margin: 5px 0 !important;

        height: 1px !important;

        page-break-after: avoid !important;
        page-break-before: avoid !important;
    }

    .cut-line::after {

        top: -9px !important;

        font-size: 12px !important;

        padding: 0 5px !important;
    }


    /* =================================================
       PREVENT TABLE SPLITTING
    ================================================= */

    table,
    tr,
    td,
    th {

        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }

}


/* =====================================================
   SCREEN RECEIPT
===================================================== */

.receipts-wrapper {

    display: flex;
    flex-direction: column;
    justify-content: center;

    min-height: 85vh;
}

</style>

</head>

<body class="bg-light">


<!-- =====================================================
     NAVBAR
===================================================== -->

<div class="no-print">

    <?php include 'navbar.php'; ?>

</div>


<div class="container py-2">


    <!-- =================================================
         BUTTONS
    ================================================= -->

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">

        <a href="class_sale.php"
           class="btn btn-outline-secondary btn-sm">

            &larr; Back to Class Sale

        </a>


        <button onclick="window.print()"
                class="btn btn-primary btn-sm">

            Print Both Copies

        </button>

    </div>



    <!-- =================================================
         RECEIPTS
    ================================================= -->

    <div class="receipts-wrapper">


        <?php foreach ($copies as $i => $copyLabel): ?>


            <!-- =================================================
                 RECEIPT COPY
            ================================================= -->

            <div class="card receipt-copy shadow-sm">


                <!-- COPY LABEL -->

                <span class="copy-label">

                    <?= htmlspecialchars($copyLabel) ?>

                </span>



                <div class="card-body">


                    <!-- =================================================
                         HEADER
                    ================================================= -->

                    <div class="text-center mb-2">

                        <h5 class="mb-0">

                        BK Academy

                        </h5>


                        <div class="fw-bold">



                            <?php if (!empty($sale['class_name'])): ?>



                                <?= htmlspecialchars($sale['class_name']) ?>

                            <?php endif; ?>

                        </div>

                    </div>



                    <!-- =================================================
                         INVOICE INFORMATION
                    ================================================= -->

                    <div class="row mb-2 small">

                        <div class="col-6">

                            <strong>Invoice #:</strong>

                            <?= htmlspecialchars($sale['invoice_no']) ?>

                        </div>


                        <div class="col-6 text-end">

                            <strong>Date:</strong>

                            <?= htmlspecialchars($sale['sale_date']) ?>

                        </div>

                    </div>



                    <!-- =================================================
                         STUDENT NAME
                    ================================================= -->

                    <div class="mb-2 small">

                        <strong>Student Name:</strong>

                        <span class="student-name-line">

                            &nbsp;

                        </span>

                    </div>



                    <!-- =================================================
                         TABLE
                    ================================================= -->

                    <div class="mx-auto"
                         style="max-width: 92%;">

                        <table class="table table-bordered mb-1 receipt-table">


                            <!-- TABLE HEADER -->

                            <thead class="table-light">

                                <tr>

                                    <th>
                                        Product
                                    </th>

                                    <th class="text-end">
                                        Qty
                                    </th>

                                    <th class="text-end">
                                        Price
                                    </th>

                                    <th class="text-end">
                                        Total
                                    </th>

                                </tr>

                            </thead>



                            <!-- TABLE BODY -->

                            <tbody>

                                <?php foreach ($sale['items'] as $item): ?>

                                    <tr>

                                        <td>

                                            <?= htmlspecialchars(
                                                $item['product_name']
                                            ) ?>

                                        </td>


                                        <td class="text-end">

                                            <?= (int)$item['quantity'] ?>

                                        </td>


                                        <td class="text-end">

                                            <?= number_format(
                                                $item['price'],
                                                2
                                            ) ?>

                                        </td>


                                        <td class="text-end">

                                            <?= number_format(
                                                $item['total'],
                                                2
                                            ) ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>



                            <!-- GRAND TOTAL -->

                            <tfoot>

                                <tr>

                                    <th colspan="3"
                                        class="text-end">

                                        Grand Total

                                    </th>


                                    <th class="text-end">

                                        <?= number_format(
                                            $sale['total'],
                                            2
                                        ) ?>

                                    </th>

                                </tr>

                            </tfoot>


                        </table>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 CUT LINE
            ================================================= -->

            <?php if ($i === 0): ?>

                <hr class="cut-line">

            <?php endif; ?>


        <?php endforeach; ?>


    </div>

</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>