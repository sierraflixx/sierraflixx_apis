<?php

require_once "utils/authenticate.php";

$helper->setTitle("Student");
$model = new Model($db->getConnection());

$got = $helper->validateData($_GET, [
    "id" => "required",
]);

$fetched = $model->Read($got['id']);

if (count($fetched) > 0) {

    $posted = $helper->validateData($_POST, [
        'year' => 'optional',
        'exam' => 'optional',
        'index' => 'optional',
        'program' => 'optional',
        'fullName' => 'optional',
    ]);

    $student = $helper->populateObject($model, [
        'name' => $helper->setIfContained("fullName", $posted, $fetched['name']),
        'exam_id' => $helper->setIfContained("exam", $posted, $fetched['exam_id']),
        'wassce_year' => $helper->setIfContained("year", $posted, $fetched['wassce_year']),
        'program_id' => $helper->setIfContained("program", $posted, $fetched['program_id']),
        'wassce_number' => $helper->setIfContained("index", $posted, $fetched['wassce_number']),
    ]);

    if ($student->Update($got['id'])) {
        $updated = $student->Read($got['id']);
        createLog("updated student with id " . $updated['id']);

        sendDetails($updated, 200, ['secret', 'role']);
    } else {
        $error = $student->getErrorDetails();
        $helper->showMessage($error['code'], $error['message']);
    }
}
$helper->showMessage(404);
