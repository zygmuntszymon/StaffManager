<?php
include './header.php';
require_once dirname(__DIR__) . '/utils/session.php';
require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(__DIR__) . '/models/Points.php';
Session::start();

if (!Session::isLoggedIn()) {
    header("Location: login.php");
    exit();
}
$userModel = new User($pdo);
$user = $userModel->getUserByLogin($_SESSION['login']);
$users = $userModel->getAllUsers();
$message = "";

// dodawanie Pracownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_worker_btn'])) {
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $pesel = $_POST['pesel'];
    $rola = $_POST['rola'];
    $haslo = $_POST['haslo'];

    if ($userModel->createUser($imie, $nazwisko, $pesel, $rola, $haslo)) {
        $message = "Pracownik został dodany pomyślnie!";
        $users = $userModel->getAllUsers();
    } else {
        $message = "Wystąpił błąd podczas dodawania pracownika.";
    }
}

// usuwanie Pracownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_worker_btn'])) {
    $id = $_POST['id'];

    if ($userModel->deleteUser($id)) {
        $message = "Pracownik został usunięty pomyślnie!";
        $users = $userModel->getAllUsers();
    } else {
        $message = "Wystąpił błąd podczas usuwania pracownika.";
    }
}

// edytowanie Pracownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_worker_btn'])) {
    $id = $_POST['id'];
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $pesel = $_POST['pesel'];
    $rola = $_POST['rola'];
    $haslo = $_POST['haslo'];

    if ($userModel->updateUser($id, $imie, $nazwisko, $pesel, $rola, $haslo)) {
        $message = "Użytkownik został zaktualizowany!";
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
            <button type="button" onclick="openModalAdd()" class="add_btn">Dodaj &nbsp; <i class="fa-solid fa-plus"></i></button>
        </h3>

        <?php if (!empty($users)) { ?>
            <div class="pracownicy_tabela">
                <div class="pracownicy_naglowek">
                    <span>Nazwisko</span>
                    <span>Imię</span>
                    <span>Rola</span>
                    <span>Punkty</span>
                    <span >Akcje</span>
                </div>
                <?php foreach ($users as $user) { ?>
                    <div class='pracownik'>
                        <span class='pracownik_nazwisko'><?= htmlspecialchars($user['nazwisko']) ?></span>
                        <span class='pracownik_imie'><?= htmlspecialchars($user['imie']) ?></span>
                        <span class='pracownik_rola'><?= htmlspecialchars($user['rola']) ?></span>
                        <span class='pracownik_punkty'><?= htmlspecialchars($user['punkty']) ?><i class="fa-solid fa-trophy"></i></span>
                        <div class='pracownik_akcje'>
                            <button type="button" class="delete_btn--icon" onclick="openModalDelete(<?= $user['id'] ?>)"><i class="fa-solid fa-trash-can"></i></button>
                            <button type="button" class="update_btn--icon" onclick="openModalUpdate(
                                <?= $user['id'] ?>,
                                '<?= htmlspecialchars($user['imie'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($user['nazwisko'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($user['pesel'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($user['rola'], ENT_QUOTES) ?>'
                            )"><i class="fa-solid fa-pen-to-square"></i></button>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else {
            echo "<p>Brak pracowników w bazie danych.</p>";
        } ?>
    </div>
</div>

<!-- Okno Modalne dodwanie-->
<div id="add_worker_modal" class="modal" style="display: none;">
    <div class="add_worker_form">
        <form id="add_Worker_form" method="POST" action="" onsubmit="return validateAddForm()">
            <h3>Dodaj Pracownika</h3>
            <input type="text" name="imie" id="imie" placeholder="Imię" required>
            <input type="text" name="nazwisko" id="nazwisko" placeholder="Nazwisko" required>
            <input type="text" name="pesel" id="pesel" placeholder="PESEL" required pattern="\d{11}">
            <select name="rola" required>
                <option value="" disabled selected>Wybierz rolę</option>
                <option value="pracownik">Pracownik</option>
                <option value="pracodawca">Pracodawca</option>
            </select>
            <div style="position: relative; width: 100%;">
                <input type="password" id="haslo" name="haslo" placeholder="Hasło" required>
            </div>
            <button type="submit" name="add_worker_btn" class="btn_modal--accept">Dodaj</button>
            <button type="button" class="btn_modal--cancel" onclick="closeModal()">Anuluj</button>
        </form>
    </div>
</div>

<!-- Okno Modalne usuwania -->
<div id="delete_worker_modal" class="modal" style="display: none;">
    <div class="delete_worker_modal">
        <form id="delete_worker_form" method="POST" action="">
            <input type="hidden" name="id" id="delete_worker_id" value="">
            <h3>Usuń Pracownika</h3>
            <label>Czy napewno chcesz usunać pracownika?</label>
            <div>
                <button type="submit" name="delete_worker_btn" class="btn_modal--delete">Usuń</button>
                <button type="button" class="btn_modal--cancel" onclick="closeModalDelete()">Anuluj</button>
            </div>

        </form>
    </div>
    <?php echo $message ?>
</div>

<!-- Okno Modalne edytowania -->
<div id="update_worker_modal" class="modal" style="display: none;">
    <div class="update_worker_form">
        <form id="update_worker_form" method="POST" action="" onsubmit="return validateUpdateForm()">
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
            <button type="submit" name="update_worker_btn" class="btn_modal--accept">Zapisz zmiany</button>
            <button type="button" class="btn_modal--cancel" onclick="closeModalUpdate()">Anuluj</button>
        </form>
    </div>
    <?php echo $message ?>
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
    // walidacja dodawania
    function validateAddForm() {
        const imie = document.getElementById('imie').value;
        const nazwisko = document.getElementById('nazwisko').value;
        const pesel = document.getElementById('pesel').value;
        const haslo = document.getElementById('haslo').value;
        let message = "";

        if (!/^\p{L}+$/u.test(imie)) {
            message = "Imię może zawierać tylko litery.";
        } else if (!/^\p{L}+$/u.test(nazwisko)) {
            message = "Nazwisko może zawierać tylko litery.";
        }
        else if (!/^\d{11}$/.test(pesel)) {
            message = "PESEL musi mieć dokładnie 11 cyfr.";
        }
        else if (!/(?=.*[a-zA-Z])(?=.*\d).{8,}/.test(haslo)) {
            message = "Hasło musi mieć co najmniej 8 znaków oraz zawierać litery i cyfry.";
        }

        if (message) {
            alert(message);
            return false;
        }
        return true;
    }

    // walidacja edytcji
    function validateUpdateForm() {
        const imie = document.getElementById('update_imie').value;
        const nazwisko = document.getElementById('update_nazwisko').value;
        const pesel = document.getElementById('update_pesel').value;
        const haslo = document.getElementById('update_haslo').value;
        let message = "";

        if (!/^[a-zA-Z]+$/.test(imie)) {
            message = "Imię może zawierać tylko litery.";
        } else if (!/^[a-zA-Z]+$/.test(nazwisko)) {
            message = "Nazwisko może zawierać tylko litery.";
        }
        else if (!/^\d{11}$/.test(pesel)) {
            message = "PESEL musi mieć dokładnie 11 cyfr.";
        }
        else if (haslo && !/(?=.*[a-zA-Z])(?=.*\d).{8,}/.test(haslo)) {
            message = "Hasło musi mieć co najmniej 8 znaków oraz zawierać litery i cyfry.";
        }

        if (message) {
            alert(message);
            return false;
        }
        return true;
    }
</script>
