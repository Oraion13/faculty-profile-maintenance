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

class Forget_password_api
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

    // POST to send a reset password link
    public function post()
    {
        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Check for valid email id
        if (strpos($data->email, '@') === false) {
            send(400, 'error', 'incorrect email');
            die();
        }

        // Set the email
        $this->Users->email = $data->email;

        // Check if the user exists
        $validate = $this->Users->read_single();

        // If query cannot be executed
        if (!$validate) {
            send(400, 'error', 'user not found');
            die();
        }

        if ($validate['email'] !== $data->email) {
            send(400, 'error', 'email mismatch');
            die();
        }

        // Generate and set the password reset token and expire date
        $password_reset_token = bin2hex(random_bytes(32));
        date_default_timezone_set('Asia/kolkata');
        $password_reset_token_expire = date('Y-m-d');

        $this->Users->password_reset_token = $password_reset_token;
        $this->Users->password_reset_token_expire = $password_reset_token_expire;

        // send the password reset link
        if ($this->Users->reset_password() && verification_mail(
            $validate['email'],
            $validate['username'],
            $password_reset_token,
            'Password reset link from AUTTVL!',
            'Click the link below to reset your password,<br>',
            'Reset_password_api'
        )) {
            send(200, 'message', 'password reset link sent to mail');
        } else {
            send(400, 'error', 'unable to send password reset link');
        }
    }
}

// To check if an user is already logged in
if (isset($_SESSION['user_id'])) {
    send(400, 'error', $_SESSION['username'] . ' already logged in');
    die();
}

// POST to send a reset password link
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Forget_password_api = new Forget_password_api();
    $Forget_password_api->post();
}