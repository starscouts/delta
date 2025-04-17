<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";
initLang();
global $_PROFILE; global $_USER;

$birthdays = array_values(array_filter(array_map(function ($i) {
    $r = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/$i")), true);
    $r["_id"] = substr($i, 0, -5);
    return $r;
}, array_values(array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles"), function ($i) { return str_ends_with($i, ".json"); }))), function ($i) {
    return hasProfileSetting("birth", true, $i) && substr($i["birth"], 5) === date('m-d');
}));

if (count($birthdays) > 0): ?>
    <div class="alert alert-primary">
        <img class="icon" src="/icons/age-home.svg"><span style="vertical-align: middle; margin-left: 5px;"><?= str_replace("%1", enumerate(array_map(function ($i) {
                return str_replace("%1", "<a href='/profile/" . $i["_id"] . "'>" . ($i["nick_name"] ?? $i["first_name"]) . "</a>", l("lang_home_name"));
            }, $birthdays), l("lang_home_and")), l("lang_home_birthday")) ?></span>
    </div>
<?php else: ?>
    <div class="alert alert-secondary">
        <img class="icon" src="/icons/events.svg"><span style="vertical-align: middle; margin-left: 5px;"><?= str_replace("%1", formatDate(time()), l("lang_home_date")) ?></span>
    </div>
<?php endif; ?>