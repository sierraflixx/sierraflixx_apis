<?php

require_once "utils/authenticate.php";

$helper->setTitle("Question");
$model = new Model($db->getConnection());

$got = $helper->validateData($_GET, [
    "id" => "required",
]);

$fetched = $model->Read($got['id']);

if (count($fetched) > 0) {

    $posted = $helper->validateData($_POST, [
        'question' => 'optional',
        'exam' => 'optional',
        'serial' => 'optional',
        'score' => 'optional',
    ]);

    $question = $helper->populateObject($model, [
        'score' => $helper->setIfContained("score", $posted, $fetched['score']),
        'exam_id' => $helper->setIfContained("exam", $posted, $fetched['exam_id']),
        'serial' => $helper->setIfContained("serial", $posted, $fetched['serial']),
        'question' => $helper->setIfContained("question", $posted, $fetched['question']),
    ]);

    if ($question->Update($got['id'])) {
        $updated = $question->Read($got['id']);
        createLog("updated question with id " . $updated['id']);

        sendDetails($updated, 200);
    } else {
        $error = $question->getErrorDetails();
        $helper->showMessage($error['code'], $error['message']);
    }
}
$helper->showMessage(404);
