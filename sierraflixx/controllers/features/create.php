<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('Feature');
$model = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'title' => 'required',
    'desscription' => 'optional',
]);

$feature = $helper->populateObject($model, [
    "slug" => getSlug($posted['title']),
    "title" => ucfirst($posted['title']),
    "description" => $helper->setIfContained("role", $posted, null),
], true);

if ($feature->Create()) {
    $created = $feature->getCreated();

    $helper->showMessage(201);
} else {
    $error = $feature->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}