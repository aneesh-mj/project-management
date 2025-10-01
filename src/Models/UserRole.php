<?php
namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

class UserRole
{
    private $connection;
    private $table = 'User_Team_Roles';

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function getUserRoles(int $userId): array
    {
        try {
            $query = "SELECT utr.role, t.team_id, t.team_name 
                      FROM {$this->table} utr
                      JOIN Teams t ON utr.team_id = t.team_id
                      WHERE utr.user_id = :user_id";
            
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException('Error fetching user roles: ' . $e->getMessage());
        }
    }

    public function hasRole(int $userId, array $roles): bool
    {
        try {
            $placeholders = implode(',', array_fill(0, count($roles), '?'));
            
            $query = "SELECT 1 FROM {$this->table} 
                      WHERE user_id = ? AND role IN ($placeholders)
                      LIMIT 1";
            
            $stmt = $this->connection->prepare($query);
            
            // Bind user_id as first parameter
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            
            // Bind role values starting from index 2
            foreach ($roles as $index => $role) {
                $stmt->bindValue($index + 2, $role, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new PDOException('Error checking user roles: ' . $e->getMessage());
        }
    }

    public function assignRole(int $userId, int $teamId, string $role): bool
    {
        try {
            $query = "INSERT INTO {$this->table} (user_id, team_id, role)
                      VALUES (:user_id, :team_id, :role)";
            
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException('Error assigning role: ' . $e->getMessage());
        }
    }

    public function updateRole(int $userId, int $teamId, string $role): bool
    {
        try {
            $query = "UPDATE {$this->table} 
                      SET role = :role
                      WHERE user_id = :user_id AND team_id = :team_id";
            
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException('Error updating role: ' . $e->getMessage());
        }
    }

    public function removeRole(int $userId, int $teamId): bool
    {
        try {
            $query = "DELETE FROM {$this->table} 
                      WHERE user_id = :user_id AND team_id = :team_id";
            
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException('Error removing role: ' . $e->getMessage());
        }
    }
}