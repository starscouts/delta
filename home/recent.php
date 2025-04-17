<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";
initLang();
global $_PROFILE; global $_USER;

function showPage($item) { ?>

    <a href="/<?= $item["_type"] ?>/<?= $item["_id"] ?>" class="list-group-item list-group-item-action">
        <p style="margin-bottom: 10px;"><img class="icon" src="/icons/<?= $item["_type"] ?>.svg" style="margin-right:5px;"><span style="vertical-align: middle;"><b><?= getNameFromId($item["_id"]) ?></b> <?= l("lang_home_update") ?> <b><?= timeAgo($item["update"]) ?></b></span></p><?= trim(strip_tags($item["contents"])) !== "" ? substr(trim(strip_tags($item["contents"])), 0, 150) . (strlen(trim(strip_tags($item["contents"]))) > 150 ? "…" : "") : "-" ?>
    </a>

<?php } ?>

<h3 style="margin-bottom: 15px; margin-top: 30px;"><?= l("lang_home_recent") ?></h3>

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
        return strtotime($a["update"]) - strtotime($b["update"]);
    });

    $list = array_reverse($list);

    $index = 0; foreach ($list as $item): if ($index <= 4): ?>
        <?php showPage($item); ?>
        <?php $index++; endif; endforeach; ?>
</div>
