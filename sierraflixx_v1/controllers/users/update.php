<?php

require_once "utils/authenticate.php";

$helper->setTitle("User");
$model = new Model($db->getConnection());

$got = $helper->validateData($_GET, [
    "id" => "required",
]);

$fetched = $model->Read($got['id']);

if (count($fetched) > 0) {

    $posted = $helper->validateData($_POST, [
        'role' => 'optional',
        'fullName' => 'optional',
        'username' => 'optional',
        'is_active' => 'optional',
    ]);

    $user = $helper->populateObject($model, [
        'role' => $helper->setIfContained("role", $posted, $fetched['role']),
        'uid' => $helper->setIfContained("username", $posted, $fetched['uid']),
        'name' => $helper->setIfContained("fullName", $posted, $fetched['name']),
        'is_active' => $helper->setIfContained("is_active", $posted, $fetched['is_active']),
    ]);

    if ($user->Update($got['id'])) {
        $updated = $user->Read($got['id']);
        createLog("updated user with id " . $updated['id']);

        sendDetails($updated, 200, ['secret']);
    } else {
        $error = $user->getErrorDetails();
        $helper->showMessage($error['code'], $error['message']);
    }
}
$helper->showMessage(404);
