<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";

$id = array_values(array_filter(array_keys($_GET), function ($i) {
    return str_starts_with($i, "/") && strlen($i) > 1;
}))[0] ?? null;

if (isset($id)) {
    $id = substr($id, 1);
    if (!preg_match("/[a-zA-Z0-6]/m", $id)) {
        header("Location: /articles");
        die();
    }

    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/articles/" . $id . ".json")) {
        header("Location: /articles");
        die();
    }

    $data = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/articles/" . $id . ".json")), true);

    $title_pre = getNameFromId($id);
    $title = "lang_articles_title";
} else {
    $title = "lang_articles_title";
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";

if (!isset($id)):
?>

<div class="container">
    <br><br>
    <h1><?= l("lang_articles_title") ?></h1>

    <?php listArticles(); ?>

    <br><br>
</div>

<?php else: ?>

<div class="container">
    <br><br>
    <h1 id="btn-area-container" style="display: grid; grid-template-columns: 1fr max-content;">
        <span><?= getNameFromId($id) ?></span>
        <span id="btn-area" class="btn-group"><a style="height: 38px;" onclick="copy('<?= uuidToId($id) ?>', true)" class="btn btn-outline-dark btn-with-img" title="<?= l("lang_shortener_copy") ?>" data-bs-toggle="tooltip"><img src="/icons/copy.svg" style="width: 24px;"></a></span>
    </h1>

    <div>
        <div>
            <?php if (isset($data["contents"]) && trim($data["contents"] !== "")): ?>
                <div>
                    <?= doLinking($data["contents"]) ?>
                </div>
                <small class="print-ignore text-muted"><?= isset($data["update_user"]) ? str_replace("%2", "<a class='update-user' href='/profile/" . $data["update_user"] . "'>" . resolveUser($data["update_user"]) . "</a>", str_replace("%1", timeAgo($data["update"]), l("lang_time_update_user"))) : str_replace("%1", timeAgo($data["update"]), l("lang_time_update")) ?></small>
            <?php else: ?>
                <p class="text-muted"><?= l("lang_articles_empty") ?></p>
            <?php endif; ?>
        </div>
    </div>

    <br><br>
</div>

<?php addToUserHistory($id); endif; ?>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
