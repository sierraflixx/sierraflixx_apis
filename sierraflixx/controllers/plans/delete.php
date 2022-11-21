<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('Plan');
$model = new Model($db->getConnection());

$delete_all = isset($_GET['param']) && $_GET['param'] === "all";

require "controllers/common/delete.php";

$helper->showMessage(200, 'deleted');