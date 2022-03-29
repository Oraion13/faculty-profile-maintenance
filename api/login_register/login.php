<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../config/DbConnection.php';
require_once '../../models/Users.php';

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

if (strpos($data->username, '@') !== false) {
    $users->email = $data->username;
} else {
    $users->username = $data->username;
}

$validate = $users->read_single();

if ($validate) {
    if ($validate['is_verified'] === 0) {
        header($_SERVER["SERVER_PROTOCOL"] . ' 400 ', true, 400);
        echo json_encode(
            array('message' => 'email not verified')
        );
        die();
    }

    if (password_verify($data->password, $validate['password'])) {
        header($_SERVER["SERVER_PROTOCOL"] . ' 200 ', true, 200);
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $validate['username'];
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
