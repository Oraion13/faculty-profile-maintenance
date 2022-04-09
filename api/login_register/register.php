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

// Do some data cleaning

// Check if the fields are not empty
if (!$data->full_name || !$data->username || !$data->email || !$data->password) {
    send(400, 'error', 'provide the required fields');
    die();
}

$users->username = $data->username;
$users->email = $data->email;

// Get the user
$validate = $users->read_single();

// Checks if email id and username are unique
if ($validate) {
    if ($validate['username'] === $data->username) {
        send(409, 'error', 'username already taken');
        die();
    } else {
        send(409, 'error', 'email already registered');
        die();
    }
}

$users->full_name = $data->full_name;
// Generates a password hash
$users->password = password_hash($data->password, PASSWORD_BCRYPT);

// Email verification code
$verification_code = bin2hex(random_bytes(32));
$users->verification_code = $verification_code;
$users->is_verified = 0;

// Creates an user and send an email verification link
if ($users->create() && verification_mail(
    $data->email,
    $data->username,
    $verification_code,
    'Email verification from AUTTVL',
    'Thanks for registration!<br>
    Click the link below to verify the account,<br>',
    'verify'
)) {
    send(201, 'message', 'user created');
} else {
    send(400, 'error', 'unable to create user');
}
