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

// To check if an user is already logged in
if (isset($_SESSION['user_id'])) {
    send(400, 'error', $_SESSION['username'] . ' already logged in');
    die();
}

// Connect with DB
$dbconnection = new DbConnection();
$db = $dbconnection->connect();

// Create an object for users table to do operations
$users = new Users($db);

// Get input data as json
$data = json_decode(file_get_contents("php://input"));

// Check for valid email id
if (strpos($data->email, '@') === false) {
    send(400, 'error', 'incorrect email');
    die();
}

// Set the email
$users->email = $data->email;

// Check if the user exists
$validate = $users->read_single();

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

$users->password_reset_token = $password_reset_token;
$users->password_reset_token_expire = $password_reset_token_expire;

// send the password reset link
if ($users->reset_password() && verification_mail(
    $validate['email'],
    $validate['username'],
    $password_reset_token,
    'Password reset link from AUTTVL!',
    'Click the link below to reset your password,<br>',
    'reset_password'
)) {
    send(200, 'message', 'password reset link sent to mail');
} else {
    send(400, 'error', 'unable to send password reset link');
}
