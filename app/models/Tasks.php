<?php

require_once __DIR__ . '/../utils/config.php';

class Tasks {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function markTaskAsDone($taskId) {
        $stmt = $this->pdo->prepare("UPDATE zadania SET status = 'ukończone' WHERE id = ?");
        return $stmt->execute([$taskId]);
    }

    public function getTasksForUser($userId) {
        $stmt = $this->pdo->prepare("SELECT z.id, z.opis, z.status, z.deadline FROM zadania z
                                     JOIN pracownik_zadanie pz ON pz.zadanie_id = z.id
                                     WHERE pz.pracownik_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
