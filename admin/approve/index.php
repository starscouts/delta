<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";

$id = $_GET['id'] ?? null;

if (isset($id)) {
    if (!preg_match("/[a-zA-Z0-6]/m", $id)) {
        die();
    }

    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/" . $id . ".json")) {
        die();
    }
} else {
    die();
}

$request = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/" . $id . ".json")), true);

if (isset($_GET["md5"]) && $_GET["md5"] !== md5(json_encode($request))) {
    header("Location: /admin/requests/?modified");
    die();
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $request["author"] . ".json")) {
    while (trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $request["author"] . ".json")) === "") {}

    $profile = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $request["author"] . ".json")), true);
    loadLang(json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/lang/" . $profile["language"] . ".json"), true), "lang", $profile["language"]);

    $index = array_search($id, $profile["requests"]);

    if ($index !== false) {
        unset($profile["requests"][$index]);
    }

    $profile["alerts"][] = [
        "title" => l("lang_notifications_approve_title"),
        "message" => str_replace("%3", date('H:i', strtotime($request["date"])), str_replace("%2", formatDate($request["date"]), str_replace("%1", l("lang_request_types_" . $request["type"]), l("lang_notifications_approve_message")))),
        "date" => date('c'),
        "read" => false
    ];

    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $request["author"] . ".json", pf_utf8_encode(json_encode($profile, JSON_PRETTY_PRINT)));
}

if ($request["type"] === "galleryupload" && !isset($_GET['mark'])) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/gallery/" . $request["id"] . ".json")) {
        $gallery = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/gallery/" . $request["id"] . ".json")), true);
        $gallery["images"][] = [
            "id" => $request["uuid"],
            "author" => $request["author"],
            "date" => $request["date"]
        ];
        $gallery["update"] = date('c');
        $gallery["update_user"] = $request["author"];

        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/gallery/" . $request["id"] . ".json", pf_utf8_encode(json_encode($gallery)));
    }
}

if (($request["type"] === "gallerymeta" || $request["type"] === "article" || $request["type"] === "userpage") && !isset($_GET['mark'])) {
    $file = "/";
    if ($request["type"] === "gallerymeta") $file = $_SERVER['DOCUMENT_ROOT'] . "/includes/data/gallery/" . $request["id"] . ".json";
    if ($request["type"] === "article") $file = $_SERVER['DOCUMENT_ROOT'] . "/includes/data/articles/" . $request["id"] . ".json";
    if ($request["type"] === "userpage") $file = $_SERVER['DOCUMENT_ROOT'] . "/includes/data/people/" . $request["id"] . ".json";

    $d = json_decode(pf_utf8_decode(file_get_contents($file)), true);
    $d["contents"] = $request["contents"];
    $d["update"] = date('c');
    $d["update_user"] = $request["author"];

    file_put_contents($file, pf_utf8_encode(json_encode($d)));
}

rename($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/" . $id . ".json", $_SERVER['DOCUMENT_ROOT'] . "/includes/data/archive/" . $id . ".json");

header("Location: /admin/requests");
die();