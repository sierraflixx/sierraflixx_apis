<?php

class Model
{
    private $table;
    private $conn;
    private $error;
    private $created;

    public function __construct(PDO $conn, string $table = "")
    {
        $this->conn = $conn;
        $this->created = [];
        $this->table = empty($table) ? $GLOBALS["table"] : $table;
    }

    private function cleanData($value)
    {
        $_value = htmlspecialchars(trim($value));

        return $_value;
    }

    /* Get MySQL error */
    public function getError()
    {
        $error = $this->error;
        $error = strpos($error, '(') ? explode('(', $error)[0] : $error;
        $error = strpos($error, ':') ? explode(":", $error) : $error;
        $error = is_array($error) ? trim($error[count($error) - 1]) : trim($error);
        $code = explode(' ', $error)[0];
        $error = intval($code) > 0 ? trim(substr($error, strlen($code))) : $error;
        $error = ucfirst($error);

        return $error ? trim($error) : '';
    }
    public function getErrorDetails()
    {
        $match = [];
        $code = 500;
        $err = $this->error;
        $error = $this->getError();
        preg_match("/([A-Za-z0-9\/]+)\_id/", $err, $match);

        $match = !empty($match) ? " for this " . $match[array_key_last($match)] : "";

        if (strpos($err, "1062") > -1) {
            $code = 409;
            $error = "already created$match";

        } else if (strpos($err, "1452") > -1) {
            $code = 404;
            $error = "$match not found.";
        } else if (strpos($err, "1054") > -1) {
            $code = 400;
            $error = "has no property $match";
        }

        return ['code' => $code, 'message' => $error];
    }

    /* Generate query string */
    private function getQueryString(array $object = []): string
    {
        $index = 1;
        $query = "";

        if (empty($object)) {
            $reflected = new ReflectionObject($this);
            $public = $reflected->getProperties(ReflectionProperty::IS_PUBLIC);

            foreach ($public as $prop) {
                $key = $prop->getName();
                $query .= "$key=:" . strtolower($key) . ($index < count($public) ? ", " : "");

                $index += 1;
            }
        } else {
            foreach ($object as $key => $value) {

                $query .= "$key=:" . strtolower($key) . ($index < count($object) ? ", " : "");

                $index += 1;
            }
        }

        return $query;
    }
    private function getWhereQueryString(array $fields): string
    {
        $index = 1;
        $query = "WHERE ";

        foreach ($fields as $value) {
            $field_value = $value['value'];
            $field = $value['field'];
            $sign = isset($value['sign']) ? strtoupper($value['sign']) : "=";
            $divider = isset($value['divider']) ? strtoupper($value['divider']) : "";

            if (is_array($field_value)) {
                $query .= "$field $sign :$field $divider";

                for ($i = 1; $i < count($field_value); $i++) {
                    $query .= " :$field" . "_$i " . ($i < count($field_value) - 1 ? "$divider" : "");
                }
            } else {
                $query .= "$field $sign :$field " . ($index < count($fields) ? "$divider " : "");
            }

            $index += 1;
        }

        return trim($query);
    }

    /* Populate parameters in PDOStatement */
    private function setParam(PDOStatement $stmt, array $object = []): PDOStatement
    {
        if (empty($object)) {
            $reflected = new ReflectionObject($this);
            $public = $reflected->getProperties(ReflectionProperty::IS_PUBLIC);

            foreach ($public as $prop) {
                $value = $prop->getValue($this);
                $key = strtolower($prop->getName());
                $stmt->bindValue(":$key", $this->cleanData($value));
            }
        } else {
            foreach ($object as $key => $value) {
                $stmt->bindValue(":$key", $this->cleanData($value));
            }
        }

        return $stmt;
    }
    private function setObjectParam(PDOStatement $stmt, array $fields): PDOStatement
    {
        foreach ($fields as $field) {
            $key = $field['field'];
            $value = $field['value'];

            if (is_array($value)) {
                for ($i = 0; $i < count($value); $i++) {
                    $stmt->bindValue(":$key" . ($i < 1 ? '' : "_$i"), $this->cleanData($value[$i]));
                }
            } else {
                $stmt->bindValue(":$key", $this->cleanData($value));
            }
        }

        return $stmt;
    }

    /* Check record exits */
    public function Exist($value, string $field = "id", string $table = ""): bool
    {
        $table = empty($table) ? $this->table : $table;

        $sql = "SELECT * FROM $table WHERE `$field`=:val";

        try {
            if ($stmt = $this->conn->prepare($sql)) {
                $stmt->bindParam(":val", $value);

                if ($stmt->execute()) {
                    return $stmt->rowCount() > 0;
                }
            }
        } catch (\Throwable$th) {
            $this->error = $th->getMessage();
        }

        return false;
    }

    /* Get last inserted record id */
    public function getCreated(): array
    {
        return $this->created;
    }

