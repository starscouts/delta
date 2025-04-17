<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";

if (!isset($_GET["id"])) die();
if (!preg_match("/[a-zA-Z0-6]/m", $_GET["id"])) die();
header("Content-Type: text/plain");

if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $_GET["id"] . ".webp")) {
    header("Location: /uploads/" . $_GET["id"] . ".webp?__=" . bin2hex(random_bytes(32)));
    die();
} else {
    header("Location: /defaultuser.png");
    die();
}