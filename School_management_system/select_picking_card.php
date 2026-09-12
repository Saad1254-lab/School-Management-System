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

$conn->close();


// ==========================================
// HELPER FUNCTION
// ==========================================

function cardValue($value)
{
    if ($value === null || $value === '') {
        return '';
    }

    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>BK Academy - Select Student ID Cards</title>


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
           SEARCH / INPUT AREA
        ===================================================== */

        .student-selector {

            width: 100%;
            max-width: 700px;

            background: white;

            padding: 20px;

            border-radius: 10px;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.10);

            margin-bottom: 25px;
        }


        .selector-title {

            text-align: center;

            font-family: 'Montserrat', sans-serif;

            font-size: 22px;

            font-weight: 800;

            color: #243746;

            margin-bottom: 15px;
        }


        .input-row {

            display: flex;

            gap: 10px;

            width: 100%;
        }


        .student-id-input {

            flex: 1;

            height: 44px;

            border: 1px solid #ced4da;

            border-radius: 6px;

            padding: 0 14px;

            font-size: 15px;

            outline: none;
        }


        .student-id-input:focus {

            border-color: #198754;

            box-shadow:
                0 0 0 3px rgba(25, 135, 84, 0.10);
        }


        .add-student-btn {

            height: 44px;

            border: none;

            border-radius: 6px;

            padding: 0 20px;

            background: #198754;

            color: white;

            font-size: 14px;

            font-weight: 700;

            cursor: pointer;
        }


        .add-student-btn:hover {

            background: #157347;
        }


        .message {

            display: none;

            margin-top: 10px;

            padding: 10px;

            border-radius: 6px;

            font-size: 14px;

            text-align: center;
        }


        .message.error {

            display: block;

            background: #f8d7da;

            color: #842029;
        }


        .message.success {

            display: block;

            background: #d1e7dd;

            color: #0f5132;
        }


        /* =====================================================
           PRINT CONTROLS
        ===================================================== */

        .print-controls {

            margin-bottom: 25px;

            display: flex;

            gap: 10px;

            flex-wrap: wrap;

            justify-content: center;
        }


        .print-controls button,
        .print-controls a {

            border: none;

            padding: 10px 18px;

            border-radius: 6px;

            cursor: pointer;

            color: white;

            font-size: 14px;

            font-weight: 600;

            text-decoration: none;

            display: inline-flex;

            align-items: center;

            gap: 6px;
        }


        .print-btn {

            background: #198754;
        }


        .clear-btn {

            background: #dc3545;
        }


        .home-btn {

            background: #495057;
        }


        .print-controls button:hover,
        .print-controls a:hover {

            opacity: 0.9;
        }


        /* =====================================================
           CARDS CONTAINER
        ===================================================== */

        .cards-container {

            display: grid;

            grid-template-columns:
                repeat(3, 5.9cm);

            column-gap: 0;

            row-gap: 0cm;

            justify-content: center;

            align-items: start;

            width: 100%;
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
           REMOVE CARD BUTTON
        ===================================================== */

        .remove-card {

            position: absolute;

            top: 5px;
            right: 5px;

            width: 24px;
            height: 24px;

            border: none;

            border-radius: 50%;

            background: #dc3545;

            color: white;

            font-size: 12px;

            cursor: pointer;

            z-index: 20;

            display: flex;

            align-items: center;
            justify-content: center;
        }


        .remove-card:hover {

            background: #bb2d3b;
        }


        /* =====================================================
           EMPTY MESSAGE
        ===================================================== */

        .empty-message {

            width: 100%;

            text-align: center;

            padding: 40px 20px;

            color: #6c757d;

            font-size: 15px;
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


            .student-selector,
            .print-controls,
            .remove-card {

                display: none !important;
            }


            .cards-container {

                display: grid;

                grid-template-columns:
                    repeat(3, 5.9cm);

                column-gap: 0cm;

                row-gap: 0;

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


            .empty-message {

                display: none;
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

            .screen-wrapper {

                padding: 15px;
            }


            .input-row {

                flex-direction: column;
            }


            .add-student-btn {

                width: 100%;
            }


            .cards-container {

                grid-template-columns:
                    5.9cm;
            }
        }

    </style>

</head>


<body>


<div class="screen-wrapper">


    <!-- =====================================================
         STUDENT SELECTOR
    ====================================================== -->

    <div class="student-selector">

        <div class="selector-title">

            <i class="fas fa-id-card"></i>

            Select Students for ID Cards

        </div>


        <div class="input-row">

            <input
                type="text"
                id="studentIdInput"
                class="student-id-input"
                placeholder="Enter Student ID"
                autocomplete="off">

            <button
                type="button"
                class="add-student-btn"
                onclick="addStudent()">

                <i class="fas fa-plus"></i>

                Add Student

            </button>

        </div>


        <div
            id="message"
            class="message">
        </div>

    </div>


    <!-- =====================================================
         PRINT CONTROLS
    ====================================================== -->

    <div class="print-controls">


        <button
            type="button"
            class="print-btn"
            onclick="printCards()">

            <i class="fas fa-print"></i>

            Print ID Cards

        </button>


        <button
            type="button"
            class="clear-btn"
            onclick="clearCards()">

            <i class="fas fa-trash"></i>

            Clear All

        </button>


        <a
            href="./index.php"
            class="home-btn">

            <i class="fas fa-home"></i>

            Home

        </a>


    </div>


    <!-- =====================================================
         CARDS
    ====================================================== -->

    <div
        class="cards-container"
        id="cardsContainer">


        <div
            class="empty-message"
            id="emptyMessage">

            <i
                class="fas fa-id-card"
                style="font-size:35px;margin-bottom:10px;">
            </i>

            <br>

            Enter a Student ID above to add an ID card.

        </div>


    </div>


</div>


<script>

    /* =====================================================
       TRACK SELECTED STUDENTS
    ===================================================== */

    let selectedStudents = [];


    /* =====================================================
       ADD STUDENT
    ===================================================== */

    function addStudent() {

        const input =
            document.getElementById('studentIdInput');

        const studentId =
            input.value.trim();


        if (studentId === '') {

            showMessage(
                'Please enter a Student ID.',
                'error'
            );

            input.focus();

            return;
        }


        /* ---------------------------------------------
           PREVENT DUPLICATE
        --------------------------------------------- */

        if (selectedStudents.includes(studentId)) {

            showMessage(
                'This student is already added.',
                'error'
            );

            input.select();

            return;
        }


        /* ---------------------------------------------
           DISABLE BUTTON WHILE LOADING
        --------------------------------------------- */

        const button =
            document.querySelector('.add-student-btn');

        button.disabled = true;

        button.innerHTML =
            '<i class="fas fa-spinner fa-spin"></i> Loading...';


        /* ---------------------------------------------
           REQUEST STUDENT
        --------------------------------------------- */

        fetch(
            'get_student_picking_card.php?id=' +
            encodeURIComponent(studentId)
        )

        .then(response => {

            if (!response.ok) {

                throw new Error(
                    'Server error.'
                );
            }

            return response.json();
        })

        .then(data => {

            if (!data.success) {

                showMessage(
                    data.message ||
                    'Student not found.',
                    'error'
                );

                return;
            }


            /* -----------------------------------------
               ADD STUDENT ID TO ARRAY
            ----------------------------------------- */

            selectedStudents.push(studentId);


            /* -----------------------------------------
               HIDE EMPTY MESSAGE
            ----------------------------------------- */

            const emptyMessage =
                document.getElementById(
                    'emptyMessage'
                );

            if (emptyMessage) {

                emptyMessage.remove();
            }


            /* -----------------------------------------
               CREATE CARD
            ----------------------------------------- */

            const cardWrapper =
                document.createElement('div');

            cardWrapper.innerHTML =
                data.card;


            const card =
                cardWrapper.firstElementChild;


            /* -----------------------------------------
               REMOVE BUTTON
            ----------------------------------------- */

            const removeButton =
                document.createElement('button');

            removeButton.type = 'button';

            removeButton.className =
                'remove-card';

            removeButton.innerHTML =
                '<i class="fas fa-times"></i>';

            removeButton.title =
                'Remove this card';


            removeButton.onclick =
                function () {

                    removeStudent(
                        studentId,
                        card
                    );
                };


            card.appendChild(removeButton);


            document
                .getElementById('cardsContainer')
                .appendChild(card);


            /* -----------------------------------------
               CLEAR INPUT
            ----------------------------------------- */

            input.value = '';

            input.focus();


            showMessage(
                'Student ' + studentId +
                ' added successfully.',
                'success'
            );

        })

        .catch(error => {

            console.error(error);

            showMessage(
                'Unable to load student. Please try again.',
                'error'
            );

        })

        .finally(() => {

            button.disabled = false;

            button.innerHTML =
                '<i class="fas fa-plus"></i> Add Student';

        });
    }


    /* =====================================================
       REMOVE STUDENT
    ===================================================== */

    function removeStudent(studentId, card) {

        selectedStudents =
            selectedStudents.filter(
                id => id !== studentId
            );


        card.remove();


        /* ---------------------------------------------
           SHOW EMPTY MESSAGE AGAIN
        --------------------------------------------- */

        if (selectedStudents.length === 0) {

            const emptyMessage =
                document.createElement('div');

            emptyMessage.className =
                'empty-message';

            emptyMessage.id =
                'emptyMessage';

            emptyMessage.innerHTML =

                '<i class="fas fa-id-card" ' +
                'style="font-size:35px;margin-bottom:10px;">' +
                '</i><br>' +

                'Enter a Student ID above to add an ID card.';


            document
                .getElementById('cardsContainer')
                .appendChild(emptyMessage);
        }


        showMessage(
            'Student card removed.',
            'success'
        );
    }


    /* =====================================================
       CLEAR ALL CARDS
    ===================================================== */

    function clearCards() {

        selectedStudents = [];


        const container =
            document.getElementById(
                'cardsContainer'
            );


        container.innerHTML = `

            <div
                class="empty-message"
                id="emptyMessage">

                <i
                    class="fas fa-id-card"
                    style="font-size:35px;margin-bottom:10px;">
                </i>

                <br>

                Enter a Student ID above to add an ID card.

            </div>

        `;


        showMessage(
            'All cards cleared.',
            'success'
        );
    }


    /* =====================================================
       PRINT
    ===================================================== */

    function printCards() {

        if (selectedStudents.length === 0) {

            showMessage(
                'Please add at least one student first.',
                'error'
            );

            return;
        }


        window.print();
    }


    /* =====================================================
       MESSAGE
    ===================================================== */

    function showMessage(text, type) {

        const message =
            document.getElementById('message');


        message.textContent = text;

        message.className =
            'message ' + type;


        setTimeout(() => {

            message.className =
                'message';

            message.textContent = '';

        }, 2500);
    }


    /* =====================================================
       ENTER KEY
    ===================================================== */

    document
        .getElementById('studentIdInput')
        .addEventListener(
            'keydown',
            function(event) {

                if (event.key === 'Enter') {

                    event.preventDefault();

                    addStudent();
                }

            }
        );

</script>


</body>

</html>