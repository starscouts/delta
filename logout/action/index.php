<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
global $_PROFILE;

unlink($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . $_COOKIE['DeltaSession']);
setcookie("DeltaSession", "", 1, "/");

if (isset($_COOKIE["DeltaKiosk"])) {
    header("Location: /_dev.equestria.delta.kiosk.SessionEnd");
} else {
    header("Location: /");
}