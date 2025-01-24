<?php
require_once dirname(__DIR__) . '/utils/session.php';
Session::start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/header.css">
    <link rel="stylesheet" href="../public/css/main.css">
    <link rel="stylesheet" href="../public/css/login.css">
    <link rel="stylesheet" href="../public/css/pracownik.css">
    <link rel="stylesheet" href="../public/css/pracodawca.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>StaffManager</title>
</head>

<body>
    <div class="header">
        <div class="header_container">
            <div class="header_logo">
                <img src="./media/logo.png" alt="" style="width:200px">
            </div>
            <?php
            if (Session::isLoggedIn()) { ?>
                <div class="header_menu">
                    <a href="" class="">Zadania</a>
                    <a href="" class="">Postępy</a>                    
                    <a href="">Wnioski</a>
                    <a href="logout.php" id="btn_logout">Wyloguj się</a>
                </div>
            <?php
            }
            ?>
        </div>
    </div>