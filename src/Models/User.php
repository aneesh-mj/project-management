<?php
namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

class User
{
    private $conn;
    private $table = 'users';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllUsers()
    {
        try {
            $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new PDOException("Error fetching users: " . $e->getMessage());
        }
    }

    public function getUserById($id)
    {
        try {
            $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new PDOException("Error fetching user: " . $e->getMessage());
        }
    }

    public function createUser($data)
    {
        try {
            $query = "INSERT INTO {$this->table} (name, email) VALUES (:name, :email)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            
            if ($stmt->execute()) {
                return [
                    'id' => $this->conn->lastInsertId(),
                    'name' => $data['name'],
                    'email' => $data['email']
                ];
            }
            return false;
        } catch (PDOException $e) {
            throw new PDOException("Error creating user: " . $e->getMessage());
        }
    }

    public function updateUser($id, $data)
    {
        try {
            $query = "UPDATE {$this->table} SET name = :name, email = :email WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Error updating user: " . $e->getMessage());
        }
    }

    public function deleteUser($id)
    {
        try {
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Error deleting user: " . $e->getMessage());
        }
    }
}