<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_6.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';
require_once '../../../../utils/loggedin_verified.php';

// TYPE 6 file
class Exp_abroad_api extends Type_6 implements api
{
    private $Exp_abroad;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Exp_abroad = new Type_6($db);

        // Set table name
        $this->Exp_abroad->table = 'faculty_exp_abroad';

        // Set column names
        $this->Exp_abroad->id_name = 'exp_abroad_id';
        $this->Exp_abroad->text_name = 'exp_abroad';
        $this->Exp_abroad->from_name = 'exp_abroad_from';
        $this->Exp_abroad->to_name = 'exp_abroad_to';
        $this->Exp_abroad->text_int_name = 'purpose_of_visit';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Exp_abroad->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about experience abroad found');
            die();
        }
    }

    // Get all the data of a user's exp_abroad
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->Exp_abroad->user_id = $id;
        $all_data = $this->Exp_abroad->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about experience abroad found');
            die();
        }
    }
    // POST a new user's exp_abroad
    public function post()
    {
        if (!$this->Exp_abroad->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'experience abroad cannot be added');
            die();
        }
    }

    // PUT a user's exp_abroad
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Exp_abroad->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's exp_abroad
    public function delete_by_id()
    {
        if (!$this->Exp_abroad->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Exp_abroad
    public function put()
    {
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's exp_abroad info from DB
        $this->Exp_abroad->user_id = $_SESSION['user_id'];
        $all_data = $this->Exp_abroad->read_row();

        // Store all exp_abroad_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Exp_abroad->text = $data[$count]->exp_abroad;

            $from = date('Y-m-01', strtotime($data[$count]->exp_abroad_from));
            $this->Exp_abroad->from = $from;
            $to = date('Y-m-01', strtotime($data[$count]->exp_abroad_to));
            $this->Exp_abroad->to = $to;

            $this->Exp_abroad->text_int = $data[$count]->purpose_of_visit;

            if ($data[$count]->exp_abroad_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->exp_abroad_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['exp_abroad_id'], $data_IDs)) {
                $this->Exp_abroad->id = (int)$DB_data[$count]['exp_abroad_id'];
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
                if ($element['exp_abroad_id'] == $data[$count]->exp_abroad_id) {
                    $this->Exp_abroad->id = $element['exp_abroad_id'];
                    $this->Exp_abroad->text = $data[$count]->exp_abroad;

                    $from = date('Y-m-01', strtotime($data[$count]->exp_abroad_from));
                    $this->Exp_abroad->from = $from;
                    $to = date('Y-m-01', strtotime($data[$count]->exp_abroad_to));
                    $this->Exp_abroad->to = $to;
        
                    $this->Exp_abroad->text_int = $data[$count]->purpose_of_visit;

                    $this->update_by_id($element['exp_abroad'], $data[$count]->exp_abroad, 'exp_abroad');
                    $this->update_by_id($element['exp_abroad_from'], $from, 'exp_abroad_from');
                    $this->update_by_id($element['exp_abroad_to'], $to, 'exp_abroad_to');
                    $this->update_by_id($element['purpose_of_visit'], $data[$count]->purpose_of_visit, 'purpose_of_visit');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id($_SESSION['user_id']);
    }
}

// GET all the user's Exp_abroad
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Exp_abroad_api = new Exp_abroad_api();
    if (isset($_GET['ID'])) {
        $Exp_abroad_api->get_by_id($_GET['ID']);
    } else {
        $Exp_abroad_api->get();
    }
}

// To check if an user is logged in and verified
loggedin_verified();

// If a user logged in ...

// POST/UPDATE (PUT) a user's Exp_abroad
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Exp_abroad_api = new Exp_abroad_api();
    $Exp_abroad_api->put();
}
