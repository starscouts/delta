<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
$title = "lang_admin_title";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";

?>

<div class="container">
    <br><br>
    <h1><?= l("lang_admin_title") ?></h1>

    <div class="list-group">
        <a href="/admin/requests" class="list-group-item list-group-item-action"><?= l("lang_admin_titles_requests") ?></a>
        <a href="/admin/registrations" class="list-group-item list-group-item-action"><?= l("lang_admin_titles_registrations") ?></a>
        <a href="/admin/objects" class="list-group-item list-group-item-action"><?= l("lang_admin_titles_objects") ?></a>
        <a href="/admin/codes" class="list-group-item list-group-item-action"><?= l("lang_admin_titles_codes") ?></a>
        <a href="/admin/avatars" class="list-group-item list-group-item-action"><?= l("lang_admin_titles_avatars") ?></a>
    </div>

    <br><br>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
