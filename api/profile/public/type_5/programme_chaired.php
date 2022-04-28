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
class Programme_chaired_api extends Type_5 implements api
{
    private $Programme_chaired;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Programme_chaired = new Type_5($db);

        // Set table name
        $this->Programme_chaired->table = 'faculty_programme_chaired';

        // Set column names
        $this->Programme_chaired->id_name = 'programme_chaired_id';
        $this->Programme_chaired->text_name = 'programme_chaired';
        $this->Programme_chaired->from_name = 'programme_chaired_from';
        $this->Programme_chaired->to_name = 'programme_chaired_to';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Programme_chaired->read();

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

    // Get all the data of a user's programme_chaired
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->Programme_chaired->user_id = $id;
        $all_data = $this->Programme_chaired->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about Programme_chaired found');
            die();
        }
    }
    // POST a new user's programme_chaired
    public function post()
    {
        if (!$this->Programme_chaired->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'programme_chaired cannot be added');
            die();
        }
    }

    // PUT a user's programme_chaired
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Programme_chaired->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's programme_chaired
    public function delete_by_id()
    {
        if (!$this->Programme_chaired->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Programme_chaired
    public function put()
    {
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's programme_chaired info from DB
        $this->Programme_chaired->user_id = $_SESSION['user_id'];
        $all_data = $this->Programme_chaired->read_row();

        // Store all programme_chaired_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Programme_chaired->text_title = $data[$count]->programme_chaired;
            $this->Programme_chaired->from_text = $data[$count]->programme_chaired_from;
            $this->Programme_chaired->to_int = $data[$count]->programme_chaired_to;

            if ($data[$count]->programme_chaired_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->programme_chaired_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['programme_chaired_id'], $data_IDs)) {
                $this->Programme_chaired->id = (int)$DB_data[$count]['programme_chaired_id'];
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
                if ($element['programme_chaired_id'] == $data[$count]->programme_chaired_id) {
                    $this->Programme_chaired->id = $element['programme_chaired_id'];
                    $this->Programme_chaired->text_title = $data[$count]->programme_chaired;
                    $this->Programme_chaired->from_text = $data[$count]->programme_chaired_from;
                    $this->Programme_chaired->to_int = $data[$count]->programme_chaired_to;

                    $this->update_by_id($element['programme_chaired'], $data[$count]->programme_chaired, 'programme_chaired');
                    $this->update_by_id($element['programme_chaired_from'], $data[$count]->programme_chaired_from, 'programme_chaired_from');
                    $this->update_by_id($element['programme_chaired_to'], $data[$count]->programme_chaired_to, 'programme_chaired_to');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id($_SESSION['user_id']);
    }
}

// GET all the user's Programme_chaired
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Programme_chaired_api = new Programme_chaired_api();
    if (isset($_GET['ID'])) {
        $Programme_chaired_api->get_by_id($_GET['ID']);
    } else {
        $Programme_chaired_api->get();
    }
}

// To check if an user is logged in and verified
loggedin_verified();

// If a user logged in ...

// POST/UPDATE (PUT) a user's Programme_chaired
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Programme_chaired_api = new Programme_chaired_api();
    $Programme_chaired_api->put();
}
