<?php

declare(strict_types=1);

class Database
{
    private string $host = "localhost";
    private string $driver = "mysql";
    private string $dbName = "pp_blogging_platform";
    private string $user = "root";
    private string $pass = "";

    public PDO $dbh;
    public $stmt;
    public $error;

    public function __construct()
    {
        $dsn = "{$this->driver}:host={$this->host};dbname={$this->dbName}";

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo "PDO Exception: " . $e->getMessage();
        }
    }

    public function query(string $sql)
    {
        $this->stmt = $this->dbh->prepare($sql);
    }

    public function bind(string|int $param, mixed $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute(array $data = []) : mixed
    {
        return $this->stmt->execute($data);
    }

    public function fetchAll(array $data = []) : array|bool
    {
        $this->stmt->execute($data);
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchOne(array $data = []) : array|bool
    {
        $this->stmt->execute($data);
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function rowCount() : int
    {
        return $this->stmt->rowCount();
    }
}