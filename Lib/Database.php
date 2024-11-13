<?php

class DatabaseConnection {
    private PDO | null $pdo;

    private string $dsn = 'mysql:host=db;port=3306;dbname=smarthaven';
    private string $username = 'smarthaven';
    private string $password = 'smarthaven';

    private function openConnection() {
        try {
            $this->pdo = new PDO($this->dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception('Connection failed: ' . $e->getMessage());
        }
    }
    

    private function closeConnection(): void{
        $this->pdo = null;
    }

    /**
     * @throws Exception
     */
    
    public function query(string $query, array $params = []): array {
        $this->openConnection();
        
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        $this->closeConnection();
    
        return $result ?: [];
    }
    
    
}