<?php
include '../app/views/header.php';
require_once __DIR__ . '/../app/utils/session.php';
Session::start();
?>

<div class="panel panel-nagrody">
    <h1>Wymień punkty na nagrody!</h1>
    <div class="nagroda">
        <div>
            <p class="nagroda-title"><i class="fa-solid fa-money-check-dollar"></i> Premia pieniężna</p>
        </div>
        <form action="../app/utils/wymien_punkty.php" method="post">
            <input type="hidden" name="nagroda_id" value="premia">
            <input type="hidden" name="punkty" value="500">
            <button type="submit" class="nagroda-button">
                <i class="fa-solid fa-coins"></i> 500
            </button>
        </form>
    </div>
    <div class="nagroda">
        <div>
            <p class="nagroda-title"><i class="fa-solid fa-bed"></i> Dodatkowy dzień wolny</p>
        </div>
        <form action="../app/utils/wymien_punkty.php" method="post">
            <input type="hidden" name="nagroda_id" value="dzien-wolny">
            <input type="hidden" name="punkty" value="1500">
            <button type="submit" class="nagroda-button">
                <i class="fa-solid fa-coins"></i> 1 500
            </button>
        </form>
    </div>
</div>
