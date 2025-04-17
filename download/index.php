<?php

$title = "lang_download_title";

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";

global $unreadAlerts;

?>

<div class="container">
    <br><br>
    <h1><?= l("lang_download_title2") ?></h1>

    <p><?= l("lang_download_intro_0") ?></p>
    <p><?= l("lang_download_intro_1") ?></p>
    <p><?= l("lang_download_intro_2") ?></p>

    <?php if (isset($_COOKIE["DeltaKiosk"])): ?>
    <div class="alert alert-secondary">
        <?= l("lang_download_kiosk") ?>
    </div>
    <?php else: ?>
    <a href="/download/start/" onclick="this.classList.add('disabled');" class="btn btn-primary"><?= l("lang_download_start") ?></a>
    <?php endif; ?>

    <br><br>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
