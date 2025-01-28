<?php
include '../app/views/header.php';
require_once __DIR__ . '/../app/utils/session.php';
require_once __DIR__ . '/../app/models/Tasks.php';

Session::start();

if (!Session::isLoggedIn()) {
    header("Location: login.php");
    exit();
}
$tasksModel = new Tasks($pdo);
$tasksToBeDone = $tasksModel->getTasksByStatus('w realizacji');
$message =  "";
?>
<div class="panel">
    <div class="zadania_menu_container">
        <h3 class="zadania_menu_header">
            <a href="zadania.php">    Zadania</a> &nbsp
            <a href="zadania_realizacja.php">Realizacja</a> &nbsp
            <a href="zadania_ukonczone.php">Ukończone</a>
        </h3>
    </div>
        <h3 class="lista_pracownikow_header">
            Lista zadań w realizacji:
        </h3>


        <?php
        if (!empty($tasksToBeDone)) {
        foreach ($tasksToBeDone as $task) {
        $workerName = $tasksModel->getNameAndSurnameWorker($task['id']);
        echo "<div class='pracownik'>";
        echo "<span class='pracownik_nazwisko'>" . htmlspecialchars($task['opis']) . "</span>";
        echo "<span class='pracownik_imie'>" . htmlspecialchars($task['deadline']) . "</span>";
        echo "<span class='pracownik_rola'>" . htmlspecialchars($task['ilosc_punkty']) .  ' <i class="fa-solid fa-trophy"></i>' . "</span>";
        echo "<span class='pracownik_rola'>" . htmlspecialchars($task['status']) . "</span>";
        echo "<span class='pracownik_rola'>" . htmlspecialchars($workerName) . "</span>";
        echo "<div class='pracownik_akcje'>";
        ?>
    </div>
</div>
<?php
}
} else {
    echo "Brak zadań w bazie danych.";
}
?>

