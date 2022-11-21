<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('Plan Feature');
$model = new Model($db->getConnection());

$got = $helper->validateData($_GET, [
    "id" => "required",
]);

$fetched = $model->Read($got['id']);

if (count($fetched) > 0) {

    $posted = $helper->validateData($_POST, [
        'plan' => 'optional',
        'value' => 'optional',
        'feature' => 'optional',
    ]);

    $user = $helper->populateObject($model, [
        "value" => $posted['value'],
        'updated_at' => date('y-m-d h:m:s'),
        "plan_id" => $helper->setIfContained("plan", $posted, $fetched['plan_id']),
        "feature_id" => $helper->setIfContained("feature", $posted, $fetched['feature_id']),
    ]);

    if ($user->Update($got['id'])) {
        $updated = $user->Read($got['id']);

        $helper->showMessage(200, 'updated');
    } else {
        $error = $user->getErrorDetails();
        $helper->showMessage($error['code'], $error['message']);
    }
}
$helper->showMessage(404);