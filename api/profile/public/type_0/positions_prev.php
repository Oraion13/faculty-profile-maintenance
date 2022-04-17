<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Positions_prev.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';

class Positions_prev_api extends Positions_prev implements api
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


    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Positions_prev->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about previous positions found');
            die();
        }
    }

    // Get all the data of a user's previous position by ID
    public function get_by_id()
    {
        // Get the user info from DB
        $this->Positions_prev->user_id = $_GET['ID'];
        $all_data = $this->Positions_prev->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about previous positions found');
            die();
        }
    }

    // POST a new user's previous position
    public function post()
    {
        if (!$this->Positions_prev->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'previous positions cannot be added');
            die();
        }
    }

    // PUT a user's previous position
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Positions_prev->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's previous position
    public function delete_by_id()
    {
        if (!$this->Positions_prev->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's previous positions
    public function put()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // if (count($data) <= 0) {
        //     send(400, 'error', 'no data recieved');
        //     die();
        // }

        // Get all the user's previous position info from DB
        $this->Positions_prev->user_id = $_SESSION['user_id'];
        $all_data = $this->Positions_prev->read_row();

        // Store all position_prev_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Positions_prev->position_id = $data[$count]->position_id;
            $this->Positions_prev->department_id = $data[$count]->department_id;
            $this->Positions_prev->position_prev_where = $data[$count]->position_prev_where;
            $this->Positions_prev->position_prev_from = $data[$count]->position_prev_from;
            $this->Positions_prev->position_prev_to = $data[$count]->position_prev_to;

            if ($data[$count]->position_prev_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->position_prev_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['position_prev_id'], $data_IDs)) {
                $this->Positions_prev->position_prev_id = (int)$DB_data[$count]['position_prev_id'];
                $this->delete_by_id();
            }

            ++$count;
        }

        // Update the data which is available
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            // print_r($row);
            foreach ($DB_data as $key => $element) {
                if ($element['position_prev_id'] == $data[$count]->position_prev_id) {
                    $this->Positions_prev->position_prev_id = $element['position_prev_id'];
                    $this->Positions_prev->position_id = $data[$count]->position_id;
                    $this->Positions_prev->department_id = $data[$count]->department_id;
                    $this->Positions_prev->position_prev_where = $data[$count]->position_prev_where;
                    $this->Positions_prev->position_prev_from = $data[$count]->position_prev_from;
                    $this->Positions_prev->position_prev_to = $data[$count]->position_prev_to;

                    $this->update_by_id($element['position_id'], $data[$count]->position_id, 'position_id');
                    $this->update_by_id($element['department_id'], $data[$count]->department_id, 'department_id');
                    $this->update_by_id($element['position_prev_where'], $data[$count]->position_prev_where, 'position_prev_where');
                    $this->update_by_id($element['position_prev_from'], $data[$count]->position_prev_from, 'position_prev_from');
                    $this->update_by_id($element['position_prev_to'], $data[$count]->position_prev_to, 'position_prev_to');

                    break;
                }
            }


            ++$count;
        }

        $this->get_by_id();
    }
}

// GET all the user's previous positions
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Positions_prev_api = new Positions_prev_api();
    if (isset($_GET['ID'])) {
        $Positions_prev_api->get_by_id();
    } else {
        $Positions_prev_api->get();
    }
}

// To check if an user is logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...

// POST/UPDATE (PUT) a user's previous positions
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Positions_prev_api = new Positions_prev_api();
    $Positions_prev_api->put();
}
