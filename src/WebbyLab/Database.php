<?php

namespace WebbyLab;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    public PDO $connection;
    private PDOStatement $stmt;

    public function __construct()
    {
        try {
            $this->connection = new PDO("mysql:host=127.0.0.1;dbname=webbylab", 'root', '');
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection failed: '.$e->getMessage();
        }
    }

    public function query(string $query, array $params = []): Database
    {
        $this->stmt = $this->connection->prepare($query);

        $this->stmt->execute($params);

        return $this;
    }

    public function count()
    {
        return $this->stmt->fetchColumn();
    }

    public function id()
    {
        return $this->connection->lastInsertId();
    }

    public function find()
    {
        return $this->stmt->fetch();
    }
}