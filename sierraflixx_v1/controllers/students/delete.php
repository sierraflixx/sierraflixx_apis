<?php
require_once "utils/authenticate.php";

$delete_all = isset($_GET['param']) && $_GET['param'] === "all";

require "controllers/common/delete.php";

sendDetails($deletes, 200, ['secret', 'role']);
