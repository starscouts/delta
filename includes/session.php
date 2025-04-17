<?php

die(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/maintenance.html"));
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";

global $_USER;
global $_PROFILE;

if (isset($_COOKIE["DeltaSession"])) {
    if (preg_match("/^[a-zA-Z0-9]*$/m", $_COOKIE['DeltaSession']) !== false) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . $_COOKIE['DeltaSession'])) {
            $_SessionCheck_Data = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . $_COOKIE['DeltaSession'])), true);
            if (time() - strtotime($_SessionCheck_Data["date"]) < 86400 * 90 && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $_SessionCheck_Data["user"] . ".json")) {
                $_USER = $_SessionCheck_Data["user"];

                while (trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $_SessionCheck_Data["user"] . ".json")) === "") {}

                $_PROFILE = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $_SessionCheck_Data["user"] . ".json")), true);

                if (isset($api)) $data["loggedIn"] = true;

                if ($_PROFILE["blocked"] >= 3 && $_SERVER['PHP_SELF'] !== "/login/blocked/index.php") {
                    if (!isset($api)) {
                        header("Location: /login/blocked/?return=" . rawurlencode($_SERVER['REQUEST_URI']));
                    } else {
                        $data["blocked"] = true;
                    }
                    if (!isset($api)) die();
                } elseif ($_PROFILE["blocked"] < 3 && $_SERVER['PHP_SELF'] === "/login/blocked/index.php") {
                    if (!isset($api)) header("Location: " . ($_GET["return"] ?? "/"));
                    if (!isset($api)) die();
                }
            } else {
                if (!isset($api)) header("Location: /login/?return=" . rawurlencode($_SERVER['REQUEST_URI']));
                if (!isset($api)) die();
            }
        } else {
            if (!isset($api)) header("Location: /login/?return=" . rawurlencode($_SERVER['REQUEST_URI']));
            if (!isset($api)) die();
        }
    } else {
        if (!isset($api)) header("Location: /login/?return=" . rawurlencode($_SERVER['REQUEST_URI']));
        if (!isset($api)) die();
    }
} else {
    if (!isset($api)) header("Location: /login/?return=" . rawurlencode($_SERVER['REQUEST_URI']));
    if (!isset($api)) die();
}

if (str_starts_with($_SERVER['REQUEST_URI'], "/admin") && (!isset($_PROFILE["admin"]) || !$_PROFILE["admin"])) {
    header("Location: /");
    die();
}

function saveProfile(): void {
    global $_PROFILE;
    global $_USER;

    while (trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $_USER . ".json")) === "") {}

    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $_USER . ".json", pf_utf8_encode(json_encode($_PROFILE, JSON_PRETTY_PRINT)));
}

$userLang = $_GET["hl"] ?? $_COOKIE["DeltaLanguage"] ?? (isset($_PROFILE) ? $_PROFILE["language"] : substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? "en", 0, 2));

if (isset($_PROFILE)) {
    if (!isset($api) && date('d-m-Y') !== date('d-m-Y', strtotime($_PROFILE["last_seen"]))) {
        $_PROFILE["last_seen"] = date('d-m-Y');
        saveProfile();
    }

    if (isset($_PROFILE["nick_name"]) && (!isset($_PROFILE["ultra"]) || !$_PROFILE["ultra"])) {
        $_PROFILE["nick_name"] = null;
        saveProfile();
    }
}

function addToUserHistory($id) {
    global $_USER;

    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/history/" . $_USER . ".json")) file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/history/" . $_USER . ".json", pf_utf8_encode("{}"));

    while (trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/history/" . $_USER . ".json")) === "") {}
    $history = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/history/" . $_USER . ".json")), true);

    if (isset($history[$id])) {
        $history[$id]++;
    } else {
        $history[$id] = 1;
    }

    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/history/" . $_USER . ".json", pf_utf8_encode(json_encode($history, JSON_PRETTY_PRINT)));
}

initLang();
