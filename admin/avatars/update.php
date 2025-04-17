<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";

if (!isset($_POST["id"])) die();
if (!isset($_POST["upload"])) die();

if (!preg_match("/[a-zA-Z0-6]/m", $_POST["id"])) die();
header("Content-Type: text/plain");

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $_POST["id"] . ".json")) die();

$name = tempnam("/tmp", "Delta-PFP-Upload-");
$uuid = $_POST["id"];
file_put_contents($name, base64_decode(explode(",", $_POST["upload"])[1]));

$_FILES["upload"] = [
    "error" => 0,
    "type" => mime_content_type($name),
    "tmp_name" => $name
];

var_dump($_FILES);
var_dump(mime_content_type($name));

if ($_FILES["upload"]["type"] !== "image/png" && $_FILES["upload"]["type"] !== "image/jpeg" && $_FILES["upload"]["type"] !== "image/webp" && $_FILES["upload"]["type"] !== "image/gif" && $_FILES["upload"]["type"] !== "image/bmp" && $_FILES["upload"]["type"] !== "image/avif") {
    die();
}

$im = imagecreate(1, 1);

switch ($_FILES["upload"]["type"]) {
    case "image/png":
        $im = imagecreatefrompng($_FILES["upload"]["tmp_name"]);
        break;

    case "image/jpeg":
        $im = imagecreatefromjpeg($_FILES["upload"]["tmp_name"]);
        break;

    case "image/webp":
        $im = imagecreatefromwebp($_FILES["upload"]["tmp_name"]);
        break;

    case "image/gif":
        $im = imagecreatefromgif($_FILES["upload"]["tmp_name"]);
        break;

    case "image/bmp":
        $im = imagecreatefrombmp($_FILES["upload"]["tmp_name"]);
        break;

    case "image/avif":
        $im = imagecreatefromavif($_FILES["upload"]["tmp_name"]);
        break;
}

$res = false;

while (!$res) {
    $res = imagewebp($im, $_SERVER['DOCUMENT_ROOT'] . "/uploads/temp-" . $uuid . ".webp");
}

$size = getimagesize($_SERVER['DOCUMENT_ROOT'] . "/uploads/temp-" . $uuid . ".webp");
var_dump($size);

$ratio_orig = $size[0] / $size[1];
$width = 512;
$height = 1080;

if ($width / $height > $ratio_orig) {
    $width = $height * $ratio_orig;
} else {
    $height = $width / $ratio_orig;
}

if ($size[0] > 512 || $size[1] > 512) {
    imagescale($im, $width, $height);

    $res = false;

    while (!$res) {
        $res = imagewebp($im, $_SERVER['DOCUMENT_ROOT'] . "/uploads/temp-" . $uuid . ".webp");

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $uuid . ".webp")) {
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/archive/" . $uuid . ".webp")) unlink($_SERVER['DOCUMENT_ROOT'] . "/uploads/archive/" . $uuid . ".webp");

            rename($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $uuid . ".webp", $_SERVER['DOCUMENT_ROOT'] . "/uploads/archive/" . $uuid . ".webp");
        }

        rename($_SERVER['DOCUMENT_ROOT'] . "/uploads/temp-" . $uuid . ".webp", $_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $uuid . ".webp");
    }
} else {
    rename($_SERVER['DOCUMENT_ROOT'] . "/uploads/temp-" . $uuid . ".webp", $_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $uuid . ".webp");
}
header("Location: /admin/avatars");
die();