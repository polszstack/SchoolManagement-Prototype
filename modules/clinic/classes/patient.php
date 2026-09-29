<?php
require_once '/../../database/db.php';
 
class Patient {
    private $conn;
    private $table_name = "cln_patients";

    public $patient_id;
    public $first_name;
    public $last_name;
    public $gender;
    public $date_of_birth;
    public $phone;
    public $email;
    public $address;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create patient
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET first_name=:first_name, last_name=:last_name, gender=:gender, 
                  date_of_birth=:dob, phone=:phone, email=:email, address=:address";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':dob', $this->date_of_birth);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':address', $this->address);
        return $stmt->execute();
    }

    // Read all patients
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY patient_id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>