<?php

require_once "utils/authenticate.php";

$helper->setTitle("Response");
$model = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'exam' => 'required',
    'responses' => 'required',
]);
$created = true;
$responses = json_decode($posted['responses'], true);

$model->transaction("begin");

foreach ($responses as $res) {
    $response = $helper->populateObject($model, [
        "exam_id" => $posted['exam'],
        "answer_id" => $res['answer'],
        "question_id" => $res['question'],
        "student_id" => $GLOBALS['authId'],
    ], true);

    if (!$response->Create()) {
        $created = false;
        break;
    }
}

if ($created) {
    $model->transaction("commit");
    $helper->showMessage(201, "submitted successfully");
} else {
    $model->transaction("rollback");
    $error = $model->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}
