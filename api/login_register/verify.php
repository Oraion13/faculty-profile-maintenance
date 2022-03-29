<?php

header('Access-Control-Allow-Origin: *');

require_once '../../config/DbConnection.php';
require_once '../../models/Users.php';

if (isset($_GET['email']) && isset($_GET['verification_code'])) {
    $dbconnection = new DbConnection();
    $db = $dbconnection->connect();

    $users = new Users($db);

    $users->email = $_GET['email'];
    $users->verification_code = $_GET['verification_code'];

    $validate = $users->verify_user();
    if ($validate) {
        if ($validate['is_verified'] === 0) {
            if ($users->update_verification()) {
                header($_SERVER["SERVER_PROTOCOL"] . ' 400 ', true, 200);
                echo json_encode(
                    array('message' => 'email verified successfully')
                );
            } else {
                header($_SERVER["SERVER_PROTOCOL"] . ' 400 ', true, 400);
                echo json_encode(
                    array('message' => 'unable to verify user')
                );
            }
        } else {
            header($_SERVER["SERVER_PROTOCOL"] . ' 400 ', true, 400);
            echo json_encode(
                array('message' => 'user already registered')
            );
        }
    } else {
        header($_SERVER["SERVER_PROTOCOL"] . ' 400 ', true, 400);
        echo json_encode(
            array('message' => 'no user account found')
        );
    }
}
