<?php

require_once 'model.php';

// Operations for 
// faculty_additional_responsibilities_present, 
// faculty_honors, 
// faculty_invited_lectures
// faculty_invigilation_duties
// faculty_onduty_orders
// is handeled here
class Type_4 implements model
{
    private $conn;

    public $table = '';
    private $users = 'faculty_users';
    private $user_info = 'faculty_user_info';
    private $positions = 'faculty_positions';
    private $departments = 'faculty_departments';

    public $id_name = '';
    public $text_name = '';
    public $from_name = '';

    public $id = 0;
    public $user_id = 0;
    public $text = '';
    public $from = '';

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
        $columns = $this->table . '.' . $this->id_name . ', '
                    . $this->table . '.' . $this->text_name . ', '
                    . $this->table . '.' . $this->from_name . ', '
                    . $this->users . '.user_id, '
                    . $this->users . '.honorific, '
                    . $this->users . '.full_name, '
                    . $this->departments . '.department, '
                    . $this->positions . '.position';
        $query = 'SELECT ' . $columns . ' FROM ((((' . $this->table . ' INNER JOIN ' . $this->users . ' ON ' . $this->table . '.' . $this->from_name 
        . ' BETWEEN :start AND :end AND ' . $this->table . '.user_id = ' . $this->users . '.user_id) INNER JOIN '
        . $this->user_info . ' ON ' . $this->users . '.user_id = ' . $this->user_info . '.user_id) INNER JOIN '
        . $this->positions . ' ON ' . $this->user_info . '.position_id = ' . $this->positions . '.position_id) INNER JOIN '
        . $this->departments . ' ON ' . $this->departments . '.department_id = ' . $this->user_info . '.department_id)';

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
            . $this->text_name . ' = :' . $this->text_name . ', ' . $this->from_name . ' = :' . $this->from_name;

        $stmt = $this->conn->prepare($query);

        // Clean the data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->text = htmlspecialchars(strip_tags($this->text));
        $this->from = htmlspecialchars(strip_tags($this->from));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':' . $this->text_name, $this->text);
        $stmt->bindParam(':' . $this->from_name, $this->from);

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
