<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
$title = "lang_admin_title";
$title_pre = l("lang_admin_titles_registrations");
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";

?>

<div class="container">
    <br><br>
    <a href="/admin">← <?= l("lang_admin_title") ?></a>
    <h1><?= l("lang_admin_titles_registrations") ?></h1>

    <?php

    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/registrations")) mkdir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/registrations");

    $requestsPre = array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/registrations"), function ($i) { return !str_starts_with($i, "."); });

    usort($requestsPre, function ($a, $b) {
        return strtotime(json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/registrations/$a")), true)["date"]) - strtotime(json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/registrations/$b")), true)["date"]);
    });

    $requests = [...array_map(function ($i) {
        $id = $i;
        $i = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/registrations/$i")), true);
        $i["_id"] = explode(".", $id)[0];
        $i["_original"] = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/registrations/$id")), true);
        return $i;
    }, $requestsPre)];

    ?>

    <div class="list-group">
        <?php foreach ($requests as $request): ?>
        <details class="list-group-item list-group-item-action">
            <summary><?= $request["id"] ?> (<?= l("lang_admin_requests_preview_author") ?> <?= ($request["use_name"] ?? $request["first_name"]) . " " . $request["last_name"] ?>; <?= timeAgo($request["date"]) ?>)</summary>

            <div class="list-group-item" style="margin-top: 10px;">
                <p>
                    <b><?= l("lang_admin_registrations_view_0") ?></b> <?= $request["_id"] ?><br>
                    <b><?= l("lang_admin_registrations_view_1") ?></b> <?= $request["id"] ?><br>
                    <b><?= l("lang_admin_registrations_view_2") ?></b> <?= date('r', strtotime($request["date"])) ?><br>
                    <b><?= l("lang_admin_registrations_view_3") ?></b> <?= ($request["use_name"] ?? $request["first_name"]) . " " . $request["last_name"] ?> (<?= $request["first_name"] . " " . $request["last_name"] ?>)<br>
                    <b><?= l("lang_admin_registrations_view_4") ?></b> <?= $request["email"] ?><?= isset($request["phone"]) ? ", " . $request["phone"] : "" ?><br>
                    <b><?= l("lang_admin_registrations_view_5") ?></b> <?= formatDate($request["birth_date"]) ?> (<?= trim(timeAgo($request["birth_date"], false, true) . " " . l("lang_profile_old")) ?>)<br>
                    <b><?= l("lang_admin_registrations_view_6") ?></b> <?= $request["underage_email"] ?? "" ?><br>
                    <b><?= l("lang_admin_registrations_view_7") ?></b> <?= locale_get_display_language($request["lang"], l("lang__name")) ?><br>
                </p>

                <details style="margin-bottom: 1rem;">
                    <summary><?= l("lang_admin_requests_full") ?></summary>
                    <pre style="margin-bottom: 0;"><?= str_replace(">", "&gt;", str_replace("<", "&lt;", json_encode($request, JSON_PRETTY_PRINT))) ?></pre>
                </details>

                <a href="/admin/register_approve/?id=<?= $request["_id"] ?>" class="btn btn-outline-success"><?= l("lang_admin_requests_mark") ?></a> <a onclick="reject('<?= $request["_id"] ?>');" class="btn btn-outline-danger"><?= l("lang_admin_requests_reject") ?></a><br>
                <small class="text-muted">*<?= l("lang_admin_requests_notify") ?> <span class="text-warning">*<?= l("lang_admin_requests_manual") ?>, <a style="color: inherit;" href="/admin/create/?skel=profiles&registration=<?= $request["_id"] ?>"><?= l("lang_admin_registrations_start") ?></a></span></small>
            </div>
        </details>
        <?php endforeach; ?>
    </div>

    <?php if (count($requests) === 0): ?>
    <div class="text-muted"><?= l("lang_admin_registrations_nothing") ?></div>
    <?php endif; ?>

    <br><br>

    <script>
        function reject(id) {
            let reason = prompt("<?= str_replace("%1", locale_get_display_language($request["lang"], l("lang__name")), l("lang_admin_registrations_reject")) ?>");

            if (reason !== null) {
                location.href = "/admin/register_reject/?id=" + id + "&reason=" + encodeURIComponent(reason);
            }
        }
    </script>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>