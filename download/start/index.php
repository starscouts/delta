<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $_PROFILE; global $_USER;

function recursive_rmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
                    recursive_rmdir($dir. DIRECTORY_SEPARATOR .$object);
                else
                    unlink($dir. DIRECTORY_SEPARATOR .$object);
            }
        }
        rmdir($dir);
    }
}

$tempDir = "/tmp/delta-takeout-" . bin2hex(random_bytes(32));
mkdir($tempDir);
chdir($tempDir);

$name1 = l("lang_download_files_user");
$name2 = l("lang_download_files_requests");
$name3 = l("lang_download_files_gallery");
$name4 = l("lang_download_files_pending");
$name5 = l("lang_download_files_closed");
$name6 = l("lang_download_files_public");
$name7 = l("lang_download_files_deleted");
$name8 = l("lang_download_files_history");
$name9 = l("lang_download_files_profile");
$name10 = l("lang_download_files_avatar");
$name11 = l("lang_download_files_uploads");

mkdir($name1);

mkdir($name2);
mkdir("$name2/$name4");
mkdir("$name2/$name5");

mkdir($name3);
mkdir("$name3/$name6");
mkdir("$name3/$name7");

mkdir($name11);
mkdir("$name11/$name6");
mkdir("$name11/$name7");

foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests"), function ($i) { return !str_starts_with($i, "."); }) as $file) {
    $id = substr($file, 0, -5);
    $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/" . $file), true);

    if ($data["author"] === $_USER) {
        file_put_contents("$name2/$name4/" . $file, json_encode($data, JSON_PRETTY_PRINT));
    }

    if ($data["type"] === "galleryupload" && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/olduploads/" . $id . ".webp")) {
        file_put_contents("$name3/$name6/" . $id . ".webp", file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $id . ".webp"));
    }
}

foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/archive"), function ($i) { return !str_starts_with($i, "."); }) as $file) {
    $id = substr($file, 0, -5);
    $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/archive/" . $file), true);

    if ($data["author"] === $_USER) {
        file_put_contents("$name2/$name5/" . $file, json_encode($data, JSON_PRETTY_PRINT));
    }

    if ($data["type"] === "galleryupload" && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/olduploads/" . $id . ".webp")) {
        file_put_contents("$name3/$name7/" . $id . ".webp", file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/olduploads/" . $id . ".webp"));
    }
}

file_put_contents("$name1/$name9.json", json_encode($_PROFILE, JSON_PRETTY_PRINT));

if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/history/" . $_USER . ".json")) {
    file_put_contents("$name1/$name8.json", json_encode(json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/history/" . $_USER . ".json"), true), JSON_PRETTY_PRINT));
} else {
    file_put_contents("$name1/$name8.json", "{}");
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $_USER . ".webp")) {
    file_put_contents("$name1/$name10.webp", file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $_USER . ".webp"));
} else {
    file_put_contents("$name1/$name10.webp", "");
}

$list = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/embeds.json")), true);

foreach ($list as $item) {
    if ($item["author"] === $_USER) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $item["id"] . ".webp")) {
            file_put_contents("$name11/$name6/$item[id].webp", file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $item["id"] . ".webp"));
        } elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/archive/" . $item["id"] . ".webp")) {
            file_put_contents("$name11/$name7/$item[id].webp", file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/uploads/archive/" . $item["id"] . ".webp"));
        } else {
            file_put_contents("$name11/$name7/$item[id].webp", "");
        }
    }
}

$zip = new ZipArchive;
$tmp_file = $tempDir . ".zip";

if ($zip->open($tmp_file, ZipArchive::CREATE)) {
    foreach (array_filter(scandir($tempDir), function ($i) { return !str_starts_with($i, "."); }) as $file) {
        if (is_dir($file)) {
            $zip->addEmptyDir($file);

            foreach (array_filter(scandir($tempDir . "/" . $file), function ($i) { return !str_starts_with($i, "."); }) as $file2) {
                if (is_dir($file . "/" . $file2)) {
                    $zip->addEmptyDir($file . "/" . $file2);

                    foreach (array_filter(scandir($tempDir . "/" . $file . "/" . $file2), function ($i) { return !str_starts_with($i, "."); }) as $file3) {
                        $zip->addFile($file . "/" . $file2 . "/" . $file3, $file . "/" . $file2 . "/" . $file3);
                    }
                } else {
                    $zip->addFile($file . "/" . $file2, $file . "/" . $file2);
                }
            }
        } else {
            $zip->addFile($file, $file);
        }
    }

    $zip->close();
}

recursive_rmdir($tempDir);

header("Content-type: application/zip");
header("Content-Disposition: attachment; filename=" . str_replace("%1", $_PROFILE["first_name"] . " " . $_PROFILE["last_name"], l("lang_download_name")) . ".zip")  ;
header("Content-length: " . filesize($tempDir . ".zip"));
header("Pragma: no-cache");
header("Expires: 0");

readfile($tempDir . ".zip");
unlink($tempDir . ".zip");