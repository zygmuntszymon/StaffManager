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

// Usuwanie zadania
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_zadanie_btn'])) {
    $id = $_POST['id'];

    if ($tasksModel->deleteTask($id)) {
        $message = "Zadanie zostało usunięte pomyślnie!";
        // Odświeżenie listy $tasks
        $tasksToBeDone = $tasksModel->getTasksByStatus('w realizacji');
    } else {
        $message = "Wystąpił błąd podczas usuwania pracownika.";
    }
}
?>
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
        <button type="button" class="delete_zadanie_btn" onclick="openModalDelete(<?php echo $task['id']; ?>)">Usuń
        </button>
    </div>
</div>
<?php
}
} else {
    echo "Brak zadań w bazie danych.";
}
?>

<!-- Okno Modalne do usuwania zadania-->
<div id="delete_worker_modal" class="modal" style="display: none;">
    <div class="delete_worker_modal">
        <form id="delete_worker_form" method="POST" action="">
            <input type="hidden" name="id" id="delete_zadanie_id" value="">
            <h3>Usuwanie Zadania</h3>
            <label>Czy napewno chcesz usunać zadanie?</label>
            <div>
                <button type="submit" name="delete_zadanie_btn" class="delete_zadanie_btn">Usuń</button>
                <button type="button" class="cancel-btn" onclick="closeModalDelete()">Anuluj</button>
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

