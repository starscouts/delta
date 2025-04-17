<?php

$title = "lang_alerts_title";

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";

global $unreadAlerts;

?>

<div class="container">
    <br><br>
    <h1><?= l("lang_alerts_title") ?></h1>
    <span class="badge rounded-pill bg-secondary"><?= str_replace("%1", $unreadAlerts, l($unreadAlerts > 1 ? "lang_alerts_unread2" : "lang_alerts_unread1")) ?></span>

    <?php

    global $_PROFILE;
    $alerts = $_PROFILE["alerts"];

    foreach ($alerts as $index => $alert) {
        $alerts[$index]["_id"] = $index;
    }

    usort($alerts, function ($a, $b) {
        return strtotime($b["date"]) - strtotime($a["date"]);
    });

    ?>

    <div class="list-group" style="margin-top: 1rem;">
        <?php $index = 0; foreach ($alerts as $alert): ?>
        <a href="/alerts/read/?id=<?= $alert["_id"] ?>" class="list-group-item list-group-item-action <?= $alert["read"] ? "" : "list-group-item-primary" ?>">
            <p style="margin-bottom:0.3rem;"><b><?= l($alert["title"]) ?></b></p>
            <p style="margin-bottom:0.3rem;"><?= l($alert["message"]) ?></p>
            <small class="text-muted"><?= timeAgo($alert["date"]) ?></small>
        </a>
        <?php $index++; endforeach; ?>
    </div>

    <br><br>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>