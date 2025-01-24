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
    <link rel="stylesheet" href="../public/css/header.css">
    <link rel="stylesheet" href="../public/css/main.css">
    <link rel="stylesheet" href="../public/css/login.css">
    <link rel="stylesheet" href="../public/css/pracownik.css">
    <link rel="stylesheet" href="../public/css/pracodawca.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>StaffManager</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Funkcja sprawdzająca, czy użytkownik jest zalogowany i czy pracownik
        function isLoggedInAndEmployee() {
            return <?php echo json_encode(Session::isLoggedIn() && $_SESSION['rola'] === 'pracownik'); ?>;
        }

        // Funkcja aktualizująca punkty użytkownika
        function updatePoints() {
            if (isLoggedInAndEmployee()) {
                $.ajax({
                    url: '../app/utils/update_points.php',
                    method: 'POST',
                    success: function(response) {
                        console.log('Punkty zostały zaktualizowane: ' + response);
                        $('#user-points').text(response); // Aktualizacja punktów w nagłówku
                    }
                });
            }
        }

        // Uruchom funkcję co 5 sekund, jeśli użytkownik jest zalogowany
        if (isLoggedInAndEmployee()) {
            setInterval(updatePoints, 5000);
        }
    </script>
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
                    <?php
                    if (Session::isLoggedIn() && $_SESSION['rola'] === 'pracownik') { ?>
                        <a href="" class="">Zadania</a>
                        <a href="nagrody.php" class="">Nagrody</a>
                        <a href="">Wnioski</a>
                        <div id="header_points">
                            <i class="fa-solid fa-coins"></i> &nbsp;<span id="user-points"> <?php echo $_SESSION['punkty']; ?></span>
                        </div>
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