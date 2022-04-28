<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../config/DbConnection.php';
require_once '../../models/Users.php';
require_once '../../utils/send.php';
require_once '../../utils/verification_mail.php';

class Verify_email_api extends Users
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

    // Update email verification code
    public function put()
    {
        $this->Users->username = $_SESSION['username'];

        // Get the user
        $user = $this->Users->read_single();
        // Email verification code
        $verification_code = bin2hex(random_bytes(32));
        $this->Users->verification_code = $verification_code;
        $this->Users->is_verified = 0;

        // Creates an user and send an email verification link
        if ($this->Users->update_verification_code_email() && verification_mail(
            $user['email'],
            $user['username'],
            $verification_code,
            'Email verification from AUTTVL',
            'Verify Email<br>
    Click the link below to verify the account,<br>',
            'verify'
        )) {
            send(201, 'message', 'check email to verify account');
        } else {
            send(400, 'error', 'unable to send verification link');
        }
    }
}

// To check if an user is already logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// verify email
$Verify_email_api = new Verify_email_api();
$Verify_email_api->put();
