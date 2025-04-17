<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $_USER;

if (isset($_GET["skel"])) {
    if (preg_match("/^[a-z]*$/m", $_GET["skel"]) === false || !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/skels/" . $_GET["skel"] . ".json")) {
        header("Location: /admin/objects");
        die();
    }

    if (isJson($_POST["contents"])) {
        $id = uuid();

        if ($_GET["skel"] === "profiles") {
            $d = json_decode($_POST["contents"], true);
            $d["date"] = date('c');
            $_POST["contents"] = json_encode($d);
        }

        $d["update"] = date('c');
        $d["update_user"] = $_USER;

        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/" . $_GET["skel"] . "/" . $id . ".json", pf_utf8_encode(json_encode(json_decode($_POST["contents"]), JSON_PRETTY_PRINT)));
        header("Location: /admin/edit/?id=$id");
        die();
    } else {
        $error = l("lang_admin_json");
        require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/create/index.php";
    }
} else {
    header("Location: /admin/objects");
    die();
}