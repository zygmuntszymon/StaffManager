<?php

require_once __DIR__ . '/../utils/config.php';

class Leaves {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function submitLeaveRequest($employeeId, $leaveDates) {
        if (empty($leaveDates)) {
            throw new Exception("Musisz wybrać co najmniej jeden dzień.");
        }

        $requiredDays = count(explode(',', $leaveDates));
        $availableDays = $this->getTotalAvailableDays($employeeId);

        if ($requiredDays > $availableDays) {
            throw new Exception("Nie masz wystarczająco dużo dni urlopowych.");
        }

        $this->validateDates($employeeId, $leaveDates);

        $stmt = $this->pdo->prepare("INSERT INTO urlopy (pracownik_id, daty_urlopu) VALUES (?, ?)");
        $stmt->execute([$employeeId, $leaveDates]);

        $this->deductAvailableDays($employeeId, $requiredDays);

        return true;
    }

    private function validateDates($employeeId, $leaveDates) {
        $selectedDates = explode(',', $leaveDates);
    
        $takenDates = $this->getEmployeeTakenDates($employeeId);
        foreach ($selectedDates as $date) {
            if (in_array($date, $takenDates)) {
                throw new Exception();
            }
        }
    }

    public function getEmployeeTakenDates($employeeId) {
        $stmt = $this->pdo->prepare("SELECT daty_urlopu FROM urlopy WHERE pracownik_id = ?");
        $stmt->execute([$employeeId]);
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $takenDates = [];
        foreach ($results as $dates) {
            $takenDates = array_merge($takenDates, explode(',', $dates));
        }

        return $takenDates;
    }

    public function getTakenDates() {
        $stmt = $this->pdo->query("SELECT daty_urlopu FROM urlopy");
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $takenDates = [];
        foreach ($results as $dates) {
            $takenDates = array_merge($takenDates, explode(',', $dates));
        }

        return $takenDates;
    }

    public function getTotalAvailableDays($employeeId) {

        $stmt = $this->pdo->prepare("SELECT dostepne_dni_wolne FROM pracownicy WHERE id = ?");
        $stmt->execute([$employeeId]);
        $baseDays = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT SUM(ilosc) FROM dodatkowe_dni_wolne WHERE pracownik_id = ?");
        $stmt->execute([$employeeId]);
        $additionalDays = $stmt->fetchColumn() ?? 0;

        return $baseDays + $additionalDays;
    }

    private function deductAvailableDays($employeeId, $daysToDeduct) {
        $stmt = $this->pdo->prepare("SELECT id, ilosc FROM dodatkowe_dni_wolne WHERE pracownik_id = ? ORDER BY id");
        $stmt->execute([$employeeId]);
        $additionalDays = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($additionalDays as $dayRecord) {
            if ($daysToDeduct <= 0) break;

            $daysToUse = min($dayRecord['ilosc'], $daysToDeduct);
            $newAmount = $dayRecord['ilosc'] - $daysToUse;

            if ($newAmount > 0) {
                $stmt = $this->pdo->prepare("UPDATE dodatkowe_dni_wolne SET ilosc = ? WHERE id = ?");
                $stmt->execute([$newAmount, $dayRecord['id']]);
            } else {
                $stmt = $this->pdo->prepare("DELETE FROM dodatkowe_dni_wolne WHERE id = ?");
                $stmt->execute([$dayRecord['id']]);
            }

            $daysToDeduct -= $daysToUse;
        }

        if ($daysToDeduct > 0) {
            $stmt = $this->pdo->prepare("UPDATE pracownicy SET dostepne_dni_wolne = dostepne_dni_wolne - ? WHERE id = ?");
            $stmt->execute([$daysToDeduct, $employeeId]);
        }
    }
}