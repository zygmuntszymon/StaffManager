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
$tasksToBeDone = $tasksModel->getTasksByStatus('ukończone');
$message = "";


// Usuwanie Pracownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_zadanie_btn'])) {
    $id = $_POST['id'];

    if ($tasksModel->deleteTask($id)) {
        $message = "Zadanie zostało usunięte pomyślnie!";
        // Odświeżenie listy $tasks
        $tasksToBeDone = $tasksModel->getTasksByStatus('ukończone');
    } else {
        $message = "Wystąpił błąd podczas usuwania pracownika.";
    }
}
?>
<div class="panel">
    <div class="zadania_menu_container">
        <h3 class="zadania_menu_header">
            <a href="zadania.php">Zadania</a>
            <a href="zadania_realizacja.php">Realizacja</a>
            <a href="zadania_ukonczone.php">Ukończone</a>
        </h3>
    </div>
        <h3 class="lista_pracownikow_header">
            Ukończone zadania
        </h3>

        <div class="zadania_tabela">
    <div class="zadania_naglowek">
        <span>Opis</span>
        <span>Termin</span>
        <span>Punkty</span>
        <span>Status</span>
        <span>Akcje</span>
    </div>
    <?php if (!empty($tasksToBeDone)) { ?>
        <?php foreach ($tasksToBeDone as $task) { ?>
            <div class='zadanie'>
                <span class='zadanie_nazwa'><?= htmlspecialchars($task['opis']) ?></span>
                <span class='zadanie_deadline'><?= htmlspecialchars($task['deadline']) ?></span>
                <span class='zadanie_punkty'><?= htmlspecialchars($task['ilosc_punkty']) ?> <i class="fa-solid fa-trophy"></i></span>
                <span class='zadanie_status'><?= htmlspecialchars($task['status']) ?></span>
                <span class='zadanie_akcje'>
                    <button type="button" class="delete_btn--icon" onclick="openModalDelete(<?= $task['id'] ?>)"><i class="fa-solid fa-trash-can"></i></button>
                </span>
            </div>
        <?php } ?>
    <?php } else { ?>
        <p>Brak ukończonych zadań w bazie danych.</p>
    <?php } ?>
</div>


<!-- Okno Modalne do usuwania pracownika-->
<div id="delete_worker_modal" class="modal" style="display: none;">
    <div class="delete_worker_modal">
        <form id="delete_worker_form" method="POST" action="">
            <input type="hidden" name="id" id="delete_zadanie_id" value="">
            <h3>Usuwanie Zadania</h3>
            <label>Czy napewno chcesz usunać zadanie?</label>
            <div>
                <button type="submit" name="delete_btn" class="btn_modal--accept">Usuń</button>
                <button type="button" class="btn_modal--cancel" onclick="closeModalDelete()">Anuluj</button>
            </div>

        </form>
    </div>
</div>

<script>
    function openModalDelete(taskId) {
        document.getElementById('delete_zadanie_id').value = taskId;
        document.getElementById('delete_worker_modal').style.display = 'flex';
    }

    function closeModalDelete() {
        document.getElementById('delete_worker_modal').style.display = 'none';
    }
</script>