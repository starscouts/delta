<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";

global $_PROFILE;

if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_PROFILE["alerts"][(int)$_GET['id']])) {
    $_PROFILE["alerts"][(int)$_GET['id']]["read"] = !$_PROFILE["alerts"][(int)$_GET['id']]["read"];
    saveProfile();
}

header("Location: /alerts");
die();