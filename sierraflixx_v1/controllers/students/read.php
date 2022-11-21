<?php
require_once "utils/authenticate.php";

$read_all = isset($_GET['param']) && $_GET['param'] === "all";

require_once "controllers/common/read.php";

$format = [
    'program_id' => [
        "fetch" => [
            'object' => $model,
            'table' => 'programs',
            'is_parent' => true,
            'field' => 'id',
        ],
    ],
];

sendDetails($fetched, 200, ['secret', 'role'], $format);
