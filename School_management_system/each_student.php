<?php
include 'db.php';
include 'navbar.php';

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check Student ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Student ID is required.");
}

$student_id = intval($_GET['id']);

// Fetch student details
$sql = "SELECT * FROM students WHERE student_id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $student_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Student not found.");
}

$student = $result->fetch_assoc();

$stmt->close();


// =====================================================
// FIND SIBLINGS USING FATHER CNIC
// =====================================================

$siblings = [];

$father_cnic = trim($student['father_cnic'] ?? '');

if ($father_cnic !== '') {

    $sibling_sql = "
        SELECT student_id, gr_no, name, class, status
        FROM students
        WHERE father_cnic = ?
        AND student_id != ?
        AND status != 'Withdrawn'
        ORDER BY name ASC
    ";

    $sibling_stmt = $conn->prepare($sibling_sql);

    if ($sibling_stmt) {

        $sibling_stmt->bind_param(
            "si",
            $father_cnic,
            $student_id
        );

        $sibling_stmt->execute();

        $sibling_result = $sibling_stmt->get_result();

        while ($sibling_row = $sibling_result->fetch_assoc()) {

            $siblings[] = $sibling_row;
        }

        $sibling_stmt->close();
    }
}


// Close database connection
$conn->close();


// =====================================================
// HELPER FUNCTION
// =====================================================

