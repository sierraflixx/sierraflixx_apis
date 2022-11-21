<?php

require 'entry.php';
$helper = $GLOBALS['helper'];
$helper->setTitle("Unauthorised:");
$authorised = verifyToken($token);

if ($authorised) {
    $uid = $authorised->uid;
    $auths = ['users', 'students'];

    if (!authenticate($uid, $db, $auths)) {
        $helper->showMessage(401, "valid credentials required");
    }
} else {
    $helper->showMessage(401, "valid credentials required");
}

function authenticate($uid, Database $db, array $auths): bool
{
    $model = null;
    $conn = $db->getConnection();

    foreach ($auths as $auth) {
        $authModel = new Model($conn, $auth);

        if ($authModel->Exist($uid)) {
            $model = $authModel;
            break;
        }
        unset($authModel);
    }

    if (gettype($model) !== "object") {
        return false;
    }

    $GLOBALS['authId'] = $uid;

    return hasPrivilegeRight($model);
}

function hasPrivilegeRight(Model $user): bool
{
    $table = $GLOBALS['table'];
    $auth = $user->Read($GLOBALS["authId"]);
    $privilege = $user->readFromSearch([
        [
            "sign" => "=",
            "value" => $auth['role'],
            "field" => "role",
            "divider" => "and",
        ],
        [
            "sign" => "=",
            "value" => $table,
            "field" => "endpoint",
        ],
    ], "privileges");

    if (count($privilege) > 0) {
        $actions = [
            "GET" => "read",
            "POST" => "create",
            "DELETE" => "delete",
        ];
        $action = $actions[$_SERVER['REQUEST_METHOD']];
        $req_arr = explode("/", $_SERVER['REQUEST_URI']);
        $req_path = explode("?", $req_arr[count($req_arr) - 1])[0];

        if ($req_path !== $table && $_SERVER['REQUEST_METHOD'] === "POST") {

            $action = "update";
        }

        if ($privilege["can_$action"] === "true") {
            $GLOBALS['userType'] = $auth['role'];
            return true;
        }
    }

    return false;
}
