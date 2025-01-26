<?php
require_once __DIR__ . '/session.php';
require_once '../models/Points.php'; // Załaduj model Points
Session::start();

if (Session::isLoggedIn()) {
    $userId = $_SESSION['user_id'];

    // Utwórz instancję Points i zwiększ punkty użytkownika
    $pointsModel = new Points($pdo);
    $pointsModel->incrementUserPoints($userId, 5);

    // Pobierz zaktualizowaną wartość punktów i zwróć ją jako odpowiedź
    $updatedPoints = $pointsModel->getUserPoints($userId);
    echo $updatedPoints;
}
?>
