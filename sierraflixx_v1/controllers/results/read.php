<?php
require_once "utils/authenticate.php";

$orderBy = "student_id";
$read_all = isset($_GET['param']) && $_GET['param'] === "all";

require_once "controllers/common/read.php";

$format = [
    'exam_id' => [
        "fetch" => [
            'object' => $model,
            'table' => 'exams',
            'is_parent' => true,
            'field' => 'id',
        ],
    ],
    'student_id' => [
        "fetch" => [
            'object' => $model,
            'table' => 'students',
            'is_parent' => true,
            'field' => 'id',
        ],
    ],
    'question_id' => [
        "fetch" => [
            'object' => $model,
            'table' => 'questions',
            'is_parent' => true,
            'field' => 'id',
        ],
    ],
    'answer_id' => [
        "fetch" => [
            'object' => $model,
            'table' => 'answers',
            'is_parent' => true,
            'field' => 'id',
        ],
    ],
];

sendDetails($fetched, 200, [], $format);
