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
$tasksToBeDone = $tasksModel->getTasksByStatus('do wykonania');
$workersList = $tasksModel->getAllWorkers();
$message = "";

// Dodwanie Zadania
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_zadanie_btn'])) {
    $opis = $_POST['opis'];
    $deadline = $_POST['deadline'];
    $ilosc_punkty = $_POST['ilosc_punkty'];
    $status = $_POST['status'];

    if ($tasksModel->addTask($opis, $deadline, $ilosc_punkty, $status)) {
        $message = "Zadanie zostało dodane pomyślnie!";
        // odświeżenie listy
        $tasksToBeDone = $tasksModel->getTasksByStatus('do wykonania');
    } else {
        $message = "Wystąpił błąd podczas dodawania zadania.";
    }
}

// usuwanie Pracownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_zadanie_btn'])) {
    $id = $_POST['id'];

    if ($tasksModel->deleteTask($id)) {
        $message = "Zadanie zostało usunięte pomyślnie!";
        // odświeżenie listy
        $tasksToBeDone = $tasksModel->getTasksByStatus('do wykonania');
    } else {
        $message = "Wystąpił błąd podczas usuwania pracownika.";
    }
}

// edytowanie Pracownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task_btn'])) {
    $id = $_POST['id'];
    $opis = $_POST['opis'];
    $deadline = $_POST['deadline'];
    $ilosc_punkty = $_POST['ilosc_punkty'];

    $success = $tasksModel->updateTask($id, $opis, $deadline, $ilosc_punkty);
    if ($success) {
        $message = "Użytkownik został zaktualizowany!";
        $tasksToBeDone = $tasksModel->getTasksByStatus('do wykonania');
    } else {
        $message = "Wystąpił błąd podczas aktualizacji danych.";
    }
}
// przypisywanie zadania dla Prcownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['przypisz_btn'])) {
    $pracownik_id = $_POST['pracownik_id'];
    $zadanie_id = $_POST['zadanie_id'];

    if ($tasksModel->taskForUser($pracownik_id, $zadanie_id)) {
        $message = "Zadanie zostało przypisane!";
        // odświeżenie listy
        $tasksToBeDone = $tasksModel->getTasksByStatus('do wykonania');
    } else {
        $message = "Wystąpił błąd podczas przypisania zadania.";
    }
}


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
        Lista zadań:
        <button type="button" onclick="openModalAdd()" class="add_btn">Dodaj Zadanie&nbsp; <i
                class="fa-solid fa-plus"></i></button>
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
                    <div class='zadanie_akcje'>
                        <button type="button" class="delete_btn--icon" onclick="openModalDelete(<?= $task['id'] ?>)"><i class="fa-solid fa-trash-can"></i></button>
                        <button type="button" class="update_btn--icon" onclick="openModalUpdate(
                        <?= $task['id'] ?>,
                        '<?= htmlspecialchars($task['opis'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($task['deadline'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($task['ilosc_punkty'], ENT_QUOTES) ?>'
                    )"><i class="fa-solid fa-pen-to-square"></i></button>
                        <button type="button" class="action_btn--green" onclick="openModalPrzypisz(<?= $task['id'] ?>)">Przypisz</button>
                    </div>
                </div>
            <?php } ?>
        <?php } else {
            echo "<p style='margin:2rem'>Brak nieprzypisanych zadań.</p>";
        } ?>
    </div>

    <!-- Okno Modalne dodawania-->
    <div id="add_worker_modal" class="modal" style="display: none;">
        <div class="add_worker_form">
            <form id="add_Worker_form" method="POST" action="">
                <h3>Dodaj Zadanie</h3>
                <input type="text" name="opis" placeholder="Opis" required>
                <input type="date" name="deadline" placeholder="Deadline" required>
                <input type="text" name="ilosc_punkty" placeholder="Ilość punktów" required>
                <input type="hidden" name="status" value="do wykonania">
                <button type="submit" name="add_zadanie_btn">Dodaj</button>
                <button type="button" class="btn_modal--cancel" onclick="closeModal()">Anuluj</button>
            </form>
        </div>
    </div>

    <!-- Okno Modalne usuwania-->
    <div id="delete_worker_modal" class="modal" style="display: none;">
        <div class="delete_worker_modal">
            <form id="delete_worker_form" method="POST" action="">
                <input type="hidden" name="id" id="delete_zadanie_id" value="">
                <h3>Usuwanie Zadania</h3>
                <label>Czy napewno chcesz usunać zadanie?</label>
                <div>
                    <button type="submit" name="delete_zadanie_btn" class="btn_modal--delete">Usuń</button>
                    <button type="button" class="btn_modal--cancel" onclick="closeModalDelete()">Anuluj</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Okno Modalne edytowania -->
    <div id="update_worker_modal" class="modal" style="display: none;">
        <div class="update_worker_form">
            <form id="update_worker_form" method="POST" action="">
                <h3>Edytuj Pracownika</h3>
                <input type="hidden" name="id" id="update_user_id" value="">
                <input type="text" name="opis" id="update_opis" placeholder="Opis" required>
                <input type="date" name="deadline" id="update_deadline" placeholder="Deadline" required>
                <input type="text" name="ilosc_punkty" id="update_ilosc_punkty" placeholder="Ilość punktów" required>
                <button type="submit" name="update_task_btn">Zapisz zmiany</button>
                <button type="button" class="cancel-btn" onclick="closeModalUpdate()">Anuluj</button>
            </form>
        </div>
    </div>

    <!-- Okno Modalne przypisania-->
    <div id="przypisz_modal" class="modal" style="display: none;">
        <div class="przypisz_modal">
            <form id="przypisz_modal" method="POST" action="">
                <h3>Przypisz zadanie</h3>
                <select name="pracownik_id" required>
                    <option value="" disabled selected>Wybierz pracownika</option>
                    <?php foreach ($workersList as $worker): ?>
                        <option value="<?php echo $worker['id']; ?>">
                            <?php echo htmlspecialchars($worker['imie'] . ' ' . $worker['nazwisko'], ENT_QUOTES); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="zadanie_id" id="przypisz_zadanie_id" value="">
                <div>
                    <button type="submit" name="przypisz_btn" class="btn_modal--accept">Przypisz</button>
                    <button type="button" class="btn_modal--cancel" onclick="closeModalPrzypisz()">Anuluj</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        function openModalAdd() {
            document.getElementById('add_worker_modal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('add_worker_modal').style.display = 'none';
        }

        function openModalDelete(taskId) {
            document.getElementById('delete_zadanie_id').value = taskId;
            document.getElementById('delete_worker_modal').style.display = 'flex';
        }

        function closeModalDelete() {
            document.getElementById('delete_worker_modal').style.display = 'none';
        }

        function openModalUpdate(id, opis, deadline, ilosc_punkty) {
            document.getElementById('update_user_id').value = id;
            document.getElementById('update_opis').value = opis;
            document.getElementById('update_deadline').value = deadline;
            document.getElementById('update_ilosc_punkty').value = ilosc_punkty;

            document.getElementById('update_worker_modal').style.display = 'flex';
        }

        function closeModalUpdate() {
            document.getElementById('update_worker_modal').style.display = 'none';
        }

        function openModalPrzypisz(taskId) {
            document.getElementById('przypisz_zadanie_id').value = taskId;
            document.getElementById('przypisz_modal').style.display = 'flex';
        }

        function closeModalPrzypisz() {
            document.getElementById('przypisz_modal').style.display = 'none';
        }
    </script>