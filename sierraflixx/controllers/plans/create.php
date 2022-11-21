<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('Plan');
$model = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'title' => 'required',
    'desscription' => 'optional',
]);

$plan = $helper->populateObject($model, [
    "slug" => getSlug($posted['title']),
    "title" => ucfirst($posted['title']),
    "description" => $helper->setIfContained("role", $posted, null),
], true);

if ($plan->Create()) {
    $created = $plan->getCreated();

    $helper->showMessage(201);
} else {
    $error = $plan->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}