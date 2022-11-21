<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('Login ');
$model = new Model($db->getConnection(), 'accounts');

$posted = $helper->validateData($_POST, [
    'uid' => 'required',
    'password' => 'required',
    'type' => 'required'
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

            $token = createToken($user['id'], "1 years");

            setAuthCookie($token);

            $helper->showMessage(200, 'login success');
        } else {
            $helper->showMessage(406, "account has been disabled");
        }
    }

    $helper->showMessage(400, "username or password is incorrect");
} else {

    $has_error = !empty($model->getError());

    if (!$has_error) {

        $helper->setTitle('');
        $message = '<p className="m-0"></p>';

        switch ($posted['type']) {
            case 'phone':
                $message .= 'Sorry, we can\'t find an account with this number. Please make sure to select the correct country code or sign in with email.';
                break;
            case 'email':
            default:
                $message .= 'Sorry, we can\'t find an account with this email address. Please try again or <a href="/signup">create a new account</a>';
                break;
        }

        $message .= '</p>';

        $helper->showMessage(400, $message);
    } else {
        $helper->showMessage(500, $model->getError());
    }
}