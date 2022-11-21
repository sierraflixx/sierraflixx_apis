<?php

require_once "utils/authenticate.php";

$helper->setTitle("Program");
$model = new Model($db->getConnection());

$got = $helper->validateData($_GET, [
    "id" => "required",
]);

$fetched = $model->Read($got['id']);

if (count($fetched) > 0) {

    $posted = $helper->validateData($_POST, [
        'title' => 'optional',
        'description' => 'optional',
    ]);

    $program = $helper->populateObject($model, [
        'title' => $helper->setIfContained("title", $posted, $fetched['title']),
        'description' => $helper->setIfContained("description", $posted, $fetched['description']),
    ]);

    if ($program->Update($got['id'])) {
        $updated = $program->Read($got['id']);
        createLog("updated program with id " . $updated['id']);

        sendDetails($updated, 200);
    } else {
        $error = $program->getErrorDetails();
        $helper->showMessage($error['code'], $error['message']);
    }
}
$helper->showMessage(404);
