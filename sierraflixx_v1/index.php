<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS');
header('Access-Control-Allow-Headers:  X-Requested-With, Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization');

use Dotenv\Dotenv;

require './vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once 'utils/router.php';
require_once "utils/model.php";
require_once "config/db.config.php";

$GLOBALS["table"] = "";
$router = new Router($_SERVER);
$url = $router->getRequest();

$router->addRoute($url, function () {
    $req = "request";
    $request = $this->$req;
    $path = getResponse($request, $_SERVER['REQUEST_METHOD']);

    if (is_string($path)) {
        if (file_exists($path)) {
            include $path;
        } else {
            getError(404, $request);
        }
    } else {
        getError(404, $request);
    }
});

function getError(int $code, $path = "")
{
    http_response_code($code);
    $method = $_SERVER['REQUEST_METHOD'];
    $path = empty($path) ? $_SERVER['REQUEST_URI'] : $path;

    exit('<!DOCTYPE html>
          <html lang="en">
            <head>
                <meta charset="utf-8">
                <title>Error</title>
            </head>
            <body>
                <pre>Cannot ' . $method . " " . $path . '</pre>
            </body>
          </html>');
}

function getResponse(string $path, string $method)
{
    $action = null;
    $path = strpos($path, '?') ? explode('?', $path)[0] : $path;

    if (strpos($path, '/')) {
        $action = readPath($path);
    } else {
        $res = readPage($method);
        $action = $res ? "$path/$res" : $res;
    }

    $GLOBALS["table"] = explode("/", $action)[0];

    return $action ? "controllers/$action.php" : 501;
}

function readPage(string $method)
{
    switch ($method) {
        case 'GET':
            return 'read';
        case 'POST':
            return 'create';
        case 'PUT':
        case 'PATCH':
            return 'update';
        case 'DELETE':
            return 'delete';
        default:
            return false;
    }
}

function readPath(string $path): string
{
    $goto = "";
    $path_arr = explode('/', $path);

    for ($i = 0; $i < count($path_arr); $i++) {
        $value = $path_arr[$i];

        $goto .= $i >= count($path_arr) - 1 ? $value : "$value/";
    }

    return $goto;
}

$router->run();

exit;