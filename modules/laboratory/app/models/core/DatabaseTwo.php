<?php

class DatabaseTwo
{
    private static $instance = null;
    protected $conn;

    private function __construct()
    {
        $this->conn = new PDO(
            "mysql:host=localhost;dbname=sms;charset=utf8",
            "root",
            "",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function begin()
    {
        $this->conn->beginTransaction();
    }

    public function commit()
    {
        $this->conn->commit();
    }

    public function rollback()
    {
        $this->conn->rollBack();
    }

    public function lastInsertId()
    {
        return $this->conn->lastInsertId();
    }
}
