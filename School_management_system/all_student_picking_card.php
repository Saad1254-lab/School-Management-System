<?php

include 'db.php';

// ==========================================
// DATABASE CONNECTION
// ==========================================

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ==========================================
// SCHOOL LOGO
// ==========================================

$logoPath = 'logo.png';

// ==========================================
// FETCH ALL ACTIVE STUDENTS
// ==========================================

$sql = "SELECT *
        FROM students
        WHERE status = 'Active'
        ORDER BY class ASC, student_id ASC";

$result = $conn->query($sql);

if (!$result) {
    die("Error fetching students: " . $conn->error);
}

$students = [];

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$conn->close();

// ==========================================
// HELPER FUNCTION
// ==========================================

function cardValue($value)
{
    if ($value === null || $value === '') {
        return '';
    }

    return htmlspecialchars($value);
}

// ==========================================
// CLASS BACKGROUND COLORS
// SAME ARRAY USED IN SINGLE STUDENT CARD
// ==========================================

$classColors = [

    [
        'light'  => '#f0fff5',
        'mid'    => '#e1f8eb',
        'dark'   => '#ccefd9',
        'border' => '#8fd3ae'
    ],

    [
        'light'  => '#f0f7ff',
        'mid'    => '#e0efff',
        'dark'   => '#c9e2ff',
        'border' => '#91bfff'
    ],

    [
        'light'  => '#f7f2ff',
        'mid'    => '#eee5ff',
        'dark'   => '#dfceff',
        'border' => '#b99de0'
    ],

    [
        'light'  => '#fff3f8',
        'mid'    => '#fce5ef',
        'dark'   => '#f7d0df',
        'border' => '#e8a1bd'
    ],

    [
        'light'  => '#fff8ef',
        'mid'    => '#fff0dc',
        'dark'   => '#ffe0bb',
        'border' => '#ffbb80'
    ],

    [
        'light'  => '#edfffa',
        'mid'    => '#ddf8f0',
        'dark'   => '#c5eee3',
        'border' => '#8ee3cc'
    ],

    [
        'light'  => '#f5efff',
        'mid'    => '#ebe0ff',
        'dark'   => '#d9c5ff',
        'border' => '#b89cff'
    ],

    [
        'light'  => '#fff1f2',
        'mid'    => '#ffe4e6',
        'dark'   => '#ffcdd1',
        'border' => '#ee9ca4'
    ],

    [
        'light'  => '#effcff',
        'mid'    => '#e0f9ff',
        'dark'   => '#c8f1fa',
        'border' => '#8de5f4'
    ],

    [
        'light'  => '#faf6f4',
        'mid'    => '#f3ece9',
        'dark'   => '#e5d8d2',
        'border' => '#b9a39b'
    ],

    [
        'light'  => '#f3f7f9',
        'mid'    => '#e9f0f3',
        'dark'   => '#d8e3e8',
        'border' => '#aebdc4'
    ],

    [
        'light'  => '#eff9ff',
        'mid'    => '#e2f3fc',
        'dark'   => '#cce7f5',
        'border' => '#8fc4e2'
    ],

    [
        'light'  => '#fff5e8',
        'mid'    => '#ffead0',
        'dark'   => '#ffd6a3',
        'border' => '#e9a85c'
    ],

    [
        'light'  => '#f1f0ff',
        'mid'    => '#e5e3ff',
        'dark'   => '#d1ceff',
        'border' => '#9d98df'
    ],

    [
        'light'  => '#edf8f1',
        'mid'    => '#d9efdf',
        'dark'   => '#bfe3c8',
        'border' => '#7fbe8d'
    ],

    [
        'light'  => '#fff0eb',
        'mid'    => '#ffe0d6',
        'dark'   => '#ffc8b8',
        'border' => '#df947d'
    ],

    [
        'light'  => '#eef5ff',
        'mid'    => '#dceaff',
        'dark'   => '#c2d8f5',
        'border' => '#83a9d8'
    ],

    [
        'light'  => '#f8f0fa',
        'mid'    => '#f0e0f3',
        'dark'   => '#e0c9e5',
        'border' => '#b58abd'
    ],

    [
        'light'  => '#f4f8e9',
        'mid'    => '#e7efcf',
        'dark'   => '#d3e1a8',
        'border' => '#9db46b'
    ],

    [
        'light'  => '#eefafa',
        'mid'    => '#dcf1f1',
        'dark'   => '#c0e1e1',
        'border' => '#7db7b7'
    ]

];

