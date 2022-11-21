<?php
require_once "utils/authenticate.php";

$read_all = isset($_GET['param']) && $_GET['param'] === "all";

require_once "controllers/common/read.php";

$format = [
    'question_id' => [
        "fetch" => [
            'object' => $model,
            'table' => 'questions',
            'is_parent' => true,
            'field' => 'id',
        ],
    ],
];

sendDetails($fetched, 200, [], $format);
