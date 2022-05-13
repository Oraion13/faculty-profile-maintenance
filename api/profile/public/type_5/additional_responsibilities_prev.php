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
class Additional_responsibilities_prev_api extends Type_5 implements api
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

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Additional_responsibilities_prev->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about additional responsibilities - previous found');
            die();
        }
    }

    // Get all the data of a user's additional_responsibility_prev
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->Additional_responsibilities_prev->user_id = $id;
        $all_data = $this->Additional_responsibilities_prev->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about additional responsibilities - previous found');
            die();
        }
    }
        
    // Get all data by dates
    public function get_by_date($start, $end)
    {
        // Get data from DB
        $this->Additional_responsibilities_prev->start = $start;
        $this->Additional_responsibilities_prev->end = $end;
        $all_data = $this->Additional_responsibilities_prev->read_row_date();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about additional responsibilities - previous found');
            die();
        }
    }

    // POST a new user's additional_responsibility_prev
    public function post()
    {
        if (!$this->Additional_responsibilities_prev->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'previous additional responsibility cannot be added');
            die();
        }
    }

    // PUT a user's additional_responsibility_prev
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Additional_responsibilities_prev->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's additional_responsibility_prev
    public function delete_by_id()
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
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's additional_responsibility_prev info from DB
        $this->Additional_responsibilities_prev->user_id = $_SESSION['user_id'];
        $all_data = $this->Additional_responsibilities_prev->read_row();

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

            $from = date('Y-m-01', strtotime($data[$count]->additional_responsibility_prev_from));
            $this->Additional_responsibilities_prev->from_text = $from;
            $to = date('Y-m-01', strtotime($data[$count]->additional_responsibility_prev_to));
            $this->Additional_responsibilities_prev->to_int = $to;

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
                if ($element['additional_responsibility_prev_id'] == $data[$count]->additional_responsibility_prev_id) {
                    $this->Additional_responsibilities_prev->id = $element['additional_responsibility_prev_id'];
                    $this->Additional_responsibilities_prev->text_title = $data[$count]->additional_responsibility_prev;

                    $from = date('Y-m-01', strtotime($data[$count]->additional_responsibility_prev_from));
                    $this->Additional_responsibilities_prev->from_text = $from;
                    $to = date('Y-m-01', strtotime($data[$count]->additional_responsibility_prev_to));
                    $this->Additional_responsibilities_prev->to_int = $to;

                    $this->update_by_id($element['additional_responsibility_prev'], $data[$count]->additional_responsibility_prev, 'additional_responsibility_prev');
                    $this->update_by_id($element['additional_responsibility_prev_from'], $from, 'additional_responsibility_prev_from');
                    $this->update_by_id($element['additional_responsibility_prev_to'], $to, 'additional_responsibility_prev_to');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id($_SESSION['user_id']);
    }
}

// GET all the user's Additional_responsibilities_prev
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Additional_responsibilities_prev_api = new Additional_responsibilities_prev_api();
    if (isset($_GET['ID'])) {
        $Additional_responsibilities_prev_api->get_by_id($_GET['ID']);
    } else if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1 && isset($_GET['from']) && isset($_GET['to'])) {
        $Additional_responsibilities_prev_api->get_by_date($_GET['from'], $_GET['to']);
    } else {
        $Additional_responsibilities_prev_api->get();
    }
}

// To check if an user is logged in and verified
loggedin_verified();

// If a user logged in ...

// POST/UPDATE (PUT) a user's Additional_responsibilities_prev
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Additional_responsibilities_prev_api = new Additional_responsibilities_prev_api();
    $Additional_responsibilities_prev_api->put();
}