    /* Get last inserted record id */
    private function getData(PDOStatement $stmt, bool $is_many = false): array
    {

        $count = $stmt->rowCount();

        if ($count > 0) {
            if ($count > 1 || $is_many) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return [];

    }

    /* Get to start a transaction for an action */
    public function transaction(string $action)
    {
        switch ($action) {
            case 'begin':
                $this->conn->beginTransaction();
                break;
            case 'commit':
                $this->conn->commit();
                break;
            case 'rollback':
            default:
                $this->conn->rollBack();
                break;
        }
    }

    /* Create */
    public function Create(): bool
    {
        $sql = "INSERT INTO $this->table SET " . $this->getQueryString();

        try {
            $stmt = $this->setParam($this->conn->prepare($sql));

            $created = $stmt->execute() > 0;

            $key = "id";
            $this->created = $this->Read(isset($this->$key) ? $this->$key : $this->conn->lastInsertId());

            return $created;
        } catch (\Throwable$th) {
            $this->error = $th->getMessage();
        }

        return false;
    }

    /* Read */
    public function Read($value, $field = "id", $multiple = false): array
    {
        $sql = "SELECT * FROM $this->table WHERE `$field`=:val";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":val", $value);
            $stmt->execute();

            return $this->getData($stmt, $multiple);

        } catch (\Throwable$th) {
            $this->error = $th->getMessage();
            return [];
        }
    }
    public function readAll($field = "id", $type = "ASC"): array
    {
        $sql = "SELECT * FROM $this->table ORDER BY $field $type";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $this->getData($stmt, true);
        } catch (\Throwable$th) {
            $this->error = $th->getMessage();

            return [];
        }
    }

    public function readRelatedWith(string $table, $value, bool $is_parent = false, string $key = "id")
    {
        $sql = "SELECT * FROM $table WHERE `$key`=:val";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":val", $value);
            $stmt->execute();

            return $this->getData($stmt, !$is_parent);
        } catch (\Throwable$th) {
            $this->error = $th->getMessage();

            return [];
        }
    }
    /* data required schema
    $fields = [
    [
    "sign" => "=",
    "value" => "admin",
    "field" => "title",
    "divider" => "and",
    ],
    [
    "sign" => "between",
    "value" => ['2022-09-20', '2022-09-30'],
    "field" => "created_date",
    "divider" => "and",
    ],
    ];*/
    public function readFromSearch(array $fields, string $table = "")
    {
        $table = empty($table) ? $this->table : $table;
        $sql = "SELECT * FROM $table " . $this->getWhereQueryString($fields);

        try {
            $stmt = $this->setObjectParam($this->conn->prepare($sql), $fields);

            $stmt->execute();

            return $this->getData($stmt);
        } catch (\Throwable$th) {
            $this->error = $th->getMessage();

            return [];
        }
    }

    /* Update */
    public function Update($value = 0, $field = "id"): bool
    {
        $_this = (array) $this;
        $value = $value > 0 ? $value : $_this[$field];
        $sql = "UPDATE $this->table SET " . $this->getQueryString() . " WHERE `$field`=:val";

        try {
            $stmt = $this->setParam($this->conn->prepare($sql));
            $stmt->bindParam(":val", $value);

            return $stmt->execute() > 0;
        } catch (\Throwable$th) {
            $this->error = $th->getMessage();
        }

        return false;
    }
    public function updateOther(string $table, array $fields, $value, string $field = "id"): bool
    {
        $sql = "UPDATE $table SET " . $this->getQueryString($fields) . " WHERE `$field`=:val";

        try {
            $stmt = $this->setParam($this->conn->prepare($sql), $fields);
            $stmt->bindParam(":val", $value);

            return $stmt->execute() > 0;
        } catch (\Throwable$th) {
            $this->error = $th->getMessage();
        }

        return false;
    }
    public function updateAll(): bool
    {
        $_this = (array) $this;

        $sql = "UPDATE $this->table SET " . $this->getQueryString() . " WHERE 1=1";

        try {
            $stmt = $this->setParam($this->conn->prepare($sql));

            return $stmt->execute() > 0;
        } catch (\Throwable$th) {
            $this->error = $th->getMessage();
        }

        return false;
    }
    public function updateExcept($exp, $field = "id"): bool
    {
        $_this = (array) $this;
        $exp = $exp > 0 ? $exp : $_this[$field];
        $sql = "UPDATE $this->table SET " . $this->getQueryString() . " WHERE `$field`!=:exp";

        try {
            $stmt = $this->setParam($this->conn->prepare($sql));
            $stmt->bindParam(":exp", $exp);

            return $stmt->execute() > 0;
        } catch (\Throwable$th) {
            $this->error = $th->getMessage();
        }

        return false;
    }

    /* Delete */
    public function Delete($value, $field = "id"): bool
    {
        $sql = "DELETE FROM $this->table WHERE $field=:val";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":val", $value);

            return $stmt->execute() > 0;
        } catch (\Throwable$th) {
            $this->error = $th->getMessage();
        }

        return false;
    }
    public function deleteAll(): bool
    {
        $sql = "DELETE FROM $this->table";

        try {
            return $this->conn->prepare($sql)->execute() > 0;
        } catch (\Throwable$th) {
            $this->error = $th->getMessage();
        }

        return false;
    }
    public function deleteTemporal($data, $value, $field = "id"): bool
    {
        $sql = "UPDATE $this->table SET deleted=:act, deleted_by=:auth, deleted_ip=:ip, deleted_date=:ddate, deleted_time=:dtime WHERE `$field`=:val";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":val", $value);
            $stmt->bindParam(":ip", $data['ip']);
            $stmt->bindParam(":auth", $data['auth']);
            $stmt->bindParam(":act", $data['trashed']);
            $stmt->bindParam(":ddate", $data['date']);
            $stmt->bindParam(":dtime", $data['time']);

            return $stmt->execute() > 0;
        } catch (\Throwable$th) {
            $this->error = $th->getMessage();
        }

        return false;
    }
    public function deleteAllTemporal(array $data): bool
    {
        $sql = "UPDATE $this->table SET deleted=:act, deleted_by=:auth, deleted_ip=:ip, deleted_date=:ddate, deleted_time=:dtime WHERE 1=1";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":ip", $data['ip']);
            $stmt->bindParam(":auth", $data['auth']);
            $stmt->bindParam(":act", $data['trashed']);
            $stmt->bindParam(":ddate", $data['date']);
            $stmt->bindParam(":dtime", $data['time']);

            $stmt->execute() > 0;
        } catch (\Throwable$th) {
            $this->error = $th->getMessage();
        }

        return false;
    }
}
