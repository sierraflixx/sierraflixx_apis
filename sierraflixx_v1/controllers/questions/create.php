<?php

require_once "utils/authenticate.php";

$helper->setTitle("Question");
$model = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'question' => 'required',
    'exam' => 'required',
    'serial' => 'required',
    'score' => 'required',
]);

$question = $helper->populateObject($model, [
    "score" => $posted['score'],
    "exam_id" => $posted['exam'],
    "serial" => $posted['serial'],
    "question" => $posted['question'],
], true);

if ($question->Create()) {
    $created = $question->getCreated();
    createLog("create question with id " . $created['id']);

    sendDetails($created, 201);
} else {
    $error = $question->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}
