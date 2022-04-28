<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Users.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';
require_once '../../../../utils/loggedin_verified.php';

class Users_api extends Users implements api
{
    private $Users;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Users = new Users($db);
    }

    // Get all data of users
    public function get()
    {
        // Get the users from DB
        $all_data = $this->Users->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no users found');
            die();
        }
    }

    // Get all the data of a user by ID
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->Users->user_id = $id;
        $all_data = $this->Users->read_by_id();

        if ($all_data) {
            echo json_encode($all_data);
            die();
        } else {
            send(400, 'error', 'no user found');
            die();
        }
    }

    // Update a user
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        // later use
    }

    // Delete a user
    public function delete_by_id()
    {
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        $this->Users->user_id = $_SESSION['user_id'];
        if ($this->Users->delete_row()) {
            send(200, 'message', 'user deleted successfully');
            header('Location: ../../../login_register/Logout_api.php');
            return;
        } else {
            send(400, 'error', 'user cannot be deleted');
        }
    }

    // Update user's info
    public function put()
    {
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Clean the data
        $this->Users->user_id = $_SESSION['user_id'];
        $all_data = $this->Users->read_by_id();

        if ($all_data) {
            $error = false;
            $message = '';

            $this->Users->honorific = $data->honorific;
            $this->Users->full_name = $data->full_name;
            $this->Users->username = $data->username;
            $this->Users->email = $data->email;

            if (strcmp($data->honorific, $all_data['honorific']) !== 0) {
                if (!$this->Users->update_honorific()) {
                    $error = true;
                    $message .= ',honorific,';
                }
            }

            if (strcmp($data->full_name, $all_data['full_name']) !== 0) {
                if (!$this->Users->update_full_name()) {
                    $error = true;
                    $message .= ',full_name,';
                }
            }

            if (strcmp($data->username, $all_data['username']) !== 0) {
                // Get the user
                $validate = $this->Users->read_single();

                // Checks if username is unique
                if ($validate) {
                    send(409, 'error', 'username already taken');
                    die();
                }

                if (!$this->Users->update_username()) {
                    $error = true;
                    $message .= ',username,';
                }
            }

            if (strcmp($data->email, $all_data['email']) !== 0) {
                // Get the user
                $validate = $this->Users->read_single();

                // Checks if mail is unique
                if ($validate) {
                    send(409, 'error', 'email already taken');
                    die();
                }

                if (!$this->Users->update_email()) {
                    $error = true;
                    $message .= ',email,';
                }
            }

            // if (strcmp($data->email, $all_data['email']) !== 0) {
            //     // Get the user
            //     $validate = $this->Users->read_single();

            //     // Checks if email id and username are unique
            //     if (strcmp($validate['email'], $data->email) === 0) {
            //         send(409, 'error', 'email already taken');
            //         die();
            //     }

            //     // Email verification code
            //     $verification_code = bin2hex(random_bytes(32));
            //     $this->Users->verification_code = $verification_code;
            //     $this->Users->is_verified = 0;

            //     if (!$this->Users->update_email() && verification_mail(
            //         $data->email,
            //         $data->username,
            //         $verification_code,
            //         'Email verification from AUTTVL',
            //         'Thanks for registration!<br>
            //         Click the link below to verify the account,<br>',
            //         'Verify_api'
            //     )) {
            //         $error = true;
            //         $message .= ',email,';
            //     }
            // }

            // If updated successfully, get the data, else throw an error message 
            $this->get_by_id($_SESSION['user_id']);
        } else {
            send(400, 'error', 'no user found');
        }
    }

    public function post()
    {
        // Later use
    }
}

// GET all the users
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Users_api = new Users_api();
    if (isset($_GET['ID'])) {
        $Users_api->get_by_id($_GET['ID']);
    } else {
        $Users_api->get();
    }
}

// To check if an user is logged in and verified
loggedin_verified();

// If a user logged in ...

// UPDATE (PUT) a user's info
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Users_api = new Users_api();
    $Users_api->put();
}


// DELETE a user
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $Users_api = new Users_api();
    $Users_api->delete_by_id();
}
