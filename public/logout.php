<?php
require_once __DIR__ . '/../app/controllers/AuthController.php';
$auth = new AuthController($pdo);
$auth->logout();
?>
