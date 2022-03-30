<?php

class Users
{
    private $conn;
    private $table = 'users';

    public $id;
    public $username;
    public $email;
    public $password;
    public $verification_code;
    public $password_reset_token;
    public $password_reset_token_expire;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read()
    {
        $query = 'SELECT * FROM ' . $this->table;

        $stmt = $this->conn->prepare($query);
        $stmt->exexute();

        return $stmt;
    }

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

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function read_single()
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE username = :username OR email = :email';

        $stmt = $this->conn->prepare($query);

        // clean the data
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row;
        }

        return false;
    }

    public function verify_user()
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE email = :email AND verification_code = :verification_code';

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->verification_code = htmlspecialchars(strip_tags($this->verification_code));

        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':verification_code', $this->verification_code);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row;
        }

        return false;
    }

    public function update_verification()
    {
        $query = "UPDATE " . $this->table . " SET `is_verified`='1' WHERE email = :email";

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(':email', $this->email);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function password_reset()
    {
        $query = "UPDATE " . $this->table . " SET password_reset_token = :password_reset_token, password_reset_token_expire = :password_reset_token_expire WHERE email = :email";

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(':password_reset_token', $this->password_reset_token);
        $stmt->bindParam(':password_reset_token_expire', $this->password_reset_token_expire);
        $stmt->bindParam(':email', $this->email);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function verify_password_reset()
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE email = :email AND password_reset_token = :password_reset_token AND password_reset_token_expire = :password_reset_token_expire';

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password_reset_token = htmlspecialchars(strip_tags($this->password_reset_token));
        $this->password_reset_token_expire = htmlspecialchars(strip_tags($this->password_reset_token_expire));

        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password_reset_token', $this->password_reset_token);
        $stmt->bindParam(':password_reset_token_expire', $this->password_reset_token_expire);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row;
        }

        return false;
    }

    public function update_password_reset()
    {
        $query = "UPDATE " . $this->table . " SET password = :password, password_reset_token = NULL, password_reset_token_expire = NULL WHERE email = :email";

        $stmt = $this->conn->prepare($query);

        $this->password = htmlspecialchars(strip_tags($this->password));

        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
