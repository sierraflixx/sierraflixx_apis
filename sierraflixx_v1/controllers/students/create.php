<?php

require_once "utils/authenticate.php";

$helper->setTitle("Student");
$model = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'year' => 'required',
    'exam' => 'required',
    'index' => 'required',
    'program' => 'required',
    'fullName' => 'required',
]);

$student = $helper->populateObject($model, [
    "exam_id" => $posted['exam'],
    "name" => $posted['fullName'],
    "wassce_year" => $posted['year'],
    "program_id" => $posted['program'],
    "wassce_number" => $posted['index'],
    "id" => bin2hex(random_int(1e6, 9e6)),
]);

if ($student->Create()) {
    $created = $student->getCreated();
    createLog("create student with id " . $created['id']);

    sendDetails($created, 201, ['secret', 'role']);
} else {
    $error = $student->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}
