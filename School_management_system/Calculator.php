<?php include_once("./navbar.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Age Calculator</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .result-box {
      display: none;
      background: #f1f8f6;
      border: 1px solid #cfe8df;
      border-radius: 0.75rem;
      padding: 1.25rem;
    }
    .result-box.show { display: block; }
    .result-main { font-size: 1.6rem; }
    .result-sub { color: #555; font-size: 0.95rem; }
    .is-clickable { cursor: pointer; background-color: #fff; }
  </style>
</head>
<body class="bg-gradient" style="background: linear-gradient(to right, #74ebd5, #9face6);">

  <!-- Calculator Section -->
  <div class="d-flex justify-content-center align-items-center min-vh-100 py-4">
    <div class="card shadow-lg p-5 rounded-4" style="max-width: 1000px; width: 100%;">

      <h1 class="h3 text-center mb-4 fw-bold">Age Calculator</h1>

      <form id="dateForm" class="d-grid gap-4" novalidate>

        <!-- USER DOB -->
        <div>
          <label for="dobFormatted" class="form-label fs-5">Enter your Date of Birth:</label>

          <!-- real date input (kept in the DOM for validation / value, visually hidden) -->
          <input type="date" class="form-control form-control-lg visually-hidden" id="dob" name="dob"
                 max="2026-04-06" required>

          <!-- visible formatted output, click or keyboard-activates the native picker -->
          <input type="text" class="form-control form-control-lg is-clickable" id="dobFormatted"
                 placeholder="Click to select date" readonly
                 role="button" tabindex="0" aria-describedby="dobHelp">
          <div id="dobHelp" class="form-text">Tap the field above to open the date picker.</div>
          <div id="dobError" class="text-danger small mt-1" style="display:none;"></div>
        </div>

        <!-- FIXED COMPARE DATE -->
        <div>
          <label for="compareFormatted" class="form-label fs-5">Date to Calculate From:</label>
          <input type="date" class="form-control form-control-lg visually-hidden" id="compareDate"
                 name="compareDate" value="2026-04-06" readonly>
          <input type="text" class="form-control form-control-lg" id="compareFormatted"
                 value="06, April, 2026" readonly>
        </div>

        <button type="submit" class="btn btn-success btn-lg w-100">Calculate</button>
      </form>

      <div id="result" class="result-box mt-4 text-center" role="status" aria-live="polite">
        <div class="result-main fw-bold" id="resultMain"></div>
        <div class="result-sub mt-2" id="resultSub"></div>
      </div>

    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const dobHidden       = document.getElementById("dob");
    const dobFormatted     = document.getElementById("dobFormatted");
    const dobError         = document.getElementById("dobError");
    const compareHidden    = document.getElementById("compareDate");
    const resultBox        = document.getElementById("result");
    const resultMain       = document.getElementById("resultMain");
    const resultSub        = document.getElementById("resultSub");

    // Open the native date picker when the formatted field is clicked or activated via keyboard.
    function openPicker() {
      if (typeof dobHidden.showPicker === "function") {
        dobHidden.showPicker();
      } else {
        // Fallback for browsers without showPicker() support (e.g. Safari, Firefox < 101)
        dobHidden.focus();
        dobHidden.click();
      }
    }
    dobFormatted.addEventListener("click", openPicker);
    dobFormatted.addEventListener("keydown", function (e) {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        openPicker();
      }
    });

    function formatDate(dateObj) {
      return dateObj.getDate().toString().padStart(2, "0") + ", " +
             dateObj.toLocaleString("en-US", { month: "long" }) + ", " +
             dateObj.getFullYear();
    }

    dobHidden.addEventListener("change", function () {
      dobError.style.display = "none";
      if (!this.value) {
        dobFormatted.value = "";
        return;
      }
      const d = new Date(this.value + "T00:00:00");
      dobFormatted.value = formatDate(d);
      resultBox.classList.remove("show");
    });

    // Accurate calendar-based difference (handles varying month lengths / leap years correctly,
    // unlike dividing by an average of 365.25 / 30.44 days).
    function calendarDiff(start, end) {
      let years  = end.getFullYear() - start.getFullYear();
      let months = end.getMonth() - start.getMonth();
      let days   = end.getDate() - start.getDate();

      if (days < 0) {
        months -= 1;
        // Days in the month before `end`'s month
        const prevMonth = new Date(end.getFullYear(), end.getMonth(), 0);
        days += prevMonth.getDate();
      }
      if (months < 0) {
        years -= 1;
        months += 12;
      }

      const totalDays = Math.round((end - start) / (1000 * 60 * 60 * 24));
      return { years, months, days, totalDays };
    }

    function nextBirthday(dob, from) {
      let next = new Date(from.getFullYear(), dob.getMonth(), dob.getDate());
      if (next < from) next.setFullYear(next.getFullYear() + 1);
      const daysUntil = Math.round((next - from) / (1000 * 60 * 60 * 24));
      return daysUntil;
    }

    document.getElementById("dateForm").addEventListener("submit", function (e) {
      e.preventDefault();
      dobError.style.display = "none";

      if (!dobHidden.value) {
        dobError.textContent = "Please select your Date of Birth.";
        dobError.style.display = "block";
        resultBox.classList.remove("show");
        return;
      }

      const dob     = new Date(dobHidden.value + "T00:00:00");
      const compare = new Date(compareHidden.value + "T00:00:00");

      if (dob > compare) {
        dobError.textContent = "Date of Birth must be on or before " + compareHidden.value + ".";
        dobError.style.display = "block";
        resultBox.classList.remove("show");
        return;
      }

      const { years, months, days, totalDays } = calendarDiff(dob, compare);
      const daysUntilBirthday = nextBirthday(dob, compare);

      resultMain.textContent = `${years} years, ${months} months, and ${days} days`;
      resultSub.innerHTML =
        `Total: ${totalDays.toLocaleString()} days &nbsp;|&nbsp; ` +
        `Next birthday in ${daysUntilBirthday} day${daysUntilBirthday === 1 ? "" : "s"}`;

      resultBox.classList.add("show");
    });
  </script>

</body>
</html>