<?php
include './header.php';
require_once dirname(__DIR__) . '/utils/session.php';
require_once dirname(__DIR__) . '/models/Tasks.php';

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
        <?php
        $currentFileName = basename($_SERVER['PHP_SELF']);
        ?>
        <div class="zadania_menu_header">
            <a href="zadania.php" class="<?= ($currentFileName == 'zadania.php') ? 'selected' : ''; ?>">Zadania</a>
            <a href="zadania_realizacja.php" class="<?= ($currentFileName == 'zadania_realizacja.php') ? 'selected' : ''; ?>">W realizacji</a>
            <a href="zadania_ukonczone.php" class="<?= ($currentFileName == 'zadania_ukonczone.php') ? 'selected' : ''; ?>">Ukończone</a>
        </div>
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
            echo "<p style='margin:2rem'>Brak zadań w realizacji.</p>";
        } ?>
    </div>

    <script>
        window.addEventListener('load', function() {
            var links = document.querySelectorAll('.zadania_menu_header a');
            var currentURL = window.location.href;

            links.forEach(function(link) {
                if (currentURL.endsWith(link.getAttribute('href'))) {
                    link.classList.add('selected');
                }
            });
        });
    </script>