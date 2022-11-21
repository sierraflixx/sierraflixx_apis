<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('Plan');
$model = new Model($db->getConnection());

$got = $helper->validateData($_GET, [
    "id" => "required",
]);

$fetched = $model->Read($got['id']);

if (count($fetched) > 0) {

    $posted = $helper->validateData($_POST, [
        'title' => 'optional',
        'description' => 'optional',
    ]);

    $plan = $helper->populateObject($model, [
        'title' => $helper->setIfContained("title", $posted, $fetched['title']),
        'description' => $helper->setIfContained("description", $posted, $fetched['description']),
        'slug' => $helper->hasContent('title', $posted) ? getSlug($posted['title']) : $fetched['slug'],
        'updated_at' => date('y-m-d h:m:s')
    ]);

    if ($plan->Update($got['id'])) {
        $updated = $plan->Read($got['id']);

        $helper->showMessage(200, 'updated');
    } else {
        $error = $plan->getErrorDetails();
        $helper->showMessage($error['code'], $error['message']);
    }
}
$helper->showMessage(404);