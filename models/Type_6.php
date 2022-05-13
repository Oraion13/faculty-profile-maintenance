<?php

require_once 'model.php';

// Operations for '
// faculty_exp_abroad,
// faculty_research_degree,
// faculty_extension_outreach,
// faculty_sponsered_projects_completed,
// faculty_incharge_duty_files
// is handeled here
class Type_6 implements model
{
    private $conn;

    public $table = '';

    public $id_name = '';
    public $text_name = '';
    public $from_name = '';
    public $to_name = '';
    public $text_int_name = '';

    public $id = 0;
    public $user_id = 0;
    public $text = '';
    public $from = '';
    public $to = '';
    public $text_int = '';
    
    public $start = '';
    public $end = '';

    // Connect to the DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Read all data
    public function read()
    {
        $query = 'SELECT * FROM ' . $this->table;

        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            // If data exists, return the data
            if ($stmt) {
                return $stmt;
            }
        }

        return false;
    }

    // Read all data of a user by ID
    public function read_row()
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

    // Read all data by dates
    public function read_row_date()
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE ' . $this->from_name . ' BETWEEN ' . $this->start
                    . ' AND ' . $this->end ;

        $stmt = $this->conn->prepare($query);

        // Clean the data
        $this->start = htmlspecialchars(strip_tags($this->start));
        $this->end = htmlspecialchars(strip_tags($this->end));

        $stmt->bindParam(':start', $this->start);
        $stmt->bindParam(':end', $this->end);

        if ($stmt->execute()) {
            // If data exists, return the data
            if ($stmt) {
                return $stmt;
            }
        }

        return false;
    }

    // Insert user data
    public function post()
    {
        $query = 'INSERT INTO ' . $this->table . ' SET user_id = :user_id, '
            . $this->text_name . ' = :' . $this->text_name . ', ' . $this->from_name . ' = :' . $this->from_name
            . ', ' . $this->to_name . ' = :' . $this->to_name . ', '
            . $this->text_int_name . ' = :' . $this->text_int_name;

        $stmt = $this->conn->prepare($query);

        // Clean the data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->text = htmlspecialchars(strip_tags($this->text));
        $this->from = htmlspecialchars(strip_tags($this->from));
        $this->to = $this->to;
        $this->text_int = htmlspecialchars(strip_tags($this->text_int));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':' . $this->text_name, $this->text);
        $stmt->bindParam(':' . $this->from_name, $this->from);
        $stmt->bindParam(':' . $this->to_name, $this->to);
        $stmt->bindParam(':' . $this->text_int_name, $this->text_int);

        // If data inserted successfully, return True
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update a field
    public function update_row($to_update)
    {
        $to_set = $to_update . ' = :' . $to_update;
        $query = 'UPDATE ' . $this->table . ' SET ' . $to_set . ' WHERE ' . $this->id_name . ' = :' . $this->id_name;

        $stmt = $this->conn->prepare($query);

        // Set the value to update
        $temp = '';
        switch ($to_update) {
            case $this->text_name:
                $temp = 'text';
                break;
            case $this->from_name:
                $temp = 'from';
                break;
            case $this->to_name:
                $temp = 'to';
                break;
            case $this->text_int_name:
                $temp = 'text_int';
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
