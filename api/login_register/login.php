<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../config/DbConnection.php';
require_once '../../models/Users.php';
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

// Check if the given data is username or email
if (strpos($data->username, '@') !== false) {
    $users->email = $data->username;
} else {
    $users->username = $data->username;
}

// Check if the email is verified for the user
$validate = $users->read_single();

if ($validate) {
    if ($validate['is_verified'] === 0) {
        send(400, 'error', 'email not verified');
        die();
    }

    // If the user has given correct crediantials, they will be logged in and a new SESSION will be started
    if (password_verify($data->password, $validate['password'])) {
        $_SESSION['user_id'] = $validate['user_id'];
        $_SESSION['username'] = $validate['username'];
        send(200, 'message', $validate['username'] . ' logged in');
    } else {
        // header('X-PHP-Response-Code: 400', true, 400);
        // header("HTTP/1.1 404 Not Found");
        send(400, 'error', 'Incorrect password');
    }
} else {
    send(400, 'error', 'incorrect username/email');
}
