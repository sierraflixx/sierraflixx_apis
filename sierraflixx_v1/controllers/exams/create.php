<?php

require_once "utils/authenticate.php";

$helper->setTitle("Exam");
$model = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'title' => 'required',
    'date' => 'required',
    'period' => 'required',
    'year' => 'required',
    'description' => 'optional',
]);

$exam = $helper->populateObject($model, [
    "year" => $posted['year'],
    "title" => $posted['title'],
    "duration" => $posted['period'],
    "start_date" => strtotime(date($posted['date'])) * 1000,
    "description" => $helper->setIfContained("description", $posted, null),
], true);

if ($exam->Create()) {
    $created = $exam->getCreated();
    createLog("create exam with id " . $created['id']);
    sendDetails($created, 201);
} else {
    $error = $exam->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}
