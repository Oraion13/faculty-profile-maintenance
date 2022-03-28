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

if (strpos($data->username, '@') !== false) {
    $users->email = $data->username;
} else {
    $users->username = $data->username;
}

$validate = $users->read_single();

if ($validate) {
    if (password_verify($data->password, $validate['password'])) {
        header($_SERVER["SERVER_PROTOCOL"] . ' 200 ', true, 200);
        echo json_encode(
            array('message' => $validate['username'] . ' logged in')
        );
    } else {
        header($_SERVER["SERVER_PROTOCOL"] . ' 400 ', true, 400);
        // header('X-PHP-Response-Code: 400', true, 400);
        // header("HTTP/1.1 404 Not Found");
        echo json_encode(
            array('message' => 'Incorrect password')
        );
    }
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 400 ', true, 400);
    echo json_encode(
        array('message' => 'incorrect username/email')
    );
}
