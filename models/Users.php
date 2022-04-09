<?php

// Operations for 'faculty_users' is handeled here
class Users
{
    private $conn;
    private $table = 'faculty_users';

    public $id;
    public $username;
    public $email;
    public $password;
    public $verification_code;
    public $password_reset_token;
    public $password_reset_token_expire;
    public $position_id;
    public $department_id;

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
            // return the data
            return $stmt;
        }

        return false;
    }

    // Insert a new data
    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . ' SET username = :username, email = :email, password = :password, verification_code = :verification_code, is_verified = 0';

        $stmt = $this->conn->prepare($query);

        // clean the data
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->verification_code = htmlentities($this->verification_code);

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':verification_code', $this->verification_code);

        // If data inserted successfully, return True
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Read a single data using username/email
    public function read_single()
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE username = :username OR email = :email';

        $stmt = $this->conn->prepare($query);

        // clean the data
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);

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

    // For email verification purpose
    public function verify_user()
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE email = :email AND verification_code = :verification_code';

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->verification_code = htmlspecialchars(strip_tags($this->verification_code));

        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':verification_code', $this->verification_code);

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

    // If email verified successfully, is_verified field is set to 1
    public function update_verification()
    {
        $query = "UPDATE " . $this->table . " SET `is_verified`='1' WHERE email = :email";

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(':email', $this->email);

        // If data updated successfully, return True
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Set a forget password token and expire date
    public function reset_password()
    {
        $query = "UPDATE " . $this->table . " SET password_reset_token = :password_reset_token, password_reset_token_expire = :password_reset_token_expire WHERE email = :email";

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(':password_reset_token', $this->password_reset_token);
        $stmt->bindParam(':password_reset_token_expire', $this->password_reset_token_expire);
        $stmt->bindParam(':email', $this->email);

        // If data updated successfully, return True
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verify the reset password token and expire date
    public function verify_reset_password()
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE email = :email AND password_reset_token = :password_reset_token AND password_reset_token_expire = :password_reset_token_expire';

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password_reset_token = htmlspecialchars(strip_tags($this->password_reset_token));
        $this->password_reset_token_expire = htmlspecialchars(strip_tags($this->password_reset_token_expire));

        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password_reset_token', $this->password_reset_token);
        $stmt->bindParam(':password_reset_token_expire', $this->password_reset_token_expire);

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

    // Update the new password
    public function update_reset_password()
    {
        $query = "UPDATE " . $this->table . " SET password = :password, password_reset_token = NULL, password_reset_token_expire = NULL WHERE email = :email";

        $stmt = $this->conn->prepare($query);

        $this->password = htmlspecialchars(strip_tags($this->password));

        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);

        // If data updated successfully, return True
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
