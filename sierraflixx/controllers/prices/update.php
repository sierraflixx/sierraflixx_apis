<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('Price');
$model = new Model($db->getConnection());

$got = $helper->validateData($_GET, [
    "id" => "required",
]);

$fetched = $model->Read($got['id']);

if (count($fetched) > 0) {

    $posted = $helper->validateData($_POST, [
        'region' => 'optional',
        'region_code' => 'optional',
        'amount' => 'optional',
        'plan' => 'optional',
        'period' => 'optional',
    ]);

    $price = $helper->populateObject($model, [
        'updated_at' => date('y-m-d h:m:s'),
        "plan_id" => $helper->setIfContained("plan", $posted, $fetched['plan_id']),
        "amount" => $helper->setIfContained("amount", $posted, $fetched['amount']),
        "period" => $helper->setIfContained("period", $posted, $fetched['period']),
        "continent" => $helper->setIfContained("region", $posted, $fetched['continent'], 'capitalise'),
        "continent_code" => $helper->setIfContained("region_code", $posted, $fetched['continent_code'], 'uppercase')
    ]);

    if ($price->Update($got['id'])) {
        $updated = $price->Read($got['id']);

        $helper->showMessage(200, 'updated');
    } else {
        $error = $price->getErrorDetails();
        $helper->showMessage($error['code'], $error['message']);
    }
}
$helper->showMessage(404);