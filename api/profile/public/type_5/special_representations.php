<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_5.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';
require_once '../../../../utils/loggedin_verified.php';

// TYPE 5 file
class Special_respresentations_api extends Type_5 implements api
{
    private $Special_representations;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Special_representations = new Type_5($db);

        // Set table name
        $this->Special_representations->table = 'faculty_special_representations';

        // Set column names
        $this->Special_representations->id_name = 'special_representation_id';
        $this->Special_representations->text_name = 'special_representation';
        $this->Special_representations->from_name = 'special_representation_from';
        $this->Special_representations->to_name = 'special_representation_to';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Special_representations->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about special representations found');
            die();
        }
    }

    // Get all the data of a user's special_representation
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->Special_representations->user_id = $id;
        $all_data = $this->Special_representations->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about special representations found');
            die();
        }
    }
    // POST a new user's special_representation
    public function post()
    {
        if (!$this->Special_representations->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'special representation cannot be added');
            die();
        }
    }

    // PUT a user's special_representation
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Special_representations->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's special_representation
    public function delete_by_id()
    {
        if (!$this->Special_representations->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Special_representations
    public function put()
    {
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's special_representation info from DB
        $this->Special_representations->user_id = $_SESSION['user_id'];
        $all_data = $this->Special_representations->read_row();

        // Store all special_representation_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Special_representations->text_title = $data[$count]->special_representation;

            $from = date('Y-m-01', strtotime($data[$count]->special_representation_from));
            $this->Special_representations->from_text = $from;
            $to = date('Y-m-01', strtotime($data[$count]->special_representation_to));
            $this->Special_representations->to_int = $to;

            if ($data[$count]->special_representation_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->special_representation_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['special_representation_id'], $data_IDs)) {
                $this->Special_representations->id = (int)$DB_data[$count]['special_representation_id'];
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
                if ($element['special_representation_id'] == $data[$count]->special_representation_id) {
                    $this->Special_representations->id = $element['special_representation_id'];
                    $this->Special_representations->text_title = $data[$count]->special_representation;

                    $from = date('Y-m-01', strtotime($data[$count]->special_representation_from));
                    $this->Special_representations->from_text = $from;
                    $to = date('Y-m-01', strtotime($data[$count]->special_representation_to));
                    $this->Special_representations->to_int = $to;

                    $this->update_by_id($element['special_representation'], $data[$count]->special_representation, 'special_representation');
                    $this->update_by_id($element['special_representation_from'], $from, 'special_representation_from');
                    $this->update_by_id($element['special_representation_to'], $to, 'special_representation_to');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id($_SESSION['user_id']);
    }
}

// GET all the user's Special_representations
// GET all the user info
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Special_respresentations_api = new Special_respresentations_api();
    if (isset($_GET['ID'])) {
        $Special_respresentations_api->get_by_id($_GET['ID']);
    } else {
        $Special_respresentations_api->get();
    }
}

// To check if an user is logged in and verified
loggedin_verified();

// If a user logged in ...

// POST/UPDATE (PUT) a user's Special_representations
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Special_respresentations_api = new Special_respresentations_api();
    $Special_respresentations_api->put();
}
