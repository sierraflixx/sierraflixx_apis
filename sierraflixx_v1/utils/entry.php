<?php

use Firebase\JWT\JWT;
use Firebase\JWT\KEY;

require_once "helper.php";
session_start();

$token = getToken();

$GLOBALS['authId'] = null;
$GLOBALS['helper'] = new Helper("Entry");

function getToken(): string
{
    /*$auth = " ";

    if (function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    if (isset($headers["Authorization"])) {
    $auth = trim($headers["Authorization"]);
    } else if (isset($headers["authorization"])) {
    $auth = trim($headers["authorization"]);
    }
    } else if ($_SERVER["Authorization"]) {
    $auth = trim($_SERVER["Authorization"]);
    } else if ($_SERVER["HTTP_AUTHORIZATION"]) {
    $auth = trim($_SERVER["HTTP_AUTHORIZATION"]);
    }*/

    if (isset($_SESSION['auth'])) {
        return $_SESSION['auth'];
    }

    return "";
    //return explode(" ", $auth)[1];
}

function createToken(string $Id, string $period = "30 minutes"): string
{
    $issuedAt = new DateTimeImmutable();
    $expire = $issuedAt->modify("+$period")->getTimestamp();

    $serverName = $_SERVER['SERVER_NAME'];

    $payload = [
        'uid' => $Id,
        'exp' => $expire,
        'iss' => $serverName,
        'iat' => $issuedAt->getTimestamp(),
        'nbf' => $issuedAt->getTimestamp(),
    ];

    $token = JWT::encode($payload, $_ENV['API_SECRET'], 'HS256');

    return $token;
}

function verifyToken(string $token)
{
    try {
        $decoded = JWT::decode($token, new Key($_ENV['API_SECRET'], 'HS256'));

        if (!is_null($decoded)) {
            $expired = $decoded->exp - time() < 0;

            if (!$expired) {
                return $decoded;
            }
        }

        return false;
    } catch (\Throwable $exp) {
        return false;
    }
}

function doExclude(array $data, array $excludes): array
{
    foreach ($excludes as $exclude) {
        unset($data[$exclude]);
    }

    return $data;
}

function sendDetails(array $obj, int $code = 200, array $exclude = [], array $format = [])
{
    if (count($exclude) > 0) {
        if (isset($obj['id'])) {
            $obj = doExclude($obj, $exclude);
        } else {
            foreach ($obj as $key => $value) {
                $obj[$key] = doExclude($value, $exclude);
            }
        }
    }

    if (count($format) <= 0) {
        $GLOBALS['helper']->sendData($obj, $code);
    } else {
        $GLOBALS['helper']->sendModify($obj, $format, isset($obj['id']));
    }
}

function setAuthCookie(string $key, $data, float $expires)
{
    setcookie($key, $data, time() + $expires, '/', '/', true, false);
}

function createLog(string $action, string $user_type = "")
{
    $db = new Database();
    $model = new Model($db->getConnection(), "logs");
    $log = $GLOBALS['helper']->populateObject($model, [
        "action" => $action,
        "action_date" => date("y-m-d"),
        "action_time" => date("h:m:sa"),
        "user_id" => $GLOBALS['authId'],
        "action_ip" => $_SERVER['REMOTE_ADDR'],
        "user_type" => empty($user_type) ? (isset($GLOBALS['userType']) ? $GLOBALS['userType'] : "user") : $user_type,
    ]);

    $log->Create();
}