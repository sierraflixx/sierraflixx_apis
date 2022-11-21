<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle("User");
$model = new Model($db->getConnection(), 'accounts');

$posted = $helper->validateData($_POST, [
    'phone' => 'required',
    'email' => 'optional',
]);

$user = $helper->populateObject($model, [
    "phone" => $posted['phone'],
    "email" => $helper->setIfContained('email', $posted, null)
], true);

$fetched = $model->readFromSearch([
    [
        "sign" => "=",
        "value" => $helper->setIfContained('email', $posted, "false"),
        "field" => "email",
        "divider" => "or",
    ],
    [
        "sign" => "=",
        "value" => $posted['phone'],
        "field" => "phone",
    ],
], 'accounts');

if (count($fetched) > 0) {
    sendUser($fetched, 200, $helper);
}

if ($user->Create()) {
    $created = $user->getCreated();

    sendUser($created, 201, $helper);
} else {
    $error = $user->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}

function sendUser($account, $code, $helper)
{
    $token = createToken($account['id'], "1 years");

    setAuthCookie($token);

    $helper->showMessage($code);
}