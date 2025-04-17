<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
$title = "lang_blocked_login_title"; require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
global $_USER; global $_PROFILE;

?>

<div class="container">
    <br><br>
    <h1><?= l("lang_blocked_login_title2") ?></h1>

    <div class="alert alert-danger">
        <p><?= str_replace("%1", $_PROFILE["nick_name"] ?? $_PROFILE["first_name"] . " " . $_PROFILE["last_name"], l("lang_blocked_login_message_0")) ?></p>
        <p><?= l("lang_blocked_login_message_1") ?></p>
        <?= l("lang_blocked_login_message_2") ?>
        <ul>
            <li><?= l("lang_blocked_login_message_3") ?></li>
            <li><?= l("lang_blocked_login_message_4") ?></li>
            <li><?= l("lang_blocked_login_message_5") ?></li>
            <li><?= l("lang_blocked_login_message_6") ?></li>
            <li><?= l("lang_blocked_login_message_7") ?></li>
            <li><?= l("lang_blocked_login_message_8") ?></li>
        </ul>
        <p><?= l("lang_blocked_login_message_9") ?></p>
        <p><?= l("lang_blocked_login_message_10") ?></p>
        <p><?= l("lang_blocked_login_message_11") ?></p>
        <p><?= l("lang_blocked_login_message_12") ?></p>
        <small class="opacity-50"><?= l("lang_blocked_login_id") ?> <?= $_USER ?><br><?= l("lang_blocked_login_date") ?> <?= timeAgo($_PROFILE["date"]) ?></small>
    </div>
</div>

<br><br>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>