// ==========================================
// ASSIGN COLOR TO EACH CLASS
// ==========================================

$classColorMap = [];

foreach ($students as $student) {

    $className = trim($student['class']);

    if (!isset($classColorMap[$className])) {

        $colorIndex =
            abs(crc32($className)) % count($classColors);

        $classColorMap[$className] =
            $classColors[$colorIndex];
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>BK Academy - All Student ID Cards</title>

    <!-- GOOGLE FONT -->

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@600;700;800&display=swap"
        rel="stylesheet">

    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>

        /* =====================================================
           GLOBAL
        ===================================================== */

        * {
            box-sizing: border-box;

            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        html,
        body {

            margin: 0;
            padding: 0;

            width: 100%;
            min-height: 100%;

            font-family: 'Inter', Arial, sans-serif;

            background: #e9ecef;
        }

        /* =====================================================
           SCREEN WRAPPER
        ===================================================== */

        .screen-wrapper {

            min-height: 100vh;

            display: flex;
            flex-direction: column;

            align-items: center;

            padding: 30px;
        }

        /* =====================================================
           CARDS CONTAINER
        ===================================================== */

        .cards-container {

            display: grid;

            grid-template-columns:
                repeat(3, 5.9cm);

            column-gap: 0.45cm;

            row-gap: 0.45cm;

            justify-content: center;

            align-items: start;
        }

        /* =====================================================
           ID CARD
        ===================================================== */

        .id-card {

            width: 5.9cm;
            height: 8cm;

            position: relative;

            overflow: hidden;

            background:
                linear-gradient(
                    145deg,
                    var(--class-bg-light) 0%,
                    var(--class-bg-mid) 50%,
                    var(--class-bg-dark) 100%
                );

            border-radius: 0.16cm;

            border:
                1px solid var(--class-border);

            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.18);

            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* =====================================================
           WATERMARK LOGO
        ===================================================== */

        .watermark-logo {

            position: absolute;

            left: 50%;
            top: 51%;

            transform:
                translate(-50%, -50%);

            width: 3.8cm;
            height: 3.8cm;

            object-fit: contain;

            opacity: 0.3;

            z-index: 1;

            pointer-events: none;
        }

        /* =====================================================
           CARD CONTENT
        ===================================================== */

        .card-content {

            position: relative;

            z-index: 5;

            height: 100%;
        }

        /* =====================================================
           DECORATIVE CIRCLE ONE
        ===================================================== */

        .bg-circle-one {

            position: absolute;

            width: 3.5cm;
            height: 3.5cm;

            border-radius: 50%;

            background:
                rgba(25, 135, 84, 0.06);

            top: -1.5cm;
            right: -1.2cm;

            z-index: 0;
        }

        /* =====================================================
           DECORATIVE CIRCLE TWO
        ===================================================== */

        .bg-circle-two {

            position: absolute;

            width: 2.8cm;
            height: 2.8cm;

            border-radius: 50%;

            background:
                rgba(13, 110, 253, 0.05);

            bottom: 0.4cm;
            left: -1.3cm;

            z-index: 0;
        }

        /* =====================================================
           TOP BRAND STRIP
           ALWAYS GREEN
        ===================================================== */

        .top-strip {

            height: 0.16cm;
            width: 100%;

            background:
                linear-gradient(
                    90deg,
                    #16834c,
                    #39b86d,
                    #16834c
                );
        }

        /* =====================================================
           HEADER
        ===================================================== */

        .card-header {

            position: relative;

            text-align: center;

            padding:
                0.10cm 0.25cm 0.02cm;
        }

        /* =====================================================
           ID CARD LABEL
        ===================================================== */

        .id-card-label {

            position: absolute;

            right: 0.18cm;
            top: 0.10cm;

            font-size: 0.18cm;

            font-weight: 800;

            color: #4b5c65;

            letter-spacing: 0.03cm;

            text-transform: uppercase;
        }

        /* =====================================================
           STUDENT NUMBER
        ===================================================== */

        .student-number {

            margin-top: 1.035cm;

            font-family: 'Montserrat', sans-serif;

            font-size: 0.47cm;

            line-height: 1;

            font-weight: 800;

            color: #243746;

            letter-spacing: 0.035cm;
        }

        /* =====================================================
           STUDENT INFORMATION
        ===================================================== */

        .student-information {

            position: relative;

            text-align: center;

            padding:
                0.25cm 0.20cm 0;
        }

        /* =====================================================
           STUDENT NAME
        ===================================================== */

        .student-name {

            font-family: 'Montserrat', sans-serif;

            font-size: 0.39cm;

            line-height: 1.08;

            font-weight: 800;

            color: #273947;

            text-transform: uppercase;

            letter-spacing: 0.005cm;

            min-height: 0.43cm;
        }

        /* =====================================================
           FATHER NAME
        ===================================================== */

        .father-name {

            margin-top: 0.08cm;

            font-size: 0.25cm;

            color: #5e6d75;

            font-weight: 500;
        }

        .father-name strong {

            color: #354750;

            font-weight: 700;
        }

        /* =====================================================
           CLASS BADGE
           ALWAYS GREEN
        ===================================================== */

        .class-wrapper {

            display: flex;

            justify-content: center;

            margin-top: 1.13cm;
        }

        .class-badge {

            min-width: 3.9cm;
            max-width: 5.0cm;

            padding:
                0.09cm 0.18cm;

            border-radius: 0.15cm;

            background:
                linear-gradient(
                    135deg,
                    #55c878,
                    #229b58
                );

            color: white;

            font-family: 'Montserrat', sans-serif;

            font-size: 0.35cm;

            font-weight: 800;

            letter-spacing: 0.015cm;

            text-transform: uppercase;

            box-shadow:
                0 0.04cm 0.08cm
                rgba(30, 120, 70, 0.20);
        }

        /* =====================================================
           DIVIDER
        ===================================================== */

        .divider {

            display: flex;

            align-items: center;

            gap: 0.08cm;

            margin:
                0.13cm 0.25cm 0.10cm;
        }

        .divider-line {

            height: 1px;

            flex: 1;

            background: #c4d6dc;
        }

        .divider-icon {

            width: 0.25cm;
            height: 0.25cm;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #e1f3e8;

            color: #16834c;

            font-size: 0.12cm;
        }

        /* =====================================================
           SCHOOL FOOTER
           ALWAYS GREEN
        ===================================================== */

        .school-footer {

            position: absolute;

            left: 0;
            right: 0;
            bottom: 0;

            background:
                linear-gradient(
                    135deg,
                    #198754,
                    #35b968
                );

            color: white;

            text-align: center;

            padding:
                0.16cm 0.16cm 0.13cm;

            border-top:
                0.04cm solid
                rgba(255, 255, 255, 0.5);
        }

        /* =====================================================
           SCHOOL NAME
        ===================================================== */

        .school-name {

            font-family: 'Montserrat', sans-serif;

            font-size: 0.39cm;

            line-height: 1;

            font-weight: 800;

            letter-spacing: 0.025cm;

            text-decoration: underline;

            text-decoration-thickness: 1px;

            text-underline-offset: 0.03cm;
        }

        /* =====================================================
           TAGLINE
        ===================================================== */

        .school-tagline {

            margin-top: 0.055cm;

            font-size: 0.145cm;

            line-height: 1;

            font-weight: 700;

            letter-spacing: 0.018cm;

            text-transform: uppercase;
        }

        /* =====================================================
           SCHOOL CONTACT
        ===================================================== */

        .school-contact {

            margin-top: 0.075cm;

            font-size: 0.125cm;

            line-height: 1.35;

            font-weight: 500;

            opacity: 0.98;
        }

        .school-contact i {

            margin-right: 0.025cm;
        }

        /* =====================================================
           FOOTER ID
        ===================================================== */

        .footer-id {

            margin-top: 0.06cm;

            padding-top: 0.045cm;

            border-top:
                1px solid
                rgba(255, 255, 255, 0.30);

            font-size: 0.12cm;

            font-weight: 600;
        }

        /* =====================================================
           PRINT CONTROLS
        ===================================================== */

        .print-controls {

            margin-top: 25px;
            margin-bottom: 25px;

            display: flex;

            gap: 10px;
        }

        .print-controls button {

            border: none;

            padding: 10px 18px;

            border-radius: 6px;

            cursor: pointer;

            color: white;

            font-size: 14px;

            font-weight: 600;
        }

        .print-btn {

            background: #198754;
        }

        .close-btn {

            background: #495057;
        }

        .print-controls button:hover {

            opacity: 0.9;
        }

        /* =====================================================
           PRINT
        ===================================================== */

        @media print {

            @page {

                size: A4 portrait;

                margin: 0.6cm;
            }

            html,
            body {

                width: 100%;
                height: auto;

                margin: 0;
                padding: 0;

                background: white;
            }

            .screen-wrapper {

                width: 100%;

                min-height: 0;

                margin: 0;
                padding: 0;

                display: block;
            }

            .cards-container {

                display: grid;

                grid-template-columns:
                    repeat(3, 5.9cm);

                column-gap: 0.55cm;

                row-gap: 0.45cm;

                justify-content: center;

                align-items: start;

                width: 100%;
            }

            .id-card {

                width: 5.9cm;
                height: 8cm;

                margin: 0;

                border-radius: 0.16cm;

                box-shadow: none;

                border:
                    1px solid var(--class-border);

                page-break-inside: avoid;

                break-inside: avoid;
            }

            .print-controls {

                display: none !important;
            }
        }

        /* =====================================================
           SCREEN RESPONSIVE
        ===================================================== */

        @media screen and (max-width: 900px) {

            .cards-container {

                grid-template-columns:
                    repeat(2, 5.9cm);
            }
        }

        @media screen and (max-width: 600px) {

            .cards-container {

                grid-template-columns:
                    5.9cm;
            }
        }

    </style>

</head>

<body>

<div class="screen-wrapper">

    <!-- PRINT BUTTONS -->

    <div class="print-controls">

        <button
            class="print-btn"
            onclick="window.print()">

            <i class="fas fa-print"></i>

            Print All ID Cards

        </button>

        <button class="print-btn">

            <a
                href="./index.php"
                style="color:white;text-decoration:none;">

                Home

            </a>

        </button>

    </div>


    <!-- ALL STUDENT CARDS -->

    <div class="cards-container">

        <?php foreach ($students as $student): ?>

            <?php

            $className = trim($student['class']);

            $color = $classColorMap[$className];

            ?>

            <div
                class="id-card"
                style="
                    --class-bg-light: <?= $color['light']; ?>;
                    --class-bg-mid: <?= $color['mid']; ?>;
                    --class-bg-dark: <?= $color['dark']; ?>;
                    --class-border: <?= $color['border']; ?>;
                ">

                <!-- DECORATIVE BACKGROUND -->

                <div class="bg-circle-one"></div>

                <div class="bg-circle-two"></div>


                <!-- WATERMARK LOGO -->

                <img
                    src="<?= htmlspecialchars($logoPath); ?>"
                    class="watermark-logo"
                    alt="School Logo">


                <div class="card-content">

                    <!-- TOP GREEN STRIP -->

                    <div class="top-strip"></div>


                    <!-- HEADER -->

                    <div class="card-header">

                        <div class="id-card-label">
                            PICKING CARD
                        </div>

                        <div class="student-number">

                            <?= cardValue($student['student_id']); ?>

                        </div>

                    </div>


                    <!-- STUDENT INFORMATION -->

                    <div class="student-information">

                        <!-- STUDENT NAME -->

                        <div class="student-name">

                            <?= cardValue($student['name']); ?>

                        </div>


                        <!-- FATHER NAME -->

                        <div class="father-name">

                            <?php

                            if (stripos($className, 'Lilly') !== false) {

                                echo 'S/O';

                            } elseif (stripos($className, 'Rose') !== false) {

                                echo 'D/O';

                            }

                            ?>

                            <strong>

                                <?= cardValue($student['father_name']); ?>

                            </strong>

                        </div>


                        <!-- CLASS -->

                        <div class="class-wrapper">

                            <div class="class-badge">

                                <?= cardValue($student['class']); ?>

                            </div>

                        </div>


                        <!-- DIVIDER -->

                        <div class="divider">

                            <div class="divider-line"></div>

                            <div class="divider-icon">

                                <i class="fas fa-graduation-cap"></i>

                            </div>

                            <div class="divider-line"></div>

                        </div>

                    </div>


                    <!-- SCHOOL FOOTER -->

                    <div class="school-footer">

                        <div class="school-name">

                            BK ACADEMY

                        </div>

                        <div class="school-tagline">

                            MENTORING BESIDES TEACHING

                        </div>

                        <div class="school-contact">

                            <i class="fas fa-location-dot"></i>

                            E-27 Shamsi Cooperative Society,
                            Malir Halt, Karachi

                            <br>

                            <i class="fas fa-phone"></i>

                            021-34689271

                            &nbsp;&nbsp;

                            <i class="fas fa-mobile-screen"></i>

                            0335-9999625

                        </div>

                        <div class="footer-id">

                            Student ID:

                            <?= cardValue($student['student_id']); ?>

                        </div>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

</body>

</html>