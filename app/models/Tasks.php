<?php
require_once __DIR__ . '/../utils/config.php';

class Tasks {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function markTaskAsDone($taskId, $userId) {
        $stmt = $this->pdo->prepare("UPDATE zadania SET status = 'ukończone', data_zakonczenia = CURDATE() WHERE id = ?");
        $result = $stmt->execute([$taskId]);
        
        if ($result) {
            $points = $this->getPointsForTask($taskId);
            $stmt = $this->pdo->prepare('UPDATE pracownicy SET punkty = punkty + :increment WHERE id = :user_id');
            $stmt->execute(['increment' => $points, 'user_id' => $userId]);
        }
    
        return $result;
    }
    
    public function getPointsForTask($taskId) {
        $stmt = $this->pdo->prepare("SELECT ilosc_punkty FROM zadania WHERE id = ?");
        $stmt->execute([$taskId]);
        return $stmt->fetchColumn();  // Zwraca jedną wartość (punkty)
    }
    public function getTasksForUser($userId) {
        $stmt = $this->pdo->prepare("SELECT z.id, z.opis, z.status, z.deadline FROM zadania z
                                     JOIN pracownik_zadanie pz ON pz.zadanie_id = z.id
                                     WHERE pz.pracownik_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllTasks()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM zadania ORDER BY opis, deadline, ilosc_punkty, status");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTasksByStatus($status)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM zadania WHERE status = ?");
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addTask($opis, $deadline, $ilosc_punkty, $status) {
        $stmt = $this->pdo->prepare("INSERT INTO zadania (opis, status, deadline, ilosc_punkty) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$opis, $status, $deadline, $ilosc_punkty]);
    }
    public function taskForUser($pracownik_id, $zadanie_id) {
        $this->changeStatus($zadanie_id);
        $stmt = $this->pdo->prepare("INSERT INTO pracownik_zadanie (pracownik_id, zadanie_id) VALUES (?, ?)");
        return $stmt->execute([$pracownik_id, $zadanie_id]);
    }
    public function changeStatus($id) {
        $stmt = $this->pdo->prepare("UPDATE zadania SET status = 'w realizacji' WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function deleteTask($id) {
        $stmt = $this->pdo->prepare("DELETE FROM zadania WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateTask($id, $opis, $deadline, $ilosc_punkty)
    {
            $stmt = $this->pdo->prepare("UPDATE zadania SET opis = ?, deadline = ?, ilosc_punkty = ? WHERE id = ?");
            return $stmt->execute([$opis, $deadline, $ilosc_punkty, $id]);
    }

    public function getAllWorkers()
    {
        $stmt = $this->pdo->query("SELECT id, imie, nazwisko FROM pracownicy");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNameAndSurnameWorker($zadanie_id) {
        $stmt = $this->pdo->prepare("SELECT CONCAT(p.imie, ' ', p.nazwisko) AS nameAndSurname
        FROM pracownik_zadanie pz
        JOIN pracownicy p ON p.id = pz.pracownik_id
        WHERE pz.zadanie_id = ?
        LIMIT 1");
        $stmt->execute([$zadanie_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['nameAndSurname'] : "Nie przypisany pracownik";
    }
}
?>
