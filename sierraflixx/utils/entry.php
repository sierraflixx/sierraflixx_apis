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
    if (isset($_SESSION['auth'])) {
        return $_SESSION['auth'];
    }

    return "";
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

function setAuthCookie(string $id)
{
    setcookie('memcid', $id, time() + (365 * 24 * 60 * 60), '/');
}


function getSlug(string $str)
{
    return strtolower(str_replace(' ', '-', $str));
}

function getFeatureDetails(array $data)
{
    if (isset($data['id'])) {
        $data['value'] = getRefineValue($data['value']);
    } else {
        foreach ($data as $key => $value) {
            $data[$key]['value'] = getRefineValue($value['value']);
        }
    }

    return $data;
}

function getRefineValue(string $value)
{
    $decoded = htmlspecialchars_decode($value);

    return json_decode($decoded);
}

function getImage(string $key)
{
    if (!isset($_FILES[$key]) && empty($_FILES[$key]['name'])) return null;

    $image = file_get_contents($_FILES[$key]['tmp_name']);

    return base64_encode($image);
}

function routeUser(array $user)
{
    $location = '/';

    if ($user['has_password'] === 'true') {
        if ($user['']) {
        }
    } else {
        $location = '/signup/password';
    }

    header("Location: $location");
}

function verifyUser1(Model $model, string $phone, string $email = '')
{
    $db = new Database();
    $auth = $_COOKIE['memcid'];
    $helper = $GLOBALS['helper'];
    $model = new Model($db->getConnection(), 'cookies');


    if (!$auth) {
        $fetched = $model->readFromSearch([
            [
                "sign" => "=",
                "value" => $email,
                "field" => "email",
                "divider" => "or",
            ],
            [
                "sign" => "=",
                "value" => $phone,
                "field" => "phone",
            ],
        ], 'accounts');

        if (count($fetched) <= 0) {

            $cookie = $helper->populateObject($model, [
                'ref' => $phone,
                'token' => createToken($fetched['id'], '1 years'),
            ], true);

            $cookie->Create();
            $created = $cookie->getCreated();

            setcookie('memcid', $created['id'], time() * (365 * 24 * 60 * 60), '/', '/', true, false);
        }
    }
}

function verifyUser(Model $model, string $phone, string $email = '')
{
    $db = new Database();
    $auth = $_COOKIE['memcid'];
    $helper = $GLOBALS['helper'];
    $model = new Model($db->getConnection(), 'cookies');


    if (!$auth) {
        $fetched = $model->readFromSearch([
            [
                "sign" => "=",
                "value" => $email,
                "field" => "email",
                "divider" => "or",
            ],
            [
                "sign" => "=",
                "value" => $phone,
                "field" => "phone",
            ],
        ], 'accounts');

        if (count($fetched) > 0) {

            $cookie = $model->Read($fetched['phone'], 'ref');

            setcookie('memcid', $cookie['id'], time() * (365 * 24 * 60 * 60), '/', '/', true, false);
        }
    }
}