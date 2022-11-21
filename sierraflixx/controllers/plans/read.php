<?php

require_once "utils/entry.php";

$helper = $GLOBALS['helper'];
$helper->setTitle('Plan');
$model = new Model($db->getConnection());

$read_all = isset($_GET['param']) && $_GET['param'] === "all";

require_once "controllers/common/read.php";

if (isset($fetched['id'])) {
    $fetched['meta'] = getFeatures($model, $fetched['id']);
} else {
    foreach ($fetched as $key => $value) {
        $fetched[$key]['meta'] = getFeatures($model, $value['id']);
    }
}
sendDetails($fetched, 200);

function getFeatures(Model $model, string $id)
{
    $features = $model->readRelatedWith('plan_features', $id, false, 'plan_id');

    $features = getFeatureDetails($features);

    return $features;
}