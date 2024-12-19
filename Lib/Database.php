<?php

class DatabaseConnection {
    private PDO | null $pdo = null;

    private string $dsn = 'mysql:host=db;port=3306;dbname=smarthaven';
    private string $username = 'smarthaven';
    private string $password = 'smarthaven';

    public function getPdo(): PDO {
        if ($this->pdo === null) {
            $this->pdo = new PDO($this->dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return $this->pdo;
    }

    public function query(string $query): array {
        $pdo = $this->getPdo();
        $statement = $pdo->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        return $result ?: [];
    }
}
