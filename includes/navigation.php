<?php global $unreadAlerts; global $_USER; global $_PROFILE; global $betaEligible; ?>
<style>
    <?php if (!isset($_GET["frameless"])): ?>
    #desktop-navigation-mobile {
        display: none;
    }

    @media (max-width: 990px) {
        #mobile-navigation {
            display: block;
        }

        #desktop-navigation {
            display: none;
        }

        #footer {
            display: block;
        }

        #page {
            padding-top: 35px;
        }

        #desktop-navigation.mobile-show {
            display: grid;
            position: fixed;
            top: 0;
            bottom 0;
            left: 0;
            right: 0;
            background-color: var(--bs-body-bg);
            z-index: 9999;
            grid-template-rows: 36px 57px 1fr 60px 56px;
        }

        #desktop-navigation.mobile-show #desktop-navigation-mobile {
            display: block;
        }
    }

    @media (min-width: 990px) {
        #mobile-navigation {
            display: none;
        }

        #desktop-navigation {
            display: block;
        }

        #footer {
            display: none;
        }

        #desktop-navigation {
            position: fixed;
            border-right: 1px solid rgba(0, 0, 0, .25);
            top: 0;
            bottom: 0;
            left: 0;
            width: 360px;
            z-index: 999;
            display: grid;
            grid-template-rows: 57px 1fr 60px 0;
        }

        #page {
            margin-left: 440px;
            margin-right: 80px;
        }

        body.bg-light {
            background-color: var(--bs-body-bg) !important;
        }

        div#plus-box {
            margin-left: -80px;
            margin-right: -80px;
        }

        div#plus-box > div {
            padding-left: 80px;
            padding-right: 80px;
        }
    }
    <?php endif; ?>
</style>

