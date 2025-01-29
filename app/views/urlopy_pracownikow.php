<?php
include './header.php';
require_once dirname(__DIR__) . '/utils/config.php';
require_once dirname(__DIR__) . '/models/Leaves.php';

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    die("Błąd: Brak ID użytkownika.");
}
$employeeId = $_SESSION['user_id'];

// Pobieranie miesiąca i roku (domyślnie bieżący)
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Pobieranie dni urlopowych użytkownika (za pomocą obiektu Leaves)
$leavesModel = new Leaves($pdo);
$takenDates = $leavesModel->getEmployeeTakenDates($employeeId);

// Pobieranie wszystkich dni urlopowych
$allTakenDates = $leavesModel->getTakenDates();

// Usunięcie spacji z dat w tablicy $takenDates
$takenDates = array_map('trim', $takenDates);
$allTakenDates = array_map('trim', $allTakenDates);

// Tworzenie znacznika czasu dla pierwszego dnia miesiąca
$firstDayOfMonth = strtotime("$year-$month-01");
$daysInMonth = date('t', $firstDayOfMonth);
$firstWeekday = date('N', $firstDayOfMonth); // (1 = Pn, 7 = Nd)

// Poprzedni i następny miesiąc do nawigacji
$prevMonth = ($month == 1) ? 12 : $month - 1;
$prevYear = ($month == 1) ? $year - 1 : $year;

$nextMonth = ($month == 12) ? 1 : $month + 1;
$nextYear = ($month == 12) ? $year + 1 : $year;

// Obsługa kliknięcia w datę
$selectedDate = isset($_GET['date']) ? $_GET['date'] : null;
$employeesOnLeave = $selectedDate ? $leavesModel->getEmployeesOnLeave($selectedDate) : [];
$hasEmployeeOnLeave = !empty($employeesOnLeave) && $employeesOnLeave[0]['id'] == $employeeId; // Sprawdzamy, czy aktualny pracownik ma urlop w wybranym dniu
?>
<div class="calendar-container panel" style="padding-top: 11rem !important;">
    <div class="calendar-header">
        <a href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="nav-btn"><i class="fa-solid fa-arrow-left"></i></a>
        <h2><?php echo date('F Y', $firstDayOfMonth); ?></h2>
        <a href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="nav-btn"><i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <table class="calendar-table">
        <thead>
            <tr>
                <th>Pn</th>
                <th>Wt</th>
                <th>Śr</th>
                <th>Cz</th>
                <th>Pt</th>
                <th>Sb</th>
                <th>Nd</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $day = 1;
            for ($row = 0; $row < 6; $row++) {
                echo "<tr>";

                for ($col = 1; $col <= 7; $col++) {
                    if (($row === 0 && $col < $firstWeekday) || $day > $daysInMonth) {
                        echo "<td class='empty'></td>";
                    } else {
                        // Tworzenie $dateString
                        $dateString = sprintf("%04d-%02d-%02d", $year, $month, $day);

                        // Sprawdzanie, czy dzień jest dniem urlopowym
                        $isLeaveDay = in_array($dateString, $allTakenDates);
                        $isSelected = $dateString == $selectedDate ? 'selected_' : ''; // Zaznaczenie wybranego dnia

                        echo "<td class='" . ($isLeaveDay ? "leave_day-- " : "") . "$isSelected' data-date='$dateString'>$day</td>";
                        $day++;
                    }
                }

                echo "</tr>";

                if ($day > $daysInMonth) {
                    break;
                }
            }
            ?>
        </tbody>
    </table>
    <h4 style="margin-top: 2rem;">
        Wybierz dzień aby sprawdzić kto ma wtedy urlop.
    </h4>
    <div id="leave-info" class="leave-info">
        <?php if ($selectedDate): ?>
            <h3>Pracownicy na urlopie w dniu: <?php echo htmlspecialchars($selectedDate); ?></h3>
            <?php if (!empty($employeesOnLeave)): ?>
                    <?php foreach ($employeesOnLeave as $employee): ?>
                        <p class="calendar_pracownik"><?php echo htmlspecialchars($employee['imie'] . ' ' . $employee['nazwisko']); ?></p>
                    <?php endforeach; ?>
            <?php else: ?>
                <p>Brak pracowników na urlopie w tym dniu.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    document.querySelectorAll('.calendar-table td.leave_day--').forEach(function(td) {
        td.addEventListener('click', function() {
            var date = this.getAttribute('data-date');
            if (date) {
                console.log(date)
                // Usunięcie klasy 'selected' ze wszystkich komórek
                document.querySelectorAll('.calendar-table td.selected_').forEach(function(cell) {
                    cell.classList.remove('selected_');
                });

                // Dodanie klasy 'selected' do klikniętej komórki
                this.classList.add('selected_');

                // Wysłanie AJAX request do serwera
                fetch('../utils/getLeaves.php?date=' + date)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        var leaveInfoDiv = document.getElementById('leave-info');
                        leaveInfoDiv.innerHTML = '';

                        if (data.length > 0) {
                            var div = document.createElement('div');
                            div.className = 'calendar_pracownik';
                            div.textContent = data[0].imie + ' ' + data[0].nazwisko;
                            leaveInfoDiv.appendChild(div);
                        } else {
                            leaveInfoDiv.textContent = 'Brak pracowników na urlopie w tym dniu.';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                        alert('Wystąpił błąd podczas pobierania danych.');
                    });
            }
        });
    });
</script>

<style>
    .leave_day-- {
        background-color: #ffcccc;
        color: #800000;
        cursor: pointer;
    }

    .empty {
        background-color: #f0f0f0;
    }

    .selected_ {
        background-color:rgb(92, 0, 0);
        color: white;
    }
    .calendar_pracownik{
        margin-top: 2rem;
        font-size: 24px;
    }
</style>