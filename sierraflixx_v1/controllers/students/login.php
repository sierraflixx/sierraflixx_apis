<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('Student');
$modal = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'uid' => 'required',
]);

$student = $modal->Read($posted['uid'], 'id');

if (count($student) > 0) {

    $token = createToken($student['id'], "1 days");

    // Set authorisation token
    $_SESSION['auth'] = $token;

    sendDetails($student, 200, ['secret', 'role']);
} else {

    $has_error = !empty($modal->getError());

    if (!$has_error) {
        $helper->showMessage(404);
    } else {
        $helper->showMessage(500, $modal->getError());
    }
}
