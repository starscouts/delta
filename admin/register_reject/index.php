<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/email.php";

$id = $_GET['id'] ?? null;

if (isset($id)) {
    if (!preg_match("/[a-zA-Z0-9]/m", $id)) {
        die();
    }

    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/registrations/" . $id . ".json")) {
        die();
    }
} else {
    die();
}

$reg = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/registrations/" . $id . ".json"), true);

genLang($reg["lang"]);
sendRegistrationRejection($reg["email"], $reg["use_name"] ?? $reg["first_name"], $reg["id"], $_GET["reason"]);

rename($_SERVER['DOCUMENT_ROOT'] . "/includes/data/registrations/" . $id . ".json", $_SERVER['DOCUMENT_ROOT'] . "/includes/data/archive/" . $id . ".json");

header("Location: /admin/registrations");
die();