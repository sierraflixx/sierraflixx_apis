<?php

require_once "utils/authenticate.php";

$helper->setTitle("Privilege");
$model = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'role' => 'required',
    'controller' => 'required',
    'read' => 'required',
    'create' => 'required',
    'update' => 'required',
    'delete' => 'required',
]);

$privilege = $helper->populateObject($model, [
    "role" => $posted['role'],
    "can_read" => $posted['read'],
    "can_create" => $posted['create'],
    "can_update" => $posted['update'],
    "can_delete" => $posted['delete'],
    "endpoint" => $posted['controller'],
], true);

if ($privilege->Create()) {
    $created = $privilege->getCreated();
    createLog("create privilege with id " . $created['id']);
    sendDetails($created, 201);
} else {
    $error = $privilege->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}
