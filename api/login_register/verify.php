<?php

header('Access-Control-Allow-Origin: *');

require_once '../../config/DbConnection.php';
require_once '../../models/Users.php';
require_once '../../utils/send.php';

class Verify_api extends Users
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

    public function get()
    {
        // In the link if the email and verification code are present, further operations will be procided
        if (!isset($_GET['email']) && !isset($_GET['verification_code'])) {
            send(400, 'error', 'no email/verification code found');
        }


        $this->Users->email = $_GET['email'];
        $this->Users->verification_code = $_GET['verification_code'];

        // Check if the user exists. If exists, email will be verified 
        $validate = $this->Users->verify_user();
        if ($validate) {
            if ($validate['is_verified'] === 0) {
                if ($this->Users->update_verification()) {
                    send(200, 'message', 'email verified successfully');
                } else {
                    send(400, 'error', 'unable to verify user');
                }
            } else {
                send(400, 'error', 'user already verified/registered');
            }
        } else {
            send(400, 'error', 'no user account found');
        }
    }
}

// GET to verify
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Verify_api = new Verify_api();
    $Verify_api->get();
}
