<?php
include './header.php';
require_once dirname(__DIR__) . '/controllers/AuthController.php';
require_once dirname(__DIR__) . '/utils/session.php';
require_once dirname(__DIR__) . '/models/Points.php';
Session::start();
$auth = new AuthController($pdo);
$err = '';
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
        $err = "Nieprawidłowy login lub hasło!";
    }
}
?>
<div class="login_form">
    <img src="../../public/media/logo.png" alt="" style="width:150px">
    <form method="post">
        <input type="text" name="login" placeholder="Login" required>
        <input type="password" name="haslo" placeholder="Hasło" required>
        <button type="submit">Zaloguj się</button>
    </form>
    <p>Nie masz konta? <a href="../views/register.php"><u>Zarejestruj się</u></a></p>
    <div class="err"><?php echo $err ?></div>
</div>
