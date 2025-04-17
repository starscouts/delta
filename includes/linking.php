<?php

function doLinking($text) {
    require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
    initLang();

    $list = [
        ...array_map(function ($i) {
            return [
                "link" => "/articles/" . substr($i, 0, -5),
                "name" => getNameFromId(substr($i, 0, -5))
            ];
        }, array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/articles"), function ($i) {
            return !str_starts_with($i, ".");
        })),
        ...array_map(function ($i) {
            return [
                "link" => "/people/" . substr($i, 0, -5),
                "name" => getNameFromId(substr($i, 0, -5))
            ];
        }, array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people"), function ($i) {
            return !str_starts_with($i, ".");
        })),
        ...array_map(function ($i) {
            return [
                "link" => "/people/" . substr($i, 0, -5),
                "name" => json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $i)), true)["first_name"] . " " . (json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $i)), true)["born"] ?? json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $i)), true)["last_name"])
            ];
        }, array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people"), function ($i) {
            return !str_starts_with($i, ".");
        })),
        ...array_map(function ($i) {
            return [
                "link" => "/people/" . substr($i, 0, -5),
                "name" => strtoupper(json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $i)), true)["last_name"]) . " " . json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $i)), true)["first_name"]
            ];
        }, array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people"), function ($i) {
            return !str_starts_with($i, ".");
        })),
        ...array_map(function ($i) {
            return [
                "link" => "/people/" . substr($i, 0, -5),
                "name" => strtoupper(json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $i)), true)["born"] ?? json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $i)), true)["last_name"]) . " " . json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $i)), true)["first_name"]
            ];
        }, array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people"), function ($i) {
            return !str_starts_with($i, ".");
        })),
        ...array_map(function ($i) {
            return [
                "link" => "/people/" . substr($i, 0, -5),
                "name" => json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $i)), true)["alts"][0]
            ];
        }, array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people"), function ($i) {
            return !str_starts_with($i, ".") && isset(json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $i)), true)["alts"]) && isset(json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $i)), true)["alts"][0]);
        })),
        ...array_map(function ($i) {
            return [
                "link" => "/people/" . substr($i, 0, -5),
                "name" => json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $i)), true)["alts"][1]
            ];
        }, array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people"), function ($i) {
            return !str_starts_with($i, ".") && isset(json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $i)), true)["alts"]) && isset(json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $i)), true)["alts"][1]);
        })),
        ...array_map(function ($i) {
            return [
                "link" => "/gallery/" . substr($i, 0, -5),
                "name" => getNameFromId(substr($i, 0, -5))
            ];
        }, array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/gallery"), function ($i) {
            return !str_starts_with($i, ".");
        }))
    ];

    usort($list, function ($a, $b) {
        return strlen($b["name"]) - strlen($a["name"]);
    });

    if (isset(explode("/", $_SERVER['REQUEST_URI'])[2]) && getNameFromId(explode("/", $_SERVER['REQUEST_URI'])[2]) !== null) {
        $current = getNameFromId(explode("/", $_SERVER['REQUEST_URI'])[2]);
    } else {
        $current = null;
    }

    foreach ($list as $item) {
        if ($item["name"] !== $current) {
            $text = str_ireplace($item["name"], '<a href="' . $item["link"] . '">' . $item["name"] . "</a>", $text);
        }
    }

    return $text;
}