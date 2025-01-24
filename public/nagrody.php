<?php 
include '../app/views/header.php';
require_once __DIR__ . '/../app/utils/session.php';
Session::start();
echo $_SESSION['punkty'];