<?php

header("Content-Type: application/json");

function formatPonypush($message) {
    return "Update to Ponypush 3.1.0 or later — (\$PA1$\$" . base64_encode($message) . "\$\$)";
}

$config = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../email.json"), true);
$users = array_map(function ($i) { return substr($i, 0, -5); }, array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/../data/profiles"), function ($i) { return !str_starts_with($i, "."); }));

$users = array_filter($users, function ($id) {
    $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../data/profiles/" . $id . ".json"), true);
    return $data["kiosk"];
});

function uuid() {
    $data = openssl_random_pseudo_bytes(16);
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function token() {
    $data = openssl_random_pseudo_bytes(64);
    return bin2hex($data);
}

$data = [
    "id" => uuid(),
    "ok" => false
];

$key = token();

if (isset($_GET["id"]) && in_array($_GET["id"], $users)) {
    $userData = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../data/profiles/" . $_GET["id"] . ".json"), true);

    $requests = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../kiosk.json"), true);
    $requests[] = [
        "id" => $data["id"],
        "user" => $_GET["id"],
        "key" => $key,
        "date" => date('c')
    ];
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/../kiosk.json", json_encode($requests, JSON_PRETTY_PRINT));

    if (isset($_GET["activate"])) {
        file_get_contents('https://notifications.equestria.dev/delta', false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' =>
                    "Content-Type: text/plain\r\n" .
                    "Title: " . formatPonypush("Enable Delta Kiosk?") . "\r\n" .
                    "Priority: default\r\n" .
                    "Tags: delta\r\n" .
                    "Actions: http, Approve, http://192.168.1.106:8081/dev.equestria.delta.kiosk.ApproveLogin/?id=" . $data["id"] . "&key=" . $key . ", clear=true; http, Reject, http://192.168.1.106:8081/dev.equestria.delta.kiosk.RejectLogin/?id=" . $data["id"] . "&key=" . $key . ", clear=true\r\n" .
                    "Authorization: Basic " . base64_encode($config["ntfyuser"] . ":" . $config["ntfypass"]),
                'content' => formatPonypush("Someone is trying to enable a new Delta Kiosk instance, do you want to allow it and enable this kiosk?")
            ]
        ]));
    } else {
        file_get_contents('https://notifications.equestria.dev/delta', false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' =>
                    "Content-Type: text/plain\r\n" .
                    "Title: " . formatPonypush("Approve log in request?") . "\r\n" .
                    "Priority: default\r\n" .
                    "Tags: delta\r\n" .
                    "Actions: http, Approve, http://192.168.1.106:8081/dev.equestria.delta.kiosk.ApproveLogin/?id=" . $data["id"] . "&key=" . $key . ", clear=true; http, Reject, http://192.168.1.106:8081/dev.equestria.delta.kiosk.RejectLogin/?id=" . $data["id"] . "&key=" . $key . ", clear=true\r\n" .
                    "Authorization: Basic " . base64_encode($config["ntfyuser"] . ":" . $config["ntfypass"]),
                'content' => formatPonypush($userData["first_name"] . " " . $userData["last_name"] . " (" . $_GET["id"] . ") is trying to log in to Delta from a kiosk, do you want to approve it?")
            ]
        ]));
    }

    $data["ok"] = true;
}

die(json_encode($data, JSON_PRETTY_PRINT));
