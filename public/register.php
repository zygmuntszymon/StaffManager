<?php
require_once __DIR__ . '/../app/controllers/AuthController.php';

$auth = new AuthController($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($auth->register($_POST['imie'], $_POST['nazwisko'], $_POST['pesel'], $_POST['rola'], $_POST['haslo'])) {
        header('Location: login.php');
    } else {
        echo "Błąd rejestracji!";
    }
}
?>

<form method="post">
    <input type="text" name="imie" placeholder="Imię" required>
    <input type="text" name="nazwisko" placeholder="Nazwisko" required>
    <input type="text" name="pesel" placeholder="PESEL" required>
    <select name="rola">
        <option value="pracownik">Pracownik</option>
        <option value="pracodawca">Pracodawca</option>
    </select>
    <input type="password" name="haslo" placeholder="Hasło" required>
    <button type="submit">Zarejestruj się</button>
</form>
