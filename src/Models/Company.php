<?php
namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

class Company
{
    private $conn;
    private $table = 'Companies';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllCompanies()
    {
        try {
            $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new PDOException("Error fetching companies: " . $e->getMessage());
        }
    }

    public function getCompanyById($id)
    {
        try {
            $query = "SELECT * FROM {$this->table} WHERE company_id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new PDOException("Error fetching company: " . $e->getMessage());
        }
    }

    public function createCompany($data)
    {
        try {
            $query = "INSERT INTO {$this->table} (company_name) VALUES (:company_name)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':company_name', $data['company_name']);
            
            if ($stmt->execute()) {
                return [
                    'company_id' => $this->conn->lastInsertId(),
                    'company_name' => $data['company_name']
                ];
            }
            return false;
        } catch (PDOException $e) {
            throw new PDOException("Error creating company: " . $e->getMessage());
        }
    }

    public function updateCompany($id, $data)
    {
        try {
            $query = "UPDATE {$this->table} SET company_name = :company_name WHERE company_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':company_name', $data['company_name']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Error updating company: " . $e->getMessage());
        }
    }

    public function deleteCompany($id)
    {
        try {
            $query = "DELETE FROM {$this->table} WHERE company_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Error deleting company: " . $e->getMessage());
        }
    }
}
