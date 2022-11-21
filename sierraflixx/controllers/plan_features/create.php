<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('Plan Feature');
$model = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'plan' => 'required',
    'feature' => 'required',
    'value' => 'required',
]);

$plan_feature = $helper->populateObject($model, [
    "value" => $posted['value'],
    "plan_id" => $posted['plan'],
    "feature_id" => $posted['feature'],
], true);

if ($plan_feature->Create()) {
    $created = $plan_feature->getCreated();

    $helper->showMessage(201);
} else {
    $error = $plan_feature->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}