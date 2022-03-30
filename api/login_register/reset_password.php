<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../config/DbConnection.php';
require_once '../../models/Users.php';

if (isset($_GET['email']) && isset($_GET['verification_code'])) {
    $dbconnection = new DbConnection();
    $db = $dbconnection->connect();

    $users = new Users($db);

    date_default_timezone_set('Asia/kolkata');
    $password_reset_token_expire = date('Y-m-d');

    $users->email = $_GET['email'];
    $users->password_reset_token = $_GET['verification_code'];
    $users->password_reset_token_expire = $password_reset_token_expire;

    if (!$users->verify_password_reset()) {
        header($_SERVER["SERVER_PROTOCOL"] . ' 400 ', true, 400);
        echo json_encode(
            array('message' => 'Invalid/expired link')
        );
        die();
    }

    $data = json_decode(file_get_contents("php://input"));
    if (!$data->password) {
        echo json_encode(
            array('message' => 'Enter a valid password')
        );
        header('Location:' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
        die();
    }

    $users->password = password_hash($data->password, PASSWORD_BCRYPT);
    $users->email = $_GET['email'];

    if ($users->update_password_reset()) {
        header($_SERVER["SERVER_PROTOCOL"] . ' 400 ', true, 200);
        echo json_encode(
            array('message' => 'password updated successfully')
        );
        die();
    } else {
        header($_SERVER["SERVER_PROTOCOL"] . ' 400 ', true, 400);
        echo json_encode(
            array('message' => 'unable to reset password')
        );
        die();
    }
}
