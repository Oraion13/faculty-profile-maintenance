<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_4.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';
require_once '../../../../utils/loggedin_verified.php';

// TYPE 4 file
class Invigilation_duties_api extends Type_4 implements api
{
    private $Invigilation_duties;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Invigilation_duties = new Type_4($db);

        // Set table name
        $this->Invigilation_duties->table = 'faculty_invigilation_duties';

        // Set column names
        $this->Invigilation_duties->id_name = 'invigilation_duty_id';
        $this->Invigilation_duties->text_name = 'invigilation_duty';
        $this->Invigilation_duties->from_name = 'invigilation_duty_at';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Invigilation_duties->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about invigilation duty found');
            die();
        }
    }

    // Get all the data of a user's invigilation duty by ID
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->Invigilation_duties->user_id = $id;
        $all_data = $this->Invigilation_duties->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about invigilation duty found');
            die();
        }
    }
            
    // Get all data by dates
    public function get_by_date($start, $end)
    {
        // Get data from DB
        $this->Invigilation_duties->start = $start;
        $this->Invigilation_duties->end = $end;
        $all_data = $this->Invigilation_duties->read_row_date();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about invigilation duties found');
            die();
        }
    }

    // POST a new user's invigilation duty
    public function post()
    {
        if (!$this->Invigilation_duties->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'invigilation duty cannot be added');
            die();
        }
    }

    // PUT a user's invigilation duty
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Invigilation_duties->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's invigilation duty
    public function delete_by_id()
    {
        if (!$this->Invigilation_duties->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Invigilation_duties
    public function put()
    {
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's invigilation duty info from DB
        $this->Invigilation_duties->user_id = $_SESSION['user_id'];
        $all_data = $this->Invigilation_duties->read_row();

        // Store all invigilation_duty_id 's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Invigilation_duties->text = $data[$count]->invigilation_duty;
            $at = date('Y-m-01', strtotime($data[$count]->invigilation_duty_at));
            $this->Invigilation_duties->from = $at;

            if ($data[$count]->invigilation_duty_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->invigilation_duty_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['invigilation_duty_id'], $data_IDs)) {
                $this->Invigilation_duties->id = (int)$DB_data[$count]['invigilation_duty_id'];
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
                if ($element['invigilation_duty_id'] == $data[$count]->invigilation_duty_id) {
                    $this->Invigilation_duties->id = $element['invigilation_duty_id'];
                    $this->Invigilation_duties->text = $data[$count]->invigilation_duty;
                    $at = date('Y-m-01', strtotime($data[$count]->invigilation_duty_at));
                    $this->Invigilation_duties->from = $at;

                    $this->update_by_id($element['invigilation_duty'], $data[$count]->invigilation_duty, 'invigilation_duty');
                    $this->update_by_id($element['invigilation_duty_at'], $at, 'invigilation_duty_at');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id($_SESSION['user_id']);
    }
}

// To check if an user is logged in and verified
loggedin_verified();

// If a user logged in ...

// GET all the user's Invigilation_duties
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Invigilation_duties_api = new Invigilation_duties_api();
    if (isset($_GET['ID'])) {
        $Invigilation_duties_api->get_by_id($_GET['ID']);
    } else if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1 && isset($_GET['from']) && isset($_GET['to'])) {
        $Invigilation_duties_api->get_by_date($_GET['from'], $_GET['to']);
    } else {
        $Invigilation_duties_api->get();
    }
}

// POST/UPDATE (PUT) a user's Invigilation_duties
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Invigilation_duties_api = new Invigilation_duties_api();
    $Invigilation_duties_api->put();
}
