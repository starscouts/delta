<?php

global $hourly;

function println(...$text) {
    foreach ($text as $_) echo $_;
    echo("\n");
}

$users = [];

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/email.php";

$list = array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles"), function ($i) { return !str_starts_with($i, "."); });

foreach ($list as $file) {
    $data = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/$file")), true);
    if (is_null($data)) continue;

    genLang($data["language"]);

    println($data["first_name"], " ", $data["last_name"]);

    println("    User information:");
    println("        Subscription: ", $data["ultra"] && $data["plus"] ? "Delta Ultra" : ($data["plus"] ? "Delta Plus" : "Delta"), ", renews on the ", isset($data["renewal"]) ? date('j F', strtotime($data["renewal"])) : "n/a");
    println("        Nick name: ", $data["nick_name"] ?? "-");

    println("    Jobs:");

    println("        Email linking");
    $users[$data["email"]] = substr($file, 0, -5);

    if ($data["nick_name"] && !$data["plus"]) {
        println("        Nick name removal");
        $data["nick_name"] = null;
    }

    if ($data["blocked"] !== $data["last_blocked"]) {
        println("        Block level update");

        switch ($data["blocked"]) {
            case 0:
                println("            Current block level: none (0)");

                $data["alerts"][] = [
                    "title" => l("lang_notifications_block0_title"),
                    "message" => l("lang_notifications_block0_message"),
                    "date" => date('c'),
                    "read" => false
                ];

                break;

            case 1:
                println("            Current block level: personal (1)");

                $data["alerts"][] = [
                    "title" => l("lang_notifications_block1_title"),
                    "message" => l("lang_notifications_block1_message"),
                    "date" => date('c'),
                    "read" => false
                ];

                break;

            case 2:
                println("            Current block level: requests (2)");

                $data["alerts"][] = [
                    "title" => l("lang_notifications_block2_title"),
                    "message" => l("lang_notifications_block2_message"),
                    "date" => date('c'),
                    "read" => false
                ];

                break;

            case 3:
                println("            Current block level: full (3)");

                $data["alerts"][] = [
                    "title" => l("lang_notifications_block3_title"),
                    "message" => l("lang_notifications_block3_message"),
                    "date" => date('c'),
                    "read" => false
                ];

                break;
        }

        $data["last_blocked"] = $data["blocked"];
    }

    if (count($data["devices"]) > 0) {
        println("        Old devices removal");
        $removed = 0;

        foreach ($data["devices"] as $index => $device) {
            if (time() - strtotime($device["date"]) > 86400*30) {
                $removed++;
                unset($data["devices"][$index]);
            }
        }

        println("            Removed " . $removed . " device(s)");
    }

    println("        Old sessions removal");
    $removed = 0;

    foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens"), function ($i) { return !str_starts_with($i, "."); }) as $token) {
        $tokenData = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . $token), true);

        if ($tokenData["user"] === substr($file, 0, -5)) {
            if (time() - strtotime($tokenData["date"]) >= 86400 * 90) {
                $removed++;
                unlink($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . $token);
            }
        }
    }

    println("            Removed " . $removed . " session(s)");

    $alertsToSend = [];

    foreach ($data["alerts"] as $alert) {
        if ((!isset($alert["email"]) || $alert["email"] === false) && $alert["read"] === false) {
            $alertsToSend[] = $alert;
        }
    }

    if ($hourly && (!$data["ultra"] || count($data["devices"]) < 1)) {
        println("    Email delivery (", count($alertsToSend) ,"):");

        foreach ($alertsToSend as $alert) {
            println("        ", $alert["title"]);
        }

        foreach ($data["alerts"] as $index => $alert) {
            $data["alerts"][$index]["email"] = true;
        }

        if (count($alertsToSend) > 0) {
            sendAlerts($data["email"], array_reverse($alertsToSend));
        }
    }

    if ($data["ultra"] && count($data["devices"]) > 0) {
        println("    FCM delivery (", count($alertsToSend) ,"):");

        foreach ($data["alerts"] as $index => $alert) {
            $data["alerts"][$index]["email"] = true;
        }

        if (count($alertsToSend) > 0) {
            foreach ($alertsToSend as $alert) {
                println("        ", $alert["title"]);

                foreach ($data["devices"] as $device) {
                    exec('node "' . $_SERVER['DOCUMENT_ROOT'] . "/includes/fcm/index.js" . '" "' . str_replace('"', '\\"', $device["token"]) . '" "' . str_replace('"', '\\"', $alert["title"]) . '" "' . str_replace('"', '\\"', $alert["message"]) . '"', $d);
                }
            }
        }
    }

    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/$file", pf_utf8_encode(json_encode($data, JSON_PRETTY_PRINT)));
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/users.json", pf_utf8_encode(json_encode($users, JSON_PRETTY_PRINT)));
}

println("Cleaning up support codes...");
$support = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/supportcodes.json"), true);

foreach ($support as $index => $code) {
    if (time() - strtotime($code["date"]) > 86400*14) {
        unset($support[$index]);
    }
}

file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/supportcodes.json", json_encode($support, JSON_PRETTY_PRINT));

println("Cleaning up login codes...");
$codes = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/codes.json"), true);

foreach ($codes as $name => $code) {
    if (time() - strtotime($code["date"]) > 900) {
        unset($codes[$name]);
    }
}

println("Cleaning up unused images...");
foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/uploads/"), function ($i) { return !str_starts_with($i, "."); }) as $upload) {
    $used = false;

    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/archive")) mkdir($_SERVER['DOCUMENT_ROOT'] . "/uploads/archive");

    foreach (getSearchEntries(false, true) as $entry) {
        $data = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/" . $entry["type"] . "/" . $entry["id"] . ".json");

        if (str_contains($data, $upload) || $entry["id"] === explode(".", $upload)[0]) {
            $used = true;
        }
    }

    println("    " . $upload . ", will delete: " . (!$used && time() - filemtime($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $upload) > 3600 ? "yes" : "no") . ", difference: " . (time() - filemtime($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $upload)) . ", used: " . ($used ? "yes" : "no"));

    if (!$used && time() - filemtime($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $upload) > 3600) {
        rename($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $upload, $_SERVER['DOCUMENT_ROOT'] . "/uploads/archive/" . $upload);
    }
}

file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/codes.json", json_encode($codes, JSON_PRETTY_PRINT));
