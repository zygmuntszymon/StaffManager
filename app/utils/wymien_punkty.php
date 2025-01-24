<?php
require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../models/Points.php'; 
Session::start();

if (Session::isLoggedIn() && $_SESSION['rola'] === 'pracownik') {
    $userId = $_SESSION['user_id'];
    $punkty = $_POST['punkty'];


    $pointsModel = new Points($pdo);
    if ($pointsModel->getUserPoints($userId) >= $punkty) {
        $pointsModel->decrementUserPoints($userId, $punkty);

        $_SESSION['punkty'] = $pointsModel->getUserPoints($userId);
        header('Location: ../../public/nagrody.php');
    } else {
    }
}
?>
