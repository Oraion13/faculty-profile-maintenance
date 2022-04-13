<?php

// Operations for 'faculty_position_prev_id' is handeled here
class Positions_prev
{
    private $conn;
    private $table = 'faculty_positions_prev';
    private $users = 'faculty_users';
    private $positions = 'faculty_positions';
    private $departments = 'faculty_departments';

    public $position_prev_id = 0;
    public $user_id = 0;
    public $position_id = 0;
    public $department_id = 0;
    public $position_prev_where = '';
    public $position_prev_from = '';
    public $position_prev_to = '';

    // Connect to the DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Read all data (previous positions) of a user
    public function read()
    {
        $columns = $this->table . '.position_prev_id, ' . $this->table . '.user_id, '
            . $this->table . '.position_id, ' . $this->positions . '.position, ' . $this->table . '.department_id, '
            . $this->departments . '.department, ' . $this->table . '.position_prev_where, '
            . $this->table . '.position_prev_from, ' . $this->table . '.position_prev_to';
        $query = 'SELECT ' . $columns . ' FROM ((' . $this->table . ' INNER JOIN ' . $this->positions . ' ON '
            . $this->table . '.user_id = :user_id AND ' . $this->table . '.position_id = '
            . $this->positions . '.position_id) INNER JOIN ' . $this->departments . ' ON '
            . $this->table . '.department_id = ' . $this->departments . '.department_id)';

        $stmt = $this->conn->prepare($query);

        // Clean the data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(':user_id', $this->user_id);

        if ($stmt->execute()) {
            return $stmt;
        }

        return false;
    }

    // Insert a user's previous position
    public function create()
    {
        $query = 'INSERT INTO ' . $this->table
            . ' SET user_id = :user_id, position_id = :position_id, 
                department_id = :department_id, position_prev_where = :position_prev_where, 
                position_prev_from = :position_prev_from, position_prev_to = :position_prev_to';

        $stmt = $this->conn->prepare($query);

        // Clean the data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->position_id = htmlspecialchars(strip_tags($this->position_id));
        $this->department_id = htmlspecialchars(strip_tags($this->department_id));
        $this->position_prev_where = htmlspecialchars(strip_tags($this->position_prev_where));
        $this->position_prev_from = htmlspecialchars(strip_tags($this->position_prev_from));
        $this->position_prev_to = htmlspecialchars(strip_tags($this->position_prev_to));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':position_id', $this->position_id);
        $stmt->bindParam(':department_id', $this->department_id);
        $stmt->bindParam(':position_prev_where', $this->position_prev_where);
        $stmt->bindParam(':position_prev_from', $this->position_prev_from);
        $stmt->bindParam(':position_prev_to', $this->position_prev_to);

        // If data inserted successfully, return True
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update a field in positions_prev
    public function update($to_update)
    {
        $to_set = $to_update . ' = :' . $to_update;
        $query = 'UPDATE ' . $this->table . ' SET ' . $to_set . ' WHERE position_prev_id = :position_prev_id';

        $stmt = $this->conn->prepare($query);

        $this->$to_update = htmlspecialchars(strip_tags($this->$to_update));
        $this->position_prev_id = htmlspecialchars(strip_tags($this->position_prev_id));

        $stmt->bindParam(':' . $to_update, $this->$to_update);
        $stmt->bindParam(':position_prev_id', $this->position_prev_id);

        // If data updated successfully, return True
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete a field in positions_prev
    public function delete_row()
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE position_prev_id = :position_prev_id';

        $stmt = $this->conn->prepare($query);

        $this->position_prev_id = htmlspecialchars(strip_tags($this->position_prev_id));

        $stmt->bindParam(':position_prev_id', $this->position_prev_id);

        // If data updated successfully, return True
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
