<?php
class Database
{
    private $connection;

    public function __construct()
    {
        $this->connection = null;
        $db = $_ENV['DB_NAME'];
        $pass = $_ENV['DB_PASS'];
        $user = $_ENV['DB_LOGIN'];

        try {
            $this->connection = new PDO("mysql:host=localhost;dbname=$db", $user, $pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $error) {
            echo $error->getMessage();
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
