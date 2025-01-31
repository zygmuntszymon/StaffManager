<?php
require_once dirname(__DIR__) . '/utils/session.php';
require_once dirname(__DIR__) . '/models/Points.php';

Session::start();
if (Session::isLoggedIn()) {
    $pointsModel = new Points($pdo);
    $_SESSION['punkty'] = $pointsModel->getUserPoints($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/header.css">
    <link rel="stylesheet" href="../../public/css/main.css">
    <link rel="stylesheet" href="../../public/css/login.css">
    <link rel="stylesheet" href="../../public/css/pracownik.css">
    <link rel="stylesheet" href="../../public/css/pracodawca.css">
    <link rel="stylesheet" href="../../public/css/nagrody.css">
    <link rel="stylesheet" href="../../public/css/wnioski.css">
    <link rel="stylesheet" href="../../public/css/zadania.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>StaffManager</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // sprawdza czy użytkownik jest zalogowany i jest pracownikiem
        function isLoggedInAndEmployee() {
            return <?php echo json_encode(Session::isLoggedIn() && $_SESSION['rola'] === 'pracownik'); ?>;
        }

        // aktualizowanie punktów użytkownika
        function updatePoints() {
            if (isLoggedInAndEmployee()) {
                $.ajax({
                    url: '../app/utils/update_points.php',
                    method: 'POST',
                    success: function(response) {
                        console.log('Punkty zostały zaktualizowane: ' + response);
                        // aktualizacja punktów
                        $('#user-points').text(response);
                    }
                });
            }
        }

        // uruchamia funkcję co 5 sekund i sprawdz czy użytkownik jest zalogowany
        if (isLoggedInAndEmployee()) {
            setInterval(updatePoints, 5000);
        }
    </script>
</head>

<body>
    <div class="header">
        <div class="header_container">
            <div class="header_logo">
                <img src="../../public/media/logo.png" alt="" style="width:200px">
            </div>
            <?php
            if (Session::isLoggedIn()) { ?>
                <div class="header_menu">
                    <?php
                    $currentFileName = basename($_SERVER['PHP_SELF']);
                    $taskPages = ['zadania.php', 'zadania_realizacja.php', 'zadania_ukonczone.php'];

                    if (Session::isLoggedIn() && $_SESSION['rola'] === 'pracownik') { ?>
                        <a href="../views/dashboard_pracownik.php" class="header_link <?= ($currentFileName == 'dashboard_pracownik.php') ? 'selected' : ''; ?>"><i class="fa-solid fa-list-check"></i> Zadania</a>
                        <a href="nagrody.php" class="header_link <?= ($currentFileName == 'nagrody.php') ? 'selected' : ''; ?>"> <i class="fa-solid fa-trophy"></i> Nagrody</a>
                        <a href="urlopy.php" class="header_link <?= ($currentFileName == 'urlopy.php') ? 'selected' : ''; ?>"> <i class="fa-solid fa-file"></i> Urlopy</a>
                        <div id="header_points">
                            <i class="fa-solid fa-coins"></i> &nbsp;<span id="user-points"> <?php echo $_SESSION['punkty']; ?></span>
                        </div>
                    <?php
                    }

                    if (Session::isLoggedIn() && $_SESSION['rola'] === 'pracodawca') { ?>
                        <a href="../views/dashboard_pracodawca.php" class="header_link <?= ($currentFileName == 'dashboard_pracodawca.php') ? 'selected' : ''; ?>"> <i class="fa-solid fa-user-tie"></i> Pracownicy</a>
                        <a href="zadania.php" class="header_link <?= in_array($currentFileName, $taskPages) ? 'selected' : ''; ?>"> <i class="fa-solid fa-file-lines"></i> Zadania</a>
                        <a href="urlopy_pracownikow.php" class="header_link <?= ($currentFileName == 'urlopy_pracownikow.php') ? 'selected' : ''; ?>"> <i class="fa-solid fa-calendar-days"></i> Urlopy Pracowników</a>
                    <?php
                    }
                    ?>
                    <a href="logout.php" id="btn_logout"> <i class="fa-solid fa-right-from-bracket"></i> &nbsp; Wyloguj się</a>
                </div>

            <?php
            }
            ?>
        </div>
    </div>
</body>

</html>