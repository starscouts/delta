<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
$title = "lang_admin_title";
$title_pre = l("lang_admin_titles_requests");
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";

?>

<div class="container">
    <br><br>
    <a href="/admin">← <?= l("lang_admin_title") ?></a>
    <h1><?= l("lang_admin_titles_requests") ?></h1>

    <?php if (isset($_GET["modified"])): ?>
        <div class="alert alert-warning">
            <b><?= l("lang_admin_modified_title") ?> </b><?= l("lang_admin_modified_description") ?>
        </div>
    <?php endif; ?>

    <?php

    $requestsNonPlus = array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests"), function ($i) { return !str_starts_with($i, "."); });

    $requestsNonPlus = array_values(array_filter($requestsNonPlus, function ($i) {
        $request = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/$i")), true);
        $adata = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $request["author"] . ".json")), true);
        return !$adata["plus"];
    }));

    usort($requestsNonPlus, function ($a, $b) {
        return strtotime(json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/$a")), true)["date"]) - strtotime(json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/$b")), true)["date"]);
    });

    $requestsPlus = array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests"), function ($i) { return !str_starts_with($i, "."); });

    $requestsPlus = array_values(array_filter($requestsPlus, function ($i) {
        $request = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/$i")), true);
        $adata = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $request["author"] . ".json")), true);
        return $adata["plus"];
    }));

    usort($requestsPlus, function ($a, $b) {
        return strtotime(json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/$a")), true)["date"]) - strtotime(json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/$b")), true)["date"]);
    });

    $requests = [...array_map(function ($i) {
        $id = $i;
        $i = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/$i")), true);
        $i["_id"] = explode(".", $id)[0];
        $i["_plus"] = true;
        $i["_original"] = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/$id")), true);
        return $i;
    }, $requestsPlus), ...array_map(function ($i) {
        $id = $i;
        $i = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/$i")), true);
        $i["_id"] = explode(".", $id)[0];
        $i["_plus"] = false;
        $i["_original"] = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/$id")), true);
        return $i;
    }, $requestsNonPlus)];

    ?>

    <div class="list-group">
        <?php foreach ($requests as $request): ?>
        <details class="list-group-item list-group-item-action <?= $request["_plus"] ? "list-group-item-warning" : "" ?>">
            <summary><?= json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/lang/en.json"), true)["request"]["types"][$request["type"]] ?> (<?php if (isset($request["id"]) && trim($request["id"]) !== "" && $request["id"] !== $request["author"]): ?><?= l("lang_admin_requests_preview_target") ?> <a href="/<?= str_starts_with("gallery", $request["type"]) ? "gallery" : ($request["type"] === "userpage" ? "people" : ($request["type"] === "userreport" ? "profile" : "articles")) ?>/<?= $request["id"] ?>" target="_blank"><?= getNameFromId($request["id"]) ?></a>; <?php endif; ?><?= l("lang_admin_requests_preview_author") ?> <a href="/profile/<?= $request["author"] ?>" target="_blank"><?php $adata = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $request["author"] . ".json")), true); ?> <?= isset($adata["nick_name"]) && trim($adata["nick_name"]) !== "" ? $adata["nick_name"] : $adata["first_name"] . " " . $adata["last_name"] ?></a>; <?= timeAgo($request["date"]) ?>)</summary>

            <div class="list-group-item" style="margin-top: 10px;">
                <p>
                    <b><?= l("lang_admin_requests_view_0") ?></b> <?= $request["_id"] ?><br>
                    <b><?= l("lang_admin_requests_view_1") ?></b> <?= date('r', strtotime($request["date"])) ?><br>
                    <b><?= l("lang_admin_requests_view_2") ?></b> <?php $adata = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $request["author"] . ".json")), true); ?> <?= isset($adata["nick_name"]) && trim($adata["nick_name"]) !== "" ? $adata["nick_name"] : $adata["first_name"] . " " . $adata["last_name"] ?> (<?= $request["author"] ?>)<br>
                    <b><?= l("lang_admin_requests_view_3") ?></b> <?= isset($request["id"]) && trim($request["id"]) !== "" && $request["id"] !== $request["author"] ? getNameFromId($request["id"]) . " (" . $request["id"] . ")" : "<span class='text-muted'>" . l("lang_admin_requests_view_5") . "</span>" ?><br>
                    <b><?= l("lang_admin_requests_view_4") ?></b> <?= isset($request["summary"]) && trim($request["summary"]) !== "" ? strip_tags($request["summary"]) : "<span class='text-muted'>" . l("lang_admin_requests_view_5") . "</span>" ?>
                </p>

                <details style="margin-bottom: 1rem;">
                    <summary><?= l("lang_admin_requests_full") ?></summary>
                    <pre style="margin-bottom: 0;"><?= str_replace(">", "&gt;", str_replace("<", "&lt;", json_encode($request, JSON_PRETTY_PRINT))) ?></pre>
                </details>

                <?php if ($request["type"] === "galleryupload"): ?>
                <p><img src="/uploads/<?= $request["uuid"] ?? $request["_id"] ?>.webp" style="max-width: 30vw; max-height: 30vh;"></p>
                <?php elseif(isset($request["contents"]) && trim($request["contents"]) !== ""): ?>
                <div style="max-height: 300px; overflow: auto; background-color: rgba(0, 0, 0, .25); padding: 5px 10px; border-radius: 10px; margin-bottom: 20px;">
                    <?= $request["contents"] ?>
                </div>
                <?php else: ?>
                <pre>[<?= l("lang_admin_requests_empty") ?>]</pre>
                <?php endif; ?>

                <a class="btn btn-outline-success" href="/admin/approve/?id=<?= $request["_id"] ?>&md5=<?= md5(json_encode($request["_original"])) ?>"><?php if ($request["type"] !== "article" && $request["type"] !== "gallerymeta" && $request["type"] !== "galleryupload" && $request["type"] !== "userpage"): ?><?= l("lang_admin_requests_mark") ?><?php else: ?><?= l("lang_admin_requests_merge") ?><?php endif; ?></a><?php if (!($request["type"] !== "article" && $request["type"] !== "gallerymeta" && $request["type"] !== "galleryupload" && $request["type"] !== "userpage")): ?> <a href="/admin/approve/?id=<?= $request["_id"] ?>&mark&md5=<?= md5(json_encode($request["_original"])) ?>" class="btn btn-outline-warning"><?= l("lang_admin_requests_mark") ?></a><?php endif; ?> <a href="/admin/reject/?id=<?= $request["_id"] ?>&md5=<?= md5(json_encode($request["_original"])) ?>" class="btn btn-outline-danger"><?= l("lang_admin_requests_reject") ?></a><br>
                <small class="text-muted">*<?= l("lang_admin_requests_notify") ?><?php if ($request["type"] !== "article" && $request["type"] !== "gallerymeta" && $request["type"] !== "galleryupload" && $request["type"] !== "userpage"): ?> <span class="text-warning">*<?= l("lang_admin_requests_manual") ?></span><?php endif; ?></small>
            </div>
        </details>
        <?php endforeach; ?>
    </div>

    <?php if (count($requests) === 0): ?>
    <div class="text-muted"><?= l("lang_admin_requests_nothing") ?></div>
    <?php endif; ?>

    <br><br>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>