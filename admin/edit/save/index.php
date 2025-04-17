<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";

if (isset($_GET["id"])) {
    $name = getNameFromId($_GET['id']);

    if ($name === $_GET['id']) {
        header("Location: /admin/objects");
        die();
    }

    if (trim($_POST["contents"]) === "") {
        unlink(getFileFromId($_GET["id"]));
        header("Location: /admin");
        die();
    } else {
        if (isJson($_POST["contents"])) {
            file_put_contents(getFileFromId($_GET["id"]), pf_utf8_encode(json_encode(json_decode($_POST["contents"]), JSON_PRETTY_PRINT)));
            header("Location: /admin/edit/?id=$_GET[id]");
            die();
        } else {
            $error = l("lang_admin_json");
            require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/edit/index.php";
        }
    }
} else {
    header("Location: /admin/objects");
    die();
}