<?php
require_once dirname(__DIR__) . '/controllers/AuthController.php';
require_once dirname(__DIR__) . '/utils/session.php';
require_once dirname(__DIR__) . '/models/Points.php';

Session::start();

// sprawdza czy użytkowanik jest zalogowany i ma ustawione punkty
if (Session::isLoggedIn() && isset($_SESSION['punkty'])) {
    $userId = $_SESSION['user_id'];
    $punkty = $_SESSION['punkty'];

    // przy wylogowaniu wywołuje funkcje która aktualizuje punkty użytkownika
    $pointsModel = new Points($pdo);
    $pointsModel->updateUserPoints($userId, $punkty);
}

// wylogowanie
$auth = new AuthController($pdo);
$auth->logout();
?>
