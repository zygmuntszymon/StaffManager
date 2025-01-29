<?php
include './header.php';
require_once dirname(__DIR__) . '/controllers/AuthController.php';


$auth = new AuthController($pdo);
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($errors)) {
    $imie = trim($_POST['imie']);
    $nazwisko = trim($_POST['nazwisko']);
    $pesel = trim($_POST['pesel']);
    $haslo = $_POST['haslo'];
    $rola = $_POST['rola'];

    if ($auth->register($imie, $nazwisko, $pesel, $rola, $haslo)) {
        header('Location: login.php');
        exit;
    } else {
        $errors[] = "Błąd rejestracji!";
    }
}
?>

<div class="login_form">
    <img src="./media/logo.png" alt="" style="width:150px">
    <form method="post" onsubmit="return validateForm()">
        <input type="text" name="imie" id="imie" placeholder="Imię" required>
        <input type="text" name="nazwisko" id="nazwisko" placeholder="Nazwisko" required>
        <input type="text" name="pesel" id="pesel" placeholder="PESEL" required pattern="\d{11}">
        <input type="password" name="haslo" id="haslo" placeholder="Hasło" required>
        <select name="rola" id="rola">
            <option value="pracownik">Pracownik</option>
            <option value="pracodawca">Pracodawca</option>
        </select>
        <button type="submit">Zarejestruj się</button>
    </form>

    <?php if (!empty($errors)): ?>
        <ul class="err">
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p>Masz już konto? <a href="login.php"><u>Zaloguj się</u></a></p>
</div>

<script>
    // walidacja formularza rejestracji
    function validateForm() {
        const imie = document.getElementById('imie').value;
        const nazwisko = document.getElementById('nazwisko').value;
        const pesel = document.getElementById('pesel').value;
        const haslo = document.getElementById('haslo').value;
        let message = "";

        if (!/^[a-zA-Z]+$/.test(imie)) {
            message = "Imię może zawierać tylko litery.";
        } else if (!/^[a-zA-Z]+$/.test(nazwisko)) {
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
</script>
