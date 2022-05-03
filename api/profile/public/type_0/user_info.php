<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/User_info.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';
require_once '../../../../utils/loggedin_verified.php';

class User_info_api extends User_info implements api
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

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->User_info->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info found');
            die();
        }
    }

    // Get all data of a user info by ID
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->User_info->user_id = $id;
        $all_data = $this->User_info->read_row();

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
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Clean the data
        $this->User_info->user_id = $_SESSION['user_id'];
        $this->User_info->address = $data->address;
        $this->User_info->phone = $data->phone;
        $this->User_info->position_id = $data->position_id;
        $this->User_info->department_id = $data->department_id;
        $this->User_info->position_present_where = $data->position_present_where;

        $from = date('Y-m-01', strtotime($data->position_present_from));
        $this->User_info->position_present_from = $from;


        // Get the user info from DB
        $all_data = $this->User_info->read_row();

        // If no user info exists
        if (!$all_data) {
            // If no user info exists, insert and get_by_id the data
            if ($this->User_info->post()) {
                $this->get_by_id($_SESSION['user_id']);
            } else {
                send(400, 'error', 'user info cannot be created');
            }
        } else {
            send(400, 'error', 'user info already exists');
        }
    }

    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->User_info->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // UPDATE (PUT) a existing user's info
    public function put()
    {
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Clean the data
        $this->User_info->user_id = $_SESSION['user_id'];
        $this->User_info->phone = $data->phone;
        $this->User_info->address = $data->address;
        $this->User_info->position_id = $data->position_id;
        $this->User_info->department_id = $data->department_id;
        $this->User_info->position_present_where = $data->position_present_where;

        $from = date('Y-m-01', strtotime($data->position_present_from));
        $this->User_info->position_present_from = $from;

        // Get the user info from DB
        $all_data = $this->User_info->read_row();

        $error = false;
        $message = '';
        // If user info already exists, update the user info that changed
        if ($all_data) {
            $this->User_info->user_info_id = $all_data['user_info_id'];

            $this->update_by_id($all_data['phone'], $data->phone, 'phone');
            $this->update_by_id($all_data['address'], $data->address, 'address');
            $this->update_by_id($all_data['position_id'], $data->position_id, 'position_id');
            $this->update_by_id($all_data['department_id'], $data->department_id, 'department_id');
            $this->update_by_id($all_data['position_present_where'], $data->position_present_where, 'position_present_where');
            $this->update_by_id($all_data['position_present_from'], $from, 'position_present_from');

            // If updated successfully, get_by_id the data, else throw an error message 
            $this->get_by_id($_SESSION['user_id']);
        } else {
            send(400, 'error', 'no user info found');
        }
    }

    public function delete_by_id()
    {
        // Later use
    }
}


// GET all the user info
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $User_info_api = new User_info_api();
    if (isset($_GET['ID'])) {
        $User_info_api->get_by_id($_GET['ID']);
    } else {
        $User_info_api->get();
    }
}

// To check if an user is logged in and verified
loggedin_verified();

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
