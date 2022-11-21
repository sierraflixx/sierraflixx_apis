<?php

require_once "utils/authenticate.php";

$helper->setTitle("Privilege");
$model = new Model($db->getConnection());

$got = $helper->validateData($_GET, [
    "id" => "required",
]);

$fetched = $model->Read($got['id']);

if (count($fetched) > 0) {

    $posted = $helper->validateData($_POST, [
        'read' => 'optional',
        'create' => 'optional',
        'update' => 'optional',
        'delete' => 'optional',
    ]);

    $privilege = $helper->populateObject($model, [
        'can_read' => $helper->setIfContained("read", $posted, $fetched['can_read']),
        'can_create' => $helper->setIfContained("create", $posted, $fetched['can_create']),
        'can_update' => $helper->setIfContained("update", $posted, $fetched['can_update']),
        'can_delete' => $helper->setIfContained("delete", $posted, $fetched['can_delete']),
    ]);

    if ($privilege->Update($got['id'])) {
        $updated = $privilege->Read($got['id']);
        createLog("updated privilege with id " . $updated['id']);

        sendDetails($updated, 200);
    } else {
        $error = $privilege->getErrorDetails();
        $helper->showMessage($error['code'], $error['message']);
    }
}
$helper->showMessage(404);
