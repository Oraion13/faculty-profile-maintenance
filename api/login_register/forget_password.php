<?php

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

if (strpos($data->email, '@') === false) {
    send(400, 'incorrect email');
    die();
}

$users->email = $data->email;

$validate = $users->read_single();

if ($validate['email'] !== $data->email) {
    send(400, 'user not found');
    die();
}

$password_reset_token = bin2hex(random_bytes(32));
date_default_timezone_set('Asia/kolkata');
$password_reset_token_expire = date('Y-m-d');

$users->password_reset_token = $password_reset_token;
$users->password_reset_token_expire = $password_reset_token_expire;

if ($users->password_reset() && verification_mail($validate['email'], $validate['username'], $password_reset_token, 'Password reset link from AUTTVL!<br>
Click the link below to reset your password,<br>', 'reset_password')) {
    send(200, 'password reset link sent to mail');
} else {
    send(400, 'unable to send password reset link');
}
