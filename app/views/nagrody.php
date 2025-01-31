<?php
include './header.php';
require_once dirname(__DIR__) . '/utils/session.php';
require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(__DIR__) . '/models/Points.php';
require_once dirname(__DIR__) . '/utils/config.php';

Session::start();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pointsModel = new Points($pdo);
$userModel = new User($pdo);
$userId = $_SESSION['user_id'];
$bonus = $userModel->getBonus($userId);
$dni_wolne = $userModel->getDaysOff($userId);

$punkty = $pointsModel->getUserPoints($userId);
?>

<div class="panel panel-nagrody">
    <h2>Wymień punkty na nagrody!</h2>
    <div class="nagroda">
        <div>
            <p class="nagroda-title"><i class="fa-solid fa-money-check-dollar"></i> Premia pieniężna (200zł)</p>
            <p>Twoja premia: <strong><?php echo $bonus;
                                        if ($bonus == 0) {
                                            echo '0';
                                        } ?> zł <?php if ($bonus == 1000) echo '<i>(MAX)</i>' ?></strong></p>
        </div>
        <form action="../utils/wymien_punkty.php" method="post">
            <input type="hidden" name="nagroda_id" value="premia">
            <input type="hidden" name="punkty" value="500">
            <button type="submit" class="nagroda-button"
                <?php
                if ($bonus >= 1000 || $punkty < 500) {
                    echo 'disabled style="background-color: gray; cursor: not-allowed;"';
                }
                ?>>

                <i class="fa-solid fa-coins"></i> 500
            </button>
        </form>
    </div>
    <div class="nagroda">
        <div>
            <p class="nagroda-title"><i class="fa-solid fa-bed"></i> Dodatkowy dzień wolny</p>
            <p>Twoje dodatkowe dni wolne: <strong><?php echo $dni_wolne; ?> <?php if ($dni_wolne == 3) echo '<i>(MAX)</i>' ?><?php if ($dni_wolne == 0) echo '0' ?></strong></p>
        </div>
        <form action="../utils/wymien_punkty.php" method="post">
            <input type="hidden" name="nagroda_id" value="dzien-wolny">
            <input type="hidden" name="punkty" value="1500">
            <button type="submit" class="nagroda-button"
                <?php
                if ($dni_wolne > 2 || $punkty < 1500) {
                    echo 'disabled style="background-color: gray; cursor: not-allowed;"';
                }
                ?>> 
                <i class="fa-solid fa-coins"></i> 1 500
            </button>
        </form>
    </div>
</div>