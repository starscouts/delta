<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
$title = "lang_admin_title";
$title_pre = l("lang_admin_titles_avatars");
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";

?>

<div class="container">
    <br><br>
    <a href="/admin">← <?= l("lang_admin_title") ?></a>
    <h1><?= l("lang_admin_titles_avatars") ?></h1>

    <select onchange="reloadPFP();" class="form-select" id="user">
        <option value="">-- <?= l("lang_admin_avatars_none") ?> --</option>
        <?php $letters = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"]; foreach ([...$letters, "#"] as $letter) { $users = [];
            foreach (array_filter(scandir($_SERVER["DOCUMENT_ROOT"] . "/includes/data/profiles"), function ($i) { return !str_starts_with($i, "."); }) as $_id) {
                $id = substr($_id, 0, -5);
                $name = getNameFromId($id, false);

                if (str_starts_with(strtolower($name), $letter)) {
                    $users[$id] = $name;
                }
            }

            if (count($users) > 0): ?>
                <optgroup label="<?= strtoupper($letter) ?>">
                    <?php foreach ($users as $id => $name): ?>
                    <option value="<?= $id ?>"><?= $name ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endif;
        } ?>
    </select>

    <div style="margin-top: 20px; display: grid; grid-template-columns: 96px 1fr; grid-gap: 20px;">
        <img alt="" id="preview" style="border: none; background-color: black; width: 96px; height: 96px; border-radius: 999px;">
        <div style="display: flex; align-items: center;">
            <div>
                <form action="/admin/avatars/update.php" method="post">
                    <input type="hidden" id="id-input" name="id" value="" required>
                    <input type="hidden" id="upload-input" name="upload" value="" required>
                    <p>
                        <input disabled class="form-control" onchange="updatePreview();" type="file" id="uploader" required>
                    </p>
                    <button id="upload-btn" class="disabled btn btn-primary"><?= l("lang_admin_avatars_upload") ?></button>
                </form>
            </div>
        </div>
    </div>

    <br><br>

    <script>
        function reloadPFP() {
            document.getElementById("uploader").value = "";
            document.getElementById("upload-btn").classList.add("disabled");
            document.getElementById("uploader").classList.add("disabled");
            document.getElementById("id-input").value = document.getElementById("user").value;
            document.getElementById("preview").src = "/admin/avatars/url.php?id=" + document.getElementById("user").value;

            if (document.getElementById("user").value !== "") {
                document.getElementById("uploader").removeAttribute("disabled");
            } else {
                document.getElementById("uploader").setAttribute("disabled", "");
            }
        }

        function updatePreview() {
            if (document.getElementById("uploader").value !== "") {
                document.getElementById("preview").src = URL.createObjectURL(document.getElementById("uploader").files[0]);

                let reader = new FileReader();
                reader.readAsDataURL(document.getElementById("uploader").files[0]);

                reader.onload = function () {
                    document.getElementById("upload-btn").classList.remove("disabled");
                    document.getElementById("upload-input").value = reader.result;
                }
            } else {
                document.getElementById("preview").src = "";
                document.getElementById("upload-btn").classList.add("disabled");
            }
        }

        document.getElementById("user").value = "";
        document.getElementById("uploader").value = "";
    </script>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>