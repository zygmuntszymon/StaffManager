<?php
include './header.php';
require_once dirname(__DIR__) . '/models/Leaves.php';
require_once dirname(__DIR__) . '/utils/config.php';

if (!isset($_SESSION['user_id'])) {
    die("Nie jesteś zalgowany jako pracownik.");
}

$user_id = $_SESSION['user_id'];
$leaves = new Leaves($pdo);
$msg = "";

$takenDates = $leaves->getTakenDates();
$employeeTakenDates = $leaves->getEmployeeTakenDates($user_id);

$allTakenDates = array_merge($takenDates, $employeeTakenDates);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedDates = $_POST['dates']; // daty wybrane przez użytkownika
    try {
        if ($leaves->submitLeaveRequest($user_id, $selectedDates)) {
            echo "<script>alert('Złożono wniosek o urlop!');</script>";
            header("location: urlopy.php");
        }
    } catch (Exception $e) {
        $msg = $e->getMessage(); // komunikat błędu w zmiennej
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Składanie wniosku urlopowego</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <div class="leave_panel">
        <h1>Twoje urlopy</h1>
        <?php include '../utils/calendar.php'; ?>

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

        <div id="error-message" class="error"><?php echo $msg; ?></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const takenDates = <?php echo json_encode($allTakenDates); ?>.map(date => {
            return new Date(date);
        });
        const calendar = flatpickr("#dates", {
            mode: "multiple",
            dateFormat: "Y-m-d",
            minDate: "today",
            disable: [
                function(date) {
                    return (date.getDay() === 0 || date.getDay() === 6);
                },
                ...takenDates
            ],
            onChange: function(selectedDates, dateStr, instance) {
                document.getElementById('selected-dates-list').textContent = dateStr;
            }
        });

        function validateForm() {
            const selectedDates = calendar.selectedDates;
            if (selectedDates.length === 0) {
                document.getElementById('error-message').textContent = "Musisz wybrać co najmniej jeden dzień.";
                return false;
            }
            document.getElementById('error-message').textContent = "";
            return true;
        }

        const errorMessage = "<?php echo $msg; ?>";
        if (errorMessage) {
            document.getElementById('error-message').textContent = errorMessage;
        }
    </script>
</body>

</html>
