<?php

//require_once "utils/authenticate.php";

$deletes = [];

$model = new Model($db->getConnection());
$plural = isset($plural) ? $plural : ucfirst($GLOBALS['table']);
$title = isset($title) ? $title : substr($plural, 0, strlen($plural) - 1);

$helper = $GLOBALS['helper'];
$helper->setTitle($title);
$param = "";

$delete_all = isset($delete_all) && $delete_all === true;

if ($delete_all) {
    $orderBy = isset($orderBy) ? $orderBy : "id";
    $deletes = $model->readAll($orderBy);
} else {
    $got = $helper->validateData($_GET, [
        'param' => 'required',
        'value' => 'required',
    ]);
    $param = $got['param'] . (strpos($got['param'], "id") > -1 ? "" : "_id");
    $deletes = $model->Read($got['value'], $param);
}

if (count($deletes) > 0) {

    $isDeleted = false;

    if ($delete_all) {
        $isDeleted = $model->deleteAll();
    } else {
        $isDeleted = $model->Delete($got['value'], $param);
    }

    if (!$isDeleted) {
        $error = $model->getErrorDetails();
        $helper->showMessage($error['code'], $error['message']);
    }

    createLog("delete $title");
} else {

    $has_error = !empty($model->getError());

    if (!$has_error) {
        $helper->showMessage(404);
    } else {
        $error = $model->getErrorDetails();
        $helper->showMessage($error['code'], $error['message']);
    }
}