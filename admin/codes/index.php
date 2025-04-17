<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
$title = "lang_admin_title";
$title_pre = l("lang_admin_titles_codes");
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";

?>

    <div class="container">
        <br><br>
        <a href="/admin">← <?= l("lang_admin_title") ?></a>
        <h1><?= l("lang_admin_titles_codes") ?></h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <b><?= l("lang_upload_error") ?> </b><?= $error ?>
            </div>
        <?php endif; ?>

        <form action="/admin/codes/save/" method="post">
            <p>
                <input type="hidden" name="contents">
                <div id="editor"><?= $_POST["contents"] ?? json_encode(json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/coins.json"))), JSON_PRETTY_PRINT) ?></div>

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

            <button class="btn btn-primary"><?= l("lang_admin_save") ?></button><br>
        </form>

        <br><br>
    </div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>