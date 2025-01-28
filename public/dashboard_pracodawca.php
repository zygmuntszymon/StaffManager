<?php
include '../app/views/header.php';
require_once __DIR__ . '/../app/utils/session.php';
require_once __DIR__ . '/../app/models/User.php';

Session::start();

if (!Session::isLoggedIn()) {
    header("Location: login.php");
    exit();
}
$userModel = new User($pdo);
$user = $userModel->getUserByLogin($_SESSION['login']);
$users = $userModel->getAllUsers();
$message = "";

// Dodwanie Pracownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_worker_btn'])) {
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $pesel = $_POST['pesel'];
    $rola = $_POST['rola'];
    $haslo = $_POST['haslo'];

    if ($userModel->createUser($imie, $nazwisko, $pesel, $rola, $haslo)) {
        $message = "Pracownik został dodany pomyślnie!";
        // Odświeżanie listy pracwoników
        $users = $userModel->getAllUsers();
    } else {
        $message = "Wystąpił błąd podczas dodawania pracownika.";
    }
}

// Usuwanie Pracownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_worker_btn'])) {
    $id = $_POST['id'];

    if ($userModel->deleteUser($id)) {
        $message = "Pracownik został usunięty pomyślnie!";
        // Odświeżanie listy pracwoników
        $users = $userModel->getAllUsers();
    } else {
        $message = "Wystąpił błąd podczas usuwania pracownika.";
    }
}


// Edytowanie Pracownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_worker_btn'])) {
    $id = $_POST['id'];
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $pesel = $_POST['pesel'];
    $rola = $_POST['rola'];
    $haslo = $_POST['haslo']; // może być puste

    $success = $userModel->updateUser($id, $imie, $nazwisko, $pesel, $rola, $haslo);
    if ($success) {
        $message = "Użytkownik został zaktualizowany!";
        // odświeżenie listy pracowników:
        $users = $userModel->getAllUsers();
    } else {
        $message = "Wystąpił błąd podczas aktualizacji danych.";
    }
}
?>

<div class="panel">
    <p>
        Witaj <b><?php echo ($user['imie'] . " " . $user['nazwisko'] . " (" . $user['rola'] . ")"); ?></b>
    </p>
    <div class="lista_pracownikow_container">
        <h3 class="lista_pracownikow_header">
            Lista pracowników:
            <button type="button" onclick="openModalAdd()" class="add_worker_btn">Dodaj &nbsp; <i
                    class="fa-solid fa-plus"></i></button>
        </h3>

        <?php
        if (!empty($users)) {
            foreach (
                $users

                as $user
            ) {
                echo "<div class='pracownik'>";
                echo "<span class='pracownik_nazwisko'>" . htmlspecialchars($user['nazwisko']) . "</span>";
                echo "<span class='pracownik_imie'>" . htmlspecialchars($user['imie']) . "</span>";
                echo "<span class='pracownik_rola'>" . htmlspecialchars($user['rola']) . "</span>";
                echo "<span class='pracownik_punkty'>" . htmlspecialchars($user['punkty']) . "</span>";
                echo "<div class='pracownik_akcje'>";
        ?>
                <button type="button" class="delete_worker_btn" onclick="openModalDelete(<?php echo $user['id']; ?>)">Usuń
                </button>
                <button type="button" class="update_worker_btn" onclick="openModalUpdate(<?php echo $user['id']; ?>,
                '<?php echo htmlspecialchars($user['imie'], ENT_QUOTES); ?>',
                '<?php echo htmlspecialchars($user['nazwisko'], ENT_QUOTES); ?>',
                '<?php echo htmlspecialchars($user['pesel'], ENT_QUOTES); ?>',
                '<?php echo htmlspecialchars($user['rola'], ENT_QUOTES); ?>')">Edytuj
                </button>
    </div>
</div>
<?php
            }
        } else {
            echo "Brak pracowników w bazie danych.";
        }
?>


<!-- Okno Modalne dla dodawania-->
<div id="add_worker_modal" class="modal" style="display: none;">
    <div class="add_worker_form">
        <form id="add_Worker_form" method="POST" action="">
            <h3>Dodaj Pracownika</h3>
            <input type="text" name="imie" placeholder="Imię" required>
            <input type="text" name="nazwisko" placeholder="Nazwisko" required>
            <input type="text" name="pesel" placeholder="PESEL" required pattern="\d{11}">
            <select name="rola" required>
                <option value="" disabled selected>Wybierz rolę</option>
                <option value="pracownik">Pracownik</option>
                <option value="pracodawca">Pracodawca</option>
            </select>
            <div style="position: relative; width: 100%;">
                <input type="password" id="haslo" name="haslo" placeholder="Hasło" required>
            </div>
            <button type="submit" name="add_worker_btn">Dodaj</button>
            <button type="button" class="cancel-btn" onclick="closeModal()">Anuluj</button>
        </form>
    </div>
</div>
<!-- Okno Modalne do usuwania pracownika-->
<div id="delete_worker_modal" class="modal" style="display: none;">
    <div class="delete_worker_modal">
        <form id="delete_worker_form" method="POST" action="">
            <input type="hidden" name="id" id="delete_worker_id" value="">
            <h3>Usuń Pracownika</h3>
            <label>Czy napewno chcesz usunać pracownika?</label>
            <div>
                <button type="submit" name="delete_worker_btn" class="delete_worker_btn">Usuń</button>
                <button type="button" class="cancel-btn" onclick="closeModalDelete()">Anuluj</button>
            </div>

        </form>
    </div>
</div>
<!-- Okno Modalne do edytowania pracownika-->
<div id="update_worker_modal" class="modal" style="display: none;">
    <div class="update_worker_form">
        <form id="update_worker_form" method="POST" action="">
            <h3>Edytuj Pracownika</h3>
            <input type="hidden" name="id" id="update_user_id" value="">
            <input type="text" name="imie" id="update_imie" placeholder="Imię" required>
            <input type="text" name="nazwisko" id="update_nazwisko" placeholder="Nazwisko" required>
            <input type="text" name="pesel" id="update_pesel" placeholder="PESEL" required pattern="\d{11}">
            <select name="rola" id="update_rola" required>
                <option value="pracownik">Pracownik</option>
                <option value="pracodawca">Pracodawca</option>
            </select>
            <input type="password" id="update_haslo" name="haslo" placeholder="Hasło (zostaw puste, aby nie zmieniać)">
            <button type="submit" name="update_worker_btn">Zapisz zmiany</button>
            <button type="button" class="cancel-btn" onclick="closeModalUpdate()">Anuluj</button>
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

    function openModalDelete(userId) {
        document.getElementById('delete_worker_id').value = userId;
        document.getElementById('delete_worker_modal').style.display = 'flex';
    }

    function closeModalDelete() {
        document.getElementById('delete_worker_modal').style.display = 'none';
    }

    function openModalUpdate(id, imie, nazwisko, pesel, rola) {
        document.getElementById('update_user_id').value = id;
        document.getElementById('update_imie').value = imie;
        document.getElementById('update_nazwisko').value = nazwisko;
        document.getElementById('update_pesel').value = pesel;
        document.getElementById('update_rola').value = rola;

        document.getElementById('update_worker_modal').style.display = 'flex';
    }

    function closeModalUpdate() {
        document.getElementById('update_worker_modal').style.display = 'none';
    }
</script>