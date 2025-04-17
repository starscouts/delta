<?php

$_lang = [];

function loadLang($array, $baseName, $lf) {
    global $_lang;

    $_lang["lang__name"] = $lf;

    foreach ($array as $name => $item) {
        if (is_array($item)) {
            loadLang($item, $baseName . "_" . $name, $lf);
        } else {
            $_lang[$baseName . "_" . $name] = $item;
        }
    }
}

function genLang($lf): void {
    if (str_contains($lf,  "/") || !file_exists($_SERVER['DOCUMENT_ROOT'] . "/lang/" . $lf . ".json")) {
        $lf = "en";
    }

    $lp = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/lang/" . $lf . ".json"), true);
    loadLang($lp, "lang", $lf);
}

function l($entry) {
    global $_lang;

    return $_lang[$entry] ?? strip_tags($entry);
}