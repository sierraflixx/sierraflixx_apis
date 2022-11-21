<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('Price');
$model = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'region' => 'required',
    'region_code' => 'required',
    'amount' => 'required',
    'plan' => 'required',
    'period' => 'required',
]);

$price = $helper->populateObject($model, [
    "plan_id" => $posted['plan'],
    "amount" => $posted['amount'],
    "period" => $posted['period'],
    "continent" => ucfirst($posted['region']),
    "continent_code" => strtoupper($posted['region_code']),
], true);

if ($price->Create()) {
    $created = $price->getCreated();

    $helper->showMessage(201);
} else {
    $error = $price->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}