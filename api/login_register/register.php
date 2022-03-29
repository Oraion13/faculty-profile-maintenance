<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../config/DbConnection.php';
require_once '../../models/Users.php';
require_once './verification_mail.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 400 ', true, 400);
    echo json_encode(
        array('message' => $_SESSION['username'] . ' already logged in')
    );
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
        header($_SERVER["SERVER_PROTOCOL"] . ' 409 ', true, 409);
        echo json_encode(
            array('message' => 'username already taken')
        );
        die();
    } else {
        header($_SERVER["SERVER_PROTOCOL"] . ' 409 ', true, 409);
        echo json_encode(
            array('message' => 'email already registered')
        );
        die();
    }
}

$users->password = password_hash($data->password, PASSWORD_BCRYPT);

$verification_code = bin2hex(random_bytes(32));
$users->verification_code = $verification_code;

if ($users->create() && verification_mail($data->email, $data->username, $verification_code)) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 201 ', true, 201);
    echo json_encode(
        array('message' => 'user created')
    );
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 400 ', true, 400);
    echo json_encode(
        array('message' => 'unable to create user')
    );
}
