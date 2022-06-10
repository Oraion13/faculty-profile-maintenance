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
class Papers_presented_api extends Type_5 implements api
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

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Papers_presented->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about papers presented found');
            die();
        }
    }

    // Get all the data of a user's paper_presented
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->Papers_presented->user_id = $id;
        $all_data = $this->Papers_presented->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about papers presented found');
            die();
        }
    }
        
    // Get all data by dates
    public function get_by_date($start, $end)
    {
        // Get data from DB
        $from = date('Y-m-01', strtotime($start));
        $this->Papers_presented->start = $from;
        $to = date('Y-m-01', strtotime($end));
        $this->Papers_presented->end = $to;
        $all_data = $this->Papers_presented->read_row_date();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about papers presented found');
            die();
        }
    }

    // POST a new user's paper_presented
    public function post()
    {
        if (!$this->Papers_presented->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'paper cannot be added');
            die();
        }
    }

    // PUT a user's paper_presented
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Papers_presented->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's paper_presented
    public function delete_by_id()
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
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's paper_presented info from DB
        $this->Papers_presented->user_id = $_SESSION['user_id'];
        $all_data = $this->Papers_presented->read_row();

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

            $at = date('Y-m-01', strtotime($data[$count]->paper_presented_at));
            $this->Papers_presented->from_text = $at;
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
                if ($element['paper_presented_id'] == $data[$count]->paper_presented_id) {
                    $this->Papers_presented->id = $element['paper_presented_id'];
                    $this->Papers_presented->text_title = $data[$count]->paper_presented;
                    $at = date('Y-m-01', strtotime($data[$count]->paper_presented_at));
                    $this->Papers_presented->from_text = $at;
                    $this->Papers_presented->to_int = $data[$count]->is_international;

                    $this->update_by_id($element['paper_presented'], $data[$count]->paper_presented, 'paper_presented');
                    $this->update_by_id($element['paper_presented_at'], $at, 'paper_presented_at');
                    $this->update_by_id($element['is_international'], $data[$count]->is_international, 'is_international');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id($_SESSION['user_id']);
    }
}

// GET all the user's Papers_presented
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Papers_presented_api = new Papers_presented_api();
    if (isset($_GET['ID'])) {
        $Papers_presented_api->get_by_id($_GET['ID']);
    } else if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1 && isset($_GET['from']) && isset($_GET['to'])) {
        $Papers_presented_api->get_by_date($_GET['from'], $_GET['to']);
    } else {
        $Papers_presented_api->get();
    }
}

// To check if an user is logged in and verified
loggedin_verified();

// If a user logged in ...

// POST/UPDATE (PUT) a user's Papers_presented
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Papers_presented_api = new Papers_presented_api();
    $Papers_presented_api->put();
}
