<?php
require_once __DIR__ . '/../utils/config.php';

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function generateUniqueLogin($imie, $nazwisko, $pesel) {
        $baseLogin = strtoupper(substr($imie, 0, 1) . $nazwisko . substr($pesel, 0, 4));
        $login = $baseLogin;
        $counter = 1;

        // Sprawdzamy, czy login już istnieje w bazie
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM pracownicy WHERE login LIKE ?");
        while (true) {
            $stmt->execute([$login . '%']);
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                break; // Login jest unikalny
            }

            $login = $baseLogin . $counter; // Dodajemy licznik
            $counter++;
        }

        return $login;
    }

    public function createUser($imie, $nazwisko, $pesel, $rola, $haslo) {
        $login = $this->generateUniqueLogin($imie, $nazwisko, $pesel);
        $hashedPassword = password_hash($haslo, PASSWORD_DEFAULT);
        $data_zatrudnienia = date("Y-m-d");

        $stmt = $this->pdo->prepare("INSERT INTO pracownicy (imie, nazwisko, pesel, rola, login, haslo, data_zatrudnienia) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$imie, $nazwisko, $pesel, $rola, $login, $hashedPassword, $data_zatrudnienia]);
    }

    public function getUserByLogin($login) {
        $stmt = $this->pdo->prepare("SELECT * FROM pracownicy WHERE login = ?");
        $stmt->execute([$login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTasksForUser($pracownik_id) {
        $stmt = $this->pdo->prepare("
            SELECT z.*
            FROM zadania z
            JOIN pracownik_zadanie pz ON z.id = pz.zadanie_id
            WHERE pz.pracownik_id = ?
        ");
        $stmt->execute([$pracownik_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllUsers()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pracownicy ORDER BY nazwisko, imie, rola");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getBonus($user_id) {
        $stmt = $this->pdo->prepare("SELECT COALESCE(wartosc, 0) as bonus FROM premie WHERE pracownik_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['bonus'];
    }

    public function addBonus($user_id, $amount) {
        $stmt = $this->pdo->prepare("UPDATE premie SET wartosc = wartosc + ? WHERE pracownik_id = ?");
        $stmt->execute([$amount, $user_id]);
    
        // Jeśli żadna premia nie została zaktualizowana, wstaw nową
        if ($stmt->rowCount() === 0) {
            $stmt = $this->pdo->prepare("INSERT INTO premie (pracownik_id, wartosc) VALUES (?, ?)");
            $stmt->execute([$user_id, $amount]);
        }
    }

    public function resetBonus() {
        $stmt = $this->pdo->prepare("UPDATE premie SET wartosc = 0");
        return $stmt->execute();
    }
}
?>
