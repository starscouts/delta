<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $_USER; global $_PROFILE;

$id = array_values(array_filter(array_keys($_GET), function ($i) {
    return str_starts_with($i, "/") && strlen($i) > 1;
}))[0] ?? null;

$gender = "other";

if (isset($id)) {
    $id = substr($id, 1);
    if (!preg_match("/[a-zA-Z0-6]/m", $id)) {
        header("Location: /profile/$_USER");
        die();
    }

    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $id . ".json")) {
        header("Location: /profile/$_USER");
        die();
    }

    $data = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $id . ".json")), true);

    $title_pre = $data["nick_name"] ?? $data["first_name"] . " " . $data["last_name"];
    $title = "lang_profile_title";
} else {
    header("Location: /profile/$_USER");
    die();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";

$requests = array_reverse($data["requests"]);

foreach (array_filter(scandir($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people"), function ($i) { return !str_starts_with($i, "."); }) as $_id) {
    $_d = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $_id), true);

    if ($_d["delta"] === $id) {
        $gender = $_d["gender"];
    }
}

?>

<div class="container">
    <br><br>
    <h1>
        <span><?= $data["nick_name"] ?? $data["first_name"] . " " . $data["last_name"] ?><?php if (isset($data["nick_name"]) && trim($data["nick_name"]) !== ""): ?> <small><small><small>(<?= $data["first_name"] . " " . $data["last_name"] ?>)</small></small></small><?php endif; ?></span>
        <div id="btn-area" style="float: right;">
            <div id="badges-desktop" style="display: inline-block; margin-right: 10px;">
                <?php badges($data) ?>
            </div>
            <div class="btn-group">
                <a style="height: 38px;" onclick="copy('<?= uuidToId($id) ?>', true)" class="btn btn-outline-dark btn-with-img" title="<?= l("lang_shortener_copy") ?>" data-bs-toggle="tooltip"><img src="/icons/copy.svg" style="width: 24px;"></a>
                <button style="height: 38px;" type="button" class="btn btn-outline-dark dropdown-toggle btn-with-img" title="<?= l("lang_profile_options") ?>" data-bs-toggle2="tooltip" data-bs-toggle="dropdown"><img src="/icons/admin.svg" style="width: 24px;"></button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item <?= $id !== $_USER ? "disabled" : "" ?>" href="/download">
                        <img src="/icons/download.svg" style="vertical-align: middle; width: 24px;">
                        <span style="vertical-align: middle;"><?= l("lang_download_action") ?></span>
                    </a></li>
                    <li><a class="dropdown-item" onclick="copy(formatId('<?= $id ?>'), false); alert('Code transaction copié');" style="cursor: pointer;">
                        <img src="/icons/copy.svg" style="vertical-align: middle; width: 24px;">
                        <span style="vertical-align: middle;">Copier le code transaction</span>
                    </a></li>
                </ul>
            </div>
        </div>
        <div id="badges-mobile" style="display: none;">
            <?php badges($data) ?>
        </div>
    </h1>

    <div id="profile-grid" style="margin-top: 20px; display: grid; grid-template-columns: repeat(<?= hasProfileSetting("hide", false) ? "1" : "2" ?>, 1fr); grid-column-gap: 20px;">
        <?php if (!hasProfileSetting("hide", false)): ?>
        <div class="card">
            <div class="card-body">
                <table>
                    <tr>
                        <td class="ellipsis"><img alt="" src="/icons/time.svg" style="vertical-align: middle; width: 29px; padding-right: 5px;"></td>
                        <td class="ellipsis"><?= $gender === "fem" || $gender === "trans_fem" ? l("lang_profile_since_female") : ($gender === "male" || $gender === "trans_male" ? l("lang_profile_since_male") : l("lang_profile_since")) ?> <?= timeAgo($data["date"], false) ?></td>
                    </tr>
                    <tr>
                        <td class="ellipsis"><img alt="" src="/icons/clock.svg" style="vertical-align: middle; width: 29px; padding-right: 5px;"></td>
                        <td class="ellipsis"><?php if (isset($data["last_seen"])): ?><?= $gender === "fem" || $gender === "trans_fem" ? l("lang_profile_last_female") : ($gender === "male" || $gender === "trans_male" ? l("lang_profile_last_male") : l("lang_profile_last")) ?> <?= timeAgo($data["last_seen"], true, false, true) ?><?php else: ?><?= l("lang_profile_never") ?><?php endif; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-body">
                <div id="profile-grid-user" style="display: grid; grid-template-columns: <?= hasProfileSetting("photo", true) ? "64px max-content" : "1fr" ?>; grid-gap: 10px;">
                    <?php if (hasProfileSetting("photo", true)): ?>
                    <div style="display: flex; align-items: center;">
                        <img src="<?= file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $id . ".webp") ? "/uploads/" . $id . ".webp" : "/icons/defaultuser.svg" ?>" style="width: 64px; height: 64px; border-radius: 100%;">
                    </div>
                    <?php endif; ?>
                    <table>
                        <tr>
                            <td class="ellipsis" style="width: 28px;"><img alt="" src="/icons/age.svg" style="vertical-align: middle; width: 29px; padding-right: 5px;"></td>
                            <td class="ellipsis"><?php

                            if (isset($data["birth"])):

                            $bdate = strtotime($data["birth"]);
                            echo(timeAgo($bdate, false, true) . " " . l("lang_profile_old"));

                            ?><?php if (hasProfileSetting("birth", true)): ?> (<?= $gender === "fem" || $gender === "trans_fem" ? l("lang_profile_birth_female") : ($gender === "male" || $gender === "trans_male" ? l("lang_profile_birth_male") : l("lang_profile_birth")) ?> <?= formatDate($data["birth"], false) ?>)<?php endif; ?><?php else: ?>-<?php endif; ?></td>
                        </tr>
                        <tr>
                            <?php

                            $email = $data["email"];

                            ?>
                            <td class="ellipsis" style="width: 28px;"><img alt="" src="/icons/email.svg" style="vertical-align: middle; width: 29px; padding-right: 5px;"></td>
                            <td class="ellipsis"><?php if (hasProfileSetting("email", true)): ?><a href="mailto:<?= $email ?>"><?= $email ?></a><?php else: ?><?= l("lang_studio_redacted") ?><?php endif; ?></td>
                        </tr>
                        <tr>
                            <td class="ellipsis" style="width: 28px;"><img alt="" src="/icons/phone.svg" style="vertical-align: middle; width: 29px; padding-right: 5px;"></td>
                            <td class="ellipsis"><?php if (hasProfileSetting("phone", true)): ?><?php if (isset($data["phone"]) && $data["phone"] !== ""): ?><a href="tel:<?= str_replace(" ", "", $data["phone"]) ?>"><?= $data["phone"] ?></a><?php else: ?>-<?php endif; ?><?php else: ?><?= l("lang_studio_redacted") ?><?php endif; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if (hasProfileSetting("detailed", false, $data)): ?>
    <div id="profile-grid-2" style="margin-top: 20px; display: grid; grid-template-columns: repeat(2, 1fr); grid-column-gap: 20px;">
        <div class="card">
            <div class="card-body">
                <div style="height: 28px; display: flex; align-items: center;"><b><?= l("lang_studio_details_most") ?></b></div>
                <?php

                if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/history/" . $id . ".json")) {
                    $history = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/history/" . $_USER . ".json"), true);
                } else {
                    $history = [];
                }

                uasort($history, function ($a, $b) { return $b - $a; });
                array_filter($history, function ($i) { return getFileFromId($i) !== null; }, ARRAY_FILTER_USE_KEY);

                ?>

                <table>
                    <?php if (isset(array_keys($history)[0])): ?>
                    <tr>
                        <td class="ellipsis" style="width: 28px;"><img alt="" src="/icons/<?= getTypeFromId(array_keys($history)[0]) ?>.svg" style="vertical-align: middle; width: 29px; padding-right: 5px;"></td>
                        <td class="ellipsis"><a href="<?= getUrlFromId(array_keys($history)[0]) ?>"><?= getNameFromId(array_keys($history)[0]) ?></a></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (isset(array_keys($history)[1])): ?>
                    <tr>
                        <td class="ellipsis" style="width: 28px;"><img alt="" src="/icons/<?= getTypeFromId(array_keys($history)[1]) ?>.svg" style="vertical-align: middle; width: 29px; padding-right: 5px;"></td>
                        <td class="ellipsis"><a href="<?= getUrlFromId(array_keys($history)[1]) ?>"><?= getNameFromId(array_keys($history)[1]) ?></a></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div style="height: 28px; display: flex; align-items: center;"><b><?= l("lang_studio_details_requests") ?></b></div>
                <?php if (count($requests) === 0): ?>
                <div style="height: 28px; display: flex; align-items: center;" class="text-muted"><?= l("lang_studio_details_none") ?></div>
                <?php else: ?>
                    <table>
                        <?php if (isset(array_keys($requests)[0])): ?>
                            <tr>
                                <td class="ellipsis" style="width: 28px;"><img alt="" src="/icons/<?= getTypeFromId(array_keys($requests)[0]) ?>.svg" style="vertical-align: middle; width: 29px; padding-right: 5px;"></td>
                                <td class="ellipsis"><a href="<?= getUrlFromId(array_keys($requests)[0]) ?>"><?= getNameFromId(array_keys($requests)[0]) ?></a> · <?= timeAgo(json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/" . array_values($requests)[0] . ".json"), true)["date"]) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if (isset(array_keys($requests)[1])): ?>
                            <tr>
                                <td class="ellipsis" style="width: 28px;"><img alt="" src="/icons/<?= getTypeFromId(array_keys($requests)[1]) ?>.svg" style="vertical-align: middle; width: 29px; padding-right: 5px;"></td>
                                <td class="ellipsis"><a href="<?= getUrlFromId(array_keys($requests)[1]) ?>"><?= getNameFromId(array_keys($requests)[1]) ?></a> · <?= timeAgo(json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/" . array_values($requests)[1] . ".json"), true)["date"]) ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif ;?>

    <div style="margin-top: 20px;">
        <?php if (isset($data["contents"]) && trim($data["contents"] !== "")): ?>
            <div>
                <?= str_replace("<script>", "&lt;script&gt;", $data["contents"]) ?>
            </div>
            <?php if (!hasProfileSetting("hide", false)): ?>
            <small class="text-muted"><?= str_replace("%1", timeAgo($data["update"]), l("lang_time_update")) ?></small>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-muted"><?= $gender === "fem" || $gender === "trans_fem" ? l("lang_profile_empty_female") : ($gender === "male" || $gender === "trans_male" ? l("lang_profile_empty_male") : l("lang_profile_empty")) ?></p>
        <?php endif; ?>
    </div>

    <br><br>
</div>

<script>
    function formatId(id) {
        let l = id.split("-").map(i => parseInt(i, 16)).join("").match(/.{1,5}/g).map(i => i + "00000".substring(0, 5 - i.length)).join("-");
        return "00000-00000-00000-00000-00000-00000-00000-00000".substring(0, 47 - l.length) + l;
    }
</script>

<?php if ($id !== $_USER) addToUserHistory($id); require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
