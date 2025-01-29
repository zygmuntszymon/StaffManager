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
$hasPendingTasks = false;
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_done'])) {
    $taskId = $_POST['task_id'];

    if ($tasksModel->markTaskAsDone($taskId, $user['id'])) {
        $message = "<p>Zadanie zostało oznaczone jako wykonane.</p>";
        header("Location: dashboard_pracownik.php");
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
            <?php if ($hasPendingTasks): ?>
                <div class="zadania__naglowek">
                    <span>Nazwa</span>
                    <span>Termin</span>
                </div>
            <?php endif; ?>
            
            <?php
            foreach ($zadania as $zadanie) {
                if ($zadanie['status'] == 'w realizacji') {
                    $hasPendingTasks = true;
                    echo "<div class='zadanie'>";
                    echo "<span class='zadanie_opis'>" . htmlspecialchars($zadanie['opis']) . "</span>";
                    echo "<div class='zadanie__akcje'>";
                    echo "<span class='deadline'>" . htmlspecialchars($zadanie['deadline']) . "</span>";
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
            <?php $hasCompletedTasks = false; ?>
            <?php
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

    <?php else: ?>
        <p style='margin:2rem'>Nie masz przypisanych żadnych zadań.</p>
    <?php endif; ?>
</div>
