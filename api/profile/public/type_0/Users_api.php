<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Users.php';
require_once '../../../../utils/send.php';

class Users_api
{
    private $Users;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Users = new Users($db);
    }

    // Get all data of users
    public function get()
    {
        // Get the users from DB
        $all_data = $this->Users->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no users found');
            die();
        }
    }
}

// GET all the users
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Users_api = new Users_api();
    $Users_api->get();
}
