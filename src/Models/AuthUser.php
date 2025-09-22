<?php
namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

class AuthUser
{
    private $connection;
    private $table = 'Users';

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function emailExists(string $email): bool
    {
        try {
            $query = "SELECT 1 FROM {$this->table} WHERE email = :email LIMIT 1";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new PDOException('Error checking email: ' . $e->getMessage());
        }
    }

    public function register(array $data): array
    {
        try {
            $query = "INSERT INTO {$this->table} (company_id, email, password_hash, first_name, last_name)
                      VALUES (:company_id, :email, :password_hash, :first_name, :last_name)";
            $stmt = $this->connection->prepare($query);

            $stmt->bindParam(':company_id', $data['company_id'], PDO::PARAM_INT);
            $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindParam(':password_hash', $data['password_hash'], PDO::PARAM_STR);
            $stmt->bindParam(':first_name', $data['first_name'], PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $data['last_name'], PDO::PARAM_STR);

            $stmt->execute();

            return [
                'user_id' => $this->connection->lastInsertId(),
                'company_id' => (int)$data['company_id'],
                'email' => $data['email'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name']
            ];
        } catch (PDOException $e) {
            throw new PDOException('Error registering user: ' . $e->getMessage());
        }
    }

    public function getUserByEmail(string $email): ?array
    {
        try {
            $query = "SELECT user_id, company_id, email, password_hash, first_name, last_name, created_at
                      FROM {$this->table} WHERE email = :email LIMIT 1";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (PDOException $e) {
            throw new PDOException('Error fetching user: ' . $e->getMessage());
        }
    }
}


