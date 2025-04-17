<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";

if (isset($_GET["id"])) {
    $name = getNameFromId($_GET['id']);

    if ($name === $_GET['id']) {
        header("Location: /admin");
        die();
    }

    $title_pre = $name;
} else {
    header("Location: /admin");
    die();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
$title = "lang_admin_title";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";

?>

<div class="container">
    <br><br>
    <a href="/admin/objects">← <?= l("lang_admin_titles_objects") ?></a>
    <h1><?= $name ?></h1>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <b><?= l("lang_upload_error") ?> </b><?= $error ?>
    </div>
    <?php endif; ?>

    <form action="/admin/edit/save/?id=<?= $_GET['id'] ?>" method="post">
        <p>
            <input type="hidden" name="contents">
            <div id="editor"><?= str_replace(">", "&gt;", str_replace("<", "&lt;", str_replace("&", "&amp;", $_POST["contents"] ?? json_encode(json_decode(pf_utf8_decode(file_get_contents(getFileFromId($_GET['id'])))), JSON_PRETTY_PRINT)))) ?></div>

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