<?php

if (!isset($_GET["o"])) {
    $data = []; $api = true; require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
}

$palettes = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/themes.json"), true);

$userPalette = $palettes["list"][$palettes["default"]]["light"];

if (isset($_SERVER["HTTP_REFERER"]) && isset($_PROFILE)) {
    $id = preg_replace("/^http(s|):\/\/.*\/(edit|profiles)\/([\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12})$/i", "$3", $_SERVER['HTTP_REFERER']);
}

if (isset($_GET["__"]) && trim($_GET["__"]) !== "") {
    $name = substr($_GET["__"], 1);

    if (str_contains("/", $name)) die();
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name)) die();

    if (isset($userPalette)) {
        $palette = explode(",", $userPalette);

        if (count($palette) > 0) {
            header("Content-Type: " . mime_content_type($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name));
            header("Content-Length: " . filesize($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name));

            if (isset($_GET['p'])) {
                echo(str_replace('fill="#084298"', 'fill="#' . $palette[0] . '"', str_replace('fill="#000000"', 'fill="#' . $palette[9] . '"', file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name))));
            } else {
                echo(str_replace('fill="#084298"', 'fill="#' . $palette[0] . '"', str_replace('fill="#000000"', 'fill="#' . $palette[6] . '"', file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name))));
            }
        } else {
            header("Content-Type: " . mime_content_type($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name));
            header("Content-Length: " . filesize($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name));

            readfile($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name);
        }
    } else {
        header("Content-Type: " . mime_content_type($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name));
        header("Content-Length: " . filesize($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name));

        readfile($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name);
    }
}
