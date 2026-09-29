<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table = 'enr_users';

    public $id;
    public $username;
    public $password;
    public $email;
    public $user_type;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create new user
    public function create($data) {
        try {
            $query = "INSERT INTO " . $this->table . " 
                      (username, password, email, user_type, status) 
                      VALUES 
                      (:username, :password, :email, :user_type, 1)";
            
            $stmt = $this->conn->prepare($query);
            
            // Sanitize and bind
            $this->username = htmlspecialchars(strip_tags($data['username']));
            $this->password = $data['password'];
            $this->email = htmlspecialchars(strip_tags($data['email']));
            $this->user_type = $data['user_type'] ?? 'applicant';
            
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':user_type', $this->user_type);
            
            if($stmt->execute()) {
                return [
                    'success' => true, 
                    'user_id' => $this->conn->lastInsertId(),
                    'message' => 'User created successfully'
                ];
            }
            return ['success' => false, 'message' => 'Failed to create user'];
        } catch(PDOException $e) {
            if($e->errorInfo[1] == 1062) {
                return ['success' => false, 'message' => 'Username or email already exists'];
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Login user
    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE (username = :username OR email = :username) 
                  AND password = :password 
                  AND status = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->user_type = $row['user_type'];
            return $row;
        }
        return false;
    }

    // Get user by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get user by email
    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update user
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET username = :username, 
                      email = :email, 
                      user_type = :user_type 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':user_type', $data['user_type']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Change password
    public function changePassword($id, $new_password) {
        $query = "UPDATE " . $this->table . " 
                  SET password = :password 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', md5($new_password));
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Delete user (soft delete)
    public function delete($id) {
        $query = "UPDATE " . $this->table . " SET status = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Get all users by type
    public function getByType($user_type) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE user_type = :user_type AND status = 1 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_type', $user_type);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>