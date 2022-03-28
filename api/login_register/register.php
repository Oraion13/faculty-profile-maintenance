<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/DbConnection.php';
include_once '../../models/Users.php';

$dbconnection = new DbConnection();
$db = $dbconnection->connect();

$users = new Users($db);

$data = json_decode(file_get_contents("php://input"));

$users->username = $data->username;
$users->email = $data->email;
$users->password = password_hash($data->password, PASSWORD_BCRYPT);

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

if ($users->create()) {
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
