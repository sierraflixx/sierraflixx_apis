<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle("Logout");
session_unset();

$helper->showMessage(200, "successful");
