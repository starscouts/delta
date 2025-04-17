<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions.php";

$id = $_GET["_"];
$data = parseId($id);

header("Location: " . (isset($data) ? $data['url'] : "/"));
die();