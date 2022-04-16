<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../config/DbConnection.php';
require_once '../../models/Users.php';
require_once '../../utils/send.php';

class Login_api
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

    // POST to login
    public function post()
    {
        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Do some data cleaning

        // Check if the given data is username or email
        if (strpos($data->username, '@') !== false) {
            $this->Users->email = $data->username;
        } else {
            $this->Users->username = $data->username;
        }

        // Check if the email is verified for the user
        $validate = $this->Users->read_single();

        if ($validate) {
            // if ($validate['is_verified'] === 0) {
            //     send(400, 'error', 'email not verified');
            //     die();
            // }

            // If the user has given correct crediantials, they will be logged in and a new SESSION will be started
            if (password_verify($data->password, $validate['password'])) {
                $_SESSION['user_id'] = $validate['user_id'];
                $_SESSION['username'] = $validate['username'];
                $_SESSION['is_verified'] = $validate['is_verified'];
                echo json_encode(
                    array(
                        'user_id' => $validate['user_id'],
                        'username' => $validate['username'],
                        'email' => $validate['email'],
                        'full_name' => $validate['full_name'],
                        'is_verified' => $validate['is_verified']
                    )
                );
            } else {
                // header('X-PHP-Response-Code: 400', true, 400);
                // header("HTTP/1.1 404 Not Found");
                send(400, 'error', 'Incorrect password');
            }
        } else {
            send(400, 'error', 'incorrect username/email');
        }
    }
}

// To check if an user is already logged in
if (isset($_SESSION['user_id'])) {
    send(400, 'error', $_SESSION['username'] . ' already logged in');
    die();
}

// POST to login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Login_api = new Login_api();
    $Login_api->post();
}