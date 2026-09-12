<?php

include 'db.php';

header('Content-Type: application/json; charset=utf-8');


// ==========================================
// DATABASE CONNECTION
// ==========================================

if ($conn->connect_error) {

    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed.'
    ]);

    exit;
}


// ==========================================
// SCHOOL LOGO
// ==========================================

$logoPath = 'logo.png';


// ==========================================
// HELPER FUNCTION
// ==========================================

function cardValue($value)
{
    if ($value === null || $value === '') {
        return '';
    }

    return htmlspecialchars(
        $value,
        ENT_QUOTES,
        'UTF-8'
    );
}


// ==========================================
// CLASS BACKGROUND COLORS
// IMPORTANT:
// KEEP THIS ARRAY IDENTICAL IN ALL CARD FILES
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
// GET STUDENT ID
// ==========================================

if (
    !isset($_GET['id']) ||
    trim($_GET['id']) === ''
) {

    echo json_encode([
        'success' => false,
        'message' => 'Please enter a Student ID.'
    ]);

    exit;
}


$studentId = trim($_GET['id']);


// ==========================================
// FETCH STUDENT
// ONLY ACTIVE STUDENTS
// ==========================================

$stmt = $conn->prepare(
    "SELECT *
     FROM students
     WHERE student_id = ?
     AND status = 'Active'
     LIMIT 1"
);


if (!$stmt) {

    echo json_encode([
        'success' => false,
        'message' => 'Database query error.'
    ]);

    exit;
}


$stmt->bind_param(
    "s",
    $studentId
);


$stmt->execute();


$result =
    $stmt->get_result();


if (
    !$result ||
    $result->num_rows === 0
) {

    $stmt->close();
    $conn->close();

    echo json_encode([
        'success' => false,
        'message' =>
            'Student ID ' .
            $studentId .
            ' not found or student is inactive.'
    ]);

    exit;
}


$student =
    $result->fetch_assoc();


$stmt->close();

$conn->close();


// ==========================================
// CLASS NAME
// ==========================================

$className =
    trim($student['class']);


// ==========================================
// DETERMINE CLASS COLOR
//
// THIS IS THE IMPORTANT PART.
//
// Same class name = same CRC32
// = same color everywhere.
// ==========================================

$colorIndex =
    abs(crc32($className))
    % count($classColors);


$color =
    $classColors[$colorIndex];


// ==========================================
// RELATION PREFIX
// ==========================================

$relationPrefix = 'S/O';


if (
    stripos($className, 'Rose') !== false ||
    stripos($className, 'RCL') !== false
) {

    $relationPrefix = 'D/O';

} else {

    $relationPrefix = 'S/O';
}


// ==========================================
// CREATE CARD
// ==========================================

ob_start();

?>

<div
    class="id-card"
    style="
        --class-bg-light: <?= $color['light']; ?>;
        --class-bg-mid: <?= $color['mid']; ?>;
        --class-bg-dark: <?= $color['dark']; ?>;
        --class-border: <?= $color['border']; ?>;
    ">


    <!-- ======================================
         DECORATIVE BACKGROUND
    ======================================= -->

    <div class="bg-circle-one"></div>

    <div class="bg-circle-two"></div>


    <!-- ======================================
         WATERMARK LOGO
    ======================================= -->

    <img
        src="<?= htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8'); ?>"
        class="watermark-logo"
        alt="School Logo">


    <!-- ======================================
         CARD CONTENT
    ======================================= -->

    <div class="card-content">


        <!-- ==================================
             TOP GREEN STRIP
        =================================== -->

        <div class="top-strip"></div>


        <!-- ==================================
             HEADER
        =================================== -->

        <div class="card-header">


            <div class="id-card-label">

                PICKING CARD

            </div>


            <div class="student-number">

                <?= cardValue($student['student_id']); ?>

            </div>


        </div>


        <!-- ==================================
             STUDENT INFORMATION
        =================================== -->

        <div class="student-information">


            <!-- STUDENT NAME -->

            <div class="student-name">

                <?= cardValue($student['name']); ?>

            </div>


            <!-- FATHER NAME -->

            <div class="father-name">

                <?= $relationPrefix; ?>

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


        <!-- ==================================
             SCHOOL FOOTER
        =================================== -->

        <div class="school-footer">


            <div class="school-name">

                BK ACADEMY

            </div>


            <div class="school-tagline">

                MENTORING BESIDES TEACHING

            </div>


            <div class="school-contact">

                <i class="fas fa-location-dot"></i>

                B-27 Shamsi Cooperative Society,
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

<?php

$cardHTML = ob_get_clean();


// ==========================================
// RETURN JSON
// ==========================================

echo json_encode([

    'success' => true,

    'student_id' =>
        $student['student_id'],

    'card' =>
        $cardHTML

]);

?>