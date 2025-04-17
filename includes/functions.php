<?php

die();

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/lang.php";

function formatPonypush($message) {
    return "Update to Ponypush 3.1.0 or later — (\$PA1$\$" . base64_encode($message) . "\$\$)";
}

function base64url_encode($input) {
    return strtr(base64_encode($input), '+/=', '._-');
}

function base64url_decode($input) {
    return base64_decode(strtr($input, '._-', '+/='));
}

function uuidToId($uuid) {
    return "ls" . substr(base64url_encode(hex2bin(sha1(str_replace("-", "", $uuid)))), 0, 7);
}

function parseId($id) {
    if (str_contains($id, "-")) {
        return $id;
    } else {
        foreach (getSearchEntries() as $entry) {
            $url = $entry["url"];
            $uuid = $entry["id"];

            if (uuidToId($uuid) === "l" . $id) {
                return [
                    "id" => $uuid,
                    "url" => $url
                ];
            }
        }
    }
}

function pf_utf8_decode(string $string): string {
    return iconv("UTF-8", "ISO-8859-1", $string);
}

function pf_utf8_encode(string $string): string {
    return iconv("ISO-8859-1", "UTF-8", $string);
}

function hasProfileSetting($_1, $default, $_2 = null) {
    return $default;
}

function badges($data) { ?>
    <?php if ($data["plus"] && !$data["ultra"]): ?>
        <img title="Delta Plus" data-bs-toggle="tooltip" src="/logo-plus.svg" style="width: 36px;">
    <?php endif; ?>
    <?php if ($data["ultra"] && hasProfileSetting("badge", true)): ?>
        <img title="Delta Ultra" data-bs-toggle="tooltip" src="/logo-ultra.svg" style="width: 36px;">
    <?php endif; ?>
    <?php if ($data["hunter"]): ?>
        <img title="<?= l("lang_badges_hunter") ?>" data-bs-toggle="tooltip" src="/badges/hunter.svg" style="width: 36px;">
    <?php endif; ?>
    <?php if ($data["admin"]): ?>
        <img title="<?= l("lang_badges_admin") ?>" data-bs-toggle="tooltip" src="/badges/staff.svg" style="width: 36px;">
    <?php endif; ?>
    <?php if ($data["eap"]): ?>
        <img title="<?= l("lang_badges_eap") ?>" data-bs-toggle="tooltip" src="/badges/eap.svg" style="width: 36px;">
    <?php endif; ?>
<?php }

function initLang(): void {
    global $_PROFILE;

    if (isset($_PROFILE) && isset($_PROFILE["language"])) {
        genLang("en");
        genLang($_GET["hl"] ?? $_PROFILE["language"]);
    } else {
        genLang("en");
        genLang($_GET["hl"] ?? $_COOKIE["DeltaLanguage"] ?? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? "en", 0, 2));
    }
}

initLang();

