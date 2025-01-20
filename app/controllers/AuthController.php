<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/session.php';

class AuthController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function register($imie, $nazwisko, $pesel, $rola, $haslo) {
        return $this->userModel->createUser($imie, $nazwisko, $pesel, $rola, $haslo);
    }

    public function login($login, $haslo) {
        $user = $this->userModel->getUserByLogin($login);

        if ($user && password_verify($haslo, $user['haslo'])) {
            Session::start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['rola'] = $user['rola'];
            $_SESSION['login'] = $user['login'];

            if ($user['rola'] === 'pracodawca') {
                header("Location: dashboard_pracodawca.php");
            } else {
                header("Location: dashboard_pracownik.php");
            }
            exit();
            
        }
        return false;
    }

    public function logout() {
        Session::destroy();
        header("Location: ../public/index.php");
        exit();
    }
}
?>
