<?php

require_once "utils/authenticate.php";

$helper->setTitle("Exam");
$model = new Model($db->getConnection());

$got = $helper->validateData($_GET, [
    "id" => "required",
]);

$fetched = $model->Read($got['id']);

if (count($fetched) > 0) {

    $posted = $helper->validateData($_POST, [
        'year' => 'optional',
        'date' => 'optional',
        'title' => 'optional',
        'period' => 'optional',
        'description' => 'optional',
    ]);

    $exam = $helper->populateObject($model, [
        'year' => $helper->setIfContained("year", $posted, $fetched['year']),
        'title' => $helper->setIfContained("title", $posted, $fetched['title']),
        'duration' => $helper->setIfContained("period", $posted, $fetched['duration']),
        'start_date' => $helper->setIfContained("date", $posted, $fetched['start_date']),
        'description' => $helper->setIfContained("description", $posted, $fetched['description']),
    ]);

    if ($exam->Update($got['id'])) {
        $updated = $exam->Read($got['id']);
        createLog("updated exam with id " . $updated['id']);

        sendDetails($updated, 200);
    } else {
        $error = $exam->getErrorDetails();
        $helper->showMessage($error['code'], $error['message']);
    }
}
$helper->showMessage(404);
