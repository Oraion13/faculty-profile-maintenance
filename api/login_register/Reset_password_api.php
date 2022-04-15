<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../config/DbConnection.php';
require_once '../../models/Users.php';
require_once '../../utils/send.php';

class Reset_password_api
{
    private $Users;

    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for this->Users table to do operations
        $this->Users = new Users($db);
    }

    // POST to set new password
    public function post()
    {
        // In the link if the email and verification code are present, further operations will be procided
        if (!isset($_GET['email']) && !isset($_GET['verification_code'])) {
            send(400, 'error', 'no email/verification code found');
        }

        // Get the current time
        date_default_timezone_set('Asia/kolkata');
        $password_reset_token_expire = date('Y-m-d');

        $this->Users->email = $_GET['email'];
        $this->Users->password_reset_token = $_GET['verification_code'];
        $this->Users->password_reset_token_expire = $password_reset_token_expire;

        // Checks the password reset token, email and reset token expire date are valid
        if (!$this->Users->verify_reset_password()) {
            send(400, 'error', 'Invalid/expired link');
            die();
        }

        // Get input data as json (new password)
        $data = json_decode(file_get_contents("php://input"));
        if (!$data->password) {
            echo json_encode(
                array('message' => 'Enter a valid password')
            );
            header('Location:' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
            die();
        }

        // Hash the new password and save
        $this->Users->password = password_hash($data->password, PASSWORD_BCRYPT);
        $this->Users->email = $_GET['email'];

        if ($this->Users->update_reset_password()) {
            send(200, 'message', 'password updated successfully');
            die();
        } else {
            send(400, 'error', 'unable to reset password');
            die();
        }
    }
}

// POST to set new password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Reset_password_api = new Reset_password_api();
    $Reset_password_api->post();
}
