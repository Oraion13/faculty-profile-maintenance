<?php

header('Access-Control-Allow-Origin: *');

require_once '../../config/DbConnection.php';
require_once '../../models/Users.php';
require_once '../../utils/send.php';

// In the link if the email and verification code are present, further operations will be procided
if (isset($_GET['email']) && isset($_GET['verification_code'])) {
    // Connect with DB
    $dbconnection = new DbConnection();
    $db = $dbconnection->connect();

    // Create an object for users table to do operations
    $users = new Users($db);

    $users->email = $_GET['email'];
    $users->verification_code = $_GET['verification_code'];

    // Check if the user exists. If exists, email will be verified 
    $validate = $users->verify_user();
    if ($validate) {
        if ($validate['is_verified'] === 0) {
            if ($users->update_verification()) {
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
