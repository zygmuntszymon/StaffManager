<?php
include '../app/views/header.php';
require_once __DIR__ . '/../app/utils/session.php';
require_once __DIR__ . '/../app/models/User.php';

Session::start();
$pdo = new PDO("mysql:host=localhost;dbname=staffmanager_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$userModel = new User($pdo);
$userId = $_SESSION['user_id'];
$bonus = $userModel->getBonus($userId);
$dni_wolne = $userModel ->getDaysOff($userId);
?>

<div class="panel panel-nagrody">
    <h1>Wymień punkty na nagrody!</h1>
    <div class="nagroda">
        <div>
            <p class="nagroda-title"><i class="fa-solid fa-money-check-dollar"></i> Premia pieniężna (200zł)</p>
            <p>Twoja premia: <strong><?php echo $bonus; ?> zł <?php if($bonus == 1000) echo '<i>(MAX)</i>'?></strong></p>
        </div>
        <form action="../app/utils/wymien_punkty.php" method="post">
            <input type="hidden" name="nagroda_id" value="premia">
            <input type="hidden" name="punkty" value="500">
            <button type="submit" class="nagroda-button" <?php echo ($bonus >= 1000) ? 'disabled style="background-color: gray; cursor: not-allowed;"' : ''; ?>>
                <i class="fa-solid fa-coins"></i> 500
            </button>
        </form>
    </div>
    <div class="nagroda">
        <div>
            <p class="nagroda-title"><i class="fa-solid fa-bed"></i> Dodatkowy dzień wolny</p>
            <p>Twoje dodatkowe dni wolne: <strong><?php echo $dni_wolne; ?> <?php if($dni_wolne == 3) echo '<i>(MAX)</i>'?><?php if($dni_wolne == 0) echo '0'?></strong></p>
        </div>
        <form action="../app/utils/wymien_punkty.php" method="post">
            <input type="hidden" name="nagroda_id" value="dzien-wolny">
            <input type="hidden" name="punkty" value="1500">
            <button type="submit" class="nagroda-button" <?php echo ($dni_wolne > 2) ? 'disabled style="background-color: gray; cursor: not-allowed;"' : ''; ?>>
                <i class="fa-solid fa-coins"></i> 1 500
            </button>
        </form>
    </div>
</div>
