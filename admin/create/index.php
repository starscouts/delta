<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";

if (isset($_GET["skel"])) {
    if (preg_match("/^[a-z]*$/m", $_GET["skel"]) === false || !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/skels/" . $_GET["skel"] . ".json")) {
        header("Location: /admin");
        die();
    }

    $skel = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/skels/" . $_GET["skel"] . ".json"), true);
} else {
    header("Location: /admin");
    die();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
$title = "lang_admin_title";
$title_pre = l("lang_admin_titles_create");
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";

?>

<div class="container">
    <br><br>
    <a href="/admin/objects">← <?= l("lang_admin_titles_objects") ?></a>
    <h1><?= l("lang_admin_titles_create") ?></h1>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <b><?= l("lang_upload_error") ?> </b><?= $error ?>
    </div>
    <?php endif; ?>

    <form action="/admin/create/save/?skel=<?= $_GET['skel'] ?>" method="post">
        <p>
            <input type="hidden" name="contents">
            <div id="editor"><?php

            if (isset($_GET["registration"])) {
                if (preg_match("/^[a-z0-9-]*$/m", $_GET["registration"]) === false || !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/registrations/" . $_GET["registration"] . ".json")) {
                    header("Location: /admin");
                    die();
                }

                $reg = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/registrations/" . $_GET["registration"] . ".json"), true);

                $skel["language"] = $reg["lang"];
                $skel["kiosk"] = true;
                $skel["first_name"] = $reg["use_name"] ?? $reg["first_name"];
                $skel["last_name"] = $reg["last_name"];
                $skel["email"] = $reg["email"];
                $skel["birth"] = $reg["birth_date"];
                $skel["phone"] = $reg["phone"];

                echo(json_encode($skel, JSON_PRETTY_PRINT));
            } else {
                echo($_POST["contents"] ?? json_encode($skel, JSON_PRETTY_PRINT));
            }

            ?></div>

            <style media="screen">
                #editor {
                    width: 100%;
                    height: 256px;
                }

                #editor, #editor * {
                    font-family: monospace;
                }
            </style>
            <script src="/admin/ace/ace.js" type="text/javascript" charset="utf-8"></script>
            <script>
                let editor = ace.edit("editor");
                editor.setTheme("ace/theme/one_dark");
                editor.session.setMode("ace/mode/json");

                editor.getSession().on('change', function() {
                    document.getElementsByName("contents")[0].value = editor.getSession().getValue();
                });
            </script>
        </p>

        <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/help.php"; ?>

        <button class="btn btn-primary"><?= l("lang_admin_save") ?></button><br>
        <small class="text-muted">*<?= l("lang_admin_auth") ?></small>
    </form>

    <br><br>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>