<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_5.php';
require_once '../../../../utils/send.php';

// TYPE 4 file
class Additional_responsibilities_prev_api
{
    private $Additional_responsibilities_prev;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Additional_responsibilities_prev = new Type_5($db);

        // Set table name
        $this->Additional_responsibilities_prev->table = 'faculty_additional_responsibilities_prev';

        // Set column names
        $this->Additional_responsibilities_prev->id_name = 'additional_responsibility_prev_id';
        $this->Additional_responsibilities_prev->text_name = 'additional_responsibility_prev';
        $this->Additional_responsibilities_prev->from_name = 'additional_responsibility_prev_from';
        $this->Additional_responsibilities_prev->to_name = 'additional_responsibility_prev_to';
    }

    // Get all the data of a user's additional_responsibility_prev
    public function get()
    {
        // Get the user info from DB
        $this->Additional_responsibilities_prev->user_id = $_GET['ID'];
        $all_data = $this->Additional_responsibilities_prev->read_by_id();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about Additional_responsibilities_prev found');
            die();
        }
    }
    // POST a new user's additional_responsibility_prev
    public function post()
    {
        if (!$this->Additional_responsibilities_prev->create()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'additional_responsibility_prev cannot be added');
            die();
        }
    }

    // PUT a user's additional_responsibility_prev
    public function update($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Additional_responsibilities_prev->update($update_str)) {
                // If can't update the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's additional_responsibility_prev
    public function delete_data()
    {
        if (!$this->Additional_responsibilities_prev->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Additional_responsibilities_prev
    public function put()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's additional_responsibility_prev info from DB
        $this->Additional_responsibilities_prev->user_id = $_SESSION['user_id'];
        $all_data = $this->Additional_responsibilities_prev->read_by_id();

        // Store all additional_responsibility_prev_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Additional_responsibilities_prev->text_title = $data[$count]->additional_responsibility_prev;
            $this->Additional_responsibilities_prev->from_text = $data[$count]->additional_responsibility_prev_from;
            $this->Additional_responsibilities_prev->to_int = $data[$count]->additional_responsibility_prev_to;

            if ($data[$count]->additional_responsibility_prev_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->additional_responsibility_prev_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['additional_responsibility_prev_id'], $data_IDs)) {
                $this->Additional_responsibilities_prev->id = (int)$DB_data[$count]['additional_responsibility_prev_id'];
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
                if ($element['additional_responsibility_prev_id'] == $data[$count]->additional_responsibility_prev_id) {
                    $this->Additional_responsibilities_prev->id = $element['additional_responsibility_prev_id'];
                    $this->Additional_responsibilities_prev->text_title = $data[$count]->additional_responsibility_prev;
                    $this->Additional_responsibilities_prev->from_text = $data[$count]->additional_responsibility_prev_from;
                    $this->Additional_responsibilities_prev->to_int = $data[$count]->additional_responsibility_prev_to;

                    $this->update($element['additional_responsibility_prev'], $data[$count]->additional_responsibility_prev, 'additional_responsibility_prev');
                    $this->update($element['additional_responsibility_prev_from'], $data[$count]->additional_responsibility_prev_from, 'additional_responsibility_prev_from');
                    $this->update($element['additional_responsibility_prev_to'], $data[$count]->additional_responsibility_prev_to, 'additional_responsibility_prev_to');

                    break;
                }
            }

            ++$count;
        }

        $this->get();
    }
}

// GET all the user's Additional_responsibilities_prev
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Additional_responsibilities_prev_api = new Additional_responsibilities_prev_api();
    $Additional_responsibilities_prev_api->get();
}

// To check if an user is logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...

// POST/UPDATE (PUT) a user's Additional_responsibilities_prev
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Additional_responsibilities_prev_api = new Additional_responsibilities_prev_api();
    $Additional_responsibilities_prev_api->put();
}
