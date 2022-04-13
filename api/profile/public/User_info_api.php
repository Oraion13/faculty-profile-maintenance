<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../config/DbConnection.php';
require_once '../../../models/User_info.php';
require_once '../../../utils/send.php';

class User_info_api
{
    private $User_info;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->User_info = new User_info($db);
    }

    // Get all data of a user info
    public function get()
    {
        // Get the user info from DB
        $this->User_info->user_id = $_GET['ID'];
        $all_data = $this->User_info->read();

        if ($all_data) {
            echo json_encode($all_data);
            die();
        } else {
            send(400, 'error', 'no user info found');
            die();
        }
    }

    // POST a new user info
    public function post()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Clean the data
        $this->User_info->user_id = $_SESSION['user_id'];
        $this->User_info->address = $data->address;
        $this->User_info->phone = $data->phone;
        $this->User_info->position_id = $data->position_id;
        $this->User_info->department_id = $data->department_id;
        $this->User_info->position_present_where = $data->position_present_where;
        $this->User_info->position_present_from = $data->position_present_from;

        // Get the user info from DB
        $all_data = $this->User_info->read();

        // If no user info exists
        if (!$all_data) {
            // If no user info exists, insert and get the data
            if ($this->User_info->create()) {
                $this->get();
            } else {
                send(400, 'error', 'user info cannot be created');
            }
        } else {
            send(400, 'error', 'user info already exists');
        }
    }

    // UPDATE (PUT) a existing user's info
    public function put()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Clean the data
        $this->User_info->user_id = $_SESSION['user_id'];
        $this->User_info->phone = $data->phone;
        $this->User_info->address = $data->address;
        $this->User_info->position_id = $data->position_id;
        $this->User_info->department_id = $data->department_id;
        $this->User_info->position_present_where = $data->position_present_where;
        $this->User_info->position_present_from = $data->position_present_from;

        // Get the user info from DB
        $all_data = $this->User_info->read();

        $error = false;
        $message = '';
        // If user info already exists, update the user info that changed
        if ($all_data) {
            $this->User_info->user_info_id = $all_data['user_info_id'];
            
            if (strcmp($all_data['phone'], $data->phone) !== 0) {
                if (!$this->User_info->update('phone')) {
                    $error = true;
                    $message .= ',phone number,';
                }
            }
            if (strcmp($all_data['address'], $data->address) !== 0) {
                if (!$this->User_info->update('address')) {
                    $error = true;
                    $message .= ',address,';
                }
            }
            if (strcmp($all_data['position_id'], $data->position_id) !== 0) {
                if (!$this->User_info->update('position_id')) {
                    $error = true;
                    $message .= ',faculty position,';
                }
            }
            if (strcmp($all_data['department_id'], $data->department_id) !== 0) {
                if (!$this->User_info->update('department_id')) {
                    $error = true;
                    $message .= ',department,';
                }
            }
            if (strcmp($all_data['position_present_where'], $data->position_present_where) !== 0) {
                if (!$this->User_info->update('position_present_where')) {
                    $error = true;
                    $message .= ',position present where,';
                }
            }
            if (strcmp($all_data['position_present_from'], $data->position_present_from) !== 0) {
                if (!$this->User_info->update('position_present_from')) {
                    $error = true;
                    $message .= ',position present from,';
                }
            }
            if (strcmp($all_data['department_id'], $data->department_id) !== 0) {
                if (!$this->User_info->update('department_id')) {
                    $error = true;
                    $message .= ',department,';
                }
            }

            // If updated successfully, get the data, else throw an error message 
            if ($error) {
                send(400, 'error', substr($message, 1, -1) . ' cannot be updated');
            } else {
                $this->get();
            }
        } else {
            send(400, 'error', 'no user info found');
        }
    }
}


// GET all the user info
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $User_info_api = new User_info_api();
    $User_info_api->get();
}

// To check if an user is logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...

// POST a new user info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $User_info_api = new User_info_api();
    $User_info_api->post();
}

// UPDATE (PUT) a existing user's info
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $User_info_api = new User_info_api();
    $User_info_api->put();
}
