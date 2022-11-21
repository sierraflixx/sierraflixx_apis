<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('Plan Feature');
$model = new Model($db->getConnection());

$read_all = isset($_GET['param']) && $_GET['param'] === "all";

require_once "controllers/common/read.php";

$format = [
    'plan_id' => [
        "fetch" => [
            'object' => $model,
            'table' => 'plans',
            'is_parent' => true,
            'field' => 'id',
        ],
    ],
    'feature_id' => [
        "fetch" => [
            'object' => $model,
            'table' => 'features',
            'is_parent' => true,
            'field' => 'id',
        ],
    ],
];

$fetched = getFeatureDetails($fetched);


sendDetails($fetched, 200, [], $format);