function showValue($value)
{
    if ($value === null || $value === '') {

        return '<span class="text-muted">Not Provided</span>';
    }

    return htmlspecialchars($value);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Student Details -
        <?= htmlspecialchars($student['name']); ?>
    </title>


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <style>
        /* =====================================================
           SCREEN DESIGN
        ===================================================== */

        body {

            background-color: #f8f9fa;

        }


        .student-container {

            max-width: 1000px;

            margin: 40px auto;

        }


        .student-card {

            background: #ffffff;

            border-radius: 12px;

            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);

            overflow: hidden;

        }


        .student-header {

            background: linear-gradient(135deg,
                    #0d6efd,
                    #0dcaf0);

            color: white;

            padding: 30px;

            text-align: center;

        }


        .student-header h2 {

            margin-bottom: 5px;

            font-weight: 700;

        }


        .student-header p {

            margin-bottom: 0;

            font-size: 16px;

        }


        .details-section {

            padding: 25px;

        }


        .section-title {

            font-size: 18px;

            font-weight: 700;

            color: #0d6efd;

            border-bottom: 2px solid #0d6efd;

            padding-bottom: 8px;

            margin-bottom: 20px;

        }


        .detail-box {

            background-color: #f8f9fa;

            border: 1px solid #dee2e6;

            border-radius: 8px;

            padding: 15px;

            margin-bottom: 15px;

            height: 100%;

        }


        .detail-label {

            font-size: 13px;

            color: #6c757d;

            font-weight: 600;

            margin-bottom: 5px;

        }


        .detail-value {

            font-size: 16px;

            font-weight: 500;

            color: #212529;

            word-break: break-word;

        }


        .status-active {

            color: #198754;

            font-weight: bold;

        }


        .status-withdrawn {

            color: #dc3545;

            font-weight: bold;

        }


        .status-inactive {

            color: #6c757d;

            font-weight: bold;

        }


        /* Sibling links */

        .sibling-link {

            color: #0d6efd;

            font-weight: 600;

            text-decoration: none;

        }


        .sibling-link:hover {

            text-decoration: underline;

        }


        .sibling-info {

            font-size: 12px;

            color: #6c757d;

            margin-top: 3px;

        }


        .action-buttons {

            padding: 20px 25px 30px;

            border-top: 1px solid #dee2e6;

        }


        /* =====================================================
           A4 PRINT DESIGN
        ===================================================== */

        @media print {


            @page {

                size: A4 portrait;

                margin: 8mm;

            }


            html,
            body {

                width: 100%;

                height: auto;

                background: white !important;

                margin: 0 !important;

                padding: 0 !important;

            }


            /* Hide navbar and buttons */

            .navbar,
            .action-buttons {

                display: none !important;

            }


            /* Main container */

            .student-container {

                width: 100% !important;

                max-width: 100% !important;

                margin: 0 !important;

                padding: 0 !important;

            }


            /* Main card */

            .student-card {

                width: 100% !important;

                max-width: 100% !important;

                border: 1px solid #000 !important;

                border-radius: 0 !important;

                box-shadow: none !important;

                overflow: visible !important;

            }


            /* Header */

            .student-header {

                background: white !important;

                color: black !important;

                padding: 10px !important;

                border-bottom: 2px solid #000 !important;

            }


            .student-header h2 {

                font-size: 20px !important;

                margin-bottom: 2px !important;

            }


            .student-header p {

                font-size: 12px !important;

            }


            .student-header i {

                display: none !important;

            }


            /* Details */

            .details-section {

                padding: 10px !important;

            }


            /* Section headings */

            .section-title {

                font-size: 13px !important;

                padding-bottom: 3px !important;

                margin-top: 8px !important;

                margin-bottom: 7px !important;

                border-bottom: 1px solid #000 !important;

                color: #000 !important;

            }


            .section-title i {

                display: none !important;

            }


            /* Row spacing */

            .row {

                margin-left: -3px !important;

                margin-right: -3px !important;

            }


            .row>[class*="col-"] {

                padding-left: 3px !important;

                padding-right: 3px !important;

            }


            /* 3 boxes per row */

            .col-md-4 {

                width: 33.333333% !important;

                flex: 0 0 33.333333% !important;

            }


            /* 2 boxes per row */

            .col-md-6 {

                width: 50% !important;

                flex: 0 0 50% !important;

            }


            /* Detail boxes */

            .detail-box {

                background: white !important;

                border: 1px solid #999 !important;

                border-radius: 3px !important;

                padding: 6px !important;

                margin-bottom: 6px !important;

                min-height: 45px !important;

                height: auto !important;

                page-break-inside: avoid !important;

            }


            /* Labels */

            .detail-label {

                font-size: 9px !important;

                margin-bottom: 2px !important;

                color: #555 !important;

                line-height: 1.1 !important;

            }


            /* Values */

            .detail-value {

                font-size: 11px !important;

                line-height: 1.2 !important;

                font-weight: 600 !important;

                color: #000 !important;

            }


            /* Status */

            .status-active,
            .status-withdrawn,
            .status-inactive {

                font-size: 11px !important;

            }


            /* Hide icons */

            .detail-value i {

                display: none !important;

            }


            /* Smaller spacing */

            .mt-4 {

                margin-top: 8px !important;

            }


            /* Prevent page breaks */

            .student-card,
            .details-section,
            .row {

                page-break-inside: avoid !important;

            }


            /* Sibling links print as normal text */

            .sibling-link {

                color: #000 !important;

                text-decoration: none !important;

            }


            .sibling-info {

                font-size: 9px !important;

            }


            .text-muted {

                color: #555 !important;

            }

        }
    </style>

</head>


<body>


    <div class="container student-container">

        <div class="student-card">


            <!-- =================================================
             STUDENT HEADER
        ================================================== -->

            <div class="student-header">

                <h2>

                    <i class="fas fa-user-graduate me-2"></i>

                    <?= htmlspecialchars($student['name']); ?>

                </h2>


                <p>

                    Student ID:

                    <strong>

                        <?= htmlspecialchars($student['student_id']); ?>

                    </strong>

                </p>

            </div>



            <!-- =================================================
             STUDENT DETAILS
        ================================================== -->

            <div class="details-section">


                <!-- =================================================
                 BASIC INFORMATION
            ================================================== -->

                <div class="section-title">

                    <i class="fas fa-user me-2"></i>

                    Basic Information

                </div>


                <div class="row">


                    <!-- Student ID -->

                    <div class="col-md-4">

                        <div class="detail-box">

                            <div class="detail-label">

                                Student ID

                            </div>

                            <div class="detail-value">

                                <?= showValue($student['student_id']); ?>

                            </div>

                        </div>

                    </div>


                    <!-- GR No -->

                    <div class="col-md-4">

                        <div class="detail-box">

                            <div class="detail-label">

                                GR No

                            </div>

                            <div class="detail-value">

                                <?= showValue($student['gr_no'] ?? ''); ?>

                            </div>

                        </div>

                    </div>


                    <!-- Class -->

                    <div class="col-md-4">

                        <div class="detail-box">

                            <div class="detail-label">

                                Class

                            </div>

                            <div class="detail-value">

                                <?= showValue($student['class']); ?>

                            </div>

                        </div>

                    </div>


                    <!-- Student Name -->

                    <div class="col-md-6">

                        <div class="detail-box">

                            <div class="detail-label">

                                Student Name

                            </div>

                            <div class="detail-value">

                                <?= showValue($student['name']); ?>

                            </div>

                        </div>

                    </div>


                    <!-- Father Name -->

                    <div class="col-md-6">

                        <div class="detail-box">

                            <div class="detail-label">

                                Father's Name

                            </div>

                            <div class="detail-value">

                                <?= showValue($student['father_name']); ?>

                            </div>

                        </div>

                    </div>


                    <!-- Father CNIC -->

                    <div class="col-md-6">

                        <div class="detail-box">

                            <div class="detail-label">

                                Father's CNIC

                            </div>

                            <div class="detail-value">

                                <?= showValue($student['father_cnic'] ?? ''); ?>

                            </div>

                        </div>

                    </div>


                    <!-- Date of Birth -->

                    <div class="col-md-6">

                        <div class="detail-box">

                            <div class="detail-label">

                                Date of Birth

                            </div>

                            <div class="detail-value">

                                <?= showValue($student['dob']); ?>

                            </div>

                        </div>

                    </div>


                </div>



                <!-- =================================================
                 SIBLING INFORMATION
            ================================================== -->

                <?php if (!empty($siblings)): ?>

                    <div class="section-title mt-4">

                        <i class="fas fa-people-group me-2"></i>

                        Sibling Information

                    </div>


                    <div class="row">

                        <?php foreach ($siblings as $sibling): ?>

                            <div class="col-md-4">

                                <div class="detail-box">

                                    <div class="detail-label">

                                        Sibling

                                    </div>


                                    <div class="detail-value">

                                        <a
                                            href="each_student.php?id=<?= urlencode($sibling['student_id']); ?>"
                                            class="sibling-link">

                                            <?= htmlspecialchars($sibling['name']); ?>

                                        </a>


                                        <div class="sibling-info">

                                            ID:
                                            <?= htmlspecialchars($sibling['student_id']); ?>


                                            <?php if (!empty($sibling['gr_no'])): ?>

                                                |
                                                GR:
                                                <?= htmlspecialchars($sibling['gr_no']); ?>

                                            <?php endif; ?>


                                            <?php if (!empty($sibling['class'])): ?>

                                                |
                                                <?= htmlspecialchars($sibling['class']); ?>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>



                <!-- =================================================
                 ADMISSION & FEE INFORMATION
            ================================================== -->

                <div class="section-title mt-4">

                    <i class="fas fa-school me-2"></i>

                    Admission & Fee Information

                </div>


                <div class="row">


                    <!-- Date of Admission -->

                    <div class="col-md-4">

                        <div class="detail-box">

                            <div class="detail-label">

                                Date of Admission

                            </div>

                            <div class="detail-value">

                                <?= showValue($student['date_of_admission']); ?>

                            </div>

                        </div>

                    </div>


                    <!-- Fees Applied -->

                    <div class="col-md-4">

                        <div class="detail-box">

                            <div class="detail-label">

                                Fees Applied

                            </div>

                            <div class="detail-value">

                                Rs.
                                <?= number_format(
                                    (float)$student['fees_applied']
                                ); ?>

                            </div>

                        </div>

                    </div>


                    <!-- Status -->

                    <div class="col-md-4">

                        <div class="detail-box">

                            <div class="detail-label">

                                Status

                            </div>


                            <div class="detail-value">

                                <?php

                                if ($student['status'] === 'Active') {

                                    echo '
                                <span class="status-active">

                                    <i class="fas fa-circle-check me-1"></i>

                                    Active

                                </span>';
                                } elseif ($student['status'] === 'Withdrawn') {

                                    echo '
                                <span class="status-withdrawn">

                                    <i class="fas fa-circle-xmark me-1"></i>

                                    Withdrawn

                                </span>';
                                } else {

                                    echo '
                                <span class="status-inactive">

                                    <i class="fas fa-circle-minus me-1"></i>

                                    ' .
                                        htmlspecialchars(
                                            $student['status']
                                        ) .
                                        '

                                </span>';
                                }

                                ?>

                            </div>

                        </div>

                    </div>



                    <!-- Withdraw Date -->

                    <?php if (!empty($student['withdraw_date'])): ?>

                        <div class="col-md-4">

                            <div class="detail-box">

                                <div class="detail-label">

                                    Withdraw Date

                                </div>

                                <div class="detail-value">

                                    <?= showValue(
                                        $student['withdraw_date']
                                    ); ?>

                                </div>

                            </div>

                        </div>

                    <?php endif; ?>


                </div>



                <!-- =================================================
                 CONTACT INFORMATION
            ================================================== -->

                <div class="section-title mt-4">

                    <i class="fas fa-address-book me-2"></i>

                    Contact Information

                </div>


                <div class="row">


                    <!-- Contact -->

                    <div class="col-md-6">

                        <div class="detail-box">

                            <div class="detail-label">

                                Contact Number

                            </div>

                            <div class="detail-value">

                                <?= showValue(
                                    $student['contact']
                                ); ?>

                            </div>

                        </div>

                    </div>


                    <!-- Address -->

                    <div class="col-md-6">

                        <div class="detail-box">

                            <div class="detail-label">

                                Address

                            </div>

                            <div class="detail-value">

                                <?= showValue(
                                    $student['address']
                                ); ?>

                            </div>

                        </div>

                    </div>


                </div>


            </div>



            <!-- =================================================
             ACTION BUTTONS
        ================================================== -->

            <div class="action-buttons text-center">


                <!-- Edit Student -->

                <a
                    href="edit_student.php?id=<?= urlencode($student['student_id']); ?>"
                    class="btn btn-warning me-2">

                    <i class="fas fa-edit me-1"></i>

                    Edit Student

                </a>


                <!-- View Ledger -->

                <a
                    href="view_ledger.php?id=<?= urlencode($student['student_id']); ?>"
                    class="btn btn-primary me-2">

                    <i class="fas fa-receipt me-1"></i>

                    View Ledger

                </a>
                <a
                    href="student_id_card.php?id=<?= urlencode($student['student_id']); ?>"
                    class="btn btn-dark me-2"
                    target="_blank">

                    <i class="fas fa-id-card me-1"></i>
                    Print ID Card

                </a>
                <a
                    href="student_picking_card.php?id=<?= urlencode($student['student_id']); ?>"
                    class="btn btn-dark me-2"
                    target="_blank">

                    <i class="fas fa-id-card me-1"></i>
                    Print Picking Card

                </a>


                <!-- Print -->

                <button
                    onclick="window.print()"
                    class="btn btn-success me-2">

                    <i class="fas fa-print me-1"></i>

                    Print

                </button>


                <!-- Back -->

                <button
                    onclick="history.back()"
                    class="btn btn-secondary">

                    <i class="fas fa-arrow-left me-1"></i>

                    Back

                </button>


            </div>


        </div>

    </div>



    <!-- Bootstrap JS -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js">
    </script>


</body>

</html>