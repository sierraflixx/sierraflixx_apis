<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('Device');
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

    $user = $helper->populateObject($model, [
        'title' => $helper->setIfContained("title", $posted, $fetched['title']),
        'description' => $helper->setIfContained("description", $posted, $fetched['description']),
        'slug' => $helper->hasContent('title', $posted) ? getSlug($posted['title']) : $fetched['slug'],
        'updated_at' => date('y-m-d h:m:s')
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