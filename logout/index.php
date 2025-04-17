<?php

$title = "lang_logout_title";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";
global $_PROFILE;

?>

<div class="container">
    <br><br>
    <h1><?= l("lang_logout_title") ?></h1>

    <p><?= l("lang_logout_confirm") ?></p>
    <div class="btn-group">
        <a href="/logout/action" class="btn btn-outline-success"><?= l("lang_logout_yes") ?></a>
        <a href="/" class="btn btn-outline-danger"><?= l("lang_logout_no") ?></a>
    </div>

    <br><br><br>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
