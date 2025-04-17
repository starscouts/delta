<?php

$title = "lang_content_title";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";
global $_PROFILE;

?>

    <div class="container">
        <br><br>

        <div class="list-group">
            <?php

            $list = [];

            foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/articles"), function ($i) { return !str_starts_with($i, "."); }) as $id) {
                $data = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/articles/$id")), true);
                $id = substr($id, 0, -5);

                $data["_type"] = "articles";
                $data["_id"] = $id;

                $list[$id] = $data;
            }

            foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/gallery"), function ($i) { return !str_starts_with($i, "."); }) as $id) {
                $data = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/gallery/$id")), true);
                $id = substr($id, 0, -5);

                $data["_type"] = "gallery";
                $data["_id"] = $id;

                $list[$id] = $data;
            }

            foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people"), function ($i) { return !str_starts_with($i, "."); }) as $id) {
                $data = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people/$id")), true);
                $id = substr($id, 0, -5);

                $data["_type"] = "people";
                $data["_id"] = $id;

                $list[$id] = $data;
            }

            uasort($list, function ($a, $b) {
                return getNameFromId($a["_id"]) > getNameFromId($b["_id"]);
            });

            foreach ($list as $item): ?>
                <a href="/<?= $item["_type"] ?>/<?= $item["_id"] ?>" class="list-group-item list-group-item-action">
                    <img class="icon" src="/icons/<?= $item["_type"] ?>.svg" style="margin-right:5px;"><span style="vertical-align: middle;"><?= getNameFromId($item["_id"]) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <br><br><br>
    </div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>