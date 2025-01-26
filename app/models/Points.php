<?php
require_once __DIR__ . '/../utils/config.php';

class Points {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getUserPoints($userId) {

        $stmt = $this->pdo->prepare('SELECT punkty FROM pracownicy WHERE id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['punkty'];
        } else {
            return 0;
        }
    }

    public function updateUserPoints($userId, $punkty) {
        $stmt = $this->pdo->prepare('UPDATE pracownicy SET punkty = :punkty WHERE id = :user_id');
        return $stmt->execute(['punkty' => $punkty, 'user_id' => $userId]);
    }
    public function incrementUserPoints($userId, $increment) {
        $stmt = $this->pdo->prepare('UPDATE pracownicy SET punkty = punkty + :increment WHERE id = :user_id');
        return $stmt->execute(['increment' => $increment, 'user_id' => $userId]);
    }
    public function decrementUserPoints($userId, $decrement) {
        $stmt = $this->pdo->prepare('UPDATE pracownicy SET punkty = punkty - :decrement WHERE id = :user_id');
        return $stmt->execute(['decrement' => $decrement, 'user_id' => $userId]);
    }
}
?>
