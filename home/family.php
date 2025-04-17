<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";
initLang();
global $_PROFILE; global $_USER;

?>

<?php $results = array_values(array_filter(findRelated($_USER), function ($i) use ($_PROFILE) { return str_ends_with($i["value"]["name"], " " . $_PROFILE["last_name"]); })); if (count($results)): ?>
    <h3 style="margin-bottom: 15px; margin-top: 30px;"><?= l("lang_home_family") ?></h3>
    <div class="list-group">
        <?php $index = 0; foreach ($results as $entry): if ($index <= 4): ?>
            <a href="<?= $entry["value"]["url"] ?>" class="list-group-item-action list-group-item">
                <p style="margin-bottom:.5rem;">
                    <img class="icon" src="/icons/<?= $entry["value"]["type"] ?>.svg" style="margin-right:5px;"><b style="vertical-align: middle;"><?= $entry["value"]["name"] ?></b>
                </p>
                <span><?= trim($entry["value"]["extract"]) !== "" ? substr(trim($entry["value"]["extract"]), 0, 150) . (strlen(trim($entry["value"]["extract"])) > 150 ? "…" : "") : $entry["value"]["name"] ?></span>
            </a>
            <?php $index++; endif; endforeach; ?>
    </div>
<?php endif; ?>