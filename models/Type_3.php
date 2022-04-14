<?php

// Operations for 'faculty_memberships, faculty_area_of_specialization' is handeled here
class Type_3
{
    private $conn;

    public $table = '';

    public $id_name = '';
    public $text_name = '';

    public $id = 0;
    public $user_id = 0;
    public $text = '';

    // Connect to the DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Read all data of a user
    public function read_by_id()
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE user_id = :user_id';

        $stmt = $this->conn->prepare($query);

        // Clean the data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(':user_id', $this->user_id);

        if ($stmt->execute()) {
            // If data exists, return the data
            if ($stmt) {
                return $stmt;
            }
        }

        return false;
    }

    // Insert user data
    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . ' SET user_id = :user_id, '
         . $this->text_name . ' = :' .$this->text_name ;

        $stmt = $this->conn->prepare($query);

        // Clean the data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->text = htmlspecialchars(strip_tags($this->text));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':' . $this->text_name, $this->text);

        // If data inserted successfully, return True
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update a field
    public function update($to_update)
    {
        $to_set = $to_update . ' = :' . $to_update;
        $query = 'UPDATE ' . $this->table . ' SET ' . $to_set . ' WHERE ' . $this->id_name . ' = :' . $this->id_name;

        $stmt = $this->conn->prepare($query);

        // $temp = strcmp($to_update, $this->id_name) === 0 ? 'id' : '';
        $temp = strcmp($to_update, $this->text_name) === 0 ? 'text' : '';
        $this->$temp = htmlspecialchars(strip_tags($this->$temp));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':' . $to_update, $this->$temp);
        $stmt->bindParam(':' . $this->id_name, $this->id);

        // If data updated successfully, return True
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete a field
    public function delete_row()
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->id_name . ' = :' . $this->id_name;

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':' . $this->id_name, $this->id);

        // If data updated successfully, return True
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}