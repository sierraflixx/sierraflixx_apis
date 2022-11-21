<?php

require_once "utils/authenticate.php";

$helper->setTitle("Program");
$model = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'title' => 'required',
    'description' => 'optional',
]);

$program = $helper->populateObject($model, [
    "title" => $posted['title'],
    "description" => $helper->setIfContained("description", $posted, null),
], true);

if ($program->Create()) {
    $created = $program->getCreated();
    createLog("create program with id " . $created['id']);

    sendDetails($created, 201);
} else {
    $error = $program->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}
