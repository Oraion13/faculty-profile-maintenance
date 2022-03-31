<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../config/DbConnection.php';
require_once '../../models/Users.php';
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

if (strpos($data->username, '@') !== false) {
    $users->email = $data->username;
} else {
    $users->username = $data->username;
}

$validate = $users->read_single();

if ($validate) {
    if ($validate['is_verified'] === 0) {
        send(400, 'email not verified');
        die();
    }

    if (password_verify($data->password, $validate['password'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $validate['username'];
        send(200, $validate['username'] . ' logged in');
    } else {
        // header('X-PHP-Response-Code: 400', true, 400);
        // header("HTTP/1.1 404 Not Found");
        send(400, 'Incorrect password');
    }
} else {
    send(400, 'incorrect username/email');
}