function getSearchEntries($ignoreProfiles = false, $addRequests = false) {
    $entries = [
        ...array_map(function ($i) {
            $id = substr($i, 0, -5);
            $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/articles/" . $i), true);

            return [
                "id" => $id,
                "type" => "articles",
                "url" => "/articles/" . $id,
                "name" => getNameFromId($id),
                "alts" => [],
                "extract" => strip_tags($data["contents"] ?? "")
            ];
        }, array_values(array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/articles"), function ($i) { return str_ends_with($i, ".json"); }))),

        ...array_map(function ($i) {
            $id = substr($i, 0, -5);
            $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/gallery/" . $i), true);

            return [
                "id" => $id,
                "type" => "gallery",
                "url" => "/gallery/" . $id,
                "name" => getNameFromId($id),
                "alts" => [],
                "extract" => strip_tags($data["contents"] ?? "")
            ];
        }, array_values(array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/gallery"), function ($i) { return str_ends_with($i, ".json"); }))),

        ...array_map(function ($i) {
            $id = substr($i, 0, -5);
            $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people/" . $i), true);

            return [
                "id" => $id,
                "type" => "people",
                "url" => "/people/" . $id,
                "name" => $data["first_name"] . " " . $data["last_name"],
                "alts" => $data["alts"],
                "extract" => strip_tags($data["contents"] ?? "")
            ];
        }, array_values(array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people"), function ($i) { return str_ends_with($i, ".json"); }))),
    ];

    if (!$ignoreProfiles) {
        array_push($entries, ...array_map(function ($i) {
            $id = substr($i, 0, -5);
            $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $i), true);

            return [
                "id" => $id,
                "type" => "profiles",
                "url" => "/profile/" . $id,
                "alts" => [],
                "name" => $data["nick_name"] ?? ($data["first_name"] . " " . $data["last_name"]),
                "extract" => strip_tags($data["contents"] ?? "")
            ];
        }, array_values(array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles"), function ($i) { return str_ends_with($i, ".json"); }))));
    }

    if ($addRequests) {
        array_push($entries, ...array_map(function ($i) {
            $id = substr($i, 0, -5);
            $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests/" . $i), true);

            return [
                "id" => $id,
                "type" => "requests",
                "url" => null,
                "alts" => [],
                "name" => $id,
                "extract" => null
            ];
        }, array_values(array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/requests"), function ($i) { return str_ends_with($i, ".json"); }))));
    }

    return $entries;
}

function search($query, $ignoreExact = false, $ignoreProfiles = false) {
    $result = [];
    $id = "/tmp/query-" . uuid() . ".json";

    file_put_contents($id, json_encode([
        "entries" => getSearchEntries($ignoreProfiles),
        "query" => $query,
        "exact" => !$ignoreExact
    ]));

    exec('"' . $_SERVER['DOCUMENT_ROOT'] . '/includes/search/build/search-linux-x64" "' . $id . '"', $result);

    $results = json_decode(implode("\n", $result), true);
    unlink($id);

    return array_values($results);
}

function search_old($query, $ignoreExact = false, $ignoreProfiles = false): array {
    $entries = getSearchEntries($ignoreProfiles);

    $scored = array_map(function ($i) use ($query, $ignoreExact) {
        $name = explode(" ", trim(strtolower(preg_replace("/ +/m", " ", preg_replace("/[^a-zA-Z0-9-_'\"+=]/m", " ", $i['name'])))));
        $extract = explode(" ", trim(strtolower(preg_replace("/ +/m", " ", preg_replace("/[^a-zA-Z0-9-_'\"+=]/m", " ", $i['extract'])))));

        $scoreName = [];
        $scoreAlts = [];
        $scoreExtract = [];

        foreach (explode(" ", $query) as $word) {
            array_push($scoreName, ...array_map(function ($i) use ($word) {
                return levenshtein($i, $word);
            }, $name));

            array_push($scoreExtract, ...array_map(function ($i) use ($word) {
                return levenshtein($i, $word);
            }, $extract));

            foreach ($i["alts"] as $alt) {
                $alt = explode(" ", trim(strtolower(preg_replace("/ +/m", " ", preg_replace("/[^a-zA-Z0-9-_'\"+=]/m", " ", $alt)))));

                array_push($scoreAlts, ...array_map(function ($i) use ($word) {
                    return levenshtein($i, $word);
                }, $alt));
            }
        }

        $avgName = 0;
        $avgExtract = 0;
        $avgAlts = 0;

        if (count($scoreName) > 0) $avgName = array_sum($scoreName) / count($scoreName);
        if (count($scoreExtract) > 0) $avgExtract = array_sum($scoreExtract) / count($scoreExtract);
        if (count($scoreAlts) > 0) $avgAlts = array_sum($scoreAlts) / count($scoreAlts);

        $score = $avgName * 2 + $avgExtract + $avgAlts;

        return [
            "score" => $score,
            "breakdown" => [
                "name" => $avgName,
                "extract" => $avgExtract,
                "alts" => $avgAlts
            ],
            "value" => $i
        ];
    }, $entries);

    usort($scored, function ($a, $b) {
        return $a["score"] - $b["score"];
    });

    return array_values(array_filter($scored, function ($i) use ($ignoreExact, $query) { return $i["score"] <= 20 && (!$ignoreExact || ($ignoreExact && $i["value"]["name"] !== $query)); }));
}

function timeAgo($time, $showTense = true, $strict = false, $daysOnly = false): string {
    if (!is_numeric($time)) {
        $time = strtotime($time);
    }

    $periods = [l("lang_time_sec"), l("lang_time_mn"), l("lang_time_hr"), l("lang_time_d"), l("lang_time_wk"), l("lang_time_mo"), l("lang_time_y"), l("lang_time_age")];
    $lengths = array("60", "60", "24", "7", "4.35", "12", "100");

    $now = time();

    $difference = $now - $time;
    if (abs($difference) <= 10 && abs($difference) >= 0) {
        if ($showTense) {
            return $tense = l("lang_time_now");
        } else {
            return $tense = l("lang_time_less");
        }
    } elseif ($difference > 0) {
        $tense = l("lang_time_ago");
    } else {
        $tense = l("lang_time_later");
    }

    for ($j = 0; abs($difference) >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }

    $difference = $strict ? floor($difference) : round($difference);

    if ($daysOnly && $j < 3) {
        if (date('d-m-Y') === date('d-m-Y', $time)) {
            return l("lang_time_today");
        } else {
            return l("lang_time_yesterday");
        }
    }

    $period = $periods[$j];

    if ($showTense) {
        return trim(str_replace("%3", $tense, str_replace("%2", $period . (abs($difference) > 1 && !str_ends_with($period, "s") ? "s" : ""), str_replace("%1", abs($difference), l("lang_time_display")))));
    } else {
        return trim(str_replace("%3", "", str_replace("%2", $period . (abs($difference) > 1 && !str_ends_with($period, "s") ? "s" : ""), str_replace("%1", abs($difference), l("lang_time_display")))));
    }
}

function listArticles() {

    $articles = array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/articles"), function ($i) { return !str_starts_with($i, "."); });

    usort($articles, function ($a, $b) {
        return strcmp($a, $b);
    });

    ?>
    <div class="list-group">
        <?php foreach ($articles as $person): ?>
            <a href="/articles/<?= explode(".", $person)[0] ?>" class="list-group-item-ellipsis list-group-item list-group-item-action <?= explode("&", explode("?", $_SERVER['REQUEST_URI'])[0])[0] === "/articles/" . explode(".", $person)[0] ? "list-group-item-primary" : "" ?>"><?= getNameFromId(explode(".", $person)[0]) ?></a>
        <?php endforeach; ?>
    </div>
<?php }

function listAlbums() {

    $articles = array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/gallery"), function ($i) { return !str_starts_with($i, "."); });

    usort($articles, function ($a, $b) {
        return strcmp($a, $b);
    });

    ?>
    <div class="list-group">
        <?php foreach ($articles as $person): $data = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/gallery/" . $person)), true); ?>
            <a href="/gallery/<?= explode(".", $person)[0] ?>" class="list-group-item-ellipsis list-group-item list-group-item-action <?= explode("&", explode("?", $_SERVER['REQUEST_URI'])[0])[0] === "/gallery/" . explode(".", $person)[0] ? "list-group-item-primary" : "" ?>"><?= getNameFromId(explode(".", $person)[0]) ?></a>
        <?php endforeach; ?>
    </div>
<?php }

function coinsToEur($coins) {
    $price = (float)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/coinprice");
    $eur = $price * $coins;
    return number_format($eur, 2);
}

function listPeople() {

    $articles = array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people"), function ($i) { return !str_starts_with($i, "."); });

    usort($articles, function ($a, $b) {
        return strcmp($a, $b);
    });

    ?>
    <div class="list-group">
        <?php foreach ($articles as $person): $data = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people/" . $person)), true); ?>
            <a href="/people/<?= explode(".", $person)[0] ?>" class="list-group-item-ellipsis list-group-item list-group-item-action <?= explode("&", explode("?", $_SERVER['REQUEST_URI'])[0])[0] === "/people/" . explode(".", $person)[0] ? "list-group-item-primary" : "" ?>"><?= $data["first_name"] . " " . $data["last_name"] ?></a>
        <?php endforeach; ?>
    </div>
<?php }

function resolveUser($id) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $id . ".json")) {
        $p = json_decode(pf_utf8_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/data/profiles/" . $id . ".json")), true);

        if (isset($p["nick_name"]) && trim($p["nick_name"]) !== "") {
            return $p["nick_name"];
        } else {
            return $p["first_name"] . " " . $p["last_name"];
        }
    } else {
        return $id;
    }
}

function uuid() {
    $data = openssl_random_pseudo_bytes(16);
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function enumerate($list, $separator) {
    $i = "";

    $list = array_values($list);
    foreach ($list as $index => $item) {
        $i .= $item;

        if ($index < count($list) - 2) {
            $i .= ", ";
        } else if ($index < count($list) - 1) {
            $i .= " " . $separator . " ";
        }
    }

    return $i;
}

function formatDate($date, $withYear = true) {
    if (!is_int($date)) $date = strtotime($date);

    $day = date('j', $date);
    $year = date('Y', $date);
    $month = (int)date('n', $date);

    switch ($month) {
        case 1:
            $month = l("lang_months_jan");
            break;

        case 2:
            $month = l("lang_months_feb");
            break;

        case 3:
            $month = l("lang_months_mar");
            break;

        case 4:
            $month = l("lang_months_apr");
            break;

        case 5:
            $month = l("lang_months_may");
            break;

        case 6:
            $month = l("lang_months_jun");
            break;

        case 7:
            $month = l("lang_months_jul");
            break;

        case 8:
            $month = l("lang_months_aug");
            break;

        case 9:
            $month = l("lang_months_sep");
            break;

        case 10:
            $month = l("lang_months_oct");
            break;

        case 11:
            $month = l("lang_months_nov");
            break;

        case 12:
            $month = l("lang_months_dec");
            break;

        default:
            $month = "-";
            break;
    }

    if ($withYear) {
        return $day . " " . $month . " " . $year;
    } else {
        return $day . " " . $month;
    }
}

function getNameFromId($id, $allowNickName = true) {
    if (preg_replace("/^[\da-f]{8}(-[\da-f]{4}){3}-[\da-f]{12}$/m", "OK", $id) !== "OK") return $id;

    if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $id . ".json")) {
        return json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $id . ".json")), true)["first_name"] . " " . json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $id . ".json")), true)["last_name"];
    } elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/gallery/" . $id . ".json")) {
        $data = json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/gallery/" . $id . ".json")), true);

        return $data["title"][l("lang__name")] ?? $data["title"]["en"] ?? $data["title"][array_keys($data["title"])[0]] ?? "-";
    } elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/articles/" . $id . ".json")) {
        $data = json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/articles/" . $id . ".json")), true);

        return $data["title"][l("lang__name")] ?? $data["title"]["en"] ?? $data["title"][array_keys($data["title"])[0]] ?? "-";
    } elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/profiles/" . $id . ".json")) {
        $d = json_decode(pf_utf8_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/profiles/" . $id . ".json")), true);

        if ($allowNickName) {
            return $d["nick_name"] ?? $d["first_name"] . " " . $d["last_name"];
        } else {
            return $d["first_name"] . " " . $d["last_name"];
        }
    }

    return $id;
}

