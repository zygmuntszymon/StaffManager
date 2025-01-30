<?php
require_once __DIR__ . '/session.php';
require_once '../models/Points.php';
Session::start();

if (Session::isLoggedIn()) {
    $userId = $_SESSION['user_id'];

    // tworzy instancję do przyznawania punktów dodaje 5 punktów co 5 sekund
    $pointsModel = new Points($pdo);
    $pointsModel->incrementUserPoints($userId, 5);

    // aktualizuje i zwraca ilość punktów
    $updatedPoints = $pointsModel->getUserPoints($userId);
    echo $updatedPoints;
}
?>
