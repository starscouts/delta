<?php

header("Content-Type: application/json");
$requests = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../kiosk.json"), true);

$data = [
    "success" => false
];

function encode($string) {
    return preg_replace("/[^a-zA-Z0-9.]/m", "", base64_encode($string));
}

if (isset($_GET["id"]) && isset($_GET["key"]) && in_array($_GET["id"], array_map(function ($i) { return $i["id"]; }, $requests))) {
    foreach ($requests as $index => $request) {
        if ($request["id"] === $_GET["id"] && $request["key"] === $_GET["key"] && time() - strtotime($request["date"]) < 60) {
            $data["success"] = true;

            $token = encode(openssl_random_pseudo_bytes(128));
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/../tokens/" . $token, json_encode([
                "user" => $request["user"],
                "date" => date('c')
            ]));

            $requests[$index]["token"] = $token;
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/../kiosk.json", json_encode($requests, JSON_PRETTY_PRINT));
            break;
        }
    }
}

die(json_encode($data, JSON_PRETTY_PRINT));