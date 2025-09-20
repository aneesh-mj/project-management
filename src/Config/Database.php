<?php
namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private $host = 'localhost';
    private $port = '8889'; // MAMP default port
    private $dbname = 'project-management';
    private $username = 'root';
    private $password = 'F1Gi-3xgnTvXpL0Q'; // MAMP default password
    private $connection;

    public function getConnection()
    {
        $this->connection = null;
        
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Connection error: " . $e->getMessage());
        }
        
        return $this->connection;
    }
}