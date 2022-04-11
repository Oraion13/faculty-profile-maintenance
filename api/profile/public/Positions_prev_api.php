<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../config/DbConnection.php';
require_once '../../../models/Positions_prev.php';
require_once '../../../utils/send.php';

class Positions_prev_api
{
    private $Positions_prev;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Positions_prev = new Positions_prev($db);
    }

    // Get all the data of a user's previous position
    public function get()
    {
        // Get the user info from DB
        $this->Positions_prev->user_id = $_GET['ID'];
        $all_data = $this->Positions_prev->read();

        if ($all_data) {
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                echo json_encode($row);
            }
            die();
        } else {
            send(400, 'error', 'no user info about previous positions found');
            die();
        }
    }

    // POST a new user's previous position
    public function post()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Check the total number of data
        if (count($data['positions_prev']) === 0) {
            send(400, 'error', 'no data recieved');
            die();
        }

        // Clean the data
        $this->Positions_prev->user_id = $_SESSION['user_id'];

        $count = 0;
        $error = false;
        while ($count < count($data['positions_prev'])) {
            $this->Positions_prev->position_id = $data->positions_prev[$count]->position_id;
            $this->Positions_prev->department_id = $data->positions_prev[$count]->department_id;
            $this->Positions_prev->position_present_where = $data->positions_prev[$count]->position_present_where;
            $this->Positions_prev->position_present_from = $data->positions_prev[$count]->position_present_from;
            $this->Positions_prev->position_present_to = $data->positions_prev[$count]->position_present_to;

            // Try to add a new previous position for user
            if (!$this->Positions_prev->create()) {
                $error = true;
                break;
            }
        }

        if (!$error) {
            $this->get();
        } else {
            send(400, 'error', 'previous positions cannot be added');
        }
    }

    // UPDATE (PUT) a existing user's previous position
    public function put()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's previous position info from DB
        $this->Positions_prev->user_id = $_SESSION['user_id'];
        $all_data = $this->Positions_prev->read();

        $count = 0;
        $error = false;
        $message = '';
        while ($count < count($data['positions_prev'])) {
            // If a previous position's id is given replace the previous tupule, else add a new one
            if (!$data->positions_prev[$count]->position_prev_id) {
                $this->post();
                $count += 1;
                continue;
            }

            // Clean the data
            $this->Positions_prev->position_prev_id = $data->positions_prev[$count]->position_prev_id;
            $this->Positions_prev->position_id = $data->positions_prev[$count]->position_id;
            $this->Positions_prev->department_id = $data->positions_prev[$count]->department_id;
            $this->Positions_prev->position_present_where = $data->positions_prev[$count]->position_present_where;
            $this->Positions_prev->position_present_from = $data->positions_prev[$count]->position_present_from;
            $this->Positions_prev->position_present_to = $data->positions_prev[$count]->position_present_to;

            if (strcmp($all_data['position_id'], $data->positions_prev[$count]->position_id) !== 0) {
                if (!$this->Positions_prev->update('position_id')) {
                    $error = true;
                    $message .= ',position number,';
                }
            }
            if (strcmp($all_data['department_id'], $data->positions_prev[$count]->department_id) !== 0) {
                if (!$this->Positions_prev->update('department_id')) {
                    $error = true;
                    $message .= ',department number,';
                }
            }
            if (strcmp($all_data['position_present_where'], $data->positions_prev[$count]->position_present_where) !== 0) {
                if (!$this->Positions_prev->update('position_present_where')) {
                    $error = true;
                    $message .= ',position present where number,';
                }
            }
            if (strcmp($all_data['position_present_from'], $data->positions_prev[$count]->position_present_from) !== 0) {
                if (!$this->Positions_prev->update('position_present_from')) {
                    $error = true;
                    $message .= ',position present from number,';
                }
            }
            if (strcmp($all_data['position_present_to'], $data->positions_prev[$count]->position_present_to) !== 0) {
                if (!$this->Positions_prev->update('position_present_to')) {
                    $error = true;
                    $message .= ',position present to number,';
                }
            }

            if ($error) {
                break;
            }
            $count += 1;
        }
        // If updated successfully, get the data, else throw an error message 
        if (!$error) {
            $this->get();
        } else {
            send(400, 'error', substr($message, 1, -1) . ' cannot be updated');
        }
    }
}

// GET all the user info
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Positions_prev_api = new Positions_prev_api();
    $Positions_prev->get();
}

// If a user logged in ...

// POST a new user info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Positions_prev_api = new Positions_prev_api();
    $Positions_prev->post();
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Positions_prev_api = new Positions_prev_api();
    $Positions_prev->put();
}
