<?php

// Operations for '
// faculty_additional_responsibilities_prev, 
// faculty_degree, 
// faculty_other_employment,
// faculty_programme_attended,
// faculty_programme_chaired,
// faculty_programme_organized,
// faculty_special_reprasentations,
//
// faculty_papers_presented,
// faculty_papers_published,
// faculty_patents,
//
// faculty_books_published'
// is handeled here
class Type_5
{
    private $conn;

    public $table = '';

    public $id_name = '';
    public $text_name = '';
    public $from_name = '';
    public $to_name = '';

    public $id = 0;
    public $user_id = 0;
    public $text_title = '';
    public $from_text = '';
    public $to_int = '';

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
            . $this->text_name . ' = :' . $this->text_name . ', ' . $this->from_name . ' = :' . $this->from_name
            . ', ' . $this->to_name . ' = :' . $this->to_name;

        $stmt = $this->conn->prepare($query);

        // Clean the data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->text_title = htmlspecialchars(strip_tags($this->text_title));
        $this->from_text = htmlspecialchars(strip_tags($this->from_text));
        $this->to_int = htmlspecialchars(strip_tags($this->to_int));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':' . $this->text_name, $this->text_title);
        $stmt->bindParam(':' . $this->from_name, $this->from_text);
        $stmt->bindParam(':' . $this->to_name, $this->to_int);

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
            case $this->text_name:
                $temp = 'text_title';
                break;
            case $this->from_name:
                $temp = 'from_text';
                break;
            case $this->to_name:
                $temp = 'to_int';
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