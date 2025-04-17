<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";

$id = array_values(array_filter(array_keys($_GET), function ($i) {
    return str_starts_with($i, "/") && strlen($i) > 1;
}))[0] ?? null;

if (isset($id)) {
    $id = substr($id, 1);

    if (!preg_match("/[a-zA-Z0-6]/m", $id)) {
        header("Location: /people");
        die();
    }

    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people/" . $id . ".json")) {
        header("Location: /people");
        die();
    }

    $data = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people/" . $id . ".json")), true);

    $title_pre = getNameFromId($id);
    $title = "lang_people_title";
} else {
    $title = "lang_people_title";
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";

if (!isset($id)):
?>

<div class="container">
    <br><br>
    <h1><?= l("lang_people_title") ?></h1>

    <?php listPeople(); ?>

    <br><br>
</div>

<?php else: ?>

<div class="container">
    <br><br>
    <h1 id="btn-area-container" style="display: grid; grid-template-columns: 1fr max-content;">
        <span><?= getNameFromId($id) ?><?php if (isset($data["born"]) && trim($data["born"]) !== ""): ?> <small><small><small>(<?= $data["first_name"] . " " . $data["born"] ?>)</small></small></small><?php endif; ?></span>
        <span id="btn-area" class="btn-group"><a style="height: 38px;" onclick="copy('<?= uuidToId($id) ?>', true)" class="btn btn-outline-dark btn-with-img" title="<?= l("lang_shortener_copy") ?>" data-bs-toggle="tooltip"><img src="/icons/copy.svg" style="width: 24px;"></a></span>
    </h1>

    <div style="display: grid; grid-template-columns: 1fr 400px; grid-gap: 20px;" id="infobox">
        <div>
            <?php if (isset($data["contents"]) && trim($data["contents"] !== "")): ?>
                <div>
                    <?= doLinking($data["contents"]) ?>
                </div>
                <small class="print-ignore text-muted"><?= isset($data["update_user"]) ? str_replace("%2", "<a class='update-user' href='/profile/" . $data["update_user"] . "'>" . resolveUser($data["update_user"]) . "</a>", str_replace("%1", timeAgo($data["update"]), l("lang_time_update_user"))) : str_replace("%1", timeAgo($data["update"]), l("lang_time_update")) ?></small>
            <?php else: ?>
                <p class="text-muted"><?= l("lang_people_empty") ?></p>
            <?php endif; ?>
        </div>
        <div>
            <div class="card">
                <div class="card-body">
                    <h5 style="text-align: center;"><?= $data["first_name"] . " " . $data["last_name"] ?></h5>
                    <img src="<?= file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $id . ".webp") ? "/uploads/" . $id . ".webp" : "/icons/defaultpage.svg" ?>" style="width: 100%;">
                    <hr>
                    <div style="display: grid; grid-template-columns: 50% 50%; grid-column-gap: 10px; grid-row-gap: 5px;">
                        <?php if (isset($data["state"])): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_state") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["state"])) {
                                switch ($data["state"]) {
                                    case 0:
                                        if ($data["gender"] === "fem" || $data["gender"] === "trans_fem") {
                                            echo("<span style='vertical-align: middle;' class='badge rounded-pill bg-black text-white'>" . l("lang_people_advanced_dead_female") . "</span>");
                                        } elseif ($data["gender"] === "male" || $data["gender"] === "trans_male") {
                                            echo("<span style='vertical-align: middle;' class='badge rounded-pill bg-black text-white'>" . l("lang_people_advanced_dead_male") . "</span>");
                                        } else {
                                            echo("<span style='vertical-align: middle;' class='badge rounded-pill bg-black text-white'>" . l("lang_people_dead") . "</span>");
                                        }

                                        break;

                                    case 1:
                                        if ($data["gender"] === "fem" || $data["gender"] === "trans_fem") {
                                            echo("<span style='vertical-align: middle;' class='badge rounded-pill bg-warning text-white'>" . l("lang_people_advanced_notborn_female") . "</span>");
                                        } elseif ($data["gender"] === "male" || $data["gender"] === "trans_male") {
                                            echo("<span style='vertical-align: middle;' class='badge rounded-pill bg-warning text-white'>" . l("lang_people_advanced_notborn_male") . "</span>");
                                        } else {
                                            echo("<span style='vertical-align: middle;' class='badge rounded-pill bg-warning text-white'>" . l("lang_people_notborn") . "</span>");
                                        }

                                        break;

                                    case 2:
                                        if ($data["gender"] === "fem" || $data["gender"] === "trans_fem") {
                                            echo("<span style='vertical-align: middle;' class='badge rounded-pill bg-success text-white'>" . l("lang_people_advanced_alive_female") . "</span>");
                                        } elseif ($data["gender"] === "male" || $data["gender"] === "trans_male") {
                                            echo("<span style='vertical-align: middle;' class='badge rounded-pill bg-success text-white'>" . l("lang_people_advanced_alive_male") . "</span>");
                                        } else {
                                            echo("<span style='vertical-align: middle;' class='badge rounded-pill bg-success text-white'>" . l("lang_people_alive") . "</span>");
                                        }

                                        break;
                                }
                            } else {
                                echo("-");
                            } ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($data["gender"])): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_gender") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["gender"])) {
                                switch ($data["gender"]) {
                                    case "fem":
                                        echo(l("lang_people_female"));
                                        break;

                                    case "male":
                                        echo(l("lang_people_male"));
                                        break;

                                    case "other":
                                        echo(l("lang_people_emby"));
                                        break;

                                    case "trans_fem":
                                        echo(l("lang_people_transfemale"));
                                        break;

                                    case "trans_male":
                                        echo(l("lang_people_transmale"));
                                        break;

                                    default:
                                        echo("<span class='text-danger'>" . l("lang_people_invalid") . "</span>");
                                        break;
                                }
                            } else {
                                echo("-");
                            } ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($data["home"])): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_home") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["home"])): ?>
                            <?= implode("<br>", array_map(function ($i) { return trim($i); }, explode(",", $data["home"]))) ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <hr>
                    <h6 style="text-align: center;"><?= l("lang_people_relations") ?></h6>
                    <?php if (isset($data["relations"]) && count($data["relations"]) > 0): ?>
                    <div>
                        <?php if (isset($data["relations"])): ?>
                            <div class="list-group">
                            <?php foreach ($data["relations"] as $relation): if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people/" . $relation["id"] . ".json")) {
                                $parts = explode(".", $relation["id"]);

                                if (count($parts) === 2) {
                                    foreach (array_filter(scandir($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people"), function ($i) { return !str_starts_with($i, "."); }) as $_id) {
                                        $_d = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $_id), true);

                                        if (strtolower(trim($_d["first_name"])) === strtolower(trim($parts[0])) && strtolower(trim($_d["last_name"])) === strtolower(trim($parts[1]))) {
                                            $relation["id"] = substr($_id, 0, -5);
                                        }
                                    }
                                }
                            } ?>
                                <a class="list-group-item list-group-item-action" href="/people/<?= file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people/" . $relation["id"] . ".json") ? $relation["id"] : "" ?>">
                                    <?php

                                    $nid = $relation["id"];
                                    $text = "<code class='text-danger'>" . $nid . "</code>";

                                    $parts = explode(".", $relation["id"]);

                                    if (count($parts) === 2) {
                                        $text = "<span class='text-danger'>" . ucwords($parts[0]) . " " . ucwords($parts[1]) . "</span>";
                                    } else if (count($parts) === 1 && trim($parts[0]) === "-") {
                                        $text = "<span class='text-muted'>" . l("lang_people_unknown") . "</span>";
                                    }

                                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people/" . $nid . ".json")) {
                                        $d = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people/" . $nid . ".json")), true);
                                        $text = $d["first_name"] . " " . $d["last_name"];
                                    }

                                    echo($text);

                                    ?> (<?php

                                    switch ($relation["type"]) {
                                        case "parent":
                                            $t = l("lang_people_parent");
                                            break;

                                        case "grandparent":
                                            $t = l("lang_people_greatparent");
                                            break;

                                        case "child":
                                            $t = l("lang_people_child");
                                            break;

                                        case "grandchild":
                                            $t = l("lang_people_grandchild");
                                            break;

                                        case "bride":
                                            $t = l("lang_people_partner");
                                            break;

                                        case "sibling":
                                            $t = l("lang_people_sibling");
                                            break;

                                        default:
                                            $t = '<span class="text-danger">' . l("lang_people_invalid") . '</span>';
                                    }

                                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people/" . $nid . ".json")) {
                                        $relationData = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people/" . $nid . ".json")), true);

                                        if ($relation["type"] === "bride" && isset($data["marriage"]["date"]) && ($relationData["gender"] === "fem" || $relationData["gender"] === "trans_fem")) {
                                            $t = l("lang_people_advanced_partner_married-female");
                                        } elseif ($relation["type"] === "bride" && isset($data["marriage"]["date"]) && ($relationData["gender"] === "male" || $relationData["gender"] === "trans_male")) {
                                            $t = l("lang_people_advanced_partner_married-male");
                                        } elseif ($relation["type"] === "bride" && isset($data["marriage"]["date"])) {
                                            $t = l("lang_people_advanced_partner_married-neutral");
                                        } elseif ($relation["type"] === "bride" && !isset($data["marriage"]["date"]) && ($relationData["gender"] === "fem" || $relationData["gender"] === "trans_fem")) {
                                            $t = l("lang_people_advanced_partner_unmarried-female");
                                        } elseif ($relation["type"] === "bride" && !isset($data["marriage"]["date"]) && ($relationData["gender"] === "male" || $relationData["gender"] === "trans_male")) {
                                            $t = l("lang_people_advanced_partner_unmarried-male");
                                        }

                                        if ($relation["type"] === "parent" && ($relationData["gender"] === "fem" || $relationData["gender"] === "trans_fem")) {
                                            $t = l("lang_people_advanced_parent_female");
                                        } else if ($relation["type"] === "parent" && ($relationData["gender"] === "male" || $relationData["gender"] === "trans_male")) {
                                            $t = l("lang_people_advanced_parent_male");
                                        }

                                        if ($relation["type"] === "child" && ($relationData["gender"] === "fem" || $relationData["gender"] === "trans_fem")) {
                                            $t = l("lang_people_advanced_child_female");
                                        } else if ($relation["type"] === "child" && ($relationData["gender"] === "male" || $relationData["gender"] === "trans_male")) {
                                            $t = l("lang_people_advanced_child_male");
                                        }

                                        if ($relation["type"] === "sibling" && ($relationData["gender"] === "fem" || $relationData["gender"] === "trans_fem")) {
                                            $t = l("lang_people_advanced_sibling_female");
                                        } else if ($relation["type"] === "sibling" && ($relationData["gender"] === "male" || $relationData["gender"] === "trans_male")) {
                                            $t = l("lang_people_advanced_sibling_male");
                                        }

                                        if ($relation["type"] === "grandchild" && ($relationData["gender"] === "fem" || $relationData["gender"] === "trans_fem")) {
                                            $t = l("lang_people_advanced_grandchild_female");
                                        } else if ($relation["type"] === "grandchild" && ($relationData["gender"] === "male" || $relationData["gender"] === "trans_male")) {
                                            $t = l("lang_people_advanced_grandchild_male");
                                        }

                                        if ($relation["type"] === "grandparent" && ($relationData["gender"] === "fem" || $relationData["gender"] === "trans_fem")) {
                                            $t = l("lang_people_advanced_greatparent_female");
                                        } else if ($relation["type"] === "grandparent" && ($relationData["gender"] === "male" || $relationData["gender"] === "trans_male")) {
                                            $t = l("lang_people_advanced_greatparent_male");
                                        }
                                    }

                                    echo($t);

                                    ?>)
                                </a>
                            <?php endforeach; ?>
                            </div>
                        <?php else: ?>-<?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <hr>
                    <h6 style="text-align: center;"><?= l("lang_people_civil") ?></h6>
                    <div style="display: grid; grid-template-columns: 50% 50%; grid-column-gap: 10px; grid-row-gap: 5px;">
                        <?php if (isset($data['birth']["date"])): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_birth") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["birth"]["date"])): ?>
                            <?php if (strtotime($data['birth']["date"]) !== false): ?>
                                <?= formatDate($data["birth"]["date"]) ?>
                            <?php else: ?><span class="text-danger"><?= l("lang_people_invalid") ?></span><?php endif; ?>
                            <?php else: ?>-<?php endif; ?><br>
                                <?php if (isset($data["birth"]["place"])): ?>
                                    <?= $data["birth"]["place"] ?>
                                <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($data["death"]["date"])): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_death") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["death"]["date"])): ?>
                            <?php if (strtotime($data['death']["date"]) !== false): ?>
                                <?= formatDate($data["death"]["date"]) ?>
                            <?php else: ?><span class="text-danger"><?= l("lang_people_invalid") ?></span><?php endif; ?>
                            <?php else: ?>-<?php endif; ?><br>
                            <?php if (isset($data["death"]["place"])): ?>
                                <?= $data["death"]["place"] ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($data["marriage"]["date"])): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_marriage") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["marriage"]["date"])): ?>
                            <?php if (strtotime($data['marriage']["date"]) !== false): ?>
                                <?= formatDate($data["marriage"]["date"]) ?>
                            <?php else: ?><span class="text-danger"><?= l("lang_people_invalid") ?></span><?php endif; ?>
                            <?php else: ?>-<?php endif; ?><br>
                            <?php if (isset($data["marriage"]["place"])): ?>
                                <?= $data["marriage"]["place"] ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <hr>
                    <h6 style="text-align: center;"><?= l("lang_people_studies") ?></h6>
                    <div style="display: grid; grid-template-columns: 50% 50%; grid-column-gap: 10px; grid-row-gap: 5px;">
                        <?php if (isset($data["schools"]) && count($data["schools"]) > 0): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_schools") ?></b>
                        </div>
                        <div>
                            <?php $index = 0; if (isset($data["schools"])): ?>
                                <?php foreach ($data["schools"] as $school): ?>
                                    <?php foreach (explode(",", $school) as $part): ?>
                                    <?= $part ?><br>
                                    <?php endforeach; ?>
                                    <?php if ($index < count($data["schools"]) - 1): ?><hr style="margin: 0.3rem 0;"><?php endif; ?>
                                <?php $index++; endforeach; ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($data["diplomas"]) && count($data["diplomas"]) > 0): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_diplomas") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["diplomas"])): ?>
                            <?= implode("<br>", $data["diplomas"]) ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($data["education"])): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_level") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["education"])): ?>
                            <?php

                            switch ($data["education"]) {
                                case "lhs":
                                    echo(l("lang_people_lhs"));
                                    break;

                                case "uhs":
                                    echo(l("lang_people_uhs"));
                                    break;

                                case "ps":
                                    echo(l("lang_people_ps"));
                                    break;

                                case "uni":
                                    echo(l("lang_people_uni"));
                                    break;

                                case "free":
                                    echo(l("lang_people_freestudy"));
                                    break;

                                default:
                                    echo('<span class="text-danger">' . l("lang_people_invalid") . '</span>');
                                    break;
                            }

                            ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <hr>
                    <h6 style="text-align: center;"><?= l("lang_people_jobs") ?></h6>
                    <div style="display: grid; grid-template-columns: 50% 50%; grid-column-gap: 10px; grid-row-gap: 5px;">
                        <?php if (isset($data["jobs"]["past"]) && count($data["jobs"]["past"]) > 0): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_previousjobs") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["jobs"]["past"])): ?>
                            <?= implode("<br>", $data["jobs"]["past"]) ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($data["jobs"]["current"])): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_currentjob") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["jobs"]["current"])): ?>
                            <?= $data["jobs"]["current"] ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($data["jobs"]["positions"])): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_position") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["jobs"]["position"])): ?>
                            <?= $data["jobs"]["position"] ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($data["jobs"]["place"])): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_workplace") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["jobs"]["place"])): ?>
                            <?= implode("<br>", array_map(function ($i) { return trim($i); }, explode(",", $data["jobs"]["place"]))) ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($data["jobs"]["next"])): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_nextjob") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["jobs"]["next"])): ?>
                            <?= $data["jobs"]["next"] ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <hr>
                    <h6 style="text-align: center;"><?= l("lang_people_culture") ?></h6>
                    <div style="display: grid; grid-template-columns: 50% 50%; grid-column-gap: 10px; grid-row-gap: 5px;">
                        <?php if (isset($data["religion"])): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_religion") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["religion"])): ?>
                            <?= $data["religion"] ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($data["languages"]) && count($data["languages"]) > 0): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_languages") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["languages"])): ?>
                            <?= implode("<br>", array_map(function ($i) { return locale_get_display_language($i, l("lang__name")); }, $data["languages"])) ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($data["permits"]) && count($data["permits"]) > 0): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_permits") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["permits"])): ?>
                            <?= implode("<br>", $data["permits"]) ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($data["countries"]) && count($data["countries"]) > 0): ?>
                        <div style="text-align: right;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;vertical-align:middle;">
                            <b><?= l("lang_people_countries") ?></b>
                        </div>
                        <div>
                            <?php if (isset($data["countries"])): ?>
                            <?= implode("<br>", array_map(function ($i) { return locale_get_display_region("-" . strtoupper($i), l("lang__name")); }, $data["countries"])) ?>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="print-ignore">
                        <hr>
                        <div style="text-align: center;">
                            <a href="<?php if (isset($data["delta"])) echo("/profile/" . $data["delta"]) ?>" class="btn btn-primary <?php if (!isset($data["delta"])) echo("disabled") ?>"><?= l("lang_people_delta") ?></a>
                            <a href="/request/?type=metaupdate&id=<?= $id ?>" class="btn btn-outline-secondary"><?= l("lang_people_update") ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br><br>
</div>

<?php addToUserHistory($id); endif; ?>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
