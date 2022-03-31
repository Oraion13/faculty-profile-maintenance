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

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    send(400, $_SESSION['username'] . ' already logged in');
    die();
}

$dbconnection = new DbConnection();
$db = $dbconnection->connect();

$users = new Users($db);

$data = json_decode(file_get_contents("php://input"));

// Do some data cleaning

$users->username = $data->username;
$users->email = $data->email;

$validate = $users->read_single();

if ($validate) {
    if ($validate['username'] === $data->username) {
        send(409, 'username already taken');
        die();
    } else {
        send(409, 'email already registered');
        die();
    }
}

$users->password = password_hash($data->password, PASSWORD_BCRYPT);

$verification_code = bin2hex(random_bytes(32));
$users->verification_code = $verification_code;

if ($users->create() && verification_mail($data->email, $data->username, $verification_code, 'Thanks for registration!<br>
                                            Click the link below to verify the account,<br>', 'verify')) {
    send(201, 'user created');
} else {
    send(400, 'unable to create user');
}
