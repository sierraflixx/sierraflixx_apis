<?php
require_once "utils/authenticate.php";

$orderBy = "student_id";
$delete_all = isset($_GET['param']) && $_GET['param'] === "all";

require "controllers/common/delete.php";

sendDetails($deletes, 200);
