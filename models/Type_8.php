<?php

// Operations for '
// faculty_research_guidance
// is handeled here
class Type_8
{
    private $conn;

    public $table = '';

    public $id_name = '';
    public $col1_name = '';
    public $col2_name = '';
    public $col3_name = '';
    public $col4_name = '';
    public $col5_name = '';
    public $col6_name = '';

    public $id = 0;
    public $user_id = 0;
    public $col1 = '';
    public $col2 = '';
    public $col3 = '';
    public $col4 = '';
    public $col5 = '';
    public $col6 = '';

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
            . $this->col1_name . ' = :' . $this->col1_name . ', ' . $this->col2_name . ' = :' . $this->col2_name
            . ', ' . $this->col3_name . ' = :' . $this->col3_name . ', '
            . $this->col4_name . ' = :' . $this->col4_name . ', ' . $this->col5_name . ' = :' . $this->col5_name
            . ', ' . $this->col6_name . ' = :' . $this->col6_name;

        $stmt = $this->conn->prepare($query);

        // Clean the data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->col1 = htmlspecialchars(strip_tags($this->col1));
        $this->col2 = htmlspecialchars(strip_tags($this->col2));
        $this->col3 = htmlspecialchars(strip_tags($this->col3));
        $this->col4 = htmlspecialchars(strip_tags($this->col4));
        $this->col5 = htmlspecialchars(strip_tags($this->col5));
        $this->col6 = htmlspecialchars(strip_tags($this->col6));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':' . $this->col1_name, $this->col1);
        $stmt->bindParam(':' . $this->col2_name, $this->col2);
        $stmt->bindParam(':' . $this->col3_name, $this->col3);
        $stmt->bindParam(':' . $this->col4_name, $this->col4);
        $stmt->bindParam(':' . $this->col5_name, $this->col5);
        $stmt->bindParam(':' . $this->col6_name, $this->col6);


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

        // Set the value to update
        $temp = '';
        switch ($to_update) {
            case $this->col1_name:
                $temp = 'col1';
                break;
            case $this->col2_name:
                $temp = 'col2';
                break;
            case $this->col3_name:
                $temp = 'col3';
                break;
            case $this->col4_name:
                $temp = 'col4';
                break;
            case $this->col5_name:
                $temp = 'col5';
                break;
            case $this->col6_name:
                $temp = 'col6';
                break;
        }

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
