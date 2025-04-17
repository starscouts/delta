<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";

if (!isset($_GET['_'])) die();

$name = preg_replace("/[\/]/m", "", $_GET['_']);

if (str_ends_with($name, ".jpg") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . substr($name, 0, -4) . ".webp") && is_file($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . substr($name, 0, -4) . ".webp")) {
    header("Cache-Control: max-age=31536000");
    header("Content-Type: " . mime_content_type($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . substr($name, 0, -4) . ".webp"));
    header("Content-Length: " . filesize($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . substr($name, 0, -4) . ".webp"));
    readfile($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . substr($name, 0, -4) . ".webp");
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $name) && is_file($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $name)) {
    header("Cache-Control: max-age=31536000");
    header("Content-Type: " . mime_content_type($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $name));
    header("Content-Length: " . filesize($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $name));
    readfile($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $name);
} else {
    die("Not found: " . $_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $name);
}