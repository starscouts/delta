<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
$title = "lang_admin_title";
$title_pre = l("lang_admin_titles_objects");
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php";

$keys = [];

foreach (array_filter(scandir($_SERVER["DOCUMENT_ROOT"] . "/includes/data/articles"), function ($i) { return !str_starts_with($i, "."); }) as $_id) {
    $id = substr($_id, 0, -5);
    $keys[] = [
        "type" => "articles",
        "id" => $id,
        "name" => getNameFromId($id)
    ];
}

foreach (array_filter(scandir($_SERVER["DOCUMENT_ROOT"] . "/includes/data/gallery"), function ($i) { return !str_starts_with($i, "."); }) as $_id) {
    $id = substr($_id, 0, -5);
    $keys[] = [
        "type" => "gallery",
        "id" => $id,
        "name" => getNameFromId($id)
    ];
}

foreach (array_filter(scandir($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people"), function ($i) { return !str_starts_with($i, "."); }) as $_id) {
    $id = substr($_id, 0, -5);
    $keys[] = [
        "type" => "people",
        "id" => $id,
        "name" => getNameFromId($id)
    ];
}

foreach (array_filter(scandir($_SERVER["DOCUMENT_ROOT"] . "/includes/data/profiles"), function ($i) { return !str_starts_with($i, "."); }) as $_id) {
    $id = substr($_id, 0, -5);
    $keys[] = [
        "type" => "profiles",
        "id" => $id,
        "name" => json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/profiles/" . $_id)), true)["nick_name"] ?? (isset(json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/profiles/" . $_id)), true)["first_name"]) && isset(json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/profiles/" . $_id)), true)["last_name"]) ? json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/profiles/" . $_id)), true)["first_name"] . " " . json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/profiles/" . $_id)), true)["last_name"] : null)
    ];
}

foreach (array_filter(scandir($_SERVER["DOCUMENT_ROOT"] . "/includes/data/requests"), function ($i) { return !str_starts_with($i, "."); }) as $_id) {
    $id = substr($_id, 0, -5);
    $keys[] = [
        "type" => "requests",
        "id" => $id,
        "name" => null
    ];
}

usort($keys, function ($a, $b) {
    return strcmp(getNameFromId($a["id"]), getNameFromId($b["id"]));
})

?>

<style>
    .search-result span {
        white-space: nowrap;
        overflow: hidden !important;
        text-overflow: ellipsis;
        display: inline-block;
    }

    @media (max-width: 1250px) {
        .search-result {
            grid-template-columns: 1fr 2fr 0 !important;
        }
    }

    .filteredOut {
        display: none !important;
    }
</style>

