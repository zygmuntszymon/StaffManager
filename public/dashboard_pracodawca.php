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
?>

<div class="panel">
    <p>
        Witaj <b><?php echo($user['imie'] . " " . $user['nazwisko'] . " (" . $user['rola'] . ")"); ?></b>
    </p>
    <div class="lista_pracownikow_container">
        <h3 class="lista_pracownikow_header">
            Lista pracowników:
            <button type="button" onclick="openModal()" class="add_worker_btn">Dodaj &nbsp; <i class="fa-solid fa-plus"></i></button>
        </h3>

        <?php
        if (!empty($users)) {
                foreach ($users as $user) {
                echo "<div class='pracownik'>";
                echo "<span class='pracownik_nazwisko'>" . htmlspecialchars($user['nazwisko']) . "</span>";
                //echo "<div class='pracownik'>";
                echo "<span class='pracownik_imie'>" . htmlspecialchars($user['imie']) . "</span>";
                //echo "<div class='pracownik'>";
                echo "<span class='pracownik_rola'>" . htmlspecialchars($user['rola']) . "</span>";
                echo "<div class='pracownik_akcje'>";
                ?>
                <td>
                    <button type="submit" name="mark_done" class="btn_wykonane">Dodaj zadanie &nbsp; <i class="fa-solid fa-plus"></i></button>
                </td>
                </div>
                </div>
                <?php
            }
        } else {
            echo "Brak pracowników w bazie danych.";
        }
        ?>


    </div>
</div>
<div id="add_worker_modal" class="modal" style="display: none;">
    <div class="add_worker_form">
        <form id="add_Worker_Form" method="POST" action="">
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

<script>
    function openModal() {
        document.getElementById('add_worker_modal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('add_worker_modal').style.display = 'none';
    }
</script>
