<?php

include '../app/views/header.php';
require_once __DIR__ . '/../app/utils/session.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Tasks.php';

Session::start();

if (!Session::isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$userModel = new User($pdo);
$user = $userModel->getUserByLogin($_SESSION['login']);
$tasksModel = new Tasks($pdo);
$zadania = $tasksModel->getTasksForUser($user['id']);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_done'])) {
    $taskId = $_POST['task_id'];
    
    if ($tasksModel->markTaskAsDone($taskId)) {
        $message = "<p>Zadanie zostało oznaczone jako wykonane.</p>";

        header("Refresh: 1; URL=dashboard_pracownik.php");
        exit(); 
    } else {
        $message = "<p>Wystąpił błąd podczas aktualizacji statusu zadania.</p>";
    }
}

?>

<div class="panel">
    <p>
        Witaj <b><?php echo ($user['imie'] . " " . $user['nazwisko']); ?></b>
    </p>
    
    <?php if (!empty($message)) {
        echo $message;
    } ?>

    <?php if (count($zadania) > 0): ?>

        <div class="zadania-container">
            <h3 class="zadania_header">Zadania do wykonania:</h3>
            <?php
            $hasPendingTasks = false;
            foreach ($zadania as $zadanie) {
                if ($zadanie['status'] == 'w realizacji') {
                    $hasPendingTasks = true;
                    echo "<div class='zadanie'>";
                    echo "<span class='zadanie_opis'>" . htmlspecialchars($zadanie['opis']) . "</span>";
                    echo "<div class='zadanie_akcje'>";
                    echo "<span class='deadline'>Deadline: " . htmlspecialchars($zadanie['deadline']) . "</span>";
                    ?>
                    <form action="dashboard_pracownik.php" method="POST" style="display:inline;">
                        <input type="hidden" name="task_id" value="<?php echo $zadanie['id']; ?>">
                        <button type="submit" name="mark_done" class="btn_wykonane">Wykonano &nbsp; <i class="fa-solid fa-thumbs-up"></i></button>
                    </form>
                    </div>
                    </div>
                    <?php
                }
            }
            if (!$hasPendingTasks) {
                echo "<p>Brak zadań do wykonania.</p>";
            }
            ?>
        </div>

        <div class="zadania-container">
            <h3 class="zadania_header">Zadania wykonane:</h3>
            <?php
            $hasCompletedTasks = false;
            foreach ($zadania as $zadanie) {
                if ($zadanie['status'] == 'ukończone') {
                    $hasCompletedTasks = true;
                    echo "<div class='zadanie'>";
                    echo "<span class='zadanie_opis'>" . htmlspecialchars($zadanie['opis']) . "</span>";
                    echo "<span class='status'>" . htmlspecialchars($zadanie['status']) . "</span>";
                    echo "</div>";
                }
            }
            if (!$hasCompletedTasks) {
                echo "<p>Brak zadań wykonanych.</p>";
            }
            ?>
        </div>

        <div class="zadania-container">
            <h3 class="zadania_header">Niewykonane:</h3>
            <?php
            $hasOverdueTasks = false;
            foreach ($zadania as $zadanie) {
                if ($zadanie['status'] == 'do wykonania' && strtotime($zadanie['deadline']) < time()) {
                    $hasOverdueTasks = true;
                    echo "<div class='zadanie'>";
                    echo "<span class='zadanie_opis'>" . htmlspecialchars($zadanie['opis']) . "</span>";
                    echo "<div class='zadanie_akcje'>";
                    echo "<span class='deadline' style='color: #942e2e'>Deadline: " . htmlspecialchars($zadanie['deadline']) . "</span>";
                    ?>
                    <form action="dashboard_pracownik.php" method="POST" style="display:inline;">
                        <input type="hidden" name="task_id" value="<?php echo $zadanie['id']; ?>">
                        <button type="submit" name="mark_done" class="btn_wykonane--red">Wykonano &nbsp; <i class="fa-solid fa-thumbs-up"></i></button>
                    </form>
                    </div>
                    </div>
                    <?php
                }
            }
            if (!$hasOverdueTasks) {
                echo "<p>Brak niewykonanych zadań po terminie.</p>";
            }
            ?>
        </div>

    <?php else: ?>
        <p>Nie masz przypisanych żadnych zadań.</p>
    <?php endif; ?>
</div>


<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_done'])) {
    $taskId = $_POST['task_id'];
    echo "<p>Otrzymano POST dla zadania: $taskId</p>";
    
    if ($tasksModel->markTaskAsDone($taskId)) {
        echo "<p>Zadanie zostało oznaczone jako wykonane.</p>";
        header("Refresh: 1; URL=dashboard_pracownik.php");
    } else {
        echo "<p>Wystąpił błąd podczas aktualizacji statusu zadania.</p>";
    }
}
?>
