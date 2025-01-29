<?php
require_once dirname(__DIR__) . '/controllers/AuthController.php';
require_once dirname(__DIR__) . '/utils/session.php';
require_once dirname(__DIR__) . '/models/Points.php'; // Załaduj model Points

Session::start(); // Rozpocznij sesję

// Upewnij się, że użytkownik jest zalogowany i ma ustawione punkty
if (Session::isLoggedIn() && isset($_SESSION['punkty'])) {
    $userId = $_SESSION['user_id'];
    $punkty = $_SESSION['punkty'];

    // Utwórz instancję Points i zaktualizuj punkty użytkownika
    $pointsModel = new Points($pdo);
    $pointsModel->updateUserPoints($userId, $punkty);
}

// Wyloguj użytkownika
$auth = new AuthController($pdo);
$auth->logout();
?>
