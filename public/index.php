<?php
session_start();

// Jeśli użytkownik jest zalogowany, przekieruj na dashboard
if (isset($_SESSION['user'])) {
    header('Location: /dashboard.php');
    exit();
}

// Jeśli nie, przekieruj na stronę logowania
header('Location: login.php');
exit();
