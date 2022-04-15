<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_5.php';
require_once '../../../../utils/send.php';

// TYPE 5 file
class Patents_api
{
    private $Patents;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Patents = new Type_5($db);

        // Set table name
        $this->Patents->table = 'faculty_patents';

        // Set column names
        $this->Patents->id_name = 'patent_id';
        $this->Patents->text_name = 'patent';
        $this->Patents->from_name = 'file_number';
        $this->Patents->to_name = 'patent_at';
    }

    // Get all the data of a user's patent
    public function get()
    {
        // Get the user info from DB
        $this->Patents->user_id = $_GET['ID'];
        $all_data = $this->Patents->read_by_id();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about Patents found');
            die();
        }
    }
    // POST a new user's patent
    public function post()
    {
        if (!$this->Patents->create()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'patent cannot be added');
            die();
        }
    }

    // PUT a user's patent
    public function update($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Patents->update($update_str)) {
                // If can't update the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's patent
    public function delete_data()
    {
        if (!$this->Patents->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Patents
    public function put()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's patent info from DB
        $this->Patents->user_id = $_SESSION['user_id'];
        $all_data = $this->Patents->read_by_id();

        // Store all patent_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Patents->text_title = $data[$count]->patent;
            $this->Patents->from_text = $data[$count]->file_number;
            $this->Patents->to_int = $data[$count]->patent_at;

            if ($data[$count]->patent_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->patent_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['patent_id'], $data_IDs)) {
                $this->Patents->id = (int)$DB_data[$count]['patent_id'];
                $this->delete_data();
            }

            ++$count;
        }

        // Update the data which is available
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            // print_r($row);
            foreach ($DB_data as $key => $element) {
                if ($element['patent_id'] == $data[$count]->patent_id) {
                    $this->Patents->id = $element['patent_id'];
                    $this->Patents->text_title = $data[$count]->patent;
                    $this->Patents->from_text = $data[$count]->file_number;
                    $this->Patents->to_int = $data[$count]->patent_at;

                    $this->update($element['patent'], $data[$count]->patent, 'patent');
                    $this->update($element['file_number'], $data[$count]->file_number, 'file_number');
                    $this->update($element['patent_at'], $data[$count]->patent_at, 'patent_at');

                    break;
                }
            }

            ++$count;
        }

        $this->get();
    }
}

// GET all the user's Patents
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Patents_api = new Patents_api();
    $Patents_api->get();
}

// To check if an user is logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...

// POST/UPDATE (PUT) a user's Patents
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Patents_api = new Patents_api();
    $Patents_api->put();
}