<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('User');
$model = new Model($db->getConnection(), 'accounts');

$posted = $helper->validateData($_POST, [
    'uid' => 'required',
    'password' => 'required',
]);

$user = $model->readFromSearch([
    [
        "sign" => "=",
        "value" => $posted['uid'],
        "field" => "email",
        "divider" => "or",
    ],
    [
        "sign" => "LIKE",
        "value" => '%' . $posted['uid'] . '%',
        "field" => "phone",
        "divider" => "or",
    ],
], 'accounts');

if (count($user) > 0) {

    if (password_verify($posted['password'], $user['secret'])) {

        if ($user['is_active'] === "true") {

            $token = createToken($user['id'], "30 days");

            // Set authorisation token
            $_SESSION['auth'] = $token;
            createLog("successfully login to system", "user");
            sendDetails($user, 200, ['secret']);
        } else {
            $helper->showMessage(406, "account has been disabled");
        }
    }

    $helper->showMessage(400, "username or password is incorrect");
} else {

    $has_error = !empty($model->getError());

    if (!$has_error) {
        $helper->showMessage(400, "username or password is incorrect");
    } else {
        $helper->showMessage(500, $model->getError());
    }
}