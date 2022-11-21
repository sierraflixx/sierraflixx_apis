<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle("User");
$model = new Model($db->getConnection(), 'accounts');

$got = $helper->validateData($_GET, [
    "id" => "required",
]);

$fetched = $model->Read($got['id']);

if (count($fetched) > 0) {

    if ($fetched['id'] !== $got['id']) $helper->showMessage(403);

    $posted = $helper->validateData($_POST, [
        'password' => 'required',
    ]);

    $user = $helper->populateObject($model, [
        'has_secret' => 'true',
        'secret' => password_hash($posted['password'], PASSWORD_BCRYPT),
    ]);

    if ($user->Update($got['id'])) {
        $updated = $user->Read($got['id']);

        sendDetails($updated, 200, ['secret']);
    } else {
        $error = $user->getErrorDetails();
        $helper->showMessage($error['code'], $error['message']);
    }
}
$helper->showMessage(404);