<?php

header("Content-Type: application/json");

$users = array_map(function ($i) { return substr($i, 0, -5); }, array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/../data/profiles"), function ($i) { return !str_starts_with($i, "."); }));

$users = array_filter($users, function ($id) {
    $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../data/profiles/" . $id . ".json"), true);
    return $data["kiosk"] && $data["blocked"] < 3;
});

$data = [];

foreach ($users as $user) {
    $userData = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../data/profiles/" . $user . ".json"), true);

    $data[] = [
        "id" => $user,
        "name" => strtoupper($userData["last_name"]) . " " . ucwords($userData["first_name"])
    ];
}

die(json_encode($data, JSON_PRETTY_PRINT));