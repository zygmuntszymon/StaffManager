<?php
require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Points.php'; 
Session::start();

$pdo = new PDO("mysql:host=localhost;dbname=staffmanager_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (Session::isLoggedIn() && $_SESSION['rola'] === 'pracownik') {
    $userId = $_SESSION['user_id'];
    $punkty = $_POST['punkty'];
    $nagrodaId = $_POST['nagroda_id'];

    $pointsModel = new Points($pdo);
    $userModel = new User($pdo);

    if ($pointsModel->getUserPoints($userId) >= $punkty) {
        $pointsModel->decrementUserPoints($userId, $punkty);

        if ($nagrodaId === 'premia') {
            $userModel->addBonus($userId, 200);
        }

        $_SESSION['punkty'] = $pointsModel->getUserPoints($userId);
    }

    header('Location: ../../public/nagrody.php');
    exit;
}
?>
