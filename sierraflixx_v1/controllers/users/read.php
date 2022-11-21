<?php
require_once "utils/authenticate.php";

$read_all = isset($_GET['param']) && $_GET['param'] === "all";

require_once "controllers/common/read.php";

sendDetails($fetched, 200, ["secret"]);
