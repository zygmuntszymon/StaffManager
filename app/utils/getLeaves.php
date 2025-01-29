<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../models/Leaves.php';

if (!isset($_GET['date'])) {
    die('Brak daty');
}

$date = $_GET['date'];

$leavesModel = new Leaves($pdo);
$employeesOnLeave = $leavesModel->getEmployeesOnLeave($date);

header('Content-Type: application/json');
echo json_encode($employeesOnLeave);
?>
