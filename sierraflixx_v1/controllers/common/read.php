<?php

$contents = [];

$model = new Model($db->getConnection());
$plural = isset($plural) ? $plural : ucfirst($GLOBALS['table']);
$title = isset($title) ? $title : substr($plural, 0, strlen($plural) - 1);

$helper = $GLOBALS['helper'];
$helper->setTitle($title);

if ($read_all === true) {
    $orderBy = isset($orderBy) ? $orderBy : "id";
    $fetched = $model->readAll($orderBy);
} else {
    $got = $helper->validateData($_GET, [
        'param' => 'required',
        'value' => 'required',
    ]);
    $param = $got['param'] . (strpos($got['param'], "id") > -1 ? "" : "_id");
    $fetched = $model->Read($got['value'], $param, $param !== "id");
}

if (count($fetched) <= 0) {
    $has_error = !empty($model->getError());

    if (!$has_error) {
        $helper->showMessage(404);
    } else {
        $error = $model->getErrorDetails();
        $helper->showMessage($error['code'], $error['message']);
    }
}

createLog("fetch $title");
