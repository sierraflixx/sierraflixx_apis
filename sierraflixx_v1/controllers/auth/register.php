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
    sendUser($fetched, 200);
}
if ($user->Create()) {
    $created = $user->getCreated();

    sendUser($created);
} else {
    $error = $user->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}

function sendUser($account, $code = 201)
{
    $token = createToken($account['id'], "30 days");

    // Set authorisation token
    $_SESSION['auth'] = $token;

    sendDetails($account, $code, ["secret"]);
}