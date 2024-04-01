<?php

namespace WebbyLab;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    public PDO $connection;
    private PDOStatement $stmt;
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__.'/../../config/config.php';
        try {
            $dsn = http_build_query([
              'host' => $this->config['database']['host'],
              'dbname' => $this->config['database']['db']
            ], arg_separator: ';');
            $this->connection = new PDO(
              "mysql:$dsn",
              $this->config['database']['user'],
              $this->config['database']['password'],
              [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
              ]
            );
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

    public function findAll()
    {
        return $this->stmt->fetchAll();
    }
}