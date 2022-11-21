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
        'old_password' => 'required',
        'new_password' => 'required',
    ]);

    if (password_verify($posted['old_password'], $fetched['secret'])) {

        $user = $helper->populateObject($model, [
            'secret' => password_hash($posted['new_password'], PASSWORD_BCRYPT),
        ]);

        if ($user->Update($got['id'])) {
            $updated = $user->Read($got['id']);
            createLog("changed password for user with id " . $updated['id']);

            sendDetails($updated, 200, ['secret']);
        } else {
            $error = $user->getErrorDetails();
            $helper->showMessage($error['code'], $error['message']);
        }
    }
    $helper->showMessage(400, "password is incorrect");

}
$helper->showMessage(404);
