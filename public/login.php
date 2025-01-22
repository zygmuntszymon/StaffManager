<?php
include '../app/views/header.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

$auth = new AuthController($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($auth->login($_POST['login'], $_POST['haslo'])) {
        exit();
    } else {
        echo "Nieprawidłowy login lub hasło!";
    }
}
?>
<div class="login_form">
    <img src="./media/logo.png" alt="" style="width:200px">
    <form method="post">
    <input type="text" name="login" placeholder="Login" required>
    <input type="password" name="haslo" placeholder="Hasło" required>
    <button type="submit">Zaloguj się</button>
    </form>
    <p>Nie masz konta? <a href="register.php"><u>Zarejestruj się</u></a></p>
</div>

