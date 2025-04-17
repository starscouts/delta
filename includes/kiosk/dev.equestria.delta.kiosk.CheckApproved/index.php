<?php

header("Content-Type: application/json");
$requests = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../kiosk.json"), true);

$data = [
    "token" => null
];

if (isset($_GET["id"]) && in_array($_GET["id"], array_map(function ($i) { return $i["id"]; }, $requests))) {
    foreach ($requests as $index => $request) {
        if ($request["id"] === $_GET["id"] && isset($request["token"]) && time() - strtotime($request["date"]) < 90) {
            $data["token"] = $request["token"];
            unset($requests[$index]["token"]);
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/../kiosk.json", json_encode($requests, JSON_PRETTY_PRINT));
            break;
        }
    }
}

die(json_encode($data, JSON_PRETTY_PRINT));