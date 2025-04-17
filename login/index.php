<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/recaptcha/src/autoload.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/email.php";

$error = "";
$step = 0;

if (isset($_GET["failed_oauth2_notfound"])) {
    $error = "lang_login_oauth2_notfound";
}

function encode($string) {
    return preg_replace("/[^a-zA-Z0-9.]/m", "", base64_encode($string));
}

$users = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/users.json")), true);
$app = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/email.json")), true);

if (isset($_POST["p"]) && $_POST["p"] === "1") {
    if (!in_array($_POST["email"], array_keys($users))) {
        $error = "lang_login_notfound";
    }

    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $users[$_POST["email"]] . ".json")) {
        $error = "lang_login_notfound";
    } else if (!$error) {
        $step = 1;
    }

    $code = substr(hexdec(bin2hex(openssl_random_pseudo_bytes(6))), 0, 10);

    if (!$error) {
        $list = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/codes.json")), true);
        $list[$code] = [
            "date" => date('c'),
            "email" => $_POST["email"],
            "user" => $users[$_POST["email"]]
        ];

        sendCode($_POST["email"], $code);

        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/codes.json", pf_utf8_encode(json_encode($list)));
    }
} elseif (isset($_POST["p"]) && $_POST["p"] === "2") {
    $list = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/codes.json")), true);

    if (in_array($_POST['code'], array_keys($list)) && $list[$_POST['code']]["email"] === $_POST["email"]) {
        if (time() - strtotime($list[$_POST['code']]["date"]) > 900) {
            $error = "lang_login_invalid";
            $_GET["method"] = "email";
            $step = 1;
        } else {
            if ($_SERVER["SERVER_PORT"] === "81") {
                $cont = false;
                if (json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $users[$_POST["email"]] . ".json"), true)["admin"]) {
                    $cont = true;
                }

                if (!$cont) {
                    header("Location: https://delta.equestria.dev");
                    die();
                }
            }

            sendLogin($_POST["email"]);

            $token = encode(openssl_random_pseudo_bytes(128));
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . $token, pf_utf8_encode(json_encode([
                "user" => $users[$_POST["email"]],
                "date" => date('c')
            ])));
            $_USER = $users[$_POST["email"]];
            setcookie("DeltaSession", $token, time() + (86400 * 90), "/", "", false, true);
            unset($list[$_POST['code']]);
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/codes.json", pf_utf8_encode(json_encode($list)));
            $step = 2;
        }
    } else {
        $error = "lang_login_invalid";
        $_GET["method"] = "email";
        $step = 1;
    }
}

if (!isset($_GET["return"])) {
    $_GET["return"] = "/";
}

if (!isset($_GET["method"])) {
    $_GET["method"] = "email";
}

$title = "lang_login_title"; require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";

?>

<div class="container"> <?php global $palette; ?>
    <div style="background-color: #<?= $palette[0] ?>; position: fixed; inset: 0; display: flex; align-items: center; justify-content: center;">
        <div style="background-color: #<?= $palette[2] ?>; padding: 20px; border-radius: 20px; text-align: center; width: 70vw; max-height: 80vh; overflow: auto; max-width: 500px;">
    <?php if ($step < 2): ?>
        <?php if ((!isset($_GET["method"]) || $_GET["method"] === "email") && $step === 0): ?>
        <img src="/logo.svg" style="width: 48px; margin-bottom: 10px;">
        <p><b><?= l("lang_login_oobe") ?></b></p>
        <p><?= l("lang_login_oobe2") ?></p>

        <?php if (trim($error) !== ""): ?>
            <div class="alert alert-danger"><?= str_replace("%1", strip_tags($_GET["v"] ?? "-"), l($error)) ?></div>
        <?php endif; ?>

            <form method="post" id="form">
                <input type="hidden" name="p" value="1">

                <p>
                    <label>
                        <input style="text-align: left;" autocomplete="off" spellcheck="off" required name="email" type="email" placeholder="<?= l("lang_login_email") ?>" class="form-control" autofocus>
                    </label>
                </p>

                <button class="btn btn-primary g-recaptcha" data-sitekey="<?= trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/recaptcha-site")) ?>" data-callback='onSubmit' data-action='submit'><?= l("lang_login_continue") ?></button>
            </form>
        <?php elseif ($step === 1): ?>
            <img src="/icons/code.svg?o" style="width: 48px; margin-bottom: 10px;">
            <p><b><?= l("lang_login_code3") ?></b></p>
            <p><?= str_replace('%1', '<b>' . strip_tags($_POST["email"]) . '</b>', l("lang_login_code")) ?></p>

            <?php if (trim($error) !== ""): ?>
                <div class="alert alert-danger"><?= l($error) ?></div>
            <?php endif; ?>

            <form method="post" id="form">
                <input type="hidden" name="p" value="2">
                <input name="email" type="hidden" value="<?= $_POST["email"] ?>">

                <p>
                    <label>
                        <input style="text-align: left;" autocomplete="off" spellcheck="off" required name="code" type="number" maxlength="10" minlength="10" placeholder="<?= l("lang_login_code2") ?>" class="form-control" autofocus>
                    </label>
                </p>

                <button class="btn btn-primary g-recaptcha" data-sitekey="<?= trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/recaptcha-site")) ?>" data-callback='onSubmit' data-action='submit'><?= l("lang_login_continue") ?></button>
            </form>
        <?php endif; ?>
<?php else: global $_USER; $user = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $_USER . ".json")), true); ?>
    <img src="/icons/finish.svg?o" style="width: 48px; margin-bottom: 10px;">
    <p><b><?= str_replace('%1', $user["nick_name"] ?? $user["first_name"] . " " . $user["last_name"], l("lang_login_back")) ?></b></p>
    <p><?= l("lang_login_done") ?></p>
    <a href="<?= str_replace('"', '&quot;', $_GET["return"]) ?>" class="btn btn-primary"><?= l("lang_login_finish") ?></a>
<?php endif; ?>
            </div>
        </div>
</div>

<script>
    function onSubmit(_) {
        document.getElementById("form").submit();
    }

    function onSubmit2(_) {
        document.getElementById("oauth-form").submit();
    }
</script>

<br><br>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
