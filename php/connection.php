<?php

/**
 * DatabaseConnection class using postgreSQL
 */
class DatabaseConnection
{
    public function __construct(
        private string $host = 'localhost',
        private string $user = 'farid',
        private string $password = 'admin1234',
        private string $database = 'programacion_web'
    ) {}

    public function executeQuery(string $query, array $params = []): array
    {
        $connection = pg_connect("host={$this->host} dbname={$this->database} user={$this->user} password={$this->password}");

        if (!$connection) {
            throw new Exception('Connection failed: ' . pg_last_error());
        }

        $result = pg_query_params($connection, $query, $params);

        if (!$result) {
            throw new Exception('Query failed: ' . pg_last_error($connection));
        }

        $data = [];
        while ($row = pg_fetch_assoc($result)) {
            $data[] = $row;
        }

        pg_close($connection);

        return $data;
    }

    public function executeInsert(string $query, array $params = []): bool
    {
        $connection = pg_connect("host={$this->host} dbname={$this->database} user={$this->user} password={$this->password}");

        if (!$connection) {
            throw new Exception('Connection failed: ' . pg_last_error());
        }

        $result = pg_query_params($connection, $query, $params);

        if (!$result) {
            throw new Exception('Insert failed: ' . pg_last_error($connection));
        }

        pg_close($connection);

        return true;
    }

    public function executeUpdate(string $query, array $params = []): bool
    {
        $connection = pg_connect("host={$this->host} dbname={$this->database} user={$this->user} password={$this->password}");

        if (!$connection) {
            throw new Exception('Connection failed: ' . pg_last_error());
        }

        $result = pg_query_params($connection, $query, $params);

        if (!$result) {
            throw new Exception('Update failed: ' . pg_last_error($connection));
        }

        pg_close($connection);

        return true;
    }

    public function executeDelete(string $query, array $params = []): bool
    {
        $connection = pg_connect("host={$this->host} dbname={$this->database} user={$this->user} password={$this->password}");

        if (!$connection) {
            throw new Exception('Connection failed: ' . pg_last_error());
        }

        $result = pg_query_params($connection, $query, $params);

        if (!$result) {
            throw new Exception('Delete failed: ' . pg_last_error($connection));
        }

        pg_close($connection);

        return true;
    }
}
