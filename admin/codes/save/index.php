<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";

if (trim($_POST["contents"]) === "") {
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/coins.json", "{}", JSON_PRETTY_PRINT);
    header("Location: /admin/codes");
    die();
} else {
    if (isJson($_POST["contents"])) {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/coins.json", pf_utf8_encode(json_encode(json_decode($_POST["contents"]), JSON_PRETTY_PRINT)));
        header("Location: /admin/codes");
        die();
    } else {
        $error = l("lang_admin_json");
        require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/codes/index.php";
    }
}