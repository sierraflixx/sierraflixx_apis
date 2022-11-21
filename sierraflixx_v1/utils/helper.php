<?php

$db = new Database();
$client_ip = $_SERVER['REMOTE_ADDR'];

if (isset($_SERVER['HTTP_CLIENT_IP'])) {
    $client_ip = $_SERVER['HTTP_CLIENT_IP'];
} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}

class Helper
{
    private $title;

    public function __construct($title)
    {
        $this->title = $title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function setTitlePrefix(int $prefix)
    {
        $pluralise = $prefix > 1 ? "$this->title" . "s" : $this->title;
        $this->title = "$prefix $pluralise";
    }

    public function sendData(array $data, $code = 200): void
    {
        $this->sendResponse($data, $code);
    }

    public function showMessage(int $code, string $message = "")
    {

        if ($code !== 500) {

            $messages = [
                '201' => " created",
                '404' => " not found",
                '204' => " not created",
                '409' => " already created",
                '503' => " is not available",
            ];

            $code_message = isset($messages[$code]) ? $messages[$code] : '';
            $message = $this->title . (empty($message) ? $code_message : $message);
        }

        $this->sendResponse($message, $code);
    }

    private function Formats(array $value, array $formats): array
    {
        foreach ($formats as $uid => $res) {
            foreach ($res as $param => $val) {
                if (isset($value[$uid])) {
                    if (is_null($value[$uid]) || empty($value[$uid])) {
                        $value[$uid] = $value[$uid];
                    } else {
                        switch ($param) {
                            case 'fetch':
                                $related = $val['object']->readRelatedWith($val['table'], $value[$uid], $val['is_parent'], $val['field']);
                                if ($val['table'] === 'admins') {
                                    $single = isset($related['id']);

                                    if ($single) {
                                        unset($related['password']);
                                    } else if (is_array($related)) {
                                        foreach ($related as $key => $value) {
                                            unset($value['password']);

                                            $related[$key] = $value;
                                        }
                                    }
                                }

                                if (strpos($uid, "id") > -1) {
                                    unset($value[$uid]);
                                    $uid = explode("_", $uid)[0];
                                }

                                $value[$uid] = $related;
                                break;
                            default:
                                $value[$uid] = $value[$uid];
                                break;
                        }
                    }
                }
            }
        }

        return $value;
    }

    private function sendResponse($res, $code): void
    {
        http_response_code($code);
        $key = is_string($res) ? "message" : "data";
        $response = is_string($res) ? [
            $key => $res,
            'success' => $code < 400,
        ] : $res;

        exit(json_encode($response));
    }

    public function sendModify(array $data, array $formats, bool $is_single): void
    {
        if ($is_single) {
            $data = $this->Formats($data, $formats);
        } else {
            foreach ($data as $key => $value) {
                $data[$key] = $this->Formats($value, $formats);
            }
        }

        $this->sendResponse($data, 200);
    }

    private function showValidationError(string $message = ''): void
    {
        $message = empty($message) ? "field(s) not completed" : $message;

        if (strpos($message, "_") > -1) {
            $message = str_replace("_", " ", $message);
        }

        $this->showMessage(400, "'" . $message . "' is required");
    }

    private function fileValidation(string $key)
    {
        if (isset($_FILES[$key]) && !empty($_FILES[$key]['name'])) {
            return [true, $_FILES[$key]];
        }

        $this->showValidationError('file(s) not completed');
    }

    private function typeValidation(array $data, string $key, string $type): bool
    {
        $valid = false;

        if (isset($data[$key]) && !empty($data[$key])) {
            $value = $data[$key];

            switch ($type) {
                case 'int':
                    $valid = intval($value);
                    break;
                case 'float':
                    $valid = floatval($value);
                    break;
                case 'string':
                default:
                    $valid = is_string($value);
                    break;
            }
        }

        return $valid;
    }

    public function validateData($data, $required): array
    {
        foreach ($required as $key => $value) {
            $is_array = is_array($value);
            $validation = $is_array ? $value[1] : $value;

            if ($validation === 'required') {
                $completed = false;

                /* validate for completion */ {
                    if ($is_array) {
                        $data_type = $value[0];

                        if ($data_type === 'file') {
                            $completed = $this->fileValidation($key)[0];
                            $data[$key] = $this->fileValidation($key)[1];
                        } else {
                            $completed = $this->typeValidation($data, $key, $data_type);
                        }
                    } else {
                        $completed = isset($data[$key]) && !empty($data[$key]);
                    }
                }

                if (!$completed) {
                    $this->showValidationError($key);
                }
            } else if ($is_array) {
                $is_file = $value[0] === 'file' && isset($_FILES[$key]);

                if ($is_file) {
                    $has_image = !empty($_FILES[$key]['name']);

                    if ($has_image) {
                        $data[$key] = $_FILES[$key];
                    }
                }
            }
        }

        return $data;
    }

    public function populateObject(Model $object, array $data, bool $attach_id = false, array $excepts = []): Model
    {
        if ($attach_id) {
            if (isset($data['id'])) {
                unset($data['id']);
            }

            $id = "id";
            $object->$id = bin2hex(random_bytes(16));
        }
        foreach ($data as $key => $value) {
            $is_photo = strpos($key, 'avatar');
            $is_password = strpos($key, 'password');

            if (is_int($is_password)) {
                $value = password_hash($data['password'], PASSWORD_BCRYPT);
            }

            if (!isset($excepts[$key])) {
                $object->$key = $value;
            }
        }

        return $object;
    }

    public function populateObjectArray(array $objects, array $data): array
    {
        $array = [];
        foreach ($objects as $key => $value) {
            if (isset($data[$key]) && !empty($data[$key])) {
                $array[$key] = $data[$key];
            }
        }

        return $array;
    }

    public function setIfContained(string $key, array $source, $alternative)
    {
        $data = $alternative;

        if (isset($source[$key]) && !empty($source[$key])) {
            $data = $source[$key];
        }

        return $data;
    }

    public function hasContent(string $key, array $data): bool
    {
        return isset($data[$key]) && !empty($data[$key]);
    }
}