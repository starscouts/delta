<?php

die();

global $title;
global $title_pre;
global $_PROFILE;

if (isset($_PROFILE)) {
    $unreadAlerts = count(array_values(array_filter($_PROFILE["alerts"], function ($i) { return !$i["read"]; })));
} else {
    $unreadAlerts = 0;
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";
initLang();

$palettes = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/themes.json"), true);

if (isset($_PROFILE)) {
    $_PROFILE["studio_settings"] = [
        "theme" => $palettes["default"],
        "dark" => false,
        "navigation" => [
            "top" => [],
            "bottom" => [],
            "home" => []
        ],
        "profile" => [
            "badge" => true,
            "theme" => -1,
            "detailed" => false,
            "photo" => true,
            "hide" => false,
            "phone" => true,
            "email" => true,
            "birth" => true
        ]
    ];

    saveProfile();
}

$betaEligible = false;

if (isset($_PROFILE)) {
    $betaEligible = isset($_PROFILE["plus"]) && $_PROFILE["plus"];
    //$betaEligible = true || (isset($_PROFILE["plus"]) && $_PROFILE["plus"]);
}

$userPalette = $palettes["list"][$palettes["default"]]["light"];

if (isset($_GET["__"])) {
    $_id = str_replace("/", "-", substr($_GET["__"], 1));
}

if (isset($_PROFILE) && $_PROFILE["ultra"]) {
    $_PROFILE["ultra"] = false;
    $_PROFILE["hadUltra"] = true;
    saveProfile();
}

if (isset($_PROFILE) && $_PROFILE["plus"]) {
    $_PROFILE["plus"] = false;
    $_PROFILE["hadPlus"] = true;
    saveProfile();
}

?>
<!doctype html>
<html lang="en">
<head>
    <link rel="preload" as="font" href="/title.ttf">
    <link rel="preload" as="font" href="/font-italic.ttf">
    <link rel="preload" as="font" href="/font-regular.ttf">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $unreadAlerts > 0 ? "(" . $unreadAlerts . ") " : "" ?><?= isset($title_pre) ? $title_pre . " · " : "" ?><?= l($title) ?> · Delta<?php if ($_SERVER["SERVER_PORT"] === "81"): ?> (<?= trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/version")) ?>)<?php endif; ?></title>
    <link rel="shortcut icon" href="/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/bootstrap/bootstrap.min.css">
    <script src="/bootstrap/bootstrap.bundle.min.js"></script>
    <style>
        .icon {
            width: 24px;
            height: 24px;
        }

        #desktop-navigation.mobile-show > div:nth-child(3) {
            width: 100% !important;
            overflow: auto;
        }

        .btn-with-img:hover img, .btn-with-img.dropdown-toggle.show img {
            filter: brightness(0%);
        }

        <?php if (!isset($_GET["frameless"])): ?>
        @media print {
        <?php endif; ?>
            #desktop-navigation, #mobile-navigation, #footer {
                display: none !important;
            }

            body.bg-light {
                background-color: var(--bs-body-bg) !important;
            }
        <?php if (!isset($_GET["frameless"])): ?>
        }
        <?php endif; ?>

        @media print {
            .print-ignore {
                display: none;
            }

            p, a, h1, h2, h3, h4, h5, h6, .badge, td, tr, div {
                color: black !important;
            }

            .badge {
                padding: 0 !important;
                margin: 0 !important;
                font-weight: normal;
                font-size: inherit;
                background-color: transparent !important;
                margin-top: -4px !important;
            }

            a {
                text-decoration: none;
            }
        }

        .nav-link {
            color: black !important;
            opacity: .5;
            transition: opacity 200ms;
        }

        figcaption {
            font-size: 80%;
            padding: 10px;
        }

        .nav-link:hover {
            opacity: .6;
        }

        .update-user {
            color: inherit !important;
        }

        figure.image img {
            max-width: 100%;
        }

        .update-user:hover {
            opacity: .75;
        }

        .image-resized {
            margin-left: auto;
            margin-right: auto;
        }

        figure.image.image-style-side {
            float: right;
            margin-left: 10px;
        }

        .list-group-item-ellipsis {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }

        .coins {
            position: absolute;
            margin-top: -80px;
            margin-left: 15px;
            background: rgba(0, 0, 0, .5);
            color: white;
            border-radius: 5px;
            padding: 5px 10px;
        }
        
        .coins-inline {
            position: relative;
            font-size: 1rem;
            display: inline-block;
            top: 10px;
            margin-left: 0;
            float: right;
            margin-top: 0;
        }

        .nav-link.no-opacity {
            opacity: 1 !important;
        }

        .nav-link .nav-link-opacity {
            opacity: .5;
            transition: opacity 200ms;
        }

        .nav-link:hover .nav-link-opacity {
            opacity: .6;
        }

        .badge-plus {
            color: #C711E1;
            border: 2px solid #C711E1;
        }

        .badge-ultra {
            color: #e18e11;
            border: 2px solid #e18e11;
        }

        .promo-item {
            border: none;
        }

        .promo-wrapper {
            background: transparent;
            height: max-content;
            border-radius: 0.375rem;
            padding: 2px;
        }

        .promo-plus {
            background-image: linear-gradient(45deg, #C711E1 0%, #7F52FF 100%);
        }

        .promo-none {
            background-image: linear-gradient(45deg, rgba(224, 224, 224, 0.75) 0%, #bfbfbf 100%);
        }

        .promo-ultra {
            background-image: linear-gradient(45deg, rgba(214, 224, 18, 0.75) 0%, #bf763d 100%);
        }

        .modal {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        <?php if (str_starts_with($_SERVER["REQUEST_URI"], "/articles/") || str_starts_with($_SERVER["REQUEST_URI"], "/people/")): ?>
        @media print {
            #footer {
                display: none;
            }

            .navbar {
                display: none;
            }

            .btn {
                display: none;
            }

            #infobox {
                grid-template-columns: 1fr 300px !important;
                font-size: 12px;
            }

            #infobox h6 {
                font-size: 14px;
            }
        }
        <?php else: ?>
        @media print {
            * {
                display: none !important;
            }
        }
        <?php endif; ?>

        .profile-border {
            width: 32px;
            height: 32px;
        }

        .profile-border-plus {
            background-image: linear-gradient(45deg, #E44857 0%, #C711E1 50%, #7F52FF 100%);
            border-radius: 999px;
            height: 38px;
            width: 38px;
            margin-top: -4px;
            vertical-align: middle;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .profile-border-ultra {
            background-image: linear-gradient(45deg, #49e353 0%, #d6e012 50%, #ffba52 100%);
            border-radius: 999px;
            height: 38px;
            width: 38px;
            margin-top: -4px;
            vertical-align: middle;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .profile-border-plus img, .profile-border-ultra img {
            width: 28px !important;
            height: 28px;
            margin: 2px;
        }

        .profile-border-inner-active {
            background-color: #f8f9fa;
            height: 32px;
            width: 32px;
            border-radius: 999px;
            vertical-align: middle;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        @media (min-width: 992px) {
            .navbar {
                height: 64px;
            }

            .navbar-nav {
                height: 48px;
            }
        }

        @media (max-width: 1250px) {
            #badges-desktop {
                display: none !important;
            }

            #badges-mobile {
                display: block !important;
            }
        }

        @media (max-width: 992px) {
            #infobox {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width: 986px) {
            #badges-desktop {
                display: inline-block !important;
            }

            #badges-mobile {
                display: none !important;
            }
        }

        .spinner {
            animation: rotator 1.1s linear infinite;
        }

        @keyframes rotator {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(270deg);
            }
        }

        .path {
            stroke-dasharray: 187;
            stroke-dashoffset: 0;
            transform-origin: center;
            stroke: var(--bs-link-color);
            animation: dash 1.1s ease-in-out infinite;
        }

        @keyframes dash {
            0% {
                stroke-dashoffset: 187;
            }
            50% {
                stroke-dashoffset: 46.75;
                transform: rotate(135deg);
            }
            100% {
                stroke-dashoffset: 187;
                transform: rotate(450deg);
            }
        }

        @media (max-width: 767px) {
            #profile-grid, #profile-grid-2 {
                grid-template-columns: 1fr !important;
                grid-row-gap: 20px;
            }

            #badges-desktop {
                display: none !important;
            }

            #badges-mobile {
                display: block !important;
            }

            #btn-area {
                display: flex !important;
                margin-top: 10px;
            }

            #btn-area-container {
                grid-template-columns: 1fr !important;
            }
        }

        .ellipsis {
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden !important;
            text-overflow: ellipsis;
        }

        .ck-balloon-rotator, .ck-balloon-rotator__navigation, .ck-balloon-rotator__content {
            z-index: 9999 !important;
        }

        .ck-body-wrapper {
            top: 0;
            position: fixed;
        }

        @media (max-width: 387px) {
            #profile-grid-user {
                grid-template-columns: 1fr !important;
                grid-row-gap: 10px;
            }

            #profile-grid-user > div {
                justify-content: center;
            }
        }

        .user-nav {
            cursor: pointer;
        }

        .user-nav:hover {
            opacity: .75 !important;
        }

        .user-nav:active, .user-nav[aria-expanded="true"] {
            opacity: .5 !important;
        }

        @media (max-width: 991px) {
            #plus-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        @media (max-width: 767px) {
            #plus-grid {
                grid-template-columns: 1fr !important;
            }

            .plus-compare-placeholder {
                display: none;
            }
        }

        @font-face {
            src: url("/title.ttf");
            font-family: "Josefin Sans";
            font-display: swap;
        }

        @font-face {
            src: url("/font-regular.ttf");
            font-family: "Nunito";
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            src: url("/font-italic.ttf");
            font-family: "Nunito";
            font-style: italic;
            font-display: swap;
        }

        .newnav-link:hover {
            opacity: .75;
        }

        .newnav-link:active, .newnav-link:focus {
            opacity: .5;
        }

        .newnav-link-bottom {
            opacity: .5;
        }

        .newnav-link-bottom:hover {
            opacity: .75;
        }

        .newnav-link-bottom:active, .newnav-link-bottom:focus {
            opacity: 1;
        }

        .newnav-link-bottom-checked {
            opacity: 1 !important;
        }

        .cool-list-group-item[open] > summary {
            font-weight: bold !important;
        }

        * {
            font-family: "Nunito",system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue","Noto Sans","Liberation Sans",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
        }

        <?php if (isset($userPalette)):

        $palette = explode(",", $userPalette);

        function rgb($color) {
            return hexdec(substr($color, 0, 2)) . "," . hexdec(substr($color, 2, 2)) . "," . hexdec(substr($color, 4, 2));
        }

        if (count($palette) > 0):

        ?>
        :root {
            --bs-body-color:            #<?= $palette[6] ?>         !important;
            --bs-body-color-rgb:        <?= rgb($palette[6]) ?>     !important;
            --bs-body-bg:               #<?= $palette[0] ?>         !important;
            --bs-body-bg-rgb:           <?= rgb($palette[0]) ?>     !important;
            --bs-white:                 #<?= $palette[0] ?>         !important;
            --bs-white-rgb:             <?= rgb($palette[0]) ?>     !important;

            --bs-secondary-color:       #<?= $palette[6] ?>         !important;
            --bs-secondary-color-rgb:   <?= rgb($palette[6]) ?>     !important;
            --bs-secondary-bg:          #<?= $palette[1] ?>         !important;
            --bs-secondary:             #<?= $palette[6] ?>         !important;
            --bs-secondary-bg-rgb:      <?= rgb($palette[1]) ?>     !important;

            --bs-tertiary-color:        #<?= $palette[6] ?>         !important;
            --bs-tertiary-color-rgb:    <?= rgb($palette[6]) ?>     !important;
            --bs-tertiary-bg:           #<?= $palette[2] ?>         !important;
            --bs-tertiary-bg-rgb:       <?= rgb($palette[2]) ?>     !important;

            --bs-light:                 #<?= $palette[2] ?>         !important;
            --bs-light-rgb:             <?= rgb($palette[2]) ?>     !important;

            --bs-link-color:            #<?= $palette[9] ?>         !important;
            --bs-link-hover-color:      #<?= $palette[9] ?>77       !important;
            --bs-link-active-color:     #<?= $palette[9] ?>77       !important;

            --palette-0:                #<?= $palette[0] ?>         !important;
            --palette-1:                #<?= $palette[1] ?>         !important;
            --palette-2:                #<?= $palette[2] ?>         !important;
            --palette-3:                #<?= $palette[3] ?>         !important;
            --palette-4:                #<?= $palette[4] ?>         !important;
            --palette-5:                #<?= $palette[5] ?>         !important;
            --palette-6:                #<?= $palette[6] ?>         !important;
            --palette-7:                #<?= $palette[7] ?>         !important;
            --palette-8:                #<?= $palette[8] ?>         !important;
            --palette-9:                #<?= $palette[9] ?>         !important;
        }

        .dropdown-menu {
            --bs-dropdown-bg: #<?= $palette[1] ?> !important;
            --bs-dropdown-link-color: #<?= $palette[6] ?> !important;
            --bs-dropdown-link-disabled-color: #<?= $palette[6] ?>77 !important;
            --bs-dropdown-link-hover-color: #<?= $palette[6] ?> !important;
            --bs-dropdown-link-hover-bg: #<?= $palette[3] ?> !important;
            --bs-dropdown-link-active-bg: #<?= $palette[6] ?> !important;
            --bs-dropdown-link-active-color: #<?= $palette[6] ?> !important;
        }

        .coins {
            color: #<?= $palette[6] ?> !important;
            background-color: #<?= $palette[1] ?> !important;
        }

        .list-group-item-primary {
            color: var(--bs-body-bg);
            background-color: #<?= $palette[6] ?>;
        }

        .list-group-item-action.list-group-item-primary:hover, .list-group-item-action.list-group-item-primary:active, .list-group-item-action.list-group-item-primary:focus {
            background-color: #<?= $palette[6] ?>dd;
            color: var(--bs-body-bg);
        }

        .profile-border-inner-active {
            background-color: var(--bs-tertiary-bg) !important;
        }

        .bg-secondary {
            background-color: #<?= $palette[9] ?> !important;
            color: #<?= $palette[0] ?> !important;
        }

        .list-group-item-primary small {
            color: #<?= $palette[0] ?>77 !important;
        }

        .dropdown-item:active span, .dropdown-item:active img {
            filter: invert(1);
        }

        .list-group, .list-group-item, .list-group-item-action {
            --bs-list-group-color: var(--bs-body-color) !important;
            --bs-list-group-action-color: var(--bs-body-color) !important;
            --bs-list-group-action-hover-color: var(--bs-body-color) !important;
            --bs-list-group-action-active-color: var(--bs-body-color) !important;
            --bs-list-group-border-color: #<?= $palette[5] ?> !important;
            --bs-list-group-bg: #<?= $palette[2] ?> !important;
            --bs-list-group-action-hover-bg: #<?= $palette[1] ?> !important;
            --bs-list-group-action-active-bg: #<?= $palette[1] ?> !important;
        }

        .btn-outline-dark, .btn-outline-secondary {
            --bs-btn-color: var(--bs-body-color);
            --bs-btn-border-color: var(--bs-body-color);
            --bs-btn-hover-color: var(--bs-body-bg);
            --bs-btn-hover-bg: var(--bs-body-color);
            --bs-btn-hover-border-color: var(--bs-body-color);
            --bs-btn-focus-shadow-rgb: <?= rgb($palette[6]) ?>;
            --bs-btn-active-color: var(--bs-body-bg);
            --bs-btn-active-bg: var(--bs-body-color);
            --bs-btn-active-border-color: var(--bs-body-color);
            --bs-btn-active-shadow: inset 0 3px 5px rgba(<?= rgb($palette[6]) ?>, 0.125);
            --bs-btn-disabled-color: #<?= $palette[6] ?>77;
            --bs-btn-disabled-bg: transparent;
            --bs-btn-disabled-border-color: #<?= $palette[6] ?>77;
            --bs-gradient: none;
        }

        .alert-primary {
            --bs-alert-color: var(--bs-body-bg);
            --bs-alert-bg: var(--bs-body-color);
            --bs-alert-border-color: var(--bs-body-color);
        }

        .alert-danger, .alert-success, .alert-warning, .list-group-item-warning {
            --bs-alert-color: var(--bs-body-bg) !important;
            --bs-alert-bg: #<?= $palette[9] ?> !important;
            --bs-alert-border-color: #<?= $palette[9] ?> !important;
            --bs-list-group-border-color: #<?= $palette[8] ?> !important;
        }

        .list-group-item-warning, .list-group-item-success, .list-group-item-danger, .list-group-item-warning:hover, .list-group-item-success:hover, .list-group-item-danger:hover, .list-group-item-warning:active, .list-group-item-success:active, .list-group-item-danger:active {
            color: var(--bs-body-color) !important;
            background-color: #<?= $palette[8] ?> !important;
        }

        .list-group-item-warning:hover, .list-group-item-success:hover, .list-group-item-danger:hover {
            opacity: .75;
        }

        .list-group-item-warning:active, .list-group-item-success:active, .list-group-item-danger:active {
            opacity: .5;
        }

        .list-group-item[open] {
            opacity: 1 !important;
        }

        .alert-secondary {
            --bs-alert-color: var(--bs-secondary-color);
            --bs-alert-bg: var(--bs-secondary-bg);
            --bs-alert-border-color: var(--bs-secondary-bg);
        }

        .badge.bg-success, .badge.bg-danger, .badge.bg-warning {
            background-color: var(--bs-body-color) !important;
            color: var(--bs-body-bg) !important;
        }

        .badge.bg-secondary, .badge.bg-black {
            background-color: var(--bs-body-bg) !important;
            color: var(--bs-body-color) !important;
        }

        .text-muted {
            color: #<?= $palette[6] ?>77 !important;
        }

        body.bg-light {
            background-color: var(--bs-body-bg) !important;
        }

        .btn-primary {
            --bs-btn-color: #<?= $palette[8] ?>;
            --bs-btn-bg: #<?= $palette[9] ?>;
            --bs-btn-border-color: #<?= $palette[9] ?>;
            --bs-btn-hover-color: #<?= $palette[8] ?>;
            --bs-btn-hover-bg: #<?= $palette[9] ?>;
            --bs-btn-hover-border-color: #<?= $palette[9] ?>;
            --bs-btn-focus-shadow-rgb: <?= rgb($palette[9]) ?>;
            --bs-btn-active-color: #<?= $palette[8] ?>;
            --bs-btn-active-bg: #<?= $palette[9] ?>;
            --bs-btn-active-border-color: #<?= $palette[9] ?>;
            --bs-btn-active-shadow: inset 0 3px 5px rgba(<?= rgb($palette[9]) ?>, 0.25);
            --bs-btn-disabled-color: #<?= $palette[8] ?>;
            --bs-btn-disabled-bg: #<?= $palette[9] ?>77;
            --bs-btn-disabled-border-color: #<?= $palette[9] ?>77;
        }

        .btn-outline-primary {
            --bs-btn-color: #<?= $palette[9] ?>;
            --bs-btn-border-color: #<?= $palette[9] ?>;
            --bs-btn-hover-color: var(--bs-body-bg);
            --bs-btn-hover-bg: #<?= $palette[9] ?>;
            --bs-btn-hover-border-color: #<?= $palette[9] ?>;
            --bs-btn-focus-shadow-rgb: <?= rgb($palette[6]) ?>;
            --bs-btn-active-color: var(--bs-body-bg);
            --bs-btn-active-bg: #<?= $palette[9] ?>;
            --bs-btn-active-border-color: #<?= $palette[9] ?>;
            --bs-btn-active-shadow: inset 0 3px 5px rgba(<?= rgb($palette[6]) ?>, 0.125);
            --bs-btn-disabled-color: #<?= $palette[6] ?>77;
            --bs-btn-disabled-bg: transparent;
            --bs-btn-disabled-border-color: #<?= $palette[6] ?>77;
            --bs-gradient: none;
        }

        .form-control, .form-control:focus, .form-select, .form-select:focus {
            color: var(--bs-body-color);
            background-color: #<?= $palette[2] ?>;
            border-color: #<?= $palette[3] ?>;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(<?= rgb($palette[9]) ?>, .25);
        }

        .form-control::placeholder, .form-select::placeholder {
            color: #<?= $palette[6] ?>77;
        }

        .modal {
            --bs-modal-bg: #<?= $palette[1] ?> !important;
            --bs-modal-header-border-color: #<?= $palette[3] ?> !important;
        }

        .card {
            --bs-card-bg: #<?= $palette[1] ?> !important;
        }

        .btn-close {
            background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23<?= $palette[6] ?>'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat
        }

        .ck-editor {
            color: var(--bs-black);
        }

        .alert-primary a, .alert-danger a, .alert-success a, .alert-warning a {
            color: var(--bs-body-bg);
        }

        #plus-box, #plus-box > div > div > div > span, #plus-box h2 {
            color: var(--bs-body-color);
        }

        #plus-box {
            background: #<?= $palette[2] ?> !important;
        }

        #plus-box > div {
            background-image: none !important;
        }
        <?php endif; endif; ?>
    </style>
</head>
<body class="bg-light">
<?php if ($_SERVER["SERVER_PORT"] === "81"): ?>

<div style="position: fixed; z-index: 999999; bottom: 0; right: 0;" class="dse-debug">
    <div style="background-color: black; width: max-content; color: white; font-family: monospace; margin-left: auto;">Delta Staging Environment</div>
    <div style="background-color: black; width: max-content; color: white; font-family: monospace; margin-left: auto;">version <?= trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/version")) ?></div>
</div>

<div style="position: fixed; z-index: 999999; top: 0; right: 0;" class="dse-debug">
    <div style="background-color: black; width: max-content; color: white; font-family: monospace; margin-left: auto;">Node: <?= getmyinode() ?></div>
    <div style="background-color: black; width: max-content; color: white; font-family: monospace; margin-left: auto;">PID: <?= getmypid() ?></div>
</div>

<style>
    .dse-debug:hover {
        opacity: 0;
        user-select: none;
    }
</style>

<?php endif; ?>
<div class="bg-white">
