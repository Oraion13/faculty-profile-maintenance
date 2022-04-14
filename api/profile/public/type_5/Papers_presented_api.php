<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_5.php';
require_once '../../../../utils/send.php';

// TYPE 5 file
class Papers_presented_api
{
    private $Papers_presented;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Papers_presented = new Type_5($db);

        // Set table name
        $this->Papers_presented->table = 'faculty_papers_presented';

        // Set column names
        $this->Papers_presented->id_name = 'paper_presented_id';
        $this->Papers_presented->text_name = 'paper_presented';
        $this->Papers_presented->from_name = 'paper_presented_at';
        $this->Papers_presented->to_name = 'is_international';
    }

    // Get all the data of a user's paper_presented
    public function get()
    {
        // Get the user info from DB
        $this->Papers_presented->user_id = $_GET['ID'];
        $all_data = $this->Papers_presented->read_by_id();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about Papers_presented found');
            die();
        }
    }
    // POST a new user's paper_presented
    public function post()
    {
        if (!$this->Papers_presented->create()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'paper_presented cannot be added');
            die();
        }
    }

    // PUT a user's paper_presented
    public function update($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Papers_presented->update($update_str)) {
                // If can't update the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's paper_presented
    public function delete_data()
    {
        if (!$this->Papers_presented->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Papers_presented
    public function put()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's paper_presented info from DB
        $this->Papers_presented->user_id = $_SESSION['user_id'];
        $all_data = $this->Papers_presented->read_by_id();

        // Store all paper_presented_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Papers_presented->text_title = $data[$count]->paper_presented;
            $this->Papers_presented->from_text = $data[$count]->paper_presented_at;
            $this->Papers_presented->to_int = $data[$count]->is_international;

            if ($data[$count]->paper_presented_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->paper_presented_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['paper_presented_id'], $data_IDs)) {
                $this->Papers_presented->id = (int)$DB_data[$count]['paper_presented_id'];
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
                if ($element['paper_presented_id'] == $data[$count]->paper_presented_id) {
                    $this->Papers_presented->id = $element['paper_presented_id'];
                    $this->Papers_presented->text_title = $data[$count]->paper_presented;
                    $this->Papers_presented->from_text = $data[$count]->paper_presented_at;
                    $this->Papers_presented->to_int = $data[$count]->is_international;

                    $this->update($element['paper_presented'], $data[$count]->paper_presented, 'paper_presented');
                    $this->update($element['paper_presented_at'], $data[$count]->paper_presented_at, 'paper_presented_at');
                    $this->update($element['is_international'], $data[$count]->is_international, 'is_international');

                    break;
                }
            }

            ++$count;
        }

        $this->get();
    }
}

// GET all the user's Papers_presented
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Papers_presented_api = new Papers_presented_api();
    $Papers_presented_api->get();
}

// To check if an user is logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...

// POST/UPDATE (PUT) a user's Papers_presented
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Papers_presented_api = new Papers_presented_api();
    $Papers_presented_api->put();
}