function getFileFromId($id) {
    if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $id . ".json")) {
        return $_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $id . ".json";
    } elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/gallery/" . $id . ".json")) {
        return $_SERVER["DOCUMENT_ROOT"] . "/includes/data/gallery/" . $id . ".json";
    } elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/articles/" . $id . ".json")) {
        return $_SERVER["DOCUMENT_ROOT"] . "/includes/data/articles/" . $id . ".json";
    } elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/profiles/" . $id . ".json")) {
        return $_SERVER["DOCUMENT_ROOT"] . "/includes/data/profiles/" . $id . ".json";
    }

    return null;
}

function getUrlFromId($id) {
    if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $id . ".json")) {
        return "/people/" . $id;
    } elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/gallery/" . $id . ".json")) {
        return "/gallery/" . $id;
    } elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/articles/" . $id . ".json")) {
        return "/articles/" . $id;
    } elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/profiles/" . $id . ".json")) {
        return "/profile/" . $id;
    }

    return "/";
}

function getTypeFromId($id) {
    if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $id . ".json")) {
        return "people";
    } elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/gallery/" . $id . ".json")) {
        return "gallery";
    } elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/articles/" . $id . ".json")) {
        return "articles";
    } elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/includes/data/profiles/" . $id . ".json")) {
        return "profiles";
    }

    return $id;
}

