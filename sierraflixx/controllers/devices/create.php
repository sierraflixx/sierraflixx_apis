<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('Device');
$model = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'name' => 'required',
    'image' => ['file', 'required'],
]);

$device = $helper->populateObject($model, [
    "name" => $posted['name'],
    "slug" => getSlug($posted['name']),
    "img_data" => getImage('image')
], true);

if ($device->Create()) {
    $created = $device->getCreated();

    $helper->showMessage(201);
} else {
    $error = $device->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}