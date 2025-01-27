<?php
include '../app/views/header.php';
require_once __DIR__ . '/../app/models/Leaves.php'; // Załaduj klasę Leaves
require_once __DIR__ . '/../app/utils/config.php'; // Połączenie z bazą danych

if (!isset($_SESSION['user_id'])) {
    die("You are not logged in as an employee.");
}

$user_id = $_SESSION['user_id'];
$leaves = new Leaves($pdo); // Create an instance of the Leaves class

// Get taken dates (global and for the employee)
$takenDates = $leaves->getTakenDates();
$employeeTakenDates = $leaves->getEmployeeTakenDates($user_id);

// Merge and format dates properly
$allTakenDates = array_merge($takenDates, $employeeTakenDates);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedDates = $_POST['dates']; // Dates selected by the user

    try {
        if ($leaves->submitLeaveRequest($user_id, $selectedDates)) {
            header("location: urlopy.php");
            echo "<alert>Złożono wniosek o urlop!</alert>";
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Submit Leave Request</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <div class="leave_panel">
        <h1>Twoje urlopy</h1>
        <?php include '../app/utils/calendar.php'; ?>

        <h1>Wniosek urlopowy</h1>
        <form method="POST" action="urlopy.php" onsubmit="return validateForm()" class="wniosek">
            <div class="calendar">
                <label for="dates">Wybierz dni: </label>
                <input type="text" id="dates" name="dates" readonly value=" ... ">
            </div>
            <div class="selected-dates">
                <b>Wybrano:</b><br>
                <span id="selected-dates-list"> </span>
            </div>
            <button type="submit" class="leave_submit">Złóż wniosek</button>
        </form>

        <div id="error-message" class="error"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Convert PHP date strings to JavaScript Date objects for taken dates
        const takenDates = <?php echo json_encode($allTakenDates); ?>.map(date => {
            return new Date(date);
        });

        // Initialize the calendar
        const calendar = flatpickr("#dates", {
            mode: "multiple", // Allows selecting multiple dates
            dateFormat: "Y-m-d", // Date format
            minDate: "today", // Disables past dates
            disable: [
                // Disables weekends
                function(date) {
                    return (date.getDay() === 0 || date.getDay() === 6); // 0 = Sunday, 6 = Saturday
                },
                // Disable taken dates
                ...takenDates
            ],
            onChange: function(selectedDates, dateStr, instance) {
                document.getElementById('selected-dates-list').textContent = dateStr;
            }
        });

        // Form validation before submission
        function validateForm() {
            const selectedDates = calendar.selectedDates;
            if (selectedDates.length === 0) {
                document.getElementById('error-message').textContent = "Musisz wybrać co najmniej jeden dzień.";
                return false;
            }
            document.getElementById('error-message').textContent = "";
            return true;
        }
    </script>
</body>

</html>
