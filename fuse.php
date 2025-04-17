<?php

header("Content-Type: application/javascript");
die(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/search/node_modules/fuse.js/dist/fuse.min.js"));