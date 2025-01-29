<?php
session_start();

// Jeśli użytkownik jest zalogowany, przekieruj na dashboard
if (isset($_SESSION['user'])) {
    if($_SESSION['rola'] === "pracownik"){
        header('Location: ./app/views/dashboard_pracownik.php');
    }
    if($_SESSION['rola'] === "pracodawca"){
        header('Location: ./app/views/dashboard_pracodawca.php');
    }
    exit();
}

// Jeśli nie, przekieruj na stronę logowania
header('Location: ./app/views/login.php');
exit();
