<?php
declare(strict_types=1);

namespace Lib;

use PDO;
use Exception;

class DatabaseConnection {
    private ?PDO $pdo;

    private string $dsn = 'mysql:host=db;port=3306;dbname=smarthaven';
    private string $username = 'smarthaven';
    private string $password = 'smarthaven';

    private function openConnection() {
        $this->pdo = new PDO($this->dsn, $this->username, $this->password);
    }

    private function closeConnection(): void {
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

        if ($result === false) {
            throw new Exception('Error fetching data');
        }

        return $result;
    }

    public function execute(string $query, array $params = []): void {
        $this->openConnection();

        $statement = $this->pdo->prepare($query);
        $statement->execute($params);

        $this->closeConnection();
    }
}
?>
