<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";
initLang();
global $_PROFILE; global $_USER;

?>

<?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/history/" . $_USER . ".json")):

    $history = array_filter(json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/history/" . $_USER . ".json")), true), function ($i) {
        global $_PROFILE;
        global $_USER;

        if (getFileFromId($i) === null) {
            return false;
        }

        $personData = json_decode(file_get_contents(getFileFromId($i)), true);

        if ($personData["first_name"] === $_PROFILE["first_name"] && $personData["last_name"] === $_PROFILE["last_name"]) {
            return false;
        }

        if ($i === $_USER) {
            return false;
        }

        return true;
    }, ARRAY_FILTER_USE_KEY);

    if (count(array_keys($history)) > 0):

        uasort($history, function ($a, $b) { return $b - $a; });
        $top = array_keys($history)[rand(0, count($history) >= 3 ? 2 : count($history) - 1)];
        $topName = getNameFromId($top);

        if (count(findRelated($top)) > 0):

            ?>
            <h3 style="margin-bottom: 15px; margin-top: 30px;"><?= str_replace("%1", $topName, l("lang_home_because")) ?></h3>
            <div class="list-group">
                <?php $index = 0; foreach (findRelated($top) as $entry): if ($index <= 4): ?>
                    <a href="<?= $entry["value"]["url"] ?>" class="list-group-item-action list-group-item">
                        <p style="margin-bottom:.5rem;">
                            <img class="icon" src="/icons/<?= $entry["value"]["type"] ?>.svg" style="margin-right:5px;"><b style="vertical-align: middle;"><?= $entry["value"]["name"] ?></b>
                        </p>
                        <span><?= trim($entry["value"]["extract"]) !== "" ? substr(trim($entry["value"]["extract"]), 0, 150) . (strlen(trim($entry["value"]["extract"])) > 150 ? "…" : "") : $entry["value"]["name"] ?></span>
                    </a>
                    <?php $index++; endif; endforeach; ?>
            </div>

        <?php endif; endif; endif; ?>