function isJson($string): bool {
    json_decode($string);
    return (json_last_error() === JSON_ERROR_NONE);
}

function resolveRealID($id) {
    if (!str_contains($id, ".")) {
        return $id;
    }

    $parts = explode(".", $id);

    if (count($parts) === 2) {
        foreach (array_filter(scandir($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people"), function ($i) { return !str_starts_with($i, "."); }) as $_id) {
            $_d = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/includes/data/people/" . $_id), true);

            if (strtolower(trim($_d["first_name"])) === strtolower(trim($parts[0])) && strtolower(trim($_d["last_name"])) === strtolower(trim($parts[1]))) {
                $id = substr($_id, 0, -5);
            }
        }
    }

    return $id;
}

function findRelated($id) {
    if (getTypeFromId($id) === "people" || getTypeFromId($id) === "profiles") {
        $relations = [];

        if (getTypeFromId($id) === "people") {
            $relations = array_filter(array_map(function ($i) {
                return resolveRealID($i['id']);
            }, json_decode(file_get_contents(getFileFromId($id)), true)["relations"] ?? []), function ($i) {
                return getNameFromId($i) !== $i;
            });
        } else {
            $profile = json_decode(file_get_contents(getFileFromId($id)), true);

            foreach (scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/data/people") as $person) {
                if (str_starts_with($person, ".")) continue;

                $personData = json_decode(file_get_contents(getFileFromId(substr($person, 0, -5))), true);

                if ($personData["first_name"] === $profile["first_name"] && $personData["last_name"] === $profile["last_name"]) {
                    $relations = array_filter(array_map(function ($i) {
                        return resolveRealID($i['id']);
                    }, $personData["relations"] ?? []), function ($i) {
                        return getNameFromId($i) !== $i;
                    });
                }
            }
        }

        if (count($relations) < 3) {
            return search(getNameFromId($id), true, true);
        } else {
            return array_map(function ($i) {
                return [
                    "score" => -1,
                    "breakdown" => [],
                    "value" => [
                        "id" => $i,
                        "type" => getTypeFromId($i),
                        "url" => getUrlFromId($i),
                        "name" => getNameFromId($i),
                        "alts" => [],
                        "extract" => strip_tags(json_decode(file_get_contents(getFileFromId($i)), true)["contents"] ?? "")
                    ]
                ];
            }, $relations);
        }
    } else {
        return search(getNameFromId($id), true, true);
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/linking.php";
