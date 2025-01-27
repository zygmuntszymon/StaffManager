<?php
include '../app/views/header.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/utils/session.php';
require_once __DIR__ . '/../app/models/Points.php'; // Dodaj to, aby załadować model Points
Session::start();
$auth = new AuthController($pdo);

if (isset($_SESSION['user'])) {
    if($_SESSION['rola'] === "pracownik"){
        header('Location: dashboard_pracownik.php');
    }
    if($_SESSION['rola'] === "pracodawca"){
        header('Location: dashboard_pracodawca.php');
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($auth->login($_POST['login'], $_POST['haslo'])) {
        var_dump($_SESSION['user_id']);
        exit();
    } else {
        echo "Nieprawidłowy login lub hasło!";
    }
}
?>
<div class="login_form">
    <img src="./media/logo.png" alt="" style="width:150px">
    <form method="post">
        <input type="text" name="login" placeholder="Login" required>
        <input type="password" name="haslo" placeholder="Hasło" required>
        <button type="submit">Zaloguj się</button>
    </form>
    <p>Nie masz konta? <a href="register.php"><u>Zarejestruj się</u></a></p>
</div>
