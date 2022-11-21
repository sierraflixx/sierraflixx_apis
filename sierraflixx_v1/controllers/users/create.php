<?php

require_once "utils/authenticate.php";

$helper->setTitle("User");
$model = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'fullName' => 'required',
    'username' => 'required',
    'password' => 'required',
    'role' => 'required',
]);

$user = $helper->populateObject($model, [
    "role" => $posted['role'],
    "uid" => $posted['username'],
    "name" => $posted['fullName'],
    "secret" => password_hash($posted['password'], PASSWORD_BCRYPT),
], true);

if ($user->Create()) {
    $created = $user->getCreated();
    createLog("create user with id " . $created['id']);

    sendDetails($created, 201, ["secret"]);
} else {
    $error = $user->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}
