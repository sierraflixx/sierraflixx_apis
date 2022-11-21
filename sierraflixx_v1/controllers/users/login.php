<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('User');
$modal = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'username' => 'required',
    'password' => 'required',
]);

$user = $modal->Read($posted['username'], 'uid');

if (count($user) > 0) {

    if (password_verify($posted['password'], $user['secret'])) {

        if ($user['is_active'] === "true") {

            $token = createToken($user['id'], "1 days");

            // Set authorisation token
            $_SESSION['auth'] = $token;
            createLog("successfully login to system", "user");
            sendDetails($user, 200, ['secret']);
        } else {
            $helper->showMessage(406, "account has been disabled");
        }
    }

    createLog("login attempt with wrong password", "user");
    $helper->showMessage(400, "username or password is incorrect");
} else {

    $has_error = !empty($modal->getError());

    if (!$has_error) {
        $helper->showMessage(400, "username or password is incorrect");
    } else {
        $helper->showMessage(500, $modal->getError());
    }
}
