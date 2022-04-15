<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../config/DbConnection.php';
require_once '../../models/Users.php';
require_once './verification_mail.php';
require_once '../../utils/send.php';

class Register_api
{
    private $Users;

    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for this->Users table to do operations
        $this->Users = new Users($db);
    }

    // POST to register a new user
    public function post()
    {
        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Do some data cleaning

        // Check if the fields are not empty
        if (!$data->full_name || !$data->username || !$data->email || !$data->password) {
            send(400, 'error', 'provide the required fields');
            die();
        }

        $this->Users->username = $data->username;
        $this->Users->email = $data->email;
        $this->Users->honorific = $data->honorific;

        // Get the user
        $validate = $this->Users->read_single();

        // Checks if email id and username are unique
        if ($validate) {
            if (strcmp($validate['username'], $data->username) === 0) {
                send(409, 'error', 'username already taken');
                die();
            } else {
                send(409, 'error', 'email already registered');
                die();
            }
        }

        $this->Users->full_name = $data->full_name;
        // Generates a password hash
        $this->Users->password = password_hash($data->password, PASSWORD_BCRYPT);

        // Email verification code
        $verification_code = bin2hex(random_bytes(32));
        $this->Users->verification_code = $verification_code;
        $this->Users->is_verified = 0;

        // Creates an user and send an email verification link
        if ($this->Users->create() && verification_mail(
            $data->email,
            $data->username,
            $verification_code,
            'Email verification from AUTTVL',
            'Thanks for registration!<br>
    Click the link below to verify the account,<br>',
            'Verify_api'
        )) {
            send(201, 'message', 'user created');
        } else {
            send(400, 'error', 'unable to create user');
        }
    }
}

// To check if an user is already logged in
if (isset($_SESSION['user_id'])) {
    send(400, 'error', $_SESSION['username'] . ' already logged in');
    die();
}

// POST to register a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Register_api = new Register_api();
    $Register_api->post();
}
