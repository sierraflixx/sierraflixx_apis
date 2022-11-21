<?php

require_once "utils/authenticate.php";

$helper->setTitle("Answer");
$model = new Model($db->getConnection());

$posted = $helper->validateData($_POST, [
    'answer' => 'required',
    'question' => 'required',
    'letter' => 'required',
    'correct' => 'required',
]);

$answer = $helper->populateObject($model, [
    "answer" => $posted['answer'],
    "is_correct" => $posted['correct'],
    "question_id" => $posted['question'],
    "option_letter" => $posted['letter'],
], true);

if ($answer->Create()) {
    $created = $answer->getCreated();
    createLog("create answer with id " . $created['id']);

    sendDetails($created, 201);
} else {
    $error = $answer->getErrorDetails();
    $helper->showMessage($error['code'], $error['message']);
}
