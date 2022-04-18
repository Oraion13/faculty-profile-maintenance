<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_5.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';

// TYPE 5 file
class Other_employment_api extends Type_5 implements api
{
    private $Other_employment;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Other_employment = new Type_5($db);

        // Set table name
        $this->Other_employment->table = 'faculty_other_employment';

        // Set column names
        $this->Other_employment->id_name = 'other_employment_id';
        $this->Other_employment->text_name = 'other_employment';
        $this->Other_employment->from_name = 'other_employment_from';
        $this->Other_employment->to_name = 'other_employment_to';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Other_employment->read();

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

    // Get all the data of a user's other_employment
    public function get_by_id()
    {
        // Get the user info from DB
        $this->Other_employment->user_id = $_GET['ID'];
        $all_data = $this->Other_employment->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about Other_employment found');
            die();
        }
    }
    // POST a new user's other_employment
    public function post()
    {
        if (!$this->Other_employment->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'other_employment cannot be added');
            die();
        }
    }

    // PUT a user's other_employment
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Other_employment->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's other_employment
    public function delete_by_id()
    {
        if (!$this->Other_employment->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Other_employment
    public function put()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's other_employment info from DB
        $this->Other_employment->user_id = $_SESSION['user_id'];
        $all_data = $this->Other_employment->read_row();

        // Store all other_employment_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Other_employment->text_title = $data[$count]->other_employment;
            $this->Other_employment->from_text = $data[$count]->other_employment_from;
            $this->Other_employment->to_int = $data[$count]->other_employment_to;

            if ($data[$count]->other_employment_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->other_employment_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['other_employment_id'], $data_IDs)) {
                $this->Other_employment->id = (int)$DB_data[$count]['other_employment_id'];
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
                if ($element['other_employment_id'] == $data[$count]->other_employment_id) {
                    $this->Other_employment->id = $element['other_employment_id'];
                    $this->Other_employment->text_title = $data[$count]->other_employment;
                    $this->Other_employment->from_text = $data[$count]->other_employment_from;
                    $this->Other_employment->to_int = $data[$count]->other_employment_to;

                    $this->update_by_id($element['other_employment'], $data[$count]->other_employment, 'other_employment');
                    $this->update_by_id($element['other_employment_from'], $data[$count]->other_employment_from, 'other_employment_from');
                    $this->update_by_id($element['other_employment_to'], $data[$count]->other_employment_to, 'other_employment_to');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id();
    }
}

// GET all the user's Other_employment
// GET all the user info
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Other_employment_api = new Other_employment_api();
    if (isset($_GET['ID'])) {
        $Other_employment_api->get_by_id();
    } else {
        $Other_employment_api->get();
    }
}

// To check if an user is logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...

// POST/UPDATE (PUT) a user's Other_employment
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Other_employment_api = new Other_employment_api();
    $Other_employment_api->put();
}
