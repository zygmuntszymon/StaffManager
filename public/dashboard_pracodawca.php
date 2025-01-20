<?php
require_once __DIR__ . '/../app/utils/session.php';
Session::start();

if (!Session::isLoggedIn()) {
    header("Location: login.php");
    exit();
}

echo "Witaj, " . $_SESSION['login'] . "[ " . $_SESSION['rola'] . " ]";
?>

<a href="logout.php">Wyloguj się</a>