<div id="desktop-navigation" style="overflow: auto; height: 100vh;">
    <div id="desktop-navigation-mobile" style="border-bottom: 1px solid rgba(0, 0, 0, .1);background-color: var(--bs-light);">
        <a onclick="hideNav();" style="display: block; cursor: pointer; padding: 5px 10px;">
            <div>
                <img src="/icons/close.svg" class="icon">
                <span style="vertical-align: middle;"><?= l("lang_navigation_hide") ?></span>
            </div>
        </a>
    </div>
    <div style="background-color: var(--bs-light); padding: 10px;">
        <div class="dropdown" style="display:inline-block;">
            <span class="user-nav nav-link no-opacity" data-bs-toggle="dropdown"><div style="margin-top: <?= $_PROFILE["plus"] ? "0" : "2px" ?>;" class="profile-border <?= $_PROFILE["ultra"] ? "profile-border-ultra" : ($_PROFILE["plus"] ? "profile-border-plus" : "") ?>"><div class="profile-border-inner <?= $_PROFILE["plus"] ? "profile-border-inner-active" : "" ?>"><img src="<?= file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $_USER . ".webp") ? "/uploads/" . $_USER . ".jpg" : "/icons/defaultuser.svg" ?>" style="width: 32px;border-radius: 999px;"></div></div></span>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="/">
                        <img class="icon" src="/icons/dashboard.svg">
                        <span style="vertical-align: middle;"><?= l("lang_navigation_user_dashboard") ?></span>
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="/profile/<?= $_USER ?>">
                        <img class="icon" src="/icons/profiles.svg">
                        <span style="vertical-align: middle;"><?= l("lang_navigation_user_profile") ?></span>
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <?php if (isset($_COOKIE["DeltaKiosk"]) && $_PROFILE["admin"]): ?>
                <li>
                    <a class="dropdown-item" href="#" onclick="kiosk.devtools();">
                        <img class="icon" src="/icons/devtools.svg">
                        <span style="vertical-align: middle;"><?= l("lang_kiosk_devtools") ?></span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" onclick="kiosk.reload();">
                        <img class="icon" src="/icons/reload.svg">
                        <span style="vertical-align: middle;"><?= l("lang_kiosk_reload") ?></span>
                    </a>
                </li>
                    <li>
                        <a class="dropdown-item" href="/_dev.equestria.delta.kiosk.DisableKiosk">
                            <img class="icon" src="/icons/disable.svg">
                            <span style="vertical-align: middle;"><?= l("lang_kiosk_disable") ?></span>
                        </a>
                    </li>
                <li>
                    <a class="dropdown-item" href="/_dev.equestria.delta.kiosk.ShutdownKiosk">
                        <img class="icon" src="/icons/shutdown.svg">
                        <span style="vertical-align: middle;"><?= l("lang_kiosk_shutdown") ?></span>
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <?php endif; ?>

                <li>
                    <a class="dropdown-item" href="/logout">
                        <img class="icon" src="/icons/logout.svg">
                        <span style="vertical-align: middle;"><?= l("lang_navigation_user_logout") ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div style="width: 359px;">
        <?php if (str_starts_with($_SERVER['REQUEST_URI'], "/people/") || $_SERVER['REQUEST_URI'] === "/people"): ?>
        <div class="container" style="margin-top: 10px;"><?php listPeople(); ?></div>
        <?php elseif (str_starts_with($_SERVER['REQUEST_URI'], "/articles/") || $_SERVER['REQUEST_URI'] === "/articles"): ?>
        <div class="container" style="margin-top: 10px;"><?php listArticles(); ?></div>
        <?php elseif (str_starts_with($_SERVER['REQUEST_URI'], "/gallery/") || $_SERVER['REQUEST_URI'] === "/gallery"): ?>
        <div class="container" style="margin-top: 10px;"><?php listAlbums(); ?></div>
        <?php elseif (str_starts_with($_SERVER['REQUEST_URI'], "/search/") || $_SERVER['REQUEST_URI'] === "/search"): ?>
            <div style="display: flex; align-items: center; justify-content: center; text-align: center; height: 100%;opacity:.5;">
                <div>
                    <img src="/icons/search.svg" style="width: 48px;">
                    <h4><?= l("lang_navigation_search") ?></h4>
                </div>
            </div>
        <?php elseif (str_starts_with($_SERVER['REQUEST_URI'], "/alerts/") || $_SERVER['REQUEST_URI'] === "/alerts"): ?>
            <div style="display: flex; align-items: center; justify-content: center; text-align: center; height: 100%;opacity:.5;">
                <div>
                    <img src="/icons/messages.svg" style="width: 48px;">
                    <h4><?= l("lang_navigation_messages") ?></h4>
                </div>
            </div>
        <?php elseif (str_starts_with($_SERVER['REQUEST_URI'], "/support/") || $_SERVER['REQUEST_URI'] === "/support"): ?>
            <div style="display: flex; align-items: center; justify-content: center; text-align: center; height: 100%;opacity:.5;">
                <div>
                    <img src="/icons/help.svg" style="width: 48px;">
                    <h4><?= l("lang_navigation_user_help") ?></h4>
                </div>
            </div>
        <?php elseif (str_starts_with($_SERVER['REQUEST_URI'], "/plus/") || $_SERVER['REQUEST_URI'] === "/plus"): ?>
            <div style="display: flex; align-items: center; justify-content: center; text-align: center; height: 100%;opacity:.5;">
                <div>
                    <img src="/icons/upgrade-mono.svg" style="width: 48px;">
                    <h4>Delta Plus</h4>
                </div>
            </div>
        <?php elseif (str_starts_with($_SERVER['REQUEST_URI'], "/request/") || $_SERVER['REQUEST_URI'] === "/request" || str_starts_with($_SERVER['REQUEST_URI'], "/edit/") || $_SERVER['REQUEST_URI'] === "/edit"): ?>
            <div style="display: flex; align-items: center; justify-content: center; text-align: center; height: 100%;opacity:.5;">
                <div>
                    <img src="/icons/edit.svg" style="width: 48px;">
                    <h4><?= l("lang_home_editor") ?></h4>
                </div>
            </div>
        <?php elseif (str_starts_with($_SERVER['REQUEST_URI'], "/admin/") || $_SERVER['REQUEST_URI'] === "/admin"): ?>
            <div style="display: flex; align-items: center; justify-content: center; text-align: center; height: 100%;opacity:.5;">
                <div>
                    <img src="/icons/admin.svg" style="width: 48px;">
                    <h4>Admin</h4>
                </div>
            </div>
        <?php elseif (str_starts_with($_SERVER['REQUEST_URI'], "/requests/") || $_SERVER['REQUEST_URI'] === "/requests"): ?>
            <div style="display: flex; align-items: center; justify-content: center; text-align: center; height: 100%;opacity:.5;">
                <div>
                    <img src="/icons/requests.svg" style="width: 48px;">
                    <h4><?= l("lang_navigation_user_requests") ?></h4>
                </div>
            </div>
        <?php elseif (str_starts_with($_SERVER['REQUEST_URI'], "/beta/") || $_SERVER['REQUEST_URI'] === "/beta"): ?>
            <div class="container" style="margin-top: 10px;">
                <!--<pre><?php var_dump($_SERVER); ?></pre>-->
                <p>
                    <b><?= l("lang_beta_version") ?></b><br>
                    <?= trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/version")) ?>
                </p>
                <p>
                    <b><?= l("lang_beta_php") ?></b><br>
                    <?= PHP_VERSION ?> (<?= PHP_VERSION_ID ?>)<br>
                    <?= l("lang_beta_build") ?> <?= PHP_OS ?><br>
                    <?= l("lang_beta_api") ?> <?= PHP_SAPI ?>
                </p>
                <p>
                    <b><?= l("lang_beta_engine") ?></b><br>
                    Zend <?= zend_version() ?><br>
                    nginx <?= explode("/", $_SERVER["SERVER_SOFTWARE"])[1] ?><br>
                    <?= str_replace("%1", round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2), l("lang_beta_latency")) ?>
                </p>
                <p>
                    <b><?= l("lang_beta_process") ?></b><br>
                    PID <?= getmypid() ?> (<?= round(memory_get_peak_usage() / 1024) ?>K)<br>
                    <?= l("lang_beta_root") ?> <?= $_SERVER['DOCUMENT_ROOT'] ?>
                </p>
                <p>
                    <b><?= l("lang_beta_os") ?></b><br>
                    <?= php_uname("s") ?> <?= php_uname("r") ?><br>
                    <?= exec("hostname -f") ?>
                </p>
                <p>
                    <b><?= l("lang_beta_network") ?></b><br>
                    <?= $_SERVER['SERVER_ADDR'] ?? "-" ?>:<?= $_SERVER['SERVER_PORT'] ?? "-" ?><br>
                    <?= $_SERVER['REMOTE_ADDR'] ?? "-" ?>:<?= $_SERVER['REMOTE_PORT'] ?? "-" ?><br>
                    <?= $_SERVER['HTTP_X_FORWARDED_FOR'] ?? "-" ?><br>
                </p>
            </div>
        <?php elseif ($_SERVER['REQUEST_URI'] === "" || $_SERVER['REQUEST_URI'] === "/"): ?>
            <div style="display: flex; align-items: center; justify-content: center; text-align: center; height: 100%;opacity:.5;">
                <div>
                    <img src="/icons/dashboard.svg" style="width: 48px;">
                    <h4><?= l("lang_navigation_user_dashboard") ?></h4>
                </div>
            </div>
        <?php else: ?>
            <div style="display: flex; align-items: center; justify-content: center; text-align: center; height: 100%;opacity:.5;">
                <div>
                    <img src="/icons/profiles.svg" style="width: 48px;">
                    <h4><?= l("lang_navigation_profile") ?></h4>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); background-color: var(--bs-light);">
        <a href="/people" class="newnav-link-bottom <?= str_starts_with($_SERVER['REQUEST_URI'], "/people/") || $_SERVER['REQUEST_URI'] === "/people" ? "newnav-link-bottom-checked" : "" ?>" title="<?= l("lang_navigation_people") ?>" data-bs-placement="top" data-bs-toggle="tooltip" style="display: flex; align-items: center; justify-content: center;">
            <img src="/icons/people.svg" style="width: 32px;">
        </a>

        <a href="/articles" class="newnav-link-bottom <?= str_starts_with($_SERVER['REQUEST_URI'], "/articles/") || $_SERVER['REQUEST_URI'] === "/articles" ? "newnav-link-bottom-checked" : "" ?>" title="<?= l("lang_navigation_articles") ?>" data-bs-placement="top" data-bs-toggle="tooltip" style="display: flex; align-items: center; justify-content: center;">
            <img src="/icons/articles.svg" style="width: 32px;">
        </a>

        <a href="/alerts" class="newnav-link-bottom <?= str_starts_with($_SERVER['REQUEST_URI'], "/alerts/") || $_SERVER['REQUEST_URI'] === "/alerts" ? "newnav-link-bottom-checked" : "" ?>" title="<?= l("lang_navigation_messages") ?>" data-bs-placement="top" data-bs-toggle="tooltip" style="display: flex; align-items: center; justify-content: center;">
            <img src="/icons/messages.svg" style="width: 32px;">
            <?php if ($unreadAlerts): ?><span style="width: 12px; height: 12px; border-radius: 999px; background-color: var(--bs-link-color); display: inline-block; position: absolute; margin-top: -20px; margin-left: 20px;"></span><?php endif; ?>
        </a>
    </div>
    <div></div>
</div>

<nav id="mobile-navigation" class="bg-light" style="height: 35px; position: fixed;left: 0; right: 0;">
    <a onclick="showNav();" style="display: block; cursor: pointer; padding: 5px 10px;">
        <div>
            <img src="/icons/menu.svg" class="icon">
            <span style="vertical-align: middle;"><?= l("lang_navigation_show") ?></span>
        </div>
    </a>
</nav>

<script>
    function showNav() {
        document.getElementById("desktop-navigation").classList.add("mobile-show");
    }

    function hideNav() {
        document.getElementById("desktop-navigation").classList.remove("mobile-show");
    }
</script>

<div id="page">
