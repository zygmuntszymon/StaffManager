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
            <a href="zadania.php"> Zadania</a> &nbsp
            <a href="zadania_realizacja.php">Realizacja</a> &nbsp
            <a href="zadania_ukonczone.php">Ukończone</a>
        </h3>
    </div>
    <h3 class="lista_pracownikow_header">
        Lista zadań w realizacji
    </h3>


    <div class="zadania_tabela">
        <div class="zadania_naglowek">
            <span>Opis</span>
            <span>Termin</span>
            <span>Punkty</span>
            <span>Status</span>
            <span>Pracownik</span>
        </div>
        <?php if (!empty($tasksToBeDone)) { ?>
            <?php foreach ($tasksToBeDone as $task) {
                $workerName = $tasksModel->getNameAndSurnameWorker($task['id']); ?>
                <div class='zadanie'>
                    <span class='zadanie_nazwa'><?= htmlspecialchars($task['opis']) ?></span>
                    <span class='zadanie_deadline'><?= htmlspecialchars($task['deadline']) ?></span>
                    <span class='zadanie_punkty'><?= htmlspecialchars($task['ilosc_punkty']) ?> <i class="fa-solid fa-trophy"></i></span>
                    <span class='zadanie_status'><?= htmlspecialchars($task['status']) ?></span>
                    <span class='zadanie_pracownik'><?= htmlspecialchars($workerName) ?></span>
                </div>
            <?php } ?>
        <?php } else {
            echo "<p>Brak zadań w bazie danych.</p>";
        } ?>
    </div>