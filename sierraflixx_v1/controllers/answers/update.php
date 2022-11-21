<?php

require_once "utils/authenticate.php";

$helper->setTitle("Answer");
$model = new Model($db->getConnection());

$got = $helper->validateData($_GET, [
    "id" => "required",
]);

$fetched = $model->Read($got['id']);

if (count($fetched) > 0) {

    $posted = $helper->validateData($_POST, [
        'answer' => 'optional',
        'question' => 'optional',
        'letter' => 'optional',
        'correct' => 'optional',
    ]);

    $question = $helper->populateObject($model, [
        'answer' => $helper->setIfContained("answer", $posted, $fetched['answer']),
        'is_correct' => $helper->setIfContained("correct", $posted, $fetched['is_correct']),
        'question_id' => $helper->setIfContained("question", $posted, $fetched['question_id']),
        'option_letter' => $helper->setIfContained("letter", $posted, $fetched['option_letter']),
    ]);

    if ($question->Update($got['id'])) {
        $updated = $question->Read($got['id']);
        createLog("updated answer with id " . $updated['id']);

        sendDetails($updated, 200);
    } else {
        $error = $question->getErrorDetails();
        $helper->showMessage($error['code'], $error['message']);
    }
}
$helper->showMessage(404);
