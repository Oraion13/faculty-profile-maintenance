<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_5.php';
require_once '../../../../utils/send.php';
require_once '../../../../utils/api.php';

// TYPE 5 file
class Degree_api extends Type_5 implements api
{
    private $Degree;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Degree = new Type_5($db);

        // Set table name
        $this->Degree->table = 'faculty_degree';

        // Set column names
        $this->Degree->id_name = 'degree_id';
        $this->Degree->text_name = 'degree';
        $this->Degree->from_name = 'degree_from';
        $this->Degree->to_name = 'degree_to';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Degree->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about Area of specialization found');
            die();
        }
    }

    // Get all the data of a user's degree
    public function get_by_id()
    {
        // Get the user info from DB
        $this->Degree->user_id = $_GET['ID'];
        $all_data = $this->Degree->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about Degree found');
            die();
        }
    }
    // POST a new user's degree
    public function post()
    {
        if (!$this->Degree->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'degree cannot be added');
            die();
        }
    }

    // PUT a user's degree
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Degree->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's degree
    public function delete_by_id()
    {
        if (!$this->Degree->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Degree
    public function put()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's degree info from DB
        $this->Degree->user_id = $_SESSION['user_id'];
        $all_data = $this->Degree->read_row();

        // Store all degree_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Degree->text_title = $data[$count]->degree;
            $this->Degree->from_text = $data[$count]->degree_from;
            $this->Degree->to_int = $data[$count]->degree_to;

            if ($data[$count]->degree_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->degree_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['degree_id'], $data_IDs)) {
                $this->Degree->id = (int)$DB_data[$count]['degree_id'];
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
                if ($element['degree_id'] == $data[$count]->degree_id) {
                    $this->Degree->id = $element['degree_id'];
                    $this->Degree->text_title = $data[$count]->degree;
                    $this->Degree->from_text = $data[$count]->degree_from;
                    $this->Degree->to_int = $data[$count]->degree_to;

                    $this->update_by_id($element['degree'], $data[$count]->degree, 'degree');
                    $this->update_by_id($element['degree_from'], $data[$count]->degree_from, 'degree_from');
                    $this->update_by_id($element['degree_to'], $data[$count]->degree_to, 'degree_to');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id();
    }
}

// GET all the user's Degree
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Degree_api = new Degree_api();
    if (isset($_GET['ID'])) {
        $Degree_api->get_by_id();
    } else {
        $Degree_api->get();
    }
}

// To check if an user is logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...

// POST/UPDATE (PUT) a user's Degree
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Degree_api = new Degree_api();
    $Degree_api->put();
}
