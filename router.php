<?php

if (str_contains($_SERVER['REQUEST_URI'], "..")) die();

if (str_starts_with($_SERVER['REQUEST_URI'], "/people/") || $_SERVER['REQUEST_URI'] === "/people") {
    $parts = explode("/", $_SERVER['REQUEST_URI']);
    array_shift($parts); array_shift($parts);

    $_GET["__"] = implode("/", $parts);
    $_GET["/" . implode("/", $parts)] = null;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/_people/index.php";
} else if (str_starts_with($_SERVER['REQUEST_URI'], "/articles/") || $_SERVER['REQUEST_URI'] === "/articles") {
    $parts = explode("/", $_SERVER['REQUEST_URI']);
    array_shift($parts); array_shift($parts);

    $_GET["__"] = implode("/", $parts);
    $_GET["/" . implode("/", $parts)] = null;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/_articles/index.php";
} else if (str_starts_with($_SERVER['REQUEST_URI'], "/gallery/") || $_SERVER['REQUEST_URI'] === "/gallery") {
    $parts = explode("/", $_SERVER['REQUEST_URI']);
    array_shift($parts); array_shift($parts);

    $_GET["__"] = implode("/", $parts);
    $_GET["/" . implode("/", $parts)] = null;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/_gallery/index.php";
} else if (str_starts_with($_SERVER['REQUEST_URI'], "/edit/") || $_SERVER['REQUEST_URI'] === "/edit") {
    $parts = explode("/", $_SERVER['REQUEST_URI']);
    array_shift($parts); array_shift($parts);

    $_GET["__"] = implode("/", $parts);
    $_GET["/" . implode("/", $parts)] = null;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/_edit/index.php";
} else if (str_starts_with($_SERVER['REQUEST_URI'], "/profile/") || $_SERVER['REQUEST_URI'] === "/profile") {
    $parts = explode("/", $_SERVER['REQUEST_URI']);
    array_shift($parts); array_shift($parts);

    $_GET["__"] = implode("/", $parts);
    $_GET["/" . implode("/", $parts)] = null;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/_profile/index.php";
} else {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SERVER['REQUEST_URI'])) {
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . "/" . $_SERVER['REQUEST_URI'])) {
            if (str_ends_with($_SERVER['DOCUMENT_ROOT'] . "/" . $_SERVER['REQUEST_URI'], ".css")) {
                header("Content-Type: text/css");
            } else if (str_ends_with($_SERVER['DOCUMENT_ROOT'] . "/" . $_SERVER['REQUEST_URI'], ".js")) {
                header("Content-Type: application/javascript");
            } else {
                header("Content-Type: " . mime_content_type($_SERVER['DOCUMENT_ROOT'] . "/" . $_SERVER['REQUEST_URI']));
            }

            header("Content-Length: " . filesize($_SERVER['DOCUMENT_ROOT'] . "/" . $_SERVER['REQUEST_URI']));
            readfile($_SERVER['DOCUMENT_ROOT'] . "/" . $_SERVER['REQUEST_URI']);
        } else {
            require_once $_SERVER['SCRIPT_FILENAME'];
        }
    } else if (str_starts_with($_SERVER['REQUEST_URI'], "/icons/")) {
        $parts = explode("/", $_SERVER['REQUEST_URI']);
        array_shift($parts); array_shift($parts);
        $name = implode("/", $parts);

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name)) {
            header("Content-Type: " . mime_content_type($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name));
            header("Content-Length: " . filesize($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name));
            readfile($_SERVER['DOCUMENT_ROOT'] . "/_icons/" . $name);
        } else {
            die("Not found");
        }
    } else {
        require_once $_SERVER['SCRIPT_FILENAME'];
    }
}