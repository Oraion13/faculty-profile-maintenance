<?php

// Operations for 'faculty_user_info' is handeled here
class User_info
{
    private $conn;
    private $table = 'faculty_user_info';

    public $user_info_id = 0;
    public $user_id = 0;
    public $phone = 0;
    public $address = '';
    public $position_id = 0;
    public $department_id = 0;

    // Connect to the DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Read all data of a user
    public function read()
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE user_id = :user_id';

        $stmt = $this->conn->prepare($query);

        // Clean the data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(':user_id', $this->user_id);

        if ($stmt->execute()) {
            // Fetch the data
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // If data exists, return the data
            if ($row) {
                return $row;
            }
        }

        return false;
    }

    // Insert user info
    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . ' SET user_id = :user_id, phone = :phone, address = :address, position_id = :position_id, department_id = :department_id';

        $stmt = $this->conn->prepare($query);

        // Clean the data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->position_id = htmlspecialchars(strip_tags($this->position_id));
        $this->department_id = htmlspecialchars(strip_tags($this->department_id));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':position_id', $this->position_id);
        $stmt->bindParam(':department_id', $this->department_id);

        // If data inserted successfully, return True
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Read a single data using user_info_id
    public function read_single()
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE user_info_id = :user_info_id';

        $stmt = $this->conn->prepare($query);

        // clean the data
        $this->user_info_id = htmlspecialchars(strip_tags($this->user_info_id));

        $stmt->bindParam(':user_info_id', $this->user_info_id);

        if ($stmt->execute()) {
            // Fetch the data
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // If data exists, return the data
            if ($row) {
                return $row;
            }
        }

        return false;
    }

    // Update a field in user_info
    public function update($to_update)
    {
        $to_set = $to_update . ' = :' . $to_update;
        $query = 'UPDATE ' . $this->table . ' SET ' . $to_set . ' WHERE user_info_id = :user_info_id';

        $stmt = $this->conn->prepare($query);

        $this->$to_update = htmlspecialchars(strip_tags($this->$to_update));
        $this->user_info_id = htmlspecialchars(strip_tags($this->user_info_id));

        $stmt->bindParam(':' . $to_update, $this->$to_update);
        $stmt->bindParam(':user_info_id', $this->user_info_id);

        // If data updated successfully, return True
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
