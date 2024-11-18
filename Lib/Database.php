<?php

class DatabaseConnection {
    private PDO | null $pdo;

    private string $dsn = 'mysql:host=db;port=3306;dbname=smarthaven';
    private string $username = 'smarthaven';
    private string $password = 'smarthaven';

    private function openConnection() {
        $this->pdo = new PDO($this->dsn, $this->username, $this->password);
    }

    private function closeConnection(): void{
        $this->pdo = null;
    }

    /**
     * @throws Exception
     */
    public function query(string $query): array | object {
        $this->openConnection();

        $statement = $this->pdo->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();

        $this->closeConnection();

        if ($result === false) {
            throw new Exception('Error fetching data');
        }

        if (count($result) === 0) {
            throw new Exception('No data found');
        }

        return $result;
    }

    public function queryWithParams(string $query, array $params): array {
        $this->openConnection();
    
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    
        $this->closeConnection();
    
        if ($result === false) {
            throw new Exception('Error fetching data');
        }
    
        return $result;
    }
    
    public function executeQuery(string $query, array $params): void {
        $this->openConnection();
    
        $statement = $this->pdo->prepare($query);
        if (!$statement->execute($params)) {
            throw new Exception('Error executing query');
        }
    
        $this->closeConnection();
    }
}