<div class="container">
    <br><br>
    <a href="/admin">← <?= l("lang_admin_title") ?></a>
    <h1><?= l("lang_admin_titles_objects") ?></h1>

    <script src="/fuse.php"></script>
    <div class="input-group">
        <select onchange="updateFilter();" style="height: 100%;" class="form-select" id="filter">
            <option value="" selected><?= l("lang_admin_types_all") ?></option>
            <option value="articles"><?= l("lang_admin_types2_articles") ?></option>
            <option value="gallery"><?= l("lang_admin_types2_gallery") ?></option>
            <option value="people"><?= l("lang_admin_types2_people") ?></option>
            <option value="profiles"><?= l("lang_admin_types2_profiles") ?></option>
            <option value="requests"><?= l("lang_admin_types2_requests") ?></option>
        </select>
        <script>
            document.getElementById("filter").value = "";
        </script>
        <input style="width: 50%; margin-bottom: 15px;" id="search" autocapitalize="none" autocomplete="off" spellcheck="false" autofocus class="form-control" placeholder="<?= l("lang_admin_search") ?>" value="" onkeyup="search();" onkeydown="search();" onchange="search();">
    </div>

    <div class="list-group" id="all-items">
        <?php $entries = []; foreach ($keys as $item): $entries[] = ["id" => $item["id"], "name" => $item["name"] ?? null]; ?>
        <a id="search-result-<?= $item["id"] ?>" class="search-result list-group-item list-group-item-action <?= match ($item["type"]) {
            "articles" => "item-articles",
            "gallery" => "item-gallery",
            "people" => "item-people",
            "profiles" => "item-profiles",
            "requests" => "item-requests",
        } ?>" href="/admin/edit/?id=<?= $item["id"] ?>" style="display: grid; grid-template-columns: 1fr 2fr 2fr; grid-gap: 10px;">
            <?= match ($item["type"]) {
                "articles" => "<span style='width: max-content; display: flex; height: max-content; align-self: center;' class='badge bg-success rounded-pill'>" . l("lang_admin_types_articles") . "</span>",
                "gallery" => "<span style='width: max-content; display: flex; height: max-content; align-self: center;' class='badge bg-success rounded-pill'>" . l("lang_admin_types_gallery") . "</span>",
                "people" => "<span style='width: max-content; display: flex; height: max-content; align-self: center;' class='badge bg-success rounded-pill'>" . l("lang_admin_types_people") . "</span>",
                "profiles" => "<span style='width: max-content; display: flex; height: max-content; align-self: center;' class='badge bg-success rounded-pill'>" . l("lang_admin_types_profiles") . "</span>",
                "requests" => "<span style='width: max-content; display: flex; height: max-content; align-self: center;' class='badge bg-success rounded-pill'>" . l("lang_admin_types_requests") . "</span>",
            } ?> <?= isset($item["name"]) ? "<span>" . $item["name"] . "</span><span class='text-muted' style='font-family: monospace;'>$item[id]</span>" : "<span style='font-family: monospace;'>" . $item["id"] . "</span>" ?>
        </a>
        <?php endforeach; ?>
    </div>
    <div class="list-group" id="search-results"></div>
    <script>window.searchEntries = JSON.parse(atob(`<?= base64_encode(json_encode($entries)) ?>`));</script>

    <div class="dropdown" style="margin-top: 20px;">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            <?= l("lang_admin_titles_create") ?>
        </button>
        <ul class="dropdown-menu">
            <?php foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/skels"), function ($i) { return !str_starts_with($i, "."); }) as $skel): $id = substr($skel, 0, -5); ?>
                <li><a class="dropdown-item" href="/admin/create/?skel=<?= $id ?>"><?= l("lang_admin_types_" . $id) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script>
        const fuse = new Fuse(window.searchEntries, {
            keys: [
                {
                    name: "name",
                    weight: 1
                },
                {
                    name: "id",
                    weight: 0.25
                }
            ]
        })

        function search() {
            let query = document.getElementById("search").value.trim();

            if (query === "") {
                document.getElementById("search-results").style.display = "none";
                document.getElementById("search-results").inneHTML = "";
                document.getElementById("all-items").style.display = "";
            } else {
                document.getElementById("search-results").style.display = "";
                document.getElementById("search-results").inneHTML = "";
                document.getElementById("all-items").style.display = "none";
            }

            document.getElementById("search-results").innerHTML = fuse.search(query).map(i => i.item.id).map(i => document.getElementById("search-result-" + i).outerHTML).join("");
        }

        function updateFilter() {
            let filter = document.getElementById("filter").value;

            if (filter === "") {
                for (let item of Array.from(document.getElementsByClassName("search-result"))) {
                    item.classList.remove("filteredOut");
                }
            } else {
                for (let item of Array.from(document.getElementsByClassName("search-result"))) {
                    item.classList.remove("filteredOut");
                }

                for (let item of Array.from(document.getElementsByClassName("search-result"))) {
                    if (!item.classList.contains("item-" + filter)) {
                        item.classList.add("filteredOut");
                    }
                }
            }
        }
    </script>

    <br><br>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>