<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../models/Leaves.php';

if (!isset($_SESSION['user_id'])) {
    die("Użytkownik nie zalogowany!");
}
$employeeId = $_SESSION['user_id'];

// pobieranie miesiąca i roku
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// pobieranie dni urlopowych użytkownika
$leavesModel = new Leaves($pdo);
$takenDates = $leavesModel->getEmployeeTakenDates($employeeId);

// usunięcie spacji z dat w tablicy $takenDates
$takenDates = array_map('trim', $takenDates);

// tworzy znacznik czasu dla pierwszego dnia miesiąca
$firstDayOfMonth = strtotime("$year-$month-01");
$daysInMonth = date('t', $firstDayOfMonth);
$firstWeekday = date('N', $firstDayOfMonth); // (1 = Pn, 7 = Nd)

// poprzedni i następny miesiąc
$prevMonth = ($month == 1) ? 12 : $month - 1;
$prevYear = ($month == 1) ? $year - 1 : $year;

$nextMonth = ($month == 12) ? 1 : $month + 1;
$nextYear = ($month == 12) ? $year + 1 : $year;
?>
<div class="calendar-container">
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
                        $dateString = sprintf("%04d-%02d-%02d", $year, $month, $day);
                        
            
                        $isLeaveDay = in_array($dateString, $takenDates);
            
                        echo "<td class='" . ($isLeaveDay ? "leave-day" : "") . "'>$day</td>";
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
</div>
<?php